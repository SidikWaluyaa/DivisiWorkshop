<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:admin-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the admin user role to be "admin" instead of "user"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'admin@workshop.com';
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        if ($user->role === 'admin') {
            $this->info("User {$email} is already an Admin.");
            return 0;
        }

        $user->role = 'admin';
        // Ensure access_rights is also set to full (just in case)
        // Although the model logic handles 'admin' role automatically
        $user->save();

        $this->info("Successfully updated {$email} role to 'admin'.");
        return 0;
    }
}
