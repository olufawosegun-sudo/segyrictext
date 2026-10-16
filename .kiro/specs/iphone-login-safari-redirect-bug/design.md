# iPhone Login Safari Redirect Bug - Bugfix Design

## Overview

The bug occurs when users log into the iPhone app and are immediately redirected to Safari unexpectedly. This is caused by the payment synchronization JavaScript (`payment-sync.js`) which automatically detects pending deposits in `localStorage` on page load and triggers payment recovery. The recovery mechanism calls `Browser::inApp()` to open Safari for completing the payment, which interrupts the normal post-login flow.

The fix will prevent auto-triggering of `Browser::inApp()` immediately after login by implementing a login session flag that suppresses auto-recovery for a brief window, while preserving the manual deposit functionality, payment verification polling, and proper localStorage cleanup.

## Glossary

- **Bug_Condition (C)**: The condition that triggers the bug - when a user logs in on iPhone and there's a pending deposit in localStorage
- **Property (P)**: The desired behavior - user should land on the dashboard/home screen after login without Safari auto-opening
- **Preservation**: Existing payment recovery, manual deposits, and polling functionality must remain unchanged
- **payment-sync.js**: The global payment synchronization script in `public/js/payment-sync.js` that polls for payment status
- **Browser::inApp()**: The NativePHP method that opens URLs in Safari (SFSafariViewController) from within the app
- **localStorage**: Browser storage containing `segyric_pending_payment` with pending deposit data
- **Auto-recovery**: The mechanism in `payment-sync.js` that automatically detects pending payments and resumes verification

## Bug Details

### Bug Condition

The bug manifests when a user successfully logs into the iPhone app and there is a pending payment stored in `localStorage` from a previous session. The `payment-sync.js` script's `init()` function is called on page load (including after login redirect), detects the pending payment, and automatically calls `startPolling()`. However, in the iOS branch of the deposit flow (lines 2348-2420 in `mobile.blade.php`), the payment initialization process calls `Browser::inApp()` which opens Safari. When this recovery happens immediately after login, it unexpectedly interrupts the user's post-login experience.

**Formal Specification:**
```
FUNCTION isBugCondition(input)
  INPUT: input of type PageLoadContext
  OUTPUT: boolean
  
  RETURN input.justLoggedIn = true
         AND input.platform = 'ios'
         AND localStorage.getItem('segyric_pending_payment') !== null
         AND paymentAge < MAX_PENDING_AGE_MS
         AND autoRecoveryTriggered = true
END FUNCTION
```

### Examples

- **User logs in on iPhone with pending deposit**: User enters credentials → successful login → redirected to home → payment-sync.js detects pending payment → `Browser::inApp()` auto-triggered → Safari opens unexpectedly → user confused
- **User logs in on Android with pending deposit**: User enters credentials → successful login → redirected to home → payment-sync.js detects pending payment → Android in-app modal shows (no Safari redirect) → expected behavior
- **User logs in on iPhone without pending deposit**: User enters credentials → successful login → redirected to home → no pending payment in localStorage → normal flow → expected behavior
- **User manually initiates deposit on iPhone**: User taps deposit button → enters amount → `Browser::inApp()` opens Safari for payment → expected behavior (this must continue working)

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Manual deposit functionality must continue to work exactly as before (user taps deposit → Safari opens for payment)
- Payment verification polling must continue running in the background when payments are pending
- Payment success celebration splash and balance updates must continue working
- Automatic cleanup of expired pending payments (30-minute expiry) must remain unchanged
- All non-iOS platforms (Android, web) must be completely unaffected
- Payment webhook handling and server-side verification must remain unchanged

**Scope:**
All inputs that do NOT involve the post-login auto-recovery scenario should be completely unaffected by this fix. This includes:
- User-initiated deposit flows (tapping deposit button)
- Background polling for existing active payment sessions
- Payment verification after user returns from Safari manually
- Wallet balance updates and UI synchronization
- Other page loads that are not immediately post-login

## Hypothesized Root Cause

Based on the bug description and code analysis, the most likely issues are:

1. **Indiscriminate Auto-Recovery on Page Load**: The `payment-sync.js` `init()` function runs on every page load (including post-login redirects) and immediately attempts to resume payment polling. On iOS, this triggers the code path that calls `Browser::inApp()`, causing Safari to open without user intent.

2. **No Login Context Awareness**: The auto-recovery mechanism has no knowledge of whether the page load is a result of a fresh login vs. a normal page navigation or app resume. It treats all page loads equally.

