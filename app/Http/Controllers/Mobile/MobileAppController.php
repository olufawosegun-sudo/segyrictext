<?php

namespace App\Http\Controllers\Mobile;

use App\Application\Services\NumberAllocationService;
use App\Application\Services\PaymentService;
use App\Application\Services\PricingEngineService;
use App\Application\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Payment;
use App\Models\PhoneNumber;
use App\Models\Service;
use App\Models\User;
use App\Models\VerificationSession;
use App\Models\WalletTransaction;
use App\Services\HealthMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MobileAppController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected PricingEngineService $pricingEngine,
        protected \App\Infrastructure\Payments\PaymentGatewayFactory $gatewayFactory,
        protected PaymentService $paymentService
    ) {}

    /**
     * Mobile Home / Dashboard — SMS Verification Console.
     * 
     * PERFORMANCE OPTIMIZATIONS:
     * - Eager loading to prevent N+1
     * - Single-query stats calculation
     * - Extended cache TTL
     * - Removed duplicate queries
     */
    public function home(): View|Response
    {
        $user = Auth::user();

        // Eager load wallet with currency to prevent separate query
        $wallet = $user->wallet()->with('currency')->first() 
            ?? $this->walletService->getOrCreateUserWallet($user);

        // Active verification sessions with full eager loading
        $activeSessions = VerificationSession::with([
                'phoneNumber.country', 
                'service', 
                'orderItem.order', // CRITICAL: Prevent N+1 in activity loop
                'smsMessages', 
                'latestOtp'
            ])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest('id')
            ->get();

        // OPTIMIZED: Single query for all stats using selectRaw
        $statsQuery = VerificationSession::selectRaw('
                COUNT(*) as total_verifications,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as successful_verifications,
                SUM(CASE WHEN status IN ("active", "pending") THEN 1 ELSE 0 END) as active_verifications
            ')
            ->where('user_id', $user->id)
            ->first();

        $stats = [
            'total_verifications' => $statsQuery->total_verifications ?? 0,
            'successful_verifications' => $statsQuery->successful_verifications ?? 0,
            'active_verifications' => $statsQuery->active_verifications ?? 0,
            // Extended cache from 30s to 5 minutes
            'available_numbers' => \Illuminate\Support\Facades\Cache::remember('mobile_available_numbers_count', 300, 
                fn() => (int) \App\Models\CountryService::where('is_active', true)->sum('str_stock') ?: app(\App\Infrastructure\Providers\PvaDealsClient::class)->getAvailableNumbersCount()
            ),
            'rented_numbers' => \App\Models\NumberReservation::where('user_id', $user->id)->count(),
        ];

        // FIXED: Added 'orderItem.order' eager loading to prevent N+1
        $recentSessions = VerificationSession::with([
                'phoneNumber.country', 
                'service', 
                'latestOtp', 
                'orderItem.order' // CRITICAL FIX
            ])
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(10)
            ->get();

        // Recent wallet transactions
        $recentTransactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->latest('id')
            ->limit(10)
            ->get();

        // Build dynamic unified recent activities
        $activities = collect();

        // OPTIMIZED: Process sessions without triggering additional queries
        foreach ($recentSessions as $session) {
            $status = match($session->status) {
                'completed' => 'Success',
                'cancelled' => 'Refunded',
                'expired' => 'Expired',
                'active', 'pending' => 'In Progress',
                default => ucfirst($session->status)
            };

            $statusColor = match($session->status) {
                'completed' => '#22c55e',
                'cancelled' => '#9ca3af',
                'expired' => '#ef4444',
                'active', 'pending' => '#ea580c',
                default => '#9ca3af'
            };

            // FIXED: Now uses eager-loaded orderItem (no N+1)
            $cost = $session->orderItem?->total_price ?? $session->orderItem?->unit_price ?? 0;

            $activities->push([
                'id' => 'session_' . $session->id,
                'type' => 'sms',
                'title' => $session->service?->name ?? 'SMS Verification',
                'date' => $session->created_at,
                'amount' => $cost,
                'status' => $status,
                'status_color' => $statusColor,
            ]);
        }

        foreach ($recentTransactions as $tx) {
            $isCredit = in_array($tx->type, [
                \App\Domain\Enums\TransactionType::Deposit,
                \App\Domain\Enums\TransactionType::Refund,
                \App\Domain\Enums\TransactionType::Adjustment,
                'deposit', 'refund', 'credit'
            ]);
            $activities->push([
                'id' => 'tx_' . $tx->id,
                'type' => 'wallet',
                'title' => $tx->description ?? ($isCredit ? 'Wallet Deposit' : 'Wallet Debit'),
                'date' => $tx->created_at,
                'amount' => $tx->amount,
                'status' => 'Success',
                'status_color' => '#22c55e',
            ]);
        }

        $recentActivities = $activities->sortByDesc('date')->values()->take(8);

        // Active catalog - fetch fresh each time (lightweight query with indexes)
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        // PVA Deals balance (cache for 2 minutes)
        $pvaClient = new \App\Infrastructure\Providers\PvaDealsClient();
        $pvaConfigured = $pvaClient->hasApiKey();
        $pvaBalance = null;
        if ($pvaConfigured) {
            $pvaBalance = \Illuminate\Support\Facades\Cache::get('pvadeals_carrier_balance');
        }

        // Payment gateway info
        $userCurrency = $wallet->currency?->code ?? 'USD';
        $paymentGateways = $this->gatewayFactory->getAvailableGateways();
        $suggestedGateway = $this->gatewayFactory->suggestGatewayForCurrency($userCurrency);
        $stripePublicKey = config('services.stripe.public_key', '');

        // OPTIMIZATION: Reuse already-fetched data instead of duplicate queries
        // Remove duplicate fetching of allSessions and allTransactions
        // They're already loaded as $recentSessions and $recentTransactions
        
        // For tabs that need more data, fetch lazily in separate requests
        // or use AJAX pagination instead of loading everything upfront
        
        // Notifications (limit to prevent memory issues)
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->latest('id')
            ->limit(20)
            ->get();

        // Security Events
        $securityEvents = \App\Models\SecurityEvent::where('user_id', $user->id)
            ->latest('id')
            ->limit(10)
            ->get();

        // Active tab from query param
        $requestedTab = request()->query('tab', 'dashboard');
        if ($requestedTab === 'home') $requestedTab = 'dashboard';

        return response()->view('native.home', compact(
            'user',
            'wallet',
            'countries',
            'services',
            'activeSessions',
            'notifications',
            'securityEvents',
            'requestedTab',
            'stats',
            'recentSessions',
            'recentTransactions',
            'recentActivities',
            'pvaConfigured',
            'pvaBalance',
            'paymentGateways',
            'suggestedGateway',
            'stripePublicKey'
        ))->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Mobile: Get a New Number — Country & Service selector.
     */
    public function getNumber(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        $pvaClient = new \App\Infrastructure\Providers\PvaDealsClient();
        $pvaConfigured = $pvaClient->hasApiKey();
        $pvaBalance = null;
        if ($pvaConfigured) {
            try { $pvaBalance = $pvaClient->getBalance(); } catch (\Throwable $e) {}
        }

        return view('native.get-number', compact('wallet', 'countries', 'services', 'pvaConfigured', 'pvaBalance'));
    }

    /**
     * Mobile: Verification Sessions — Active & History.
     */
    public function sessions(): View
    {
        $user = Auth::user();

        $activeSessions = VerificationSession::with(['phoneNumber.country', 'service', 'orderItem.order', 'smsMessages', 'latestOtp'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest('id')
            ->get();

        $historySessions = VerificationSession::with(['phoneNumber.country', 'service', 'latestOtp'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['active', 'pending'])
            ->latest('id')
            ->paginate(15);

        return view('native.sessions', compact('activeSessions', 'historySessions'));
    }

    /**
     * Mobile: Wallet & Transaction Ledger.
     * 
     * PERFORMANCE FIX: Moved blocking payment verification to background job
     */
    public function wallet(): View|Response
    {
        $user = Auth::user();

        // FIXED: Queue payment verification instead of blocking request
        // Check for recent pending deposits and queue them for background processing
        $recentPendingDeposit = \App\Models\Deposit::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHour())
            ->latest('id')
            ->first();

        if ($recentPendingDeposit) {
            // Dispatch to queue instead of processing synchronously
            \App\Jobs\VerifyPendingDepositJob::dispatch($recentPendingDeposit->reference)
                ->onQueue('payments');
        }

        $wallet = $this->walletService->getOrCreateUserWallet($user);

        $userCurrency = $wallet->currency?->code ?? 'USD';
        $paymentGateways = $this->gatewayFactory->getAvailableGateways();
        $suggestedGateway = $this->gatewayFactory->suggestGatewayForCurrency($userCurrency);

        // Payment gateway configuration
        $paystackPublicKey = (string) config('services.paystack.public_key', '');
        $stripePublicKey = (string) config('services.stripe.public_key', '');
        $defaultGateway = (string) config('services.default_payment_gateway', 'paystack');

        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->latest('id')
            ->paginate(20);

        return response()->view('native.wallet', compact('user', 'wallet', 'transactions', 'paymentGateways', 'suggestedGateway', 'paystackPublicKey', 'stripePublicKey', 'defaultGateway'))
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
    }

    /**
     * Mobile: Dedicated Full-Screen Deposit Page.
     */
    public function deposit(): View|Response
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $initialAmount = (float) request()->query('amount', 2000);
        if ($initialAmount <= 0) {
            $initialAmount = 2000;
        }
        $paystackPublicKey = (string) config('services.paystack.public_key', '');
        $stripePublicKey = (string) config('services.stripe.public_key', '');
        $defaultGateway = (string) config('services.default_payment_gateway', 'paystack');

        return response()->view('native.deposit', compact('user', 'wallet', 'initialAmount', 'paystackPublicKey', 'stripePublicKey', 'defaultGateway'))
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
    }

    /**
     * Local Paystack Checkout Page — Served from localhost to stay in-app.
     * This page loads the Paystack v2 Inline JS and opens the checkout popup.
     * Since the page is served via the WKWebView scheme handler (localhost/php://),
     * everything stays inside the native app — no Safari redirect.
     */
    public function checkout(string $reference): View|RedirectResponse
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);

        // Look up the pending deposit
        $deposit = \App\Models\Deposit::where('reference', $reference)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$deposit) {
            return redirect()->route('mobile.wallet')->with('error', 'Checkout session expired or not found.');
        }

        $amount = $deposit->amount;

        return view('native.wallet.checkout', compact('user', 'wallet', 'amount', 'reference', 'deposit'));
    }

    /**
     * Mobile Settings & Profile.
     */
    public function settings(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);

        return view('native.settings', compact('user', 'wallet'));
    }
}
