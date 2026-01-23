<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class GrantGudangAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users who are NOT admin (admins typically already have access)
        // But for safety, let's just check everyone or specific roles.
        // Assuming we want to give 'gudang' access to relevant staff.
        
        $users = User::all();
        
        foreach ($users as $user) {
            $currentAccess = $user->access_rights ?? [];
            
            // If user is admin, they have access via code logic, but adding it doesn't hurt for explicit checks
            // If user is NOT admin, we definitely need to add it if they are supposed to have it.
            // For dev purpose, let's add it to everyone so they can test.
            
            if (!in_array('gudang', $currentAccess)) {
                $currentAccess[] = 'gudang';
                $user->access_rights = $currentAccess;
                $user->save();
                $this->command->info("Granted 'gudang' access to user: {$user->email}");
            } else {
                $this->command->info("User {$user->email} already has 'gudang' access.");
            }
        }
    }
}
