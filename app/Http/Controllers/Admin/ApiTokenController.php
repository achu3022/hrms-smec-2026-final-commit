<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index(Request $request)
    {
        $tokens = $request->user()->tokens;
        return view('admin.api-tokens.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $request->validate(['token_name' => 'required|string|max:255']);
        $token = $request->user()->createToken($request->token_name);
        return back()->with('success', "Token created! Please copy it now as it will not be shown again: <br><strong>" . $token->plainTextToken . "</strong>");
    }

    public function destroy(Request $request, $id)
    {
        $request->user()->tokens()->where('id', $id)->delete();
        return back()->with('success', 'Token revoked');
    }
}
