<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'reporter_id', 'assignee_id',
        'title', 'description', 'status'
    ];

    // (optionnel) constantes pratiques
    public const STATUS_OPEN        = 'OPEN';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_RESOLVED    = 'RESOLVED';
    public const STATUS_CLOSED      = 'CLOSED';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(\App\Models\TicketStatusHistory::class)
                    ->orderByDesc('changed_at');
    }

}
