<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        if(isset($request->uid)){
            $user = User::where('telegram_id', $request->uid)->firstOrFail();
        }else{
            if(!auth()->check()){
                abort(401);
            }
            $user = auth()->user();
        }
        $tasks = $user->tasks;
        return view('dashboard.tasks', compact('tasks'));
    }
}
