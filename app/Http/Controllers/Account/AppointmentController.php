<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Auth::user()->appointments()->with(['service', 'staff'])->latest('start_at')->paginate(10);

        return view('account.appointments.index', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        $appointment->load(['service', 'staff', 'payments']);

        return view('account.appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);
        abort_unless($appointment->canBeCancelled(), 400, 'Lịch hẹn này không thể hủy.');

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        return back()->with('success', 'Đã hủy lịch hẹn.');
    }
}
