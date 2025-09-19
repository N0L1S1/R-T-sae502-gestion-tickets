<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ————— Utilisateurs de démo —————
            $admin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name'     => 'Admin',
                    'password' => Hash::make('password'),
                    'role'     => 'ADMIN',
                ]
            );

            $dev = User::firstOrCreate(
                ['email' => 'dev@example.com'],
                [
                    'name'     => 'Développeur',
                    'password' => Hash::make('password'),
                    'role'     => 'DEVELOPPEUR',
                ]
            );

            $reporter = User::firstOrCreate(
                ['email' => 'reporter@example.com'],
                [
                    'name'     => 'Rapporteur',
                    'password' => Hash::make('password'),
                    'role'     => 'RAPPORTEUR',
                ]
            );

            // ————— Client + Projets —————
            $client = Client::firstOrCreate(
                ['name' => 'ACME Corp.'],
                [] // ajoute d’autres colonnes si tu en as
            );

            $proj1 = Project::firstOrCreate(
                ['name' => 'Portail Client', 'client_id' => $client->id],
                []
            );

            $proj2 = Project::firstOrCreate(
                ['name' => 'API Interne', 'client_id' => $client->id],
                []
            );

            // Helper pour historiser un changement de statut
            $pushHistory = function (Ticket $t, string $old, string $new, int $byUserId, ?string $when = null) {
                TicketStatusHistory::create([
                    'ticket_id'  => $t->id,
                    'old_status' => $old,
                    'new_status' => $new,
                    'changed_by' => $byUserId,
                    'changed_at' => $when ? Carbon::parse($when) : now(),
                ]);
            };

            // ————— Tickets —————

            // TICKET 1 : créé par le rapporteur, non assigné, statut OPEN
            $t1 = Ticket::create([
                'project_id'  => $proj1->id,
                'reporter_id' => $reporter->id,
                'assignee_id' => null,
                'title'       => 'Impossible de se connecter',
                'description' => 'L’utilisateur ne peut pas se connecter depuis hier soir.',
                'status'      => 'OPEN',
            ]);

            // Historique de création (OPTIONNEL mais utile : on garde la trace dès l’OPEN)
            $pushHistory($t1, 'OPEN', 'OPEN', $reporter->id, now()->subDays(1)->toDateTimeString());

            // TICKET 2 : assigné au dev, passe de OPEN -> IN_PROGRESS
            $t2 = Ticket::create([
                'project_id'  => $proj2->id,
                'reporter_id' => $admin->id,
                'assignee_id' => $dev->id,
                'title'       => 'Erreur 500 sur /api/v1/users',
                'description' => 'Erreur sporadique en production.',
                'status'      => 'IN_PROGRESS',
            ]);
            $pushHistory($t2, 'OPEN', 'IN_PROGRESS', $dev->id, now()->subHours(6)->toDateTimeString());

            // TICKET 3 : assigné au dev, passé par IN_PROGRESS -> RESOLVED -> CLOSED
            $t3 = Ticket::create([
                'project_id'  => $proj1->id,
                'reporter_id' => $reporter->id,
                'assignee_id' => $dev->id,
                'title'       => 'Formulaire contact ne s’envoie pas',
                'description' => 'Le bouton semble inactif.',
                'status'      => 'CLOSED',
            ]);

            // Chaîne d’historique réaliste
            $pushHistory($t3, 'OPEN', 'IN_PROGRESS', $dev->id, now()->subDays(2)->toDateTimeString());
            $pushHistory($t3, 'IN_PROGRESS', 'RESOLVED', $dev->id, now()->subDay()->toDateTimeString());
            $pushHistory($t3, 'RESOLVED', 'CLOSED', $admin->id, now()->subHours(2)->toDateTimeString());
        });
    }
}
