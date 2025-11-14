<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\PaymentService;

class PurchaseController extends Controller
{
    public function __construct(private PaymentService $payment) {}

    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'nullable|integer|min:1',
            'items'  => 'nullable|array',
            'items.*.product_id' => 'required_with:items|integer|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'cardNumber' => 'required|string|min:12|max:19',
            'cvv' => 'required|string|min:3|max:4',
            'client.name' => 'required|string',
            'client.email' => 'required|email'
        ]);

        if (empty($validated['amount']) && empty($validated['items'])) {
            return response()->json(['message' => 'Provide amount or items'], 422);
        }

        $client = Client::firstOrCreate(
            ['email' => $validated['client']['email']],
            ['name' => $validated['client']['name']]
        );

        $amount = $validated['amount'] ?? 0;
        $items = $validated['items'] ?? [];

        if (!$amount && $items) {
            $productIds = collect($items)->pluck('product_id')->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $amount = 0;
            foreach ($items as $it) {
                $p = $products[$it['product_id']];
                $amount += $p->amount * $it['quantity'];
            }
        }

        return DB::transaction(function () use ($amount, $client, $items, $validated) {
            $tx = Transaction::create([
                'client_id' => $client->id,
                'amount' => $amount,
                'status' => 'declined',
                'card_last_numbers' => substr($validated['cardNumber'], -4)
            ]);

            if (!empty($items)) {
                foreach ($items as $it) {
                    $tx->products()->attach($it['product_id'], ['quantity' => $it['quantity']]);
                }
            }

            $tx = $this->payment->charge($tx, [
                'number' => $validated['cardNumber'],
                'cvv'    => $validated['cvv']
            ], $client);

            return response()->json([
                'id' => $tx->id,
                'status' => $tx->status,
                'amount' => $tx->amount,
                'external_id' => $tx->external_id,
                'gateway' => optional($tx->gateway)->name,
                'card_last_numbers' => $tx->card_last_numbers
            ], $tx->status === 'approved' ? 201 : 200);
        });
    }
}
