<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use App\Models\TicketStatusHistory;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(\App\Models\Ticket::class, 'ticket');
    }

    public function index(Request $request)
    {
        $query = Ticket::with(['project.client','reporter','assignee'])
            ->latest();

        // File d’attente : tickets OPEN non assignés
        if ($request->boolean('queue')) {
            $query->whereNull('assignee_id')->where('status', 'OPEN');
        }

        $tickets = $query->paginate(10);
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $projects   = Project::with('client')->orderBy('name')->get();
        $developers = User::where('role','DEVELOPPEUR')->orderBy('name')->get();
        return view('tickets.create', compact('projects','developers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => ['required','exists:projects,id'],
            'title'       => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'assignee_id' => ['nullable','exists:users,id'],
        ]);
        $data['reporter_id'] = auth()->id();
        $data['status']      = 'OPEN';

        $ticket = Ticket::create($data);

        // Historiser la création (optionnel)
        TicketStatusHistory::create([
            'ticket_id'  => $ticket->id,
            'old_status' => 'OPEN',
            'new_status' => 'OPEN',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        return redirect()->route('tickets.index')->with('success','Ticket créé.');
    }

    public function edit(Ticket $ticket)
    {
        $projects   = Project::with('client')->orderBy('name')->get();
        $developers = User::where('role','DEVELOPPEUR')->orderBy('name')->get();

        $ticket->load(['statusHistories.changedBy']); // 👈

        return view('tickets.edit', compact('ticket','projects','developers'));
    }


    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'project_id'  => ['required','exists:projects,id'],
            'title'       => ['required','string','max:200'],
            'description' => ['nullable','string'],
            'assignee_id' => ['nullable','exists:users,id'],
            'status'      => ['required','in:OPEN,IN_PROGRESS,RESOLVED,CLOSED'],
        ]);

        // Si quelqu'un essaie de modifier le statut -> on vérifie le droit
        if ($data['status'] !== $ticket->status) {
            $this->authorize('changeStatus', $ticket);
        }

        $old = $ticket->status;

        $ticket->update($data);

        // Historiser uniquement si le statut a vraiment changé
        if ($old !== $ticket->status) {
            \App\Models\TicketStatusHistory::create([
                'ticket_id'  => $ticket->id,
                'old_status' => $old,
                'new_status' => $ticket->status,
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        }

        return redirect()->route('tickets.index')->with('success','Ticket mis à jour.');
    }


    public function assignMe(Ticket $ticket)
    {
        // Autorisation : ADMIN/DEV + ticket non assigné
        $this->authorize('assignSelf', $ticket);

        // Anti course : recheck si quelqu’un l’a pris entre-temps
        $ticket->refresh();
        if (!is_null($ticket->assignee_id)) {
            return back()->withErrors("Ce ticket vient d'être assigné par quelqu'un d'autre.");
        }

        $updates = ['assignee_id' => auth()->id()];
        $oldStatus = $ticket->status;

        // Bonus UX : si le ticket est OPEN, on le passe en IN_PROGRESS
        if ($ticket->status === 'OPEN') {
            $updates['status'] = 'IN_PROGRESS';
        }

        $ticket->update($updates);

        // Journaliser si le statut a changé
        if (array_key_exists('status', $updates) && $updates['status'] !== $oldStatus) {
            \App\Models\TicketStatusHistory::create([
                'ticket_id'  => $ticket->id,
                'old_status' => $oldStatus,
                'new_status' => $updates['status'],
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        }

        return back()->with('success', 'Vous vous êtes assigné ce ticket.');
    }

    public function unassignMe(Ticket $ticket)
    {
        $this->authorize('unassignSelf', $ticket);

        // Si on était en "IN_PROGRESS" sans autre info, on peut rester comme ça
        // (pas de changement de statut par défaut)
        $ticket->update(['assignee_id' => null]);

        return back()->with('success', 'Vous vous êtes désassigné de ce ticket.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success','Ticket supprimé.');
    }
}
