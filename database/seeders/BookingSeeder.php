<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\User;
use App\Models\WorkingHour;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $serviceCategories = collect([
            'Phun xăm thẩm mỹ', 'Nối mi', 'Chăm sóc tóc & gội đầu', 'Chăm sóc da mặt',
        ])->mapWithKeys(function (string $name) {
            $cat = ServiceCategory::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'is_active' => true]
            );

            return [$name => $cat];
        });

        $services = [
            ['name' => 'Xăm môi', 'category' => 'Phun xăm thẩm mỹ', 'price' => 2500000, 'duration_minutes' => 120, 'deposit_amount' => 500000, 'is_featured' => true],
            ['name' => 'Xăm mày', 'category' => 'Phun xăm thẩm mỹ', 'price' => 2000000, 'duration_minutes' => 90, 'deposit_amount' => 400000, 'is_featured' => true],
            ['name' => 'Nối mi classic', 'category' => 'Nối mi', 'price' => 350000, 'duration_minutes' => 90, 'deposit_amount' => 100000, 'is_featured' => false],
            ['name' => 'Uốn mi', 'category' => 'Nối mi', 'price' => 200000, 'duration_minutes' => 60, 'deposit_amount' => 0, 'is_featured' => false],
            ['name' => 'Gội đầu dưỡng sinh', 'category' => 'Chăm sóc tóc & gội đầu', 'price' => 150000, 'duration_minutes' => 45, 'deposit_amount' => 0, 'is_featured' => true],
            ['name' => 'Chăm sóc da mặt chuyên sâu', 'category' => 'Chăm sóc da mặt', 'price' => 450000, 'duration_minutes' => 75, 'deposit_amount' => 100000, 'is_featured' => false],
        ];

        $serviceModels = collect();

        foreach ($services as $data) {
            $service = Service::firstOrCreate(
                ['name' => $data['name']],
                [
                    'slug' => Str::slug($data['name']),
                    'service_category_id' => $serviceCategories[$data['category']]->id,
                    'description' => $data['name'].' - dịch vụ làm đẹp chuyên nghiệp.',
                    'price' => $data['price'],
                    'duration_minutes' => $data['duration_minutes'],
                    'deposit_amount' => $data['deposit_amount'],
                    'is_active' => true,
                    'is_featured' => $data['is_featured'],
                ]
            );

            $serviceModels->put($data['name'], $service);
        }

        $staffMembers = [
            ['full_name' => 'Thợ Xăm Lan', 'email' => 'lan@beautyshop.vn', 'phone' => '0900000002', 'bio' => 'Chuyên gia phun xăm 8 năm kinh nghiệm', 'services' => ['Xăm môi', 'Xăm mày']],
            ['full_name' => 'Thợ Mi Hương', 'email' => null, 'phone' => '0900000005', 'bio' => 'Chuyên nối mi, uốn mi', 'services' => ['Nối mi classic', 'Uốn mi']],
            ['full_name' => 'Thợ Tóc Bình', 'email' => null, 'phone' => '0900000006', 'bio' => 'Chuyên gội đầu dưỡng sinh, chăm sóc da', 'services' => ['Gội đầu dưỡng sinh', 'Chăm sóc da mặt chuyên sâu']],
        ];

        foreach ($staffMembers as $data) {
            $userId = $data['email'] ? User::where('email', $data['email'])->value('id') : null;

            $staff = Staff::firstOrCreate(
                ['full_name' => $data['full_name']],
                [
                    'user_id' => $userId,
                    'phone' => $data['phone'],
                    'bio' => $data['bio'],
                    'is_active' => true,
                ]
            );

            $serviceIds = collect($data['services'])->map(fn ($name) => $serviceModels[$name]->id)->all();
            $staff->services()->syncWithoutDetaching($serviceIds);

            // Mon–Sat 08:30–17:30, Sunday off.
            for ($weekday = 0; $weekday <= 6; $weekday++) {
                WorkingHour::firstOrCreate(
                    ['staff_id' => $staff->id, 'weekday' => $weekday],
                    [
                        'start_time' => '08:30:00',
                        'end_time' => '17:30:00',
                        'is_off' => $weekday === 0,
                    ]
                );
            }
        }
    }
}
