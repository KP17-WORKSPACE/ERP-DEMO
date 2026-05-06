<?php

namespace App\Http\Controllers;
use App\SystemNotification;

class NotificationController extends Controller
{
    public function pending()
    {
        $user = auth()->user();

        $notes = SystemNotification::where('is_shown', true)
            ->where('is_resolved', false)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                //   ->orWhereIn('role', $roles);
            })
            ->get();

        return response()->json($notes);
    }

    public function dismiss()
    {
        $user = auth()->user();

        $this->dismissUser();

        $role = null;

        $roleMap = [
            3 => 'receivables',
            4 => 'invoice',
            10 => 'purchase',
            27 => 'accounts',
            28 => 'accounts',
            29 => 'delivery',
        ];

        $role = $roleMap[$user->role_id] ?? null;

        if (!$role) {
            return ['status' => 'error', 'message' => 'Invalid role'];
        }

        // Get all shown, unresolved notifications for this role
        $notifications = SystemNotification::where('is_shown', true)
            ->where('is_resolved', false)
            ->where('role', $role)
            ->get();

        $dubaiTime = \Carbon\Carbon::now('Asia/Dubai');

        foreach ($notifications as $notification) {
            // Hide notification and reset created_at to current time + 15 minutes
            // This will make it reappear after 15 minutes when the command runs
            $notification->is_shown = false;
            $notification->created_at = $dubaiTime;
            $notification->save();
        }

        return ['status' => 'ok', 'dismissed' => $notifications->count()];
    }

     public function dismissUser()
    {
        $user = auth()->user();
       


        // Get all shown, unresolved notifications for this role
        $notifications = SystemNotification::where('is_shown', true)
            ->where('is_resolved', false)
            ->where('user_id', $user->id)
            ->get();

        $dubaiTime = \Carbon\Carbon::now('Asia/Dubai');

        foreach ($notifications as $notification) {
            // Hide notification and reset created_at to current time + 15 minutes
            // This will make it reappear after 15 minutes when the command runs
            $notification->is_shown = false;
            $notification->created_at = $dubaiTime;
            $notification->save();
        }
    }
}
