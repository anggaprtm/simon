<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Jika Anda menggunakan Sanctum untuk API
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Pastikan HasApiTokens ada jika Anda berencana membuat API

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_program_studi', // Tambahkan ini
        'role',             // Tambahkan ini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Otomatis hashing saat diset
    ];

    /**
     * Relasi ke ProgramStudi (seorang user laboran milik satu prodi)
     * User dengan role fakultas atau superadmin bisa memiliki id_program_studi NULL.
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi');
    }

    /**
     * Relasi ke Transaksi (seorang user bisa melakukan banyak transaksi)
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_user');
    }
}