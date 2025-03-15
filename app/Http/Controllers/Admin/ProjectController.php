<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show(string $id)
    {
        $project = Project::findOrFail($id);
        return view('admin.project.show', compact('project'));
    }
}
