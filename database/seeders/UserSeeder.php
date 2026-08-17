<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Ensure a known set of test accounts exist with a known password
     * (`password`), so the app can be logged into right after cloning.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Quản lý Shop',
                'email' => 'admin@beautyshop.vn',
                'role' => User::ROLE_MANAGER,
                'phone' => '0900000001',
            ],
            [
                'name' => 'Thợ Xăm Lan',
                'email' => 'lan@beautyshop.vn',
                'role' => User::ROLE_STAFF,
                'phone' => '0900000002',
            ],
            [
                'name' => 'Nguyễn Thị Khách',
                'email' => 'khach@example.com',
                'role' => User::ROLE_CUSTOMER,
                'phone' => '0900000003',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'phone' => $account['phone'],
                    'role' => $account['role'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
