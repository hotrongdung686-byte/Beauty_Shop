<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Staff;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['service', 'staff', 'user']);

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        if ($date = $request->string('date')->value()) {
            $query->whereDate('start_at', $date);
        }

        $appointments = $query->orderByDesc('start_at')->paginate(15)->withQueryString();

        return view('admin.appointments.index', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['service', 'staff', 'user', 'payments']);
        $staffOptions = $appointment->service->staff()->active()->get();

        return view('admin.appointments.show', compact('appointment', 'staffOptions'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        $appointment->update(['status' => $data['status']]);

        return back()->with('success', 'Đã cập nhật trạng thái lịch hẹn.');
    }

    public function assignStaff(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
        ]);

        $staff = Staff::findOrFail($data['staff_id']);

        if (! $staff->isAvailable($appointment->start_at, $appointment->end_at, $appointment->id)) {
            return back()->with('error', 'Thợ này đã có lịch trùng khung giờ.');
        }

        $appointment->update(['staff_id' => $staff->id]);

        return back()->with('success', 'Đã phân công thợ thực hiện.');
    }
}
