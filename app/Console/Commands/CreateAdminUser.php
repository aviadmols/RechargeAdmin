<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
                            {--email= : Admin email}
                            {--password= : Admin password}
                            {--name=Admin : Display name}';

    protected $description = 'Create an admin user for the Filament panel (/admin)';

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Admin email');
        $password = $this->option('password') ?? $this->secret('Admin password');
        $name = $this->option('name');

        if (! $email || ! $password) {
            $this->error('Email and password are required.');
            return self::FAILURE;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email.');
            return self::FAILURE;
        }

        if (Admin::where('email', $email)->exists()) {
            $this->warn("Admin with email {$email} already exists.");
            $this->info('Log in at: ' . config('app.url') . '/admin');
            return self::SUCCESS;
        }

        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->info('Admin user created.');
        $this->info('Log in at: ' . config('app.url') . '/admin');
        return self::SUCCESS;
    }
}
