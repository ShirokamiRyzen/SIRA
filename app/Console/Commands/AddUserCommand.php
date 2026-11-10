<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:add-user {--username= : Username untuk login (unik)} {--name= : Nama lengkap} {--password= : Password akun} {--role= : Role akun (admin|verified|user)}')]
#[Description('Menambahkan user baru secara interaktif (username, nama, password, dan role).')]
class AddUserCommand extends Command
{
    /**
     * Pilihan role beserta label yang ditampilkan.
     *
     * @var array<string, string>
     */
    private const ROLES = [
        'admin' => 'Administrator',
        'verified' => 'User terverifikasi',
        'user' => 'User biasa',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $username = $this->resolveUsername();
        $name = $this->resolveName();
        $password = $this->resolvePassword();
        $role = $this->resolveRole();

        $user = User::create([
            'username' => $username,
            'name' => $name,
            'password' => $password,
            'is_admin' => $role === 'admin',
            'is_verified' => in_array($role, ['admin', 'verified'], true),
        ]);

        $this->newLine();
        $this->info("User \"{$user->username}\" berhasil dibuat dengan role {$role}.");
        $this->table(
            ['ID', 'Username', 'Nama', 'Role'],
            [[$user->id, $user->username, $user->name, self::ROLES[$role]]],
        );

        return self::SUCCESS;
    }

    private function resolveUsername(): string
    {
        $username = (string) ($this->option('username') ?: $this->ask('Username'));

        while (true) {
            if (! preg_match('/^[A-Za-z0-9_.-]+$/', $username)) {
                $this->error('Username hanya boleh berisi huruf, angka, titik, underscore, atau strip.');
                $username = (string) $this->ask('Username');

                continue;
            }

            if (User::where('username', $username)->exists()) {
                $this->error("Username \"{$username}\" sudah digunakan.");
                $username = (string) $this->ask('Username');

                continue;
            }

            return $username;
        }
    }

    private function resolveName(): string
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Nama lengkap')));

        while ($name === '') {
            $this->error('Nama lengkap wajib diisi.');
            $name = trim((string) $this->ask('Nama lengkap'));
        }

        return $name;
    }

    private function resolvePassword(): string
    {
        $password = (string) ($this->option('password') ?: $this->secret('Password (minimal 6 karakter)'));

        while (mb_strlen($password) < 6) {
            $this->error('Password minimal 6 karakter.');
            $password = (string) $this->secret('Password (minimal 6 karakter)');
        }

        return $password;
    }

    private function resolveRole(): string
    {
        $role = $this->option('role') ?: $this->choice('Role', array_keys(self::ROLES), 0);

        while (! array_key_exists($role, self::ROLES)) {
            $this->error('Role tidak valid. Pilih: '.implode(', ', array_keys(self::ROLES)));
            $role = $this->choice('Role', array_keys(self::ROLES), 0);
        }

        return $role;
    }
}
