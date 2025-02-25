<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if(isset($request->uid)){
            $user = User::where('telegram_id', $request->uid)->firstOrFail();
        }else{
            $user = auth()->user();
        }

        $projects = $user->projects;
        return view('dashboard', compact('projects', 'user'));
    }

    public function show(Request $request, string $id)
    {
        $user = User::where('telegram_id', $request->uid)->firstOrFail();
        $projects = $user->projects;
        $project = Project::findOrFail($id);
        # todo тут проверка нужна!
        return view('dashboard', compact('project', 'projects', 'user'));
    }
}
