<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // champs modifiables en masse
    protected $fillable = ['name', 'email', 'password', 'role'];

    // champs cachés à la sérialisation
    protected $hidden = ['password', 'remember_token'];

    // casts utiles (Laravel 10/11 : hash auto du password)
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations tickets
    public function reportedTickets()
    {
        return $this->hasMany(Ticket::class, 'reporter_id');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assignee_id');
    }

    // (optionnel) petits helpers
    public function isAdmin()       { return $this->role === 'ADMIN'; }
    public function isDev()         { return $this->role === 'DEVELOPPEUR'; }
    public function isReporter()    { return $this->role === 'RAPPORTEUR'; }
}
