<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $users = User::with('roles')
            ->when($search, fn($q) => $q->where('email', 'like', "%$search%"))
            ->get();

        return response()->json($users);
    }

    public function toggleTeamViewer(User $user)
    {
        if ($user->hasRole('team_viewer')) {
            $user->removeRole('team_viewer');
        } else {
            $user->assignRole('team_viewer');
        }

        return response()->json([
            'message' => 'Role updated',
            'roles' => $user->roles
        ]);
    }

    public function getTeamMembers()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'team_viewer');
        })->get(['id', 'name', 'email', 'image', 'role', 'bio']);

        return response()->json($users);
    }

}