<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', false)->orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }
}
