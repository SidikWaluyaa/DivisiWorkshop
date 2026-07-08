<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log a user activity to the database.
     *
     * @param string $activity
     * @param string|null $description
     * @return void
     */
    public static function log(string $activity, ?string $description = null): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
