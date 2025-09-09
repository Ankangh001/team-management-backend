<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Waitlist;
use Illuminate\Support\Facades\Validator;

class WaitlistController extends Controller
{
    // Static token for simple authentication (from .env)
    private function checkToken(Request $request)
    {
        $token = $request->header('X-API-TOKEN');
        $staticToken = env('WAITLIST_API_TOKEN', 'STATIC_WAITLIST_TOKEN');
        if ($token !== $staticToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($resp = $this->checkToken($request)) return $resp;
        $waitlist = Waitlist::orderByDesc('submitted_at')->get();
        return response()->json($waitlist);
    }

    public function store(Request $request)
    {
        if ($resp = $this->checkToken($request)) return $resp;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'interest' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $data['submitted_at'] = now();
        $waitlist = Waitlist::create($data);
        return response()->json($waitlist, 201);
    }
}
