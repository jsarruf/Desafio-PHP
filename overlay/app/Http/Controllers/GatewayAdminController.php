<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Gateway;

class GatewayAdminController extends Controller
{
    public function toggle($id)
    {
        Gate::authorize('manage-gateways');
        $g = Gateway::findOrFail($id);
        $g->is_active = !$g->is_active;
        $g->save();
        return response()->json($g);
    }

    public function setPriority($id, Request $request)
    {
        Gate::authorize('manage-gateways');
        $request->validate([ 'priority' => 'required|integer|min:1' ]);
        $g = Gateway::findOrFail($id);
        $g->priority = $request->priority;
        $g->save();
        return response()->json($g);
    }
}
