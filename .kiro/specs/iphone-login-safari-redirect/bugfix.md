# Bugfix Requirements Document

## Introduction

When a user logs in on the iPhone native app, the app unexpectedly opens Safari browser with a Paystack checkout URL instead of remaining within the app and navigating directly to the dashboard. This occurs when there is a pending payment stored in localStorage from a previous session.

**Impact:** The user is forced out of the native app experience into an external browser, breaking the native app flow and creating confusion about whether the login was successful.

**Root Cause:** The payment-sync.js auto-recovery mechanism automatically detects pending deposits on page load and re-initializes the payment flow, which triggers `Browser::inApp()` to open the Paystack URL in Safari (SFSafariViewController).

**Scope:** This affects only the iPhone native app login flow. The deposit/payment functionality itself should continue to use `Browser::inApp()` when users intentionally initiate a deposit.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a user logs in on iPhone native app AND there is a pending_deposit_ref in localStorage THEN the system automatically opens Safari browser with the Paystack checkout URL

1.2 WHEN a user lands on the home/dashboard page after login AND payment-sync.js detects a stored pending payment THEN the system immediately re-initializes the payment flow by calling Browser::inApp()

1.3 WHEN the auto-recovery logic triggers on login THEN the user never sees the dashboard and is immediately redirected to an external browser

### Expected Behavior (Correct)

2.1 WHEN a user logs in on iPhone native app THEN the system SHALL navigate directly to the dashboard (/home) within the native app without opening Safari, regardless of pending payment state

2.2 WHEN a user logs in on iPhone native app AND there is a pending_deposit_ref in localStorage THEN the system SHALL remain on the dashboard and optionally display a notification about the pending payment without auto-triggering Browser::inApp()

2.3 WHEN a user manually clicks "Deposit" or "Fund Wallet" button THEN the system SHALL continue to use Browser::inApp() to open Paystack in an in-app browser as designed

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a user manually initiates a wallet deposit by clicking deposit buttons THEN the system SHALL CONTINUE TO open Browser::inApp() with the Paystack URL in SFSafariViewController

3.2 WHEN a user logs in on the web platform (non-native) THEN the system SHALL CONTINUE TO handle login and payment flows as currently designed

3.3 WHEN payment-sync.js polls for payment verification status THEN the system SHALL CONTINUE TO poll and update balance when payments complete

3.4 WHEN a user returns to the deposit page after starting a payment THEN the system SHALL CONTINUE TO resume the payment verification polling

3.5 WHEN a user logs in on Android native app THEN the system SHALL CONTINUE TO handle login and payment flows as currently designed

3.6 WHEN localStorage contains expired pending payment data (>30 minutes old) THEN the system SHALL CONTINUE TO automatically clean up the expired data

3.7 WHEN a payment is successfully completed THEN the system SHALL CONTINUE TO update wallet balances and show payment success splash
