<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminAccountPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:reset-admin-account-password';

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

        $this->info('Resetting admin account password...');

        $email = $this->ask('Enter email');
        $password = $this->secret('Enter password');

        try {
            $findByEmail = \App\Models\User::where('email', $email)->first();
            if (!$findByEmail) {
                $this->error('Admin account with this email does not exist');
                return;
            }
            $findByEmail->password = Hash::make($password);
            $findByEmail->save();
        } catch (\Exception $e) {
            $this->error('Failed to reset admin account password');
            $this->error($e->getMessage());
            return;
        }

        $this->info('Admin account password reset successfully');

    }
}
