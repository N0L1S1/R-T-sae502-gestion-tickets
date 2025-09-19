<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatusHistory extends Model
{
    use HasFactory;

    // Pas de created_at/updated_at dans cette table → on les désactive
    public $timestamps = false;

    protected $fillable = [
        'ticket_id', 'old_status', 'new_status', 'changed_by', 'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
