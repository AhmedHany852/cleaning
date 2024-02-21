<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{

    public function createSubscriptions(Request $request)
    {
        $service_ids = explode(',', $request->service_id);
        // Validate the input data
        $validatedData = $request->validate([
            'description' => 'required',
            'visits' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'service_ids.*' => 'required|exists:services,id', // Ensure each service ID exists in the services table
        ]);

        // Start a database transaction
        DB::beginTransaction();


        // Create a new subscription
        $subscription = Subscription::create([
            'description' => $request->description,
            'visits' => $request->visits,
            'price' => $request->price,
            'duration' => $request->duration,
            'status' => $request->status,
        ]);

        // Attach services to the subscription
        foreach ($service_ids as $serviceId) {


            $subscription->services()->attach($serviceId);
        }

        // Commit the transaction
        DB::commit();

        // Return success response
        return response()->json([
            'message' => 'Subscription created successfully with selected services.',
            'subscription' => $subscription
        ], 200);
    }


    public function checkExpiration(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        // Retrieve the subscription
        $subscription = Subscription::findOrFail($validatedData['subscription_id']);

        // Check if the subscription is expired
        $isExpired = $subscription->isExpired();

        // Return the expiration status as JSON response
        return response()->json(['expired' => $isExpired]);
    }

    public function checkVisits(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        // Retrieve the subscription
        $subscription = Subscription::findOrFail($validatedData['subscription_id']);

        // Check if the visit limit is reached
        $isVisitLimitReached = $subscription->isVisitLimitReached();

        // Return the visit limit status as JSON response
        return response()->json(['visit_limit_reached' => $isVisitLimitReached]);
    }
}
