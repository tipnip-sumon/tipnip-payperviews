<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserModalTracking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WelcomeModalController extends Controller
{
    /**
     * Check if welcome modal should be shown
     */
    public function checkModal(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['should_show' => false]);
        }

        $schedule = UserModalTracking::getModalSchedule($user->id, 'welcome_guide');
        
        return response()->json($schedule);
    }

    /**
     * Record modal shown
     */
    public function recordShown(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['success' => false]);
        }

        $tracking = UserModalTracking::recordModalShown($user->id, 'welcome_guide');
        
        return response()->json([
            'success' => true,
            'tracking' => $tracking
        ]);
    }

    /**
     * Record modal interaction
     */
    public function recordInteraction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:clicked,dismissed,completed,next_step'
        ]);

        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['success' => false]);
        }

        $action = $request->input('action');
        $tracking = UserModalTracking::recordModalClick($user->id, 'welcome_guide', $action);
        
        // Get updated schedule
        $schedule = UserModalTracking::getModalSchedule($user->id, 'welcome_guide');
        
        return response()->json([
            'success' => true,
            'action' => $action,
            'tracking' => $tracking,
            'schedule' => $schedule
        ]);
    }

    /**
     * Get modal content
     */
    public function getContent(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['content' => null]);
        }

        $schedule = UserModalTracking::getModalSchedule($user->id, 'welcome_guide');
        
        $content = [
            'title' => 'Welcome to Laravel!',
            'subtitle' => 'Quick Setup & Installation Guide',
            'steps' => [
                [
                    'title' => '🚀 Getting Started',
                    'content' => 'Welcome to your Laravel application! This guide will help you get started quickly.',
                    'icon' => 'fas fa-rocket'
                ],
                [
                    'title' => '⚙️ Configuration',
                    'content' => 'Configure your environment variables, database connections, and application settings.',
                    'icon' => 'fas fa-cog'
                ],
                [
                    'title' => '📁 Project Structure',
                    'content' => 'Learn about Laravel\'s directory structure and where to find important files.',
                    'icon' => 'fas fa-folder-open'
                ],
                [
                    'title' => '🛠️ Development Tools',
                    'content' => 'Discover useful Laravel tools like Artisan commands, Tinker, and debugging utilities.',
                    'icon' => 'fas fa-tools'
                ],
                [
                    'title' => '📚 Documentation',
                    'content' => 'Access comprehensive Laravel documentation and community resources.',
                    'icon' => 'fas fa-book'
                ]
            ],
            'user_info' => [
                'name' => $user->firstname . ' ' . $user->lastname,
                'email' => $user->email,
                'click_count' => $schedule['click_count'],
                'schedule' => $schedule['schedule']
            ]
        ];

        return response()->json([
            'content' => $content,
            'schedule' => $schedule
        ]);
    }

    /**
     * Dismiss modal for today
     */
    public function dismiss(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['success' => false]);
        }

        $tracking = UserModalTracking::recordModalClick($user->id, 'welcome_guide', 'dismissed');
        
        return response()->json([
            'success' => true,
            'message' => 'Modal dismissed for today',
            'tracking' => $tracking
        ]);
    }
}
