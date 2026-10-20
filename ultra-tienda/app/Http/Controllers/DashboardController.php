<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Días de la semana cortos para el gráfico (índice 0 = domingo).
     */
    private const WEEKDAY_LABELS = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    /**
     * Muestra el panel administrativo con las métricas reales del negocio.
     */
    public function index()
    {
        $today = Carbon::now();
        $monthStart = $today->copy()->startOfMonth();
        $previousMonthStart = $today->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $today->copy()->subMonth()->endOfMonth();

        /* ------------------------------- Ventas -------------------------------- */

        $totalSales = (float) Order::query()
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $monthSales = (float) Order::query()
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $monthStart)
            ->sum('total');

        $previousMonthSales = (float) Order::query()
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->sum('total');

        /* ------------------------------- Pedidos ------------------------------- */

        $totalOrders = Order::query()->count();

        $monthOrders = Order::query()
            ->where('created_at', '>=', $monthStart)
            ->count();

        $previousMonthOrders = Order::query()
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->count();

        $pendingOrders = Order::query()->where('status', 'pending')->count();

        $ordersByStatus = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        /* ------------------------- Catálogo y usuarios ------------------------- */

        $activeProducts = Product::query()->where('is_active', true)->count();
        $outOfStockProducts = Product::query()->where('disponible', false)->count();

        $totalUsers = User::query()->count();
        $monthUsers = User::query()->where('created_at', '>=', $monthStart)->count();
        $previousMonthUsers = User::query()
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->count();

        /* ----------------------- Gráfico de los últimos 7 días ------------------ */

        $weeklyStart = $today->copy()->subDays(6)->startOfDay();

        $weeklyTotals = Order::query()
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $weeklyStart)
            ->get(['total', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->format('Y-m-d'))
            ->map(fn ($orders) => (float) $orders->sum('total'));

        $weeklySales = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($today, $weeklyTotals) {
                $day = $today->copy()->subDays($daysAgo)->startOfDay();

                return [
                    'label' => self::WEEKDAY_LABELS[$day->dayOfWeek],
                    'date' => $day->format('d/m/Y'),
                    'total' => $weeklyTotals->get($day->format('Y-m-d'), 0.0),
                ];
            })
            ->values();

        $weeklyTotal = (float) $weeklySales->sum('total');
        $weeklyPeak = (float) ($weeklySales->max('total') ?: 0);

        /* --------------------------- Listas destacadas -------------------------- */

        $topProducts = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('order_details.product_id')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->with('product.category')
            ->selectRaw(
                'order_details.product_id,'
                . ' SUM(order_details.quantity) as units_sold,'
                . ' SUM(order_details.quantity * order_details.price) as revenue'
            )
            ->get();

        $latestOrders = Order::query()
            ->withCount('details')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /* -------------------------- Comparativas mensuales ---------------------- */

        $salesTrend = $this->trendPercent($monthSales, $previousMonthSales);
        $ordersTrend = $this->trendPercent((float) $monthOrders, (float) $previousMonthOrders);
        $usersTrend = $this->trendPercent((float) $monthUsers, (float) $previousMonthUsers);

        return view('dashboard.index', [
            'totalSales' => $totalSales,
            'monthSales' => $monthSales,
            'salesTrend' => $salesTrend,
            'totalOrders' => $totalOrders,
            'monthOrders' => $monthOrders,
            'ordersTrend' => $ordersTrend,
            'pendingOrders' => $pendingOrders,
            'ordersByStatus' => $ordersByStatus,
            'activeProducts' => $activeProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'totalUsers' => $totalUsers,
            'monthUsers' => $monthUsers,
            'usersTrend' => $usersTrend,
            'weeklySales' => $weeklySales,
            'weeklyTotal' => $weeklyTotal,
            'weeklyPeak' => $weeklyPeak,
            'topProducts' => $topProducts,
            'latestOrders' => $latestOrders,
        ]);
    }

    /**
     * Variación porcentual entre el periodo actual y el anterior.
     */
    private function trendPercent(float $current, float $previous): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
