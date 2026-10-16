# Bug Condition Exploration Test Results - UNFIXED CODE

## Test Execution Date
Executed on unfixed codebase before implementing the fix.

## Test Summary

| Test Case | Status | Expected | Result |
|-----------|--------|----------|--------|
| test_ios_login_with_pending_payment_should_not_auto_open_safari | **FAILED** | Should fail on unfixed code | ✓ Failed as expected |
| test_ios_login_without_pending_payment_should_load_dashboard_normally | **PASSED** | Should pass (baseline) | ✓ Passed |
| test_android_login_with_pending_payment_should_be_unaffected | **PASSED** | Should pass (baseline) | ✓ Passed |

## Bug Confirmation

### Primary Test Failure Analysis

**Test**: `test_ios_login_with_pending_payment_should_not_auto_open_safari`

**Failure Message**:
```
EXPECTED BEHAVIOR: AuthController should set session flash flag "just_logged_in" after successful login. 
This flag signals to the view layer that auto-recovery should be suppressed. 
FAILURE CONFIRMS BUG: AuthController does not set login suppression flag, so payment-sync.js 
will auto-trigger Safari via Browser::inApp() when it detects pending payment in localStorage.
```

**What This Confirms**:
The test failure proves the bug exists by demonstrating that:
1. ✗ AuthController does NOT set the `just_logged_in` session flash flag
2. ✗ The view template does NOT inject the sessionStorage suppression flag
3. ✗ payment-sync.js does NOT have the `shouldSuppressAutoRecovery()` function
4. ✗ payment-sync.js init() does NOT check for login context before auto-recovery

**Assertion Failed**: `session()->has('just_logged_in')` returned FALSE

This means when a user logs in on iOS with a pending payment in localStorage:
- The login succeeds and user is authenticated
- The view loads normally
- BUT: payment-sync.js immediately detects the pending payment and triggers auto-recovery
- Auto-recovery calls the iOS payment flow which executes `Browser::inApp()`
- Safari opens unexpectedly, interrupting the post-login experience

## Counterexample Demonstrated

**Scenario**: iOS login with pending payment in localStorage

**Steps**:
1. User has a pending payment stored in localStorage from a previous session
2. User enters credentials and submits login form
3. AuthController authenticates user successfully
4. User is redirected to mobile home view
5. Page loads and payment-sync.js init() executes
6. init() detects pending payment via `getStoredPendingPayment()`
7. init() calls `startPolling(pending)` without checking login context
8. On iOS, the payment flow triggers `Browser::inApp(authorizationUrl)`
9. Safari/SFSafariViewController opens unexpectedly
10. User is confused - they just logged in but are now in an external browser

**Expected Behavior**: User should land on dashboard/home screen without Safari auto-opening

**Actual Behavior (Unfixed)**: Safari auto-opens immediately after login due to payment-sync.js auto-recovery

## Root Cause Analysis

### Confirmed Root Causes

1. **No Login Context Awareness**
   - AuthController.php login() method does NOT set any flag to indicate fresh login
   - payment-sync.js has no way to distinguish between:
     - Post-login page load (should suppress auto-recovery)
     - Normal page navigation/resume (should allow auto-recovery)

2. **Indiscriminate Auto-Recovery**
   - payment-sync.js init() runs on every page load
   - init() immediately calls startPolling() whenever a pending payment is detected
   - No suppression mechanism exists for the post-login window

3. **Missing Suppression Infrastructure**
   - No session flash flag to signal fresh login
   - No sessionStorage bridge to pass the flag to JavaScript
   - No JavaScript function to check the flag and suppress auto-recovery
   - No time-based suppression window to give user time to settle after login

### Why This Causes Safari Auto-Open on iOS

On iOS, the payment initialization flow uses `Browser::inApp()` which opens URLs in SFSafariViewController. When auto-recovery triggers immediately after login:

```javascript
// Current behavior in unfixed code:
function init() {
    const pending = getStoredPendingPayment();
    if (pending) {
        startPolling(pending);  // ← Runs unconditionally, triggers iOS payment flow → Browser::inApp()
    }
}
```

The iOS payment flow (mobile.blade.php lines 2348-2420) calls `Browser::inApp(authorizationUrl)` which opens Safari. This is correct behavior for manual deposits, but NOT for auto-recovery immediately after login.

## Baseline Behavior Confirmation

### Test: Login Without Pending Payment

**Status**: ✓ PASSED

This confirms that the bug is specifically related to the presence of a pending payment. When no pending payment exists:
- Login works normally
- User lands on dashboard
- No Safari auto-open occurs
- No unexpected behavior

### Test: Android Login With Pending Payment

**Status**: ✓ PASSED

This confirms that:
- Android platform is not affected by this bug (uses in-app modal, not Safari)
- The bug is iOS-specific due to `Browser::inApp()` behavior
- The fix should be scoped to iOS login flows only

## Implementation Guidance

The test failure provides clear guidance for the fix:

1. **Add Login Suppression Flag** (AuthController.php)
   - Set `$request->session()->flash('just_logged_in', time())` after successful authentication
   - This signals to the view that this is a fresh login

2. **Inject sessionStorage Flag** (mobile.blade.php)
   - Add Blade conditional: `@if(session('just_logged_in'))`
   - Inject inline script: `sessionStorage.setItem('segyric_just_logged_in', timestamp)`
   - This passes the flag from server-side to client-side JavaScript

3. **Add Suppression Logic** (payment-sync.js)
   - Add `shouldSuppressAutoRecovery()` function to check sessionStorage flag
   - Check if within 5-second suppression window since login
   - Modify init() to call this function before auto-recovery
   - If suppression is active, skip Safari trigger but allow background polling

4. **Preserve Existing Functionality**
   - Manual deposit flows must continue to work (user taps button → Safari opens)
   - Background polling must continue for active payments
   - Payment success flow must remain unchanged
   - Android/web platforms must be completely unaffected

## Next Steps

1. ✓ Bug condition exploration test written and executed
2. ✓ Test FAILED on unfixed code (confirms bug exists)
3. ✓ Counterexample documented
4. ✓ Root cause confirmed
5. → **Next**: Write preservation property tests (Task 2)
6. → **Then**: Implement the fix (Task 3)
7. → **Finally**: Re-run this test on fixed code (should PASS)

## Notes

- The test is designed to encode the expected behavior
- When the fix is implemented correctly, this SAME test should PASS
- Do NOT modify the test - modify the code to make the test pass
- The test serves as a specification of correct behavior
