<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Gudang; // <-- TAMBAHKAN INI
use App\Models\User;   // <-- TAMBAHKAN INI
use App\Models\Bahan; // <-- TAMBAHKAN INI
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
        Gate::define('view-any-bahan', function (User $user) {
        // Semua user boleh melihat halaman daftar bahan
        return true;
        });

        Gate::define('create-bahan', function (User $user) {
            // Hanya laboran yang bisa menambah bahan
            return $user->role === 'laboran';
        });

        Gate::define('update-bahan', function (User $user, Bahan $bahan) {
            // Hanya laboran yang bisa mengedit bahan milik prodinya sendiri
            return $user->role === 'laboran' && $user->id_program_studi === $bahan->id_program_studi;
        });

        Gate::define('delete-bahan', function (User $user, Bahan $bahan) {
            // Logikanya sama dengan update
            return Gate::allows('update-bahan', $bahan);
        });
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
        
        Gate::define('view-bahan', function (User $user, Bahan $bahan) {
        // Superadmin dan Fakultas bisa melihat semua bahan
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            return true;
        }
        // Laboran hanya bisa melihat bahan milik prodinya
        return $user->role === 'laboran' && $user->id_program_studi === $bahan->id_program_studi;
        });

        Gate::define('manage-pengajuan', function ($user) {
            return in_array($user->role, ['fakultas', 'superadmin']);
        });
        }
}