3. **Missing Post-Login Suppression Window**: There is no mechanism to suppress auto-recovery for a brief period immediately after login to allow the user to settle into the app.

4. **iOS-Specific Safari Trigger**: The iOS native shell branch (lines 2348-2420 in `mobile.blade.php`) is designed to open Safari via `Browser::inApp()` during deposit initialization, but this is being triggered by auto-recovery rather than user action.

## Correctness Properties

Property 1: Bug Condition - Post-Login Safari Auto-Open Suppression

_For any_ page load where the user just completed login on an iOS device and a pending payment exists in localStorage, the fixed payment-sync.js SHALL NOT automatically trigger `Browser::inApp()` or open Safari, allowing the user to land on the dashboard/home screen without interruption. The pending payment SHALL remain in localStorage and polling MAY continue silently in the background.

**Validates: Requirements 2.1, 2.2**

Property 2: Preservation - Manual Deposit and Background Polling

_For any_ user action that is NOT a post-login page load (manual deposit button taps, explicit payment resumption, background polling ticks, page focus/visibility changes after login window), the fixed code SHALL produce exactly the same behavior as the original code, preserving all existing payment functionality including Safari opening for manual deposits, background verification polling, and payment success handling.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**

## Fix Implementation

### Changes Required

Assuming our root cause analysis is correct:

**File**: `public/js/payment-sync.js`

**Function**: `init()` and related auto-recovery logic

**Specific Changes**:

1. **Add Login Session Detection**: 
   - Check for a login-specific session marker (e.g., `segyric_just_logged_in` flag in sessionStorage)
   - This flag should be set by the server-side login handler during the post-login redirect
   - Use `sessionStorage` (not `localStorage`) so it clears when browser/tab closes

2. **Implement Post-Login Suppression Window**:
   - In the `init()` function, before calling `startPolling()`, check if the login flag exists and is fresh (< 5 seconds old)
   - If yes, skip auto-recovery for this page load but keep the pending payment in localStorage
   - Allow background polling to continue silently without triggering the iOS Safari branch

3. **Add Conditional Auto-Recovery Logic**:
   ```javascript
   function shouldSuppressAutoRecovery() {
       try {
           const justLoggedIn = sessionStorage.getItem('segyric_just_logged_in');
           if (!justLoggedIn) return false;
           
           const loginTime = parseInt(justLoggedIn, 10);
           const elapsedMs = Date.now() - loginTime;
           const SUPPRESS_WINDOW_MS = 5000; // 5 seconds
           
           return elapsedMs < SUPPRESS_WINDOW_MS;
       } catch (e) {
           return false; // If sessionStorage unavailable, don't suppress
       }
   }
   ```

4. **Modify init() Function**:
   ```javascript
   function init() {
       const pending = getStoredPendingPayment();
       if (pending) {
           if (shouldSuppressAutoRecovery()) {
               // Suppress auto-recovery immediately after login
               // Clear the login flag after use
               try {
                   sessionStorage.removeItem('segyric_just_logged_in');
               } catch(e) {}
               // Start silent polling only (no Safari trigger)
               startPolling(pending);
           } else {
               // Normal auto-recovery flow
               startPolling(pending);
           }
       }
   }
   ```

5. **Server-Side Integration**: 
   - Modify `AuthController::login()` to inject the login timestamp into sessionStorage via a `<script>` tag or inline JavaScript in the response view
   - Set `sessionStorage.setItem('segyric_just_logged_in', Date.now())` immediately after successful login

**File**: `app/Http/Controllers/Web/AuthController.php`

**Function**: `login()` method (lines 25-70)

**Specific Changes**:

1. **Add Session Flag for Post-Login Suppression**:
   - After successful authentication but before returning the view/redirect
   - Pass a flag to the view indicating fresh login: `->with('justLoggedIn', true)`
   - Or set a flash session variable: `$request->session()->flash('just_logged_in', time());`

2. **View Integration**:
   - In `resources/views/native/home.blade.php` or the mobile layout
   - Add inline script that checks for the login flag and sets sessionStorage
   - This ensures the flag is set before payment-sync.js runs

**File**: `resources/views/native/layouts/mobile.blade.php`

**Location**: In the `<script>` section before payment-sync.js loads

**Specific Changes**:

1. **Add Login Flag Initialization Script**:
   ```javascript
   @if(session('just_logged_in'))
   <script>
       try {
           sessionStorage.setItem('segyric_just_logged_in', '{{ session('just_logged_in') * 1000 }}');
       } catch(e) {
           console.debug('SessionStorage not available for login flag');
       }
   </script>
   @endif
   ```

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that demonstrate the bug on unfixed code, then verify the fix works correctly and preserves existing behavior.

