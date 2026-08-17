<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('total');

        $monthRevenue = Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('total');

        $ordersPending = Order::where('status', Order::STATUS_PENDING)->count();
        $appointmentsToday = Appointment::whereDate('start_at', today())
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED, Appointment::STATUS_NO_SHOW])
            ->count();

        $lowStock = ProductVariant::where('stock_quantity', '<=', 5)->with('product')->orderBy('stock_quantity')->take(6)->get();
        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $upcomingAppointments = Appointment::with(['service', 'staff', 'user'])
            ->where('start_at', '>=', now())
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED, Appointment::STATUS_NO_SHOW])
            ->orderBy('start_at')
            ->take(6)
            ->get();

        $productCount = Product::count();
        $customersToday = User::where('role', User::ROLE_CUSTOMER)->whereDate('created_at', today())->count();

        // Revenue trend for the last 6 months (for the line chart).
        $monthlyRevenue = collect(range(5, 0))->map(function (int $i) {
            $month = now()->subMonths($i);
            $total = (float) Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->sum('total');

            return ['label' => 'Th'.$month->format('n'), 'total' => $total];
        });

        // Revenue for the last 7 days (for the bar chart).
        $weeklyRevenue = collect(range(6, 0))->map(function (int $i) {
            $day = now()->subDays($i);
            $total = (float) Order::whereDate('created_at', $day->toDateString())
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->sum('total');

            return ['label' => $day->format('d'), 'total' => $total];
        });

        // Order status breakdown for the donut chart.
        $orderStatusCounts = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', compact(
            'todayRevenue', 'monthRevenue', 'ordersPending', 'appointmentsToday',
            'lowStock', 'recentOrders', 'upcomingAppointments', 'productCount',
            'customersToday', 'monthlyRevenue', 'weeklyRevenue', 'orderStatusCounts'
        ));
    }
}
