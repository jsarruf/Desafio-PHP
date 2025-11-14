<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;

class ProductController extends Controller
{
    public function index() { return Product::orderBy('id')->paginate(20); }

    public function store(Request $r) {
        Gate::authorize('manage-products');
        $r->validate(['name'=>'required','amount'=>'required|integer|min:1']);
        return Product::create($r->only('name','amount'));
    }

    public function show($id) { return Product::findOrFail($id); }

    public function update(Request $r, $id) {
        Gate::authorize('manage-products');
        $r->validate(['name'=>'sometimes','amount'=>'sometimes|integer|min:1']);
        $p = Product::findOrFail($id);
        $p->update($r->only('name','amount'));
        return $p;
    }

    public function destroy($id) {
        Gate::authorize('manage-products');
        Product::findOrFail($id)->delete();
        return response()->noContent();
    }
}
