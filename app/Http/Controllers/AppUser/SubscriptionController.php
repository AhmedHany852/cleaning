<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Models\AppUsers;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Retrieve all subscriptions
        $subscriptions = Subscription::with('services')->get();

        // Return response with subscriptions
        return response()->json([
            'subscriptions' => $subscriptions
        ], 200);
    }
    public function show($Id)
    {
        // Retrieve the subscription by its ID
        $subscription = Subscription::with('services')->findOrFail($Id);

        // Return response with the subscription
        return response()->json([
            'subscription' => $subscription
        ], 200);
    }

    public function requestVisit(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        // Retrieve the subscription
        $subscription = Subscription::findOrFail($validatedData['subscription_id']);

        // Check if the subscription is expired
        if ($subscription->isExpired()) {
            return response()->json(['message' => 'Subscription has expired.'], 400);
        }

        // Check if the visit limit is reached
        if ($subscription->isVisitLimitReached()) {
            return response()->json(['message' => 'Visit limit has been reached for this subscription.'], 400);
        }

        // Increment the visit count for the subscription
        $subscription->visits++;
        $subscription->save();

        // Return a success message
        return response()->json(['message' => 'Visit requested successfully.'], 200);
    }
   
}
