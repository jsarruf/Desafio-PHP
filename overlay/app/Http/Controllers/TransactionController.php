<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Transaction;
use App\Services\PaymentService;

class TransactionController extends Controller
{
    public function __construct(private PaymentService $payment) {}

    public function index()
    {
        Gate::authorize('view-transactions');
        return Transaction::with('client','gateway')->orderByDesc('id')->paginate(20);
    }

    public function show($id)
    {
        Gate::authorize('view-transactions');
        return Transaction::with('client','gateway','products')->findOrFail($id);
    }

    public function refund($id)
    {
        Gate::authorize('refund-transaction');
        $tx = Transaction::with('gateway')->findOrFail($id);
        $this->payment->refund($tx);
        return response()->json(['message' => 'Refund requested', 'status' => $tx->status]);
    }
}
