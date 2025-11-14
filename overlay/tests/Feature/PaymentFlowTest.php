<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Gateway;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        Gateway::create(['name'=>'gateway_1','priority'=>1,'is_active'=>true]);
        Gateway::create(['name'=>'gateway_2','priority'=>2,'is_active'=>true]);
        Product::create(['name'=>'Prod 1','amount'=>1000]);
    }

    public function test_amount_from_items_and_fallback()
    {
        Http::fake([
            'gateways:3001/login' => Http::response(['accessToken'=>'AAA'], 200),
            'gateways:3001/transactions' => Http::response(['status'=>'declined'], 200),
            'gateways:3002/transacoes' => Http::response(['status'=>'approved','idTransacao'=>'T2'], 200),
        ]);

        $payload = [
            "items" => [["product_id"=>1,"quantity"=>2]],
            "cardNumber" => "5569000000006063",
            "cvv" => "010",
            "client" => ["name"=>"Tester","email"=>"tester@example.com"]
        ];

        $res = $this->postJson('/api/purchase', $payload);
        $res->assertStatus(201)->assertJsonFragment(['status'=>'approved','external_id'=>'T2']);
    }
}
