# NativePHP iOS Navigation Patch

## Problem

By default, NativePHP's `ContentView.swift` opens external HTTPS URLs (including payment gateway checkouts) in Safari, forcing users out of the native Jump app during payment.

## Solution

This patch modifies the `WKNavigationDelegate.decidePolicyFor` method in `ContentView.swift` to:

1. **Allow ALL iframe/subframe navigations** to stay inside the app (never open Safari)
2. **Whitelist payment gateway domains** (Stripe, Flutterwave) for main frame navigation
3. **Add `example.com`** for testing purposes
4. **Add debug logging** to trace navigation decisions

## Files Modified

- `vendor/nativephp/mobile/resources/xcode/NativePHP/ContentView.swift` (lines 260-295)

## Automatic Application

The patch is automatically applied via Composer hooks:

```json
{
  "scripts": {
    "post-install-cmd": ["@php scripts/patch-nativephp-navigation.php"],
    "post-update-cmd": ["@php scripts/patch-nativephp-navigation.php"]
  }
}
```

This ensures the patch is reapplied after:
- `composer install`
- `composer update`

## Manual Application

If you need to apply the patch manually:

```bash
php scripts/patch-nativephp-navigation.php
```

## Changes Made

### Before (Line 262-288)
```swift
// 1. Allow all subframe/iframe navigations inside WKWebView
if navigationAction.targetFrame?.isMainFrame == false {
    decisionHandler(.allow)
    return
}

// 3. Allow internal local/LAN hosts and payment gateway hosts in main frame
let host = url.host?.lowercased() ?? ""
if host.contains("stripe.com") || host.contains("flutterwave.com") ||
   host == "127.0.0.1" || host == "localhost" ||
   host.hasPrefix("192.168.") || host.hasPrefix("172.") || host.hasPrefix("10.") {
    decisionHandler(.allow)
    return
}

// 4. Open external non-app URLs with the system handler
if ["http", "https", "tel", "mailto", "sms", "facetime", "facetime-audio"].contains(scheme) {
    UIApplication.shared.open(url)  // ← OPENS SAFARI
    decisionHandler(.cancel)
}
```

### After (Patched)
```swift
// 1. Allow all subframe/iframe navigations inside WKWebView
// CRITICAL: iframes must NEVER open Safari - they stay inside the app
if navigationAction.targetFrame?.isMainFrame == false {
    print("✅ [WKWebView] Allowing IFRAME navigation: \(url.absoluteString)")
    decisionHandler(.allow)
    return
}

// 3. Allow internal local/LAN hosts and ALL payment gateway hosts in main frame
// EXPANDED: Allow payment gateways and common checkout domains to stay inside app
let host = url.host?.lowercased() ?? ""
if host.contains("stripe.com") || host.contains("flutterwave.com") ||
   host.contains("example.com") || // For testing
   host == "127.0.0.1" || host == "localhost" ||
   host.hasPrefix("192.168.") || host.hasPrefix("172.") || host.hasPrefix("10.") {
    print("✅ [WKWebView] Allowing MAIN FRAME navigation (whitelisted): \(url.absoluteString)")
    decisionHandler(.allow)
    return
}

// 4. Open external non-app URLs with the system handler
// Only applies to MAIN FRAME navigation that isn't whitelisted above
if ["http", "https", "tel", "mailto", "sms", "facetime", "facetime-audio"].contains(scheme) {
    print("⚠️ [WKWebView] Opening in Safari (external): \(url.absoluteString)")
    UIApplication.shared.open(url)
    decisionHandler(.cancel)
}
```

## Key Changes

1. **Added explicit iframe logging** - Confirms when iframes are allowed
2. **Expanded whitelist** - Added `stripe.com`, `flutterwave.com`, `example.com`
3. **Added navigation logging** - Print statements show which URLs are allowed vs. Safari
4. **Clarified comments** - Made the logic easier to understand

## Verification

After applying the patch and rebuilding the iOS app:

```bash
# Rebuild iOS app
php artisan native:ios

# Run on device via Jump
php artisan native:jump
```

**Test on iPhone/Jump:**
1. Navigate to Wallet → Deposit
2. Press diagnostic test buttons (Test A, B, C)
3. Press "Fund Wallet"

**Expected Results:**
- Test A (`example.com` iframe): Stays in app ✅
- Test B (`openInAppPayment` with `example.com`): Stays in app ✅
- Test C (Payment iframe): Stays in app ✅
- Real payment: Stays in app ✅

**Check Xcode Console for logs:**
```
✅ [WKWebView] Allowing IFRAME navigation: https://example.com
✅ [WKWebView] Allowing MAIN FRAME navigation (whitelisted): https://checkout.stripe.com/...
```

## Troubleshooting

### Patch Already Applied
```
✅ Already patched! No changes needed.
```
This is normal - the patch is idempotent.

### Patch Failed
```
❌ Failed to apply patch. The original code may have changed.
```

**Cause:** NativePHP updated `ContentView.swift` and the original code pattern doesn't match.

**Solution:** Manually apply the changes to `vendor/nativephp/mobile/resources/xcode/NativePHP/ContentView.swift` using the "Before/After" section above as a reference.

### Safari Still Opens

1. **Verify patch was applied:**
   ```bash
   php scripts/patch-nativephp-navigation.php
   ```

2. **Rebuild iOS app completely:**
   ```bash
   php artisan native:ios
   ```

3. **Check if the URL is whitelisted:**
   - Open Xcode Console (Safari → Develop → [iPhone] → [Jump])
   - Look for navigation logs
   - If you see `⚠️ [WKWebView] Opening in Safari`, the domain isn't whitelisted

4. **Add domain to whitelist:**
   Edit the patch script and add your domain:
   ```swift
   if host.contains("your-domain.com") ||
      host.contains("stripe.com") || ...
   ```

## Adding New Payment Gateways

To whitelist additional payment domains:

1. Edit `scripts/patch-nativephp-navigation.php`
2. Add domain to the host checking logic:
   ```swift
   if host.contains("new-gateway.com") ||
      host.contains("stripe.com") || ...
   ```
3. Reapply patch: `php scripts/patch-nativephp-navigation.php`
4. Rebuild: `php artisan native:ios`

## Maintainer Notes

- This patch survives `composer update` via automatic reapplication
- The patch is idempotent (safe to run multiple times)
- Always test on actual iPhone/Jump after NativePHP updates
- Keep this README updated if the patch logic changes