### Exploratory Bug Condition Checking

**Goal**: Surface counterexamples that demonstrate the bug BEFORE implementing the fix. Confirm or refute the root cause analysis. If we refute, we will need to re-hypothesize.

**Test Plan**: Write tests that simulate a login flow on iOS with a pending payment in localStorage, then observe whether Safari is auto-triggered. Run these tests on the UNFIXED code to observe failures and understand the root cause.

**Test Cases**:
1. **Login with Pending Payment on iOS**: Simulate login flow with pending payment in localStorage → observe Safari auto-opens (will fail on unfixed code - demonstrates bug)
2. **Login without Pending Payment on iOS**: Simulate login flow without pending payment → observe normal home screen load (should pass on unfixed code)
3. **Login with Pending Payment on Android**: Simulate login flow on Android with pending payment → observe in-app modal, no Safari redirect (should pass on unfixed code)
4. **Manual Deposit on iOS**: Simulate user tapping deposit button → observe Safari opens correctly (should pass on unfixed code - preservation test baseline)

**Expected Counterexamples**:
- Safari auto-opens immediately after login when pending payment exists in localStorage on iOS
- Possible causes: payment-sync.js init() indiscriminately triggers auto-recovery, no login context awareness, iOS branch auto-triggers Browser::inApp()

### Fix Checking

**Goal**: Verify that for all inputs where the bug condition holds, the fixed function produces the expected behavior.

**Pseudocode:**
```
FOR ALL input WHERE isBugCondition(input) DO
  result := pageLoadAfterLogin_fixed(input)
  ASSERT expectedBehavior(result)
    // Safari does NOT auto-open
    // User lands on dashboard/home
    // Pending payment remains in localStorage
    // Background polling may continue silently
END FOR
```

### Preservation Checking

**Goal**: Verify that for all inputs where the bug condition does NOT hold, the fixed function produces the same result as the original function.

**Pseudocode:**
```
FOR ALL input WHERE NOT isBugCondition(input) DO
  ASSERT pageLoad_original(input) = pageLoad_fixed(input)
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking because:
- It generates many test cases automatically across the input domain (different page loads, user actions, timing scenarios)
- It catches edge cases that manual unit tests might miss (e.g., race conditions, timing issues, partial states)
- It provides strong guarantees that behavior is unchanged for all non-login page loads

**Test Plan**: Observe behavior on UNFIXED code first for manual deposits and background polling, then write property-based tests capturing that behavior.

**Test Cases**:
1. **Manual Deposit Preservation**: Observe that manual deposit button → Safari opens correctly on unfixed code, then write test to verify this continues after fix
2. **Background Polling Preservation**: Observe that background polling continues for active payments on unfixed code, then write test to verify this continues after fix
3. **Payment Success Flow Preservation**: Observe that payment success → splash screen → balance update works on unfixed code, then write test to verify this continues after fix
4. **Payment Expiry Cleanup Preservation**: Observe that old pending payments (>30 min) are cleaned up on unfixed code, then write test to verify this continues after fix
5. **Non-iOS Platform Preservation**: Verify Android and web platforms continue working exactly as before (no changes should affect them)

### Unit Tests

- Test `shouldSuppressAutoRecovery()` function with various scenarios (fresh login, old login, no login flag, sessionStorage unavailable)
- Test `init()` function behavior with and without login suppression flag
- Test that sessionStorage login flag is set correctly after successful login
- Test that login flag is cleared after first use (not persistent across multiple page loads)
- Test edge case where sessionStorage is unavailable (should fall back to normal behavior)

### Property-Based Tests

- Generate random page load scenarios (login, navigation, resume, focus) and verify correct auto-recovery behavior
- Generate random timing scenarios (login timestamps, payment ages) and verify suppression window works correctly
- Test that manual deposit flows always trigger Safari across many scenarios (different amounts, currencies, user states)
- Test that background polling continues correctly across many payment states (pending, verified, expired)

### Integration Tests

- Test full login flow on iOS simulator: login → home screen loads → no Safari auto-open → pending payment persists → manual deposit still works
- Test login flow on Android: login → home screen loads → in-app modal may appear (expected) → verify iOS fix doesn't affect Android
- Test manual deposit flow after fix: tap deposit → enter amount → Safari opens → complete payment → return to app → balance updates
- Test background polling resilience: login → suppress auto-recovery → background polling continues silently → payment completes → balance updates
- Test sessionStorage cleanup: login → flag set → page loads → flag cleared → subsequent page loads use normal auto-recovery
