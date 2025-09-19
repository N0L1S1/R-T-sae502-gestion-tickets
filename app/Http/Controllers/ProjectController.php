<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->orderBy('name')->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get(['id','name']);
        return view('projects.create', compact('clients'));
    }

    public function __construct()
    {
        $this->authorizeResource(\App\Models\Project::class, 'project');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'   => ['required','exists:clients,id'],
            'name'        => ['required','string','max:150'],
            'description' => ['nullable','string'],
        ]);

        Project::create($data);
        return redirect()->route('projects.index')->with('success','Projet créé.');
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get(['id','name']);
        return view('projects.edit', compact('project','clients'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'client_id'   => ['required','exists:clients,id'],
            'name'        => ['required','string','max:150'],
            'description' => ['nullable','string'],
        ]);

        $project->update($data);
        return redirect()->route('projects.index')->with('success','Projet mis à jour.');
    }

    public function destroy(Project $project)
    {
        // Empêche la suppression si des tickets existent
        if ($project->tickets()->exists()) {
            return back()->withErrors(
                "Impossible de supprimer le projet : des tickets y sont encore associés. ".
                "Supprimez/archivez les tickets d'abord."
            );
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success','Projet supprimé.');
    }

}
