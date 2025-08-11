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
        $users = User::with(['roles', 'permissions'])
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

    public function toggleEditorRole(User $user)
    {
        if ($user->hasRole('team_editor')) {
            $user->removeRole('team_editor');
        } else {
            $user->assignRole('team_editor');
        }

        return response()->json([
            'message' => 'Editor role updated',
            'roles' => $user->roles
        ]);
    }

    public function getTeamMembers_old()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'team_viewer');
        })->get(['id', 'name', 'email', 'image', 'role', 'bio']);

        return response()->json($users);
    }

    public function getTeamMembers()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'team_viewer');
        })->get(['id', 'name', 'email', 'image', 'role', 'bio']);

        // Map image to full direct-image URL
        $users->transform(function ($user) {
            if ($user->image) {
                $filename = basename($user->image); // e.g., FG5wLIncrezVBVCLb8DM7NWRF9ua8sy5KxE9P3u1.webp
                $user->image_url = url('/direct-image/' . $filename);
            } else {
                $user->image_url = null; // or a default URL
            }
            return $user;
        });

        return response()->json($users);
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $user->name = $request->name;
        $user->bio = $request->bio;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/profile', 'public');
            $user->image = '/storage/' . $path;
        }

        $user->save();

        // Load roles for the response
        $user->load('roles');

        if ($user->image) {
            $filename = basename($user->image); // e.g., FG5wLIncrezVBVCLb8DM7NWRF9ua8sy5KxE9P3u1.webp
            $user->image = url('/direct-image/' . $filename);
        } else {
            $user->image = null; // or a default URL
        }

        return response()->json(['message' => 'Profile updated', 'user' => $user]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        
        // Get user data with roles
        $userData = $user->only(['id', 'name', 'email', 'bio', 'image']);
        $userData['roles'] = $user->roles;

        if ($userData['image']) {
            $filename = basename($userData['image']);
            $userData['image'] = url('/direct-image/' . $filename);
        } else {
            $userData['image'] = null;
        }
        
        return response()->json($userData);
    }

}