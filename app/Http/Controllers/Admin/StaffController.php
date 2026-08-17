<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use App\Models\WorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::withCount('appointments')->orderBy('full_name')->paginate(15);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.form', [
            'staff' => new Staff,
            'services' => Service::orderBy('name')->get(),
            'users' => User::whereIn('role', [User::ROLE_STAFF, User::ROLE_MANAGER])->orderBy('name')->get(),
            'workingHours' => collect(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('staff', 'public');
        }

        $staff = Staff::create($data);
        $staff->services()->sync($request->input('service_ids', []));
        $this->syncWorkingHours($request, $staff);

        return redirect()->route('admin.staff.edit', $staff)->with('success', 'Đã thêm nhân viên/thợ.');
    }

    public function edit(Staff $staff)
    {
        $staff->load(['services', 'workingHours']);

        return view('admin.staff.form', [
            'staff' => $staff,
            'services' => Service::orderBy('name')->get(),
            'users' => User::whereIn('role', [User::ROLE_STAFF, User::ROLE_MANAGER])->orderBy('name')->get(),
            'workingHours' => $staff->workingHours->keyBy('weekday'),
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            if ($staff->avatar) {
                Storage::disk('public')->delete($staff->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('staff', 'public');
        } elseif ($request->boolean('remove_avatar') && $staff->avatar) {
            Storage::disk('public')->delete($staff->avatar);
            $data['avatar'] = null;
        }

        $staff->update($data);
        $staff->services()->sync($request->input('service_ids', []));
        $this->syncWorkingHours($request, $staff);

        return redirect()->route('admin.staff.edit', $staff)->with('success', 'Đã cập nhật thông tin.');
    }

    public function destroy(Staff $staff)
    {
        if ($staff->appointments()->exists()) {
            return back()->with('error', 'Không thể xóa nhân viên đã có lịch hẹn.');
        }

        if ($staff->avatar) {
            Storage::disk('public')->delete($staff->avatar);
        }

        $staff->services()->detach();
        $staff->workingHours()->delete();
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Đã xóa nhân viên/thợ.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    protected function syncWorkingHours(Request $request, Staff $staff): void
    {
        foreach (range(0, 6) as $weekday) {
            $isOff = $request->boolean("working_hours.{$weekday}.is_off");
            $start = $request->input("working_hours.{$weekday}.start_time", '08:30');
            $end = $request->input("working_hours.{$weekday}.end_time", '17:30');

            WorkingHour::updateOrCreate(
                ['staff_id' => $staff->id, 'weekday' => $weekday],
                ['start_time' => $start, 'end_time' => $end, 'is_off' => $isOff]
            );
        }
    }
}
