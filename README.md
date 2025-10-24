# SAE 5.02 – Gestion de Tickets (Laravel)

Application web de gestion de tickets pour un contexte Réseaux & Télécoms.  
Trois rôles (**Admin**, **Développeur**, **Rapporteur**) gèrent clients, projets et tickets, avec assignation et historique de statuts.

> Stack : **Laravel + Breeze (Blade)** · **Tailwind + Vite** · **SQLite (dev)**

---

## Sommaire
- [Fonctionnalités](#fonctionnalités)
- [Rôles & droits](#rôles--droits)
- [Modèle de données](#modèle-de-données)
- [Installation (dev)](#installation-dev)
- [Comptes de démo](#comptes-de-démo)
- [Parcours rapide](#parcours-rapide)
- [Structure du projet](#structure-du-projet)
- [Commandes utiles](#commandes-utiles)
- [Choix techniques](#choix-techniques)
- [Troubleshooting](#troubleshooting)
- [Captures (optionnel)](#captures-optionnel)
- [Licence / Contexte](#licence--contexte)

---

## Fonctionnalités
- Authentification (Laravel Breeze, Blade)
- **Rôles & permissions** via Policies (User/Client/Project/Ticket)
- CRUD **Clients / Projets / Tickets**
- **Self-assign** (un dev peut s’assigner un ticket non assigné)
- Changement de **statut** (OPEN → IN_PROGRESS → RESOLVED → CLOSED)
- **Historique des statuts** (audit : qui/quand/quoi)
- UI Tailwind (badges de statut, boutons contrastés, tableaux lisibles)

---

## Rôles & droits

| Action / Rôle                      | Admin | Développeur | Rapporteur |
|-----------------------------------|:-----:|:-----------:|:----------:|
| Gérer **utilisateurs & rôles**    |  ✅   |      ❌      |     ❌      |
| Gérer **clients**                 |  ✅   |      ❌      |     ✅      |
| Gérer **projets**                 |  ✅   |      ✅      |     ❌      |
| Créer / modifier **tickets**      |  ✅   |   ✅ (lim.)  |     ✅      |
| Supprimer un ticket               |  ✅   |      ❌      |     ❌      |
| Changer le **statut**             |  ✅   |      ✅      |  ✅ (ses tickets) |
| **Self-assign / Unassign**        |  ✅   |      ✅      |     ❌      |
| Voir l’**historique**             |  ✅   |      ✅      |     ✅      |

> Les règles sont implémentées via **Policies**.

---

## Modèle de données
- **User** *(id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role)* — `role ∈ {ADMIN, DEVELOPPEUR, RAPPORTEUR}`
- **Client** *(id, name, contact_email, phone, created_at, updated_at)*
- **Project** *(id, client_id, name, description, created_at, updated_at)* — `belongsTo Client`
- **Ticket** *(id, project_id, reporter_id, assignee_id, title, description, status, created_at, updated_at)* `belongsTo Project`, `reporter/assignee → User`
  `status ∈ {OPEN, IN_PROGRESS, RESOLVED, CLOSED}`
- **TicketStatusHistory** *(id, ticket_id, old_status, new_status, changed_by, changed_at)*  `belongsTo Ticket`, `changed_by → User`

---

## Installation (dev)

> Prérequis : **PHP 8.2+**, **Composer**, **Node 18+**.  
> Base de données par défaut : **SQLite** (aucun serveur DB à installer).

```bash
git clone https://github.com/N0L1S1/R-T-sae502-gestion-tickets.git
cd sae502-gestion-tickets

# Back (Laravel)
composer install
cp .env.example .env
php artisan key:generate

# DB SQLite
mkdir -p database
touch database/database.sqlite
php artisan migrate --seed

# Front (Vite/Tailwind)
npm ci
# Dev (HMR) : laissez tourner dans un terminal
npm run dev
# OU build statique (sans Vite)
npm run build
```

Lancer Laravel :
```bash
php artisan serve
# http://127.0.0.1:8000
```

> En dev, ouvrez **deux terminaux** : `npm run dev` (Vite) + `php artisan serve`.  
> Sans Vite, exécutez `npm run build` (assets générés dans `public/build`).

---

## Comptes de démo
*(créés par le seeder)*

- **Admin** : `admin@example.com` / `password`  
- **Développeur** : `dev@example.com` / `password`  
- **Rapporteur** : `reporter@example.com` / `password`

> Sinon, créez un compte et passez-le admin via Tinker :
> ```bash
> php artisan tinker
> >>> $u = App\Models\User::first();
> >>> $u->role = 'ADMIN'; $u->save();
> ```

---

## Parcours rapide
1. **Connexion** (Admin) → menu **Utilisateurs** : vérifier/éditer les rôles  
2. **Clients** → créer un client  
3. **Projets** → créer un projet lié au client  
4. **Tickets** → créer un ticket (Rapporteur/Admin)  
5. **File d’attente** → un Dev clique **“M’assigner”** (passe en *IN_PROGRESS*)  
6. **Édition du ticket** → changer le **statut**, consulter l’**historique**

---

## Structure du projet (extrait)
```
app/
  Http/Controllers/ { ClientController, ProjectController, TicketController, UserController }
  Models/           { Client, Project, Ticket, TicketStatusHistory, User }
  Policies/         { ClientPolicy, ProjectPolicy, TicketPolicy, UserPolicy }
database/
  migrations/       # schéma + historique des statuts
  seeders/          # comptes & données de démo
resources/
  views/            # Blade (tickets, clients, projets, users, layout)
  css/app.css       # Tailwind + styles utilitaires (btn, badges, tables)
routes/web.php      # resources + assignation self/unassign
```

---

## Commandes utiles
```bash
php artisan migrate:fresh --seed   # reset DB + démo
php artisan optimize:clear         # vider caches
npm run dev                        # Vite (HMR)
npm run build                      # build prod
```

---

## Choix techniques
- **Laravel Breeze (Blade)** : auth server-side simple et fiable  
- **Policies** : contrôle d’accès clair par ressource  
- **SQLite** pour la SAE : migrations versionnées, setup instantané  
- **Tailwind + Vite** : styles uniformes (badges, boutons, tableaux), DX rapide

---

## Troubleshooting
- **Pas de styles ?**  
  Dev : vérifiez `npm run dev` et `@vite(['resources/css/app.css','resources/js/app.js'])` dans le layout.  
  Sans Vite : lancez `npm run build`, puis hard-reload (Ctrl+F5).
- **Erreur DB** : `.env` → `DB_CONNECTION=sqlite` + fichier `database/database.sqlite`.  
- **Accès refusé** : vérifiez les **rôles** et les **Policies**.


---

## Licence / Contexte
Projet académique – **SAE 5.02** (BUT Réseaux & Télécoms).  
Usage pédagogique et démonstration technique.

---

**Auteur :** NOLISI — 2025  
**Repo :** https://github.com/N0L1S1/R-T-sae502-gestion-tickets
