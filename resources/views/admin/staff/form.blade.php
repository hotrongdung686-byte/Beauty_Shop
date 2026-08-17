@php
    $assignedServiceIds = $staff->exists ? $staff->services->pluck('id')->all() : [];
    $weekdayLabels = \App\Models\WorkingHour::weekdayLabels();
@endphp
<x-admin-layout title="{{ $staff->exists ? 'Sửa nhân viên' : 'Thêm nhân viên' }}">
    <a href="{{ route('admin.staff.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Nhân viên / Thợ</a>

    <form action="{{ $staff->exists ? route('admin.staff.update', $staff) : route('admin.staff.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 max-w-3xl bg-white border border-gray-100 rounded-xl p-6 space-y-4">
        @csrf
        @if($staff->exists) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Họ tên</label>
                <input type="text" name="full_name" value="{{ old('full_name', $staff->full_name) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-2 block">Ảnh đại diện</label>
            @if($staff->avatar)
                <div class="mb-3 h-20 w-20 rounded-full overflow-hidden border border-gray-100">
                    <img src="{{ asset('storage/'.$staff->avatar) }}" class="w-full h-full object-cover">
                </div>
            @endif
            <input type="file" name="avatar" accept="image/*" class="text-sm">
            @if($staff->avatar)
                <label class="flex items-center gap-2 text-sm text-gray-600 mt-2">
                    <input type="checkbox" name="remove_avatar" value="1" class="rounded text-rose-600 focus:ring-rose-400">
                    Xóa ảnh hiện tại
                </label>
            @endif
        </div>

        <div>
            <label class="text-sm text-gray-600">Liên kết tài khoản đăng nhập (tuỳ chọn)</label>
            <select name="user_id" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <option value="">-- Không liên kết --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (int) old('user_id', $staff->user_id) === $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Giới thiệu ngắn</label>
            <textarea name="bio" rows="3" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">{{ old('bio', $staff->bio) }}</textarea>
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-2 block">Dịch vụ đảm nhận</label>
            <div class="flex flex-wrap gap-3">
                @forelse($services as $service)
                    <label class="flex items-center gap-2 text-sm bg-gray-50 rounded-lg px-3 py-1.5">
                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" {{ in_array($service->id, old('service_ids', $assignedServiceIds)) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
                        {{ $service->name }}
                    </label>
                @empty
                    <p class="text-sm text-gray-400">Chưa có dịch vụ nào.</p>
                @endforelse
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-2 block">Giờ làm việc</label>
            <div class="space-y-2">
                @foreach($weekdayLabels as $weekday => $label)
                    @php $wh = $workingHours->get($weekday); @endphp
                    <div class="flex items-center gap-3 text-sm">
                        <span class="w-16 text-gray-600">{{ $label }}</span>
                        <input type="time" name="working_hours[{{ $weekday }}][start_time]" value="{{ old("working_hours.$weekday.start_time", $wh->start_time ?? '08:30') }}" class="rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                        <span>-</span>
                        <input type="time" name="working_hours[{{ $weekday }}][end_time]" value="{{ old("working_hours.$weekday.end_time", $wh->end_time ?? '17:30') }}" class="rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                        <label class="flex items-center gap-1 text-gray-500">
                            <input type="checkbox" name="working_hours[{{ $weekday }}][is_off]" value="1" {{ old("working_hours.$weekday.is_off", $wh->is_off ?? false) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
                            Nghỉ
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $staff->exists ? $staff->is_active : true) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
            Đang làm việc
        </label>

        <button class="bg-rose-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Lưu nhân viên</button>
    </form>
</x-admin-layout>
