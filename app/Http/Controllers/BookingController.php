<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    protected const SLOT_STEP_MINUTES = 30;

    protected const MIN_LEAD_MINUTES = 30;

    public function create(Service $service)
    {
        abort_unless($service->is_active, 404);

        $service->load(['staff' => fn ($q) => $q->active()]);

        return view('booking.create', [
            'service' => $service,
            'minDate' => now()->format('Y-m-d'),
            'maxDate' => now()->addDays(30)->format('Y-m-d'),
        ]);
    }

    /**
     * AJAX endpoint: available time slots for a service on a given date,
     * optionally narrowed to one staff member.
     */
    public function slots(Request $request, Service $service)
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'staff_id' => ['nullable', 'integer', 'exists:staff,id'],
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $data['date'])->startOfDay();

        $staffList = $data['staff_id'] ?? null
            ? $service->staff()->active()->where('staff.id', $data['staff_id'])->with('workingHours')->get()
            : $service->staff()->active()->with('workingHours')->get();

        $slotMap = []; // 'H:i' => [staff_id, ...]

        foreach ($staffList as $staff) {
            foreach ($this->availableSlotsForStaff($staff, $date, $service->duration_minutes) as $time) {
                $slotMap[$time][] = $staff->id;
            }
        }

        ksort($slotMap);

        $slots = collect($slotMap)->map(fn ($staffIds, $time) => [
            'time' => $time,
            'staff_ids' => $staffIds,
        ])->values();

        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request, Service $service)
    {
        abort_unless($service->is_active, 404);

        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $start = Carbon::createFromFormat('Y-m-d H:i', $data['date'].' '.$data['time']);
        $end = $start->copy()->addMinutes($service->duration_minutes);

        if ($start->lt(now()->addMinutes(self::MIN_LEAD_MINUTES))) {
            throw ValidationException::withMessages(['time' => 'Vui lòng chọn khung giờ hợp lệ (tối thiểu 30 phút kể từ bây giờ).']);
        }

        $candidateStaff = $data['staff_id'] ?? null
            ? $service->staff()->active()->where('staff.id', $data['staff_id'])->get()
            : $service->staff()->active()->get();

        try {
            $appointment = DB::transaction(function () use ($service, $candidateStaff, $start, $end, $data) {
                $chosenStaff = null;

                foreach ($candidateStaff as $staff) {
                    $locked = Staff::whereKey($staff->id)->lockForUpdate()->first();
                    if ($locked && $locked->isAvailable($start, $end)) {
                        $chosenStaff = $locked;
                        break;
                    }
                }

                if (! $chosenStaff && $candidateStaff->isNotEmpty()) {
                    throw ValidationException::withMessages(['time' => 'Khung giờ này vừa có người đặt trước. Vui lòng chọn khung giờ khác.']);
                }

                return Appointment::create([
                    'user_id' => Auth::id(),
                    'service_id' => $service->id,
                    'staff_id' => $chosenStaff?->id,
                    'start_at' => $start,
                    'end_at' => $end,
                    'status' => Appointment::STATUS_PENDING,
                    'price' => $service->price,
                    'deposit_amount' => $service->deposit_amount,
                    'customer_name' => $data['customer_name'],
                    'customer_phone' => $data['customer_phone'],
                    'note' => $data['note'] ?? null,
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('booking.success', $appointment)->with('success', 'Đặt lịch thành công!');
    }

    public function success(Appointment $appointment)
    {
        abort_unless($appointment->user_id === Auth::id(), 403);

        $appointment->load(['service', 'staff']);

        return view('booking.success', compact('appointment'));
    }

    /**
     * @return array<int, string> list of 'H:i' start times the staff is free for.
     */
    protected function availableSlotsForStaff(Staff $staff, Carbon $date, int $durationMinutes): array
    {
        $weekday = (int) $date->dayOfWeek;
        $workingHour = $staff->workingHours->firstWhere('weekday', $weekday);

        if (! $workingHour || $workingHour->is_off) {
            return [];
        }

        $open = $date->copy()->setTimeFromTimeString($workingHour->start_time);
        $close = $date->copy()->setTimeFromTimeString($workingHour->end_time);
        $earliest = now()->addMinutes(self::MIN_LEAD_MINUTES);

        $slots = [];
        $cursor = $open->copy();

        while ($cursor->copy()->addMinutes($durationMinutes)->lte($close)) {
            if ($cursor->gte($earliest)) {
                $slotEnd = $cursor->copy()->addMinutes($durationMinutes);
                if ($staff->isAvailable($cursor, $slotEnd)) {
                    $slots[] = $cursor->format('H:i');
                }
            }
            $cursor->addMinutes(self::SLOT_STEP_MINUTES);
        }

        return $slots;
    }
}
