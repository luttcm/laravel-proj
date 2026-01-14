<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Picture;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('pages.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.detail', compact('user'));
    }

    public function add()
    {
        $roles = ['admin', 'user', 'finance', 'redactor', 'manager'];
        return view('pages.users.add', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:admin,user,finance,redactor,manager',
        ]);

        $password = $validated['password'] ?? Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => $validated['role'],
        ]);

        Picture::create([
            'path' => 'avatars/classicAvatar.png',
            'entity_type' => 'user',
            'entity_id' => $user->id,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Пользователь создан! Пароль: {$password}");
    }

    public function delete(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Пользователь удален");
    }

    public function profile()
    {
        $user = auth()->user();
        $picture = Picture::where('entity_type', 'user')
                    ->where('entity_id', $user->id)
                    ->first();

        return view('pages.profile', compact('user', 'picture'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        $path = $request->file('avatar')->store('avatars', 'public');

        Picture::where('entity_type', 'user')
            ->where('entity_id', $user->id)
            ->delete();

        Picture::create([
            'path' => 'storage/' . $path,
            'entity_type' => 'user',
            'entity_id' => $user->id,
        ]);

        return redirect()->route('profile')->with('success', 'Аватар обновлён');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->name = $validated['name'];
        $user->save();

        return redirect()->route('profile')->with('success', 'Имя обновлено');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = ['admin', 'user', 'finance', 'redactor', 'manager'];
        return view('pages.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,user,finance,redactor,manager',
        ]);

        $user = User::findOrFail($id);
        $user->name = $validated['name'];
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('users.show', ['id' => $user->id])->with('success', 'Пользователь обновлён');
    }
}
