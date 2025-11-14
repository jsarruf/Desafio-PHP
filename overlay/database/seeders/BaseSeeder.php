<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Gateway;

class BaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email'=>'admin@betalent.tech'], [
            'name'=>'Admin', 'password'=>Hash::make('password'), 'role'=>'ADMIN'
        ]);
        User::updateOrCreate(['email'=>'finance@betalent.tech'], [
            'name'=>'Finance', 'password'=>Hash::make('password'), 'role'=>'FINANCE'
        ]);
        User::updateOrCreate(['email'=>'manager@betalent.tech'], [
            'name'=>'Manager', 'password'=>Hash::make('password'), 'role'=>'MANAGER'
        ]);
        User::updateOrCreate(['email'=>'user@betalent.tech'], [
            'name'=>'User', 'password'=>Hash::make('password'), 'role'=>'USER'
        ]);

        Product::updateOrCreate(['name'=>'Plano Básico'], ['amount'=>1000]);
        Product::updateOrCreate(['name'=>'Plano Premium'], ['amount'=>2500]);

        Gateway::updateOrCreate(['name'=>'gateway_1'], ['priority'=>1, 'is_active'=>true]);
        Gateway::updateOrCreate(['name'=>'gateway_2'], ['priority'=>2, 'is_active'=>true]);
    }
}
