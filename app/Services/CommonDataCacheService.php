<?php

namespace App\Services;

use App\Models\User;
use App\Models\Team;
use App\Models\Contact;
use App\Models\Stage;
use App\Models\Area;
use Illuminate\Support\Facades\Cache;

class CommonDataCacheService
{
    /**
     * Cache duration in seconds (30 minutes for relatively static data)
     */
    const CACHE_TTL = 1800;

    /**
     * Get all users (cached) - for dropdowns
     */
    public static function getAllUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('all_users', self::CACHE_TTL, function () {
            return User::orderBy('name')->get(['id', 'name', 'email']);
        });
    }

    /**
     * Get all teams (cached) - for dropdowns
     */
    public static function getAllTeams(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('all_teams', self::CACHE_TTL, function () {
            return Team::orderBy('name')->get(['id', 'name']);
        });
    }

    /**
     * Get all contacts (cached) - for dropdowns
     */
    public static function getAllContacts(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('all_contacts', self::CACHE_TTL, function () {
            return Contact::orderBy('name')->get(['id', 'name', 'email']);
        });
    }

    /**
     * Get all stages (cached) - for dropdowns
     */
    public static function getAllStages(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('all_stages', self::CACHE_TTL, function () {
            return Stage::orderBy('name')->get(['id', 'name', 'description']);
        });
    }

    /**
     * Get allowed areas for user (cached per user)
     */
    public static function getAllowedAreasForUser($userId): array
    {
        return Cache::remember("user_allowed_areas:{$userId}", self::CACHE_TTL, function () use ($userId) {
            $user = User::find($userId);
            if (!$user) {
                return [];
            }

            // Use cached area structure
            $areas = Cache::get('area_structure');
            if (!$areas) {
                $areas = Area::all();
            }

            return $areas->filter(function ($area) use ($user) {
                $permission = ($area->parent ? $area->parent->slug . '.' : '') . $area->slug . '.view';
                return $user->can($permission);
            })->pluck('id')->toArray();
        });
    }

    /**
     * Clear all common data caches
     */
    public static function clearAll(): void
    {
        Cache::forget('all_users');
        Cache::forget('all_teams');
        Cache::forget('all_contacts');
        Cache::forget('all_stages');

        // Clear user-specific caches (you might want to track active users)
        // For now, we'll use a wildcard pattern if your cache driver supports it
    }

    /**
     * Clear cache for a specific type
     */
    public static function clearType(string $type): void
    {
        Cache::forget("all_{$type}");
    }

    /**
     * Clear cache for a specific user
     */
    public static function clearUserCache(int $userId): void
    {
        Cache::forget("user_allowed_areas:{$userId}");
    }
}
