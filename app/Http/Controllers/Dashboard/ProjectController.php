<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private $user;

    public function __construct()
    {
        $request = request();
        if(isset($request->uid)){
            $this->user = User::where('telegram_id', $request->uid)->firstOrFail();
        }else{
            if(!auth()->check()){
                abort(401);
            }
            $this->user = auth()->user();
        }
    }

    public function index()
    {
        $projects = $this->user->projects;
        return view('dashboard.projects', compact('projects'));
    }

    public function show(Request $request, string $id)
    {
        $project = $this->user?->projects()?->findOrFail($id);
        return view('dashboard.project', compact('project'));
    }
}
