<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index() {
        Gate::authorize('manage-users');
        return User::orderBy('id')->paginate(20);
    }

    public function store(Request $r) {
        Gate::authorize('manage-users');
        $r->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6',
            'role'=>'required|in:ADMIN,MANAGER,FINANCE,USER'
        ]);
        return User::create([
            'name'=>$r->name, 'email'=>$r->email,
            'password'=>Hash::make($r->password),
            'role'=>$r->role
        ]);
    }

    public function show($id) {
        Gate::authorize('manage-users');
        return User::findOrFail($id);
    }

    public function update(Request $r, $id) {
        Gate::authorize('manage-users');
        $r->validate([
            'name'=>'sometimes',
            'email'=>'sometimes|email|unique:users,email,'.$id,
            'password'=>'sometimes|min:6',
            'role'=>'sometimes|in:ADMIN,MANAGER,FINANCE,USER'
        ]);
        $u = User::findOrFail($id);
        $data = $r->only('name','email','role');
        if ($r->filled('password')) $data['password'] = Hash::make($r->password);
        $u->update($data);
        return $u;
    }
}
