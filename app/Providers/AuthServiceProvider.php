<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Gudang; // <-- TAMBAHKAN INI
use App\Models\User;   // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\Gate; // <-- TAMBAHKAN INI

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate untuk CRUD Gudang
        Gate::define('view-any-gudang', function (User $user) {
            // Semua user yang login bisa melihat daftar gudang
            return in_array($user->role, ['superadmin', 'fakultas', 'laboran']);
        });

        Gate::define('create-gudang', function (User $user) {
            // Hanya superadmin dan laboran yang bisa membuat gudang
            return in_array($user->role, ['superadmin', 'laboran']);
        });

        Gate::define('update-gudang', function (User $user, Gudang $gudang) {
            // Superadmin bisa update semua gudang
            if ($user->role === 'superadmin') {
                return true;
            }
            // Laboran hanya bisa update gudang milik prodinya
            if ($user->role === 'laboran') {
                return $user->id_program_studi === $gudang->id_program_studi;
            }
            return false;
        });

        Gate::define('delete-gudang', function (User $user, Gudang $gudang) {
            // Logikanya sama dengan update
            return Gate::allows('update-gudang', $gudang);
        });
        }
}
