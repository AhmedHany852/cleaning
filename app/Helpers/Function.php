<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
if (!function_exists('upload')) {
function upload($avatar, $directory)
{
        $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
        $avatar->move($directory, $avatarName);
        return $avatarName;

}

function isServiceInUserSubscription( $serviceId)
{
    $user = Auth::guard('app_users')->user();

    if (!$user) {
        return false;
    }

    $subscription = $user->subscription;

    if (!$subscription || $subscription->expire_date < now()) {
        return false;
    }
    if ($subscription->visit_count >= $subscription->visit_limit) {
        return response()->json(['error' => 'Visit count limit exceeded'], 422);
    }
    $subscriptionServices = $subscription->services;

    foreach ($subscriptionServices as $service) {
        if ($service->id === $serviceId) {
            return true;
        }
    }

    return false;
}
}
