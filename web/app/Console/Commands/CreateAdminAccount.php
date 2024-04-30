<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:create-admin-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating admin account...');

        $name = $this->ask('Enter name');
        $email = $this->ask('Enter email');
        $password = $this->secret('Enter password');

        try {
            $findByEmail = \App\Models\User::where('email', $email)->first();
            if ($findByEmail) {
                $this->error('Admin account with this email already exists');
                return;
            }
            $admin = new \App\Models\User();
            $admin->name = $name;
            $admin->email = $email;
            $admin->password = Hash::make($password);
            $admin->save();
        } catch (\Exception $e) {
            $this->error('Failed to create admin account');
            $this->error($e->getMessage());
            return;
        }

        $this->info('Admin account created successfully');
    }
}
