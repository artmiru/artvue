<?php

namespace App\Http\Controllers;

use App\Models\GiftCertificate;
use Illuminate\Http\Request;

class GiftCertificateController extends Controller
{
    /**
     * Display a listing of the gift certificates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $certificates = GiftCertificate::with('user')->get();
        return response()->json($certificates);
    }

    /**
     * Store a newly created gift certificate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'code' => 'required|string|unique:gift_certificates',
            'name' => 'required|string',
            'amount' => 'required|integer',
            'visits_total' => 'required|integer',
            'visits_used' => 'required|integer',
            'expiry_date' => 'required|date',
            'purchaser_name' => 'required|string',
            'purchaser_phone' => 'nullable|string',
            'purchaser_email' => 'required|email',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'status' => 'required|in:active,used,expired,cancelled',
            'notes' => 'nullable|string',
            'is_sent' => 'boolean',
        ]);

        $certificate = GiftCertificate::create($validated);

        return response()->json($certificate, 201);
    }

    /**
     * Display the specified gift certificate.
     *
     * @param  \App\Models\GiftCertificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function show(GiftCertificate $certificate)
    {
        $certificate->load('user');
        return response()->json($certificate);
    }

    /**
     * Update the specified gift certificate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GiftCertificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GiftCertificate $certificate)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'code' => 'sometimes|required|string|unique:gift_certificates,code,' . $certificate->id,
            'name' => 'sometimes|required|string',
            'amount' => 'sometimes|required|integer',
            'visits_total' => 'sometimes|required|integer',
            'visits_used' => 'sometimes|required|integer',
            'expiry_date' => 'sometimes|required|date',
            'purchaser_name' => 'sometimes|required|string',
            'purchaser_phone' => 'nullable|string',
            'purchaser_email' => 'sometimes|required|email',
            'payment_status' => 'sometimes|required|in:pending,paid,failed,refunded',
            'status' => 'sometimes|required|in:active,used,expired,cancelled',
            'notes' => 'nullable|string',
            'is_sent' => 'boolean',
        ]);

        $certificate->update($validated);

        return response()->json($certificate);
    }

    /**
     * Remove the specified gift certificate from storage.
     *
     * @param  \App\Models\GiftCertificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function destroy(GiftCertificate $certificate)
    {
        $certificate->delete();

        return response()->json(null, 204);
    }
}