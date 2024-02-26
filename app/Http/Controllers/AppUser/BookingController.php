<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function userBookings()
    {
        // Get the authenticated user using the app_users guard
        $user = Auth::guard('app_users')->user();

        // Check if a user is authenticated
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Retrieve bookings associated with the authenticated user
        $bookings = Booking::where('user_id', $user->id)->get();

        // Return the list of bookings
        return response()->json(['bookings' => $bookings], 200);
    }



    public function bookService(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'date' => 'required|date_format:m/d/Y H:i', // Add validation for the time format
            'meter' => 'required|numeric',
            'status' => 'boolean',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Fetch the service based on the provided service_id
        $service = Service::findOrFail($request->service_id);

        // Calculate the total price: meter * service price
        $total_price = $request->meter * $service->price;

        // Convert the selected date and time to Carbon instance
        $selectedDateTime = Carbon::createFromFormat('m/d/Y H:i', $request->date);

        // Get the authenticated user using the app_users guard
        $user = Auth::guard('app_users')->user();

        // Check if a user is authenticated
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        // Check if the date and time slot are already booked
        $existingBooking = Booking::where('service_id', $request->service_id)
            ->where('date', $selectedDateTime->format('Y-m-d H:i:s'))
            ->first();

        if ($existingBooking) {
            return response()->json(['error' => 'This date and time slot are already booked. Please choose another.'], 422);
        }

        // Create the booking
        $booking = Booking::create([
            'user_id' => $user->id, // Use the authenticated user's ID
            'service_id' => $request->service_id,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'date' => $selectedDateTime,
            'total_price' => $total_price,
            'status' => $request->has('status') ? $request->status : false,
        ]);
            // Check if the service exists in the user's subscription
            if (!isServiceInUserSubscription($request->service_id)) {
                    ///payment
            }else{

            }

        // Return success response with the created booking
        return response()->json(['message' => 'Booking created successfully', 'booking' => $booking], 201);
    }

    public function show(string $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        return response()->json(['booking' => $booking], 200);
    }


    public function cancelBooking($id)
    {
        // Find the booking by ID
        $booking = Booking::find($id);

        // Check if the booking exists
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // Get the authenticated user using the app_users guard
        $user = Auth::guard('app_users')->user();

        // Check if a user is authenticated and if the booking belongs to the user
        if (!$user || $booking->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Delete the booking
        $booking->delete();

        // Return success response
        return response()->json(['message' => 'Booking canceled successfully'], 200);
    }
}
