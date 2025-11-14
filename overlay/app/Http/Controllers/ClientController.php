<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Client;

class ClientController extends Controller
{
    public function index() {
        Gate::authorize('view-clients');
        return Client::orderBy('id')->paginate(20);
    }

    public function store(Request $r) {
        Gate::authorize('manage-clients');
        $r->validate(['name'=>'required','email'=>'required|email|unique:clients,email']);
        return Client::create($r->only('name','email'));
    }

    public function show($id) {
        Gate::authorize('view-clients');
        return Client::with('transactions')->findOrFail($id);
    }

    public function update(Request $r, $id) {
        Gate::authorize('manage-clients');
        $r->validate(['name'=>'sometimes','email'=>'sometimes|email|unique:clients,email,'.$id]);
        $c = Client::findOrFail($id);
        $c->update($r->only('name','email'));
        return $c;
    }
}
