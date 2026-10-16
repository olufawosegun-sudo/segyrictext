<?php

namespace App\Http\Controllers\Web;

use App\Application\Services\PaymentService;
use App\Application\Services\PricingEngineService;
use App\Application\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Infrastructure\Payments\PaymentGatewayFactory;
use App\Models\Country;
use App\Models\PhoneNumber;
use App\Models\PricingRule;
use App\Models\Service;
use App\Models\VerificationSession;
use App\Models\WalletTransaction;
use App\Services\HealthMetricsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected PricingEngineService $pricingEngine,
        protected PaymentGatewayFactory $gatewayFactory,
        protected PaymentService $paymentService
    ) {}

    public function index(HealthMetricsService $healthService): View|RedirectResponse|Response
    {
        $platform = request()->attributes->get('platform', 'web');
        $userAgent = request()->header('User-Agent', '');
        $isMobileOrNative = in_array($platform, ['android', 'ios', 'mobile_browser'])
            || request()->attributes->get('is_native', false)
            || preg_match('/(iPhone|iPod|iPad|Android|Mobile)/i', $userAgent)
            || getenv('JUMP_BRIDGE_PORT') !== false
            || in_array(request()->getPort(), [3000, 3001, 3002, 3003, 3004])
            || in_array(request()->header('X-Forwarded-Port'), [3000, 3001, 3002, 3003, 3004]);

        if ($isMobileOrNative) {
            return app(\App\Http\Controllers\Mobile\MobileAppController::class)->home();
        }

        $user = Auth::user();

        // Ensure user has a wallet
        $wallet = $this->walletService->getOrCreateUserWallet($user);

        // Fetch active catalog
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        // Active verification sessions for current user
        $activeSessions = VerificationSession::with(['phoneNumber.country', 'service', 'orderItem.order', 'smsMessages', 'latestOtp'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest('id')
            ->get();

        // Completed & past verification history
        $recentSessions = VerificationSession::with(['phoneNumber.country', 'service', 'orderItem.order', 'smsMessages', 'latestOtp'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['active', 'pending'])
            ->latest('id')
            ->limit(10)
            ->get();

        // Recent wallet ledger transactions
        $recentTransactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->latest('id')
            ->limit(10)
            ->get();

        // Platform & user statistics
        $stats = [
            'total_verifications' => VerificationSession::where('user_id', $user->id)->count(),
            'successful_verifications' => VerificationSession::where('user_id', $user->id)->where('status', 'completed')->count(),
            'active_verifications' => $activeSessions->count(),
            'available_numbers' => (int) \App\Models\CountryService::where('is_active', true)->sum('str_stock') ?: app(\App\Infrastructure\Providers\PvaDealsClient::class)->getAvailableNumbersCount(),
            'active_countries' => $countries->count(),
            'supported_services' => $services->count(),
        ];

        $currencyCode = $wallet->currency?->code ?? 'NGN';
        $currencySymbol = $wallet->currency?->symbol ?? '₦';
        $exchangeRate = $this->pricingEngine->convertCurrency(1.0, 'USD', $currencyCode);

        // Auto-verify payment if returned from gateway callback
        $paymentRef = request()->query('reference') ?? request()->query('trxref');
        $paymentNotice = null;
        if ($paymentRef) {
            try {
                $verification = $this->paymentService->verifyPayment($paymentRef);
                if ($verification['success']) {
                    $creditedAmt = (float) ($verification['amount'] ?? $verification['payment']?->amount ?? 0);
                    $paymentNotice = [
                        'type' => 'success',
                        'message' => 'Payment successful! Wallet credited with ' . ($wallet->currency?->symbol ?? '₦') . number_format($creditedAmt, 2) . '.',
                    ];
                    $wallet = $this->walletService->getOrCreateUserWallet($user);
                } else {
                    $paymentNotice = [
                        'type' => 'error',
                        'message' => $verification['message'] ?? 'Payment verification failed.',
                    ];
                }
            } catch (\Throwable $e) {
                // Ignore if reference already processed
            }
        }

        $health = $healthService->getSystemHealth();
        $paymentGateways = $this->gatewayFactory->getAvailableGateways();
        $suggestedGateway = $this->gatewayFactory->suggestGatewayForCurrency($currencyCode);
        $stripePublicKey = config('services.stripe.public_key', '');

        return view('web.dashboard', compact(
            'user',
            'wallet',
            'currencyCode',
            'currencySymbol',
            'exchangeRate',
            'countries',
            'services',
            'activeSessions',
            'recentSessions',
            'recentTransactions',
            'stats',
            'health',
            'paymentGateways',
            'suggestedGateway',
            'paymentNotice',
            'stripePublicKey'
        ));
    }

    public function refillCredits(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);

        // Auto-verify payment if returned from gateway callback
        $paymentRef = request()->query('reference') ?? request()->query('trxref');
        $paymentNotice = null;
        if ($paymentRef) {
            try {
                $verification = $this->paymentService->verifyPayment($paymentRef);
                if ($verification['success']) {
                    $creditedAmt = (float) ($verification['amount'] ?? $verification['payment']?->amount ?? 0);
                    $paymentNotice = [
                        'type' => 'success',
                        'message' => 'Payment successful! Wallet credited with ' . ($wallet->currency?->symbol ?? '₦') . number_format($creditedAmt, 2) . '.',
                    ];
                    $wallet = $this->walletService->getOrCreateUserWallet($user);
                } else {
                    $paymentNotice = [
                        'type' => 'error',
                        'message' => $verification['message'] ?? 'Payment verification failed.',
                    ];
                }
            } catch (\Throwable $e) {
                // Ignore if reference already processed
            }
        }

        $currencyCode = $wallet->currency?->code ?? 'NGN';
        $currencySymbol = $wallet->currency?->symbol ?? '₦';
        $exchangeRate = $this->pricingEngine->convertCurrency(1.0, 'USD', $currencyCode);
        $paymentGateways = $this->gatewayFactory->getAvailableGateways();
        $suggestedGateway = $this->gatewayFactory->suggestGatewayForCurrency($currencyCode);
        $stripePublicKey = config('services.stripe.public_key', '');

        return view('web.refill-credits', compact('user', 'wallet', 'currencyCode', 'currencySymbol', 'exchangeRate', 'paymentGateways', 'suggestedGateway', 'paymentNotice', 'stripePublicKey'));
    }

    public function strNumbers(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $currencyCode = $wallet->currency?->code ?? 'NGN';
        $currencySymbol = $wallet->currency?->symbol ?? '₦';
        $exchangeRate = $this->pricingEngine->convertCurrency(1.0, 'USD', $currencyCode);

        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        $activeSessions = VerificationSession::with(['phoneNumber.country', 'service', 'latestOtp'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->latest('id')
            ->get();

        $sessions = VerificationSession::with(['phoneNumber.country', 'service', 'latestOtp'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(15);

        return view('web.str-numbers', compact(
            'user',
            'wallet',
            'currencyCode',
            'currencySymbol',
            'exchangeRate',
            'countries',
            'services',
            'activeSessions',
            'sessions'
        ));
    }

    public function ltrNumbers(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $currencyCode = $wallet->currency?->code ?? 'NGN';
        $currencySymbol = $wallet->currency?->symbol ?? '₦';
        $reservations = \App\Models\NumberReservation::with(['phoneNumber.country'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(15);

        return view('web.ltr-numbers', compact('user', 'wallet', 'currencyCode', 'currencySymbol', 'reservations'));
    }

    public function smsLogs(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $currencyCode = $wallet->currency?->code ?? 'NGN';
        $currencySymbol = $wallet->currency?->symbol ?? '₦';
        $logs = \App\Models\SmsMessage::with(['phoneNumber'])
            ->whereHas('phoneNumber.verificationSessions', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('id')
            ->paginate(20);

        return view('web.sms-logs', compact('user', 'wallet', 'currencyCode', 'currencySymbol', 'logs'));
    }

    public function support(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $currencyCode = $wallet->currency?->code ?? 'NGN';
        $currencySymbol = $wallet->currency?->symbol ?? '₦';

        return view('web.support', compact('user', 'wallet', 'currencyCode', 'currencySymbol'));
    }

    public function account(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $currencyCode = $wallet->currency?->code ?? 'NGN';
        $currencySymbol = $wallet->currency?->symbol ?? '₦';

        return view('web.account', compact('user', 'wallet', 'currencyCode', 'currencySymbol'));
    }

    public function pricing(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $currencyCode = $wallet->currency?->code ?? 'NGN';
        $currencySymbol = $wallet->currency?->symbol ?? '₦';
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('web.pricing', compact('user', 'wallet', 'currencyCode', 'currencySymbol', 'countries', 'services'));
    }
}
