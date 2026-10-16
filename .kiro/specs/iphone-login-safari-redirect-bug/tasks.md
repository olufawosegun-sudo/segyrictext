# Implementation Plan

## Overview

This plan implements the fix for the iPhone login Safari redirect bug using the bug condition methodology. The implementation follows a three-phase approach: exploration, preservation, then implementation with verification.

---

## Tasks

- [x] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - Post-Login Safari Auto-Open on iOS
  - **CRITICAL**: This test MUST FAIL on unfixed code - failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior - it will validate the fix when it passes after implementation
  - **GOAL**: Surface counterexamples that demonstrate Safari auto-opens after login with pending payment
  - **Scoped PBT Approach**: Scope the property to the concrete failing case: iOS login with pending payment in localStorage
  - Test implementation details from Bug Condition in design:
    - Simulate user login on iOS device
    - Set `localStorage` with `segyric_pending_payment` (valid, non-expired payment)
    - Trigger page load (simulating post-login redirect)
    - Assert that `Browser::inApp()` is NOT called (or Safari redirect does not occur)
    - Assert that user lands on dashboard/home screen
    - Assert that pending payment remains in localStorage
  - The test assertions should match the Expected Behavior Properties from design
  - Run test on UNFIXED code
  - **EXPECTED OUTCOME**: Test FAILS (this is correct - it proves the bug exists)
  - Document counterexamples found: Safari auto-opens after login, interrupting user flow
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 2.1, 2.2_

- [x] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Manual Deposit and Background Polling Functionality
  - **IMPORTANT**: Follow observation-first methodology
  - Observe behavior on UNFIXED code for non-buggy inputs:
    - Test that manual deposit button → Safari opens correctly (not a post-login scenario)
    - Test that background polling continues for active payments
    - Test that payment success flow works (splash screen, balance update)
    - Test that payment expiry cleanup works (>30 min old payments removed)
    - Test that Android/web platforms are unaffected
  - Write property-based tests capturing observed behavior patterns from Preservation Requirements:
    - **Manual Deposit Preservation**: For any user-initiated deposit (not post-login), `Browser::inApp()` should open Safari
    - **Background Polling Preservation**: For any active payment session, polling should continue in background
    - **Payment Success Preservation**: For any completed payment, success flow should trigger (splash + balance update)
    - **Platform Preservation**: For any Android/web page load with pending payment, behavior should be identical to unfixed code
  - Property-based testing generates many test cases for stronger guarantees
  - Run tests on UNFIXED code
  - **EXPECTED OUTCOME**: Tests PASS (this confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

- [x] 3. Implement fix for iPhone login Safari redirect bug

  - [x] 3.1 Add login suppression logic to payment-sync.js
    - Add `shouldSuppressAutoRecovery()` function that checks for login session flag
    - Check `sessionStorage` for `segyric_just_logged_in` timestamp
    - Calculate elapsed time since login (use 5-second suppression window)
    - Return `true` if within suppression window, `false` otherwise
    - Handle edge case where sessionStorage is unavailable (return `false` to fail safe)
    - Modify `init()` function to call `shouldSuppressAutoRecovery()` before auto-recovery
    - If suppression is active, remove the login flag from sessionStorage and skip Safari trigger
    - If suppression is active, still allow background polling to continue silently
    - If no suppression, proceed with normal auto-recovery flow
    - _Bug_Condition: isBugCondition(input) where input.justLoggedIn = true AND input.platform = 'ios' AND localStorage has pending payment_
    - _Expected_Behavior: Safari does NOT auto-open; user lands on dashboard; pending payment remains; background polling may continue_
    - _Preservation: All manual deposit flows, background polling, payment success, cleanup, and non-iOS platforms must work identically_
    - _Requirements: 2.1, 2.2, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

  - [x] 3.2 Update AuthController.php to set login session flag
    - Locate the `login()` method in `app/Http/Controllers/Web/AuthController.php`
    - After successful authentication (before returning view/redirect)
    - Add flash session variable: `$request->session()->flash('just_logged_in', time());`
    - This passes a server-side timestamp to the view layer
    - The timestamp will be converted to JavaScript timestamp (milliseconds) in the view
    - _Bug_Condition: isBugCondition(input) where login occurs on iOS with pending payment_
    - _Expected_Behavior: Session flag set correctly for view layer to transfer to sessionStorage_
    - _Preservation: Login flow for all other scenarios remains unchanged_
    - _Requirements: 2.1, 2.2_

  - [x] 3.3 Update mobile.blade.php to transfer flag to sessionStorage
    - Locate `resources/views/native/layouts/mobile.blade.php`
    - Find the `<script>` section before payment-sync.js loads
    - Add Blade conditional check for `session('just_logged_in')`
    - If flag exists, inject inline JavaScript to set `sessionStorage.setItem('segyric_just_logged_in', timestamp_in_ms)`
    - Convert PHP timestamp (seconds) to JavaScript timestamp (milliseconds): `{{ session('just_logged_in') * 1000 }}`
    - Wrap in try-catch to handle sessionStorage unavailable gracefully
    - Add console.debug message for debugging if sessionStorage unavailable
    - _Bug_Condition: isBugCondition(input) where login just completed and page is loading_
    - _Expected_Behavior: sessionStorage flag set with correct timestamp before payment-sync.js runs_
    - _Preservation: All other script loading and page initialization remains unchanged_
    - _Requirements: 2.1, 2.2_

  - [x] 3.4 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - Post-Login Safari Auto-Open Suppression
    - **IMPORTANT**: Re-run the SAME test from task 1 - do NOT write a new test
    - The test from task 1 encodes the expected behavior
    - When this test passes, it confirms the expected behavior is satisfied
    - Run bug condition exploration test from step 1
    - **EXPECTED OUTCOME**: Test PASSES (confirms bug is fixed)
    - Verify that Safari does NOT auto-open after login on iOS with pending payment
    - Verify that user lands on dashboard/home screen correctly
    - Verify that pending payment remains in localStorage
    - _Requirements: 2.1, 2.2_

  - [x] 3.5 Verify preservation tests still pass
    - **Property 2: Preservation** - Manual Deposit and Background Polling
    - **IMPORTANT**: Re-run the SAME tests from task 2 - do NOT write new tests
    - Run preservation property tests from step 2
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions)
    - Verify manual deposit button still opens Safari correctly
    - Verify background polling continues for active payments
    - Verify payment success flow still works (splash + balance update)
    - Verify payment expiry cleanup still works
    - Verify Android/web platforms still work identically
    - Confirm all tests still pass after fix (no regressions)

- [x] 4. Checkpoint - Ensure all tests pass
  - Run all exploration and preservation tests
  - Verify bug is fixed: Safari does NOT auto-open after iOS login with pending payment
  - Verify preservation: All manual deposit, polling, and payment flows work correctly
  - Verify no regressions: Android/web platforms unaffected
  - Ask the user if questions arise or if any test failures occur

---

## Notes

- **sessionStorage vs localStorage**: We use sessionStorage for the login flag because it clears automatically when the browser/app session ends, preventing stale flags
- **5-second suppression window**: This gives the user time to land on the dashboard after login without being interrupted by auto-recovery
- **Background polling preservation**: Even when auto-recovery is suppressed, background polling should continue silently to ensure payments can complete
- **Platform isolation**: The fix specifically targets iOS login flows; Android and web platforms should see no behavioral changes
- **Fail-safe behavior**: If sessionStorage is unavailable, the code falls back to normal behavior (no suppression) to avoid breaking the app
