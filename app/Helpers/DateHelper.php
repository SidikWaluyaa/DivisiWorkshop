<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Calculate working days remaining until target date
     * Excludes weekends
     */
    public static function calculateDaysRemaining($estimationDate)
    {
        if (!$estimationDate) {
            return null;
        }

        $now = Carbon::now()->startOfDay();
        $target = Carbon::parse($estimationDate)->startOfDay();
        
        // If target is in the past, return 0
        if ($target->isPast()) {
            return 0;
        }
        
        // Calculate working days (exclude weekends)
        $days = 0;
        $current = $now->copy();
        
        while ($current->lt($target)) {
            if (!$current->isWeekend()) {
                $days++;
            }
            $current->addDay();
        }
        
        return $days;
    }
    
    /**
     * Get urgency level based on days remaining
     */
    public static function getUrgencyLevel($daysRemaining)
    {
        if ($daysRemaining === null) {
            return 'unknown';
        }
        
        if ($daysRemaining === 0) {
            return 'overdue';
        }
        
        if ($daysRemaining <= 3) {
            return 'urgent';
        }
        
        if ($daysRemaining <= 7) {
            return 'warning';
        }
        
        return 'safe';
    }
    
    /**
     * Get color classes for urgency level
     */
    public static function getUrgencyColor($urgencyLevel)
    {
        return match($urgencyLevel) {
            'overdue' => 'bg-red-500 text-white border-red-600',
            'urgent' => 'bg-red-100 text-red-700 border-red-300',
            'warning' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            'safe' => 'bg-green-100 text-green-700 border-green-300',
            default => 'bg-gray-100 text-gray-700 border-gray-300',
        };
    }
}
