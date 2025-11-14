<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\Client;
use App\Models\Gateway;

class PaymentService
{
    public function __construct(private GatewayRepository $repo) {}

    public function charge(Transaction $tx, array $card, Client $client): Transaction
    {
        $gateways = $this->repo->activeOrderedByPriority();

        foreach ($gateways as $g) {
            try {
                $resp = $this->callCharge($g, $tx, $card, $client);
                if (($resp['status'] ?? 'error') === 'approved') {
                    $tx->update([
                        'status' => 'approved',
                        'gateway_id' => $g->id,
                        'external_id' => $resp['external_id'] ?? null,
                    ]);
                    return $tx;
                }
            } catch (\Throwable $e) {
                Log::warning('Gateway error: '.$g->name.' '.$e->getMessage());
            }
        }
        $tx->update(['status' => 'declined']);
        return $tx;
    }

    public function refund(Transaction $tx): Transaction
    {
        if (!$tx->gateway || !$tx->external_id) return $tx;

        $g = $tx->gateway;
        $resp = $this->callRefund($g, $tx->external_id);
        if (($resp['status'] ?? 'error') === 'refunded') {
            $tx->update(['status' => 'refunded']);
        }
        return $tx;
    }

    private function callCharge(Gateway $g, Transaction $tx, array $card, Client $client): array
    {
        if ($g->name === 'gateway_1') {
            $login = Http::asJson()->post(config('gateways.g1.base').'/login', [
                'email' => config('gateways.g1.email'),
                'token' => config('gateways.g1.token'),
            ])->json();
            $token = $login['accessToken'] ?? null;
            $headers = $token ? ['Authorization' => 'Bearer '.$token] : [];

            $payload = [
                'value' => $tx->amount,
                'name'  => $client->name,
                'email' => $client->email,
                'cardNumber' => $card['number'],
                'cvv' => $card['cvv'],
            ];

            $charge = Http::withHeaders($headers)->asJson()
                ->post(config('gateways.g1.base').'/transactions', $payload);

            $data = $charge->json();
            $status = $data['status'] ?? 'error';
            $external = $data['transactionId'] ?? null;
            return ['status' => $status, 'external_id' => $external];
        }

        if ($g->name === 'gateway_2') {
            $headers = [
                'Gateway-Auth-Token' => config('gateways.g2.auth_token'),
                'Gateway-Auth-Secret' => config('gateways.g2.auth_secret'),
            ];
            $payload = [
                'valor' => $tx->amount,
                'nome'  => $client->name,
                'email' => $client->email,
                'numeroCartao' => $card['number'],
                'cvv' => $card['cvv'],
            ];

            $charge = Http::withHeaders($headers)->asJson()
                ->post(config('gateways.g2.base').'/transacoes', $payload);

            $data = $charge->json();
            $status = $data['status'] ?? 'error';
            $external = $data['idTransacao'] ?? null;
            return ['status' => $status, 'external_id' => $external];
        }

        return ['status' => 'error'];
    }

    private function callRefund(Gateway $g, string $externalId): array
    {
        if ($g->name === 'gateway_1') {
            $login = Http::asJson()->post(config('gateways.g1.base').'/login', [
                'email' => config('gateways.g1.email'),
                'token' => config('gateways.g1.token'),
            ])->json();
            $token = $login['accessToken'] ?? null;
            $headers = $token ? ['Authorization' => 'Bearer '.$token] : [];

            $res = Http::withHeaders($headers)->asJson()
                ->post(config('gateways.g1.base')."/transactions/{$externalId}/charge_back", []);

            $data = $res->json();
            return ['status' => $data['status'] ?? 'error'];
        }

        if ($g->name === 'gateway_2') {
            $headers = [
                'Gateway-Auth-Token' => config('gateways.g2.auth_token'),
                'Gateway-Auth-Secret' => config('gateways.g2.auth_secret'),
            ];
            $res = Http::withHeaders($headers)->asJson()
                ->post(config('gateways.g2.base')."/transacoes/reembolso", [
                    'idTransacao' => $externalId
                ]);

            $data = $res->json();
            return ['status' => $data['status'] ?? 'error'];
        }

        return ['status' => 'error'];
    }
}
