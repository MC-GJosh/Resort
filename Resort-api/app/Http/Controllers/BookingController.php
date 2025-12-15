<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PickleballBooking;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmed;
use App\Mail\BookingCancelled;

class BookingController extends Controller
{
    public function confirm($id)
    {
        $booking = PickleballBooking::findOrFail($id);
        $booking->status = 'confirmed';
        $booking->save();

        // Send Email
        // Note: Make sure MAIL_MAILER=log is in .env for local testing
        if ($booking->email) {
            Mail::to($booking->email)->send(new BookingConfirmed($booking));
        }

        return response()->json(['message' => 'Booking confirmed and email sent.']);
    }

    public function cancel($id)
    {
        $booking = PickleballBooking::findOrFail($id);
        $booking->status = 'cancelled';
        $booking->save();

        // Send Email
        if ($booking->email) {
            Mail::to($booking->email)->send(new BookingCancelled($booking));
        }

        return response()->json(['message' => 'Booking cancelled and email sent.']);
    }

    public function move(Request $request, $id)
    {
        $booking = PickleballBooking::findOrFail($id);

        $request->validate([
            'court_id' => 'required|exists:courts,id',
            'date' => 'required|date',
            'time' => 'required', // Assuming time slot format
        ]);

        $booking->court_id = $request->court_id;
        $booking->date = $request->date;
        $booking->time_slot = $request->time; // Ensure column name matches DB
        $booking->save();

        return response()->json(['message' => 'Booking moved successfully.', 'booking' => $booking]);
    }
}
