<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Get user's current wallet balance, available balance, and active holds.
     */
    public function balance(Request $request): JsonResponse
    {
        $wallet = $this->walletService->getOrCreateUserWallet($request->user());

        return response()->json([
            'success' => true,
            'data' => new WalletResource($wallet),
        ]);
    }

    /**
     * Get double-entry ledger audit transactions.
     */
    public function transactions(Request $request): JsonResponse
    {
        $wallet = $this->walletService->getOrCreateUserWallet($request->user());

        $transactions = WalletTransaction::query()
            ->where('wallet_id', $wallet->id)
            ->latest('id')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    /**
     * Credit/Deposit funds into wallet (instant top-up).
     */
    public function deposit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:5000'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $reference = $validated['reference'] ?? ('DEP_' . Str::ulid());
        $tx = $this->walletService->credit(
            $request->user(),
            (float) $validated['amount'],
            $reference,
            'Wallet funds top-up'
        );

        $wallet = $this->walletService->getOrCreateUserWallet($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Funds credited successfully.',
            'transaction' => $tx,
            'wallet' => new WalletResource($wallet),
        ]);
    }
}
