<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where("role", "admin")->get();

        return view('pages.users', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('pages.user-detail', compact('user'));
    }
}
