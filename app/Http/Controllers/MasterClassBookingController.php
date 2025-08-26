<?php

namespace App\Http\Controllers;

use App\Models\MasterClassBooking;
use Illuminate\Http\Request;

class MasterClassBookingController extends Controller
{
    /**
     * Display a listing of the master class bookings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookings = MasterClassBooking::with(['user', 'masterClass', 'giftCertificate'])->get();
        return response()->json($bookings);
    }

    /**
     * Store a newly created master class booking in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'master_class_id' => 'required|exists:master_classes,id',
            'gift_certificate_id' => 'nullable|exists:gift_certificates,id',
            'payment_status' => 'required|in:pending,paid,failed,refunded,partially_refunded',
            'visit_status' => 'required|in:pending,visited,no_show,cancelled,waiting_list',
            'amount' => 'required|integer',
            'order_id' => 'nullable|integer',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $booking = MasterClassBooking::create($validated);

        return response()->json($booking, 201);
    }

    /**
     * Display the specified master class booking.
     *
     * @param  \App\Models\MasterClassBooking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(MasterClassBooking $booking)
    {
        $booking->load(['user', 'masterClass', 'giftCertificate']);
        return response()->json($booking);
    }

    /**
     * Update the specified master class booking in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasterClassBooking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MasterClassBooking $booking)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'master_class_id' => 'sometimes|required|exists:master_classes,id',
            'gift_certificate_id' => 'nullable|exists:gift_certificates,id',
            'payment_status' => 'sometimes|required|in:pending,paid,failed,refunded,partially_refunded',
            'visit_status' => 'sometimes|required|in:pending,visited,no_show,cancelled,waiting_list',
            'amount' => 'sometimes|required|integer',
            'order_id' => 'nullable|integer',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $booking->update($validated);

        return response()->json($booking);
    }

    /**
     * Remove the specified master class booking from storage.
     *
     * @param  \App\Models\MasterClassBooking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterClassBooking $booking)
    {
        $booking->delete();

        return response()->json(null, 204);
    }
}