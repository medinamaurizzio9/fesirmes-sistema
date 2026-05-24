<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateStatus;
use App\Models\Activity;
use App\Models\Affiliate;
use App\Models\Audit;
use App\Models\Attendance;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statusCounts = collect(AffiliateStatus::cases())
            ->mapWithKeys(fn (AffiliateStatus $status) => [
                $status->value => Affiliate::where('status', $status->value)->count(),
            ]);

        return view('dashboard', [
            'totalAffiliates' => Affiliate::count(),
            'statusCounts' => $statusCounts,
            'latestAudits' => Audit::with('user')->latest()->limit(5)->get(),
            'activitiesDone' => Activity::where('estado', 'realizada')->count(),
            'latestActivity' => Activity::latest('fecha')->first(),
            'averageAttendance' => $this->averageAttendance(),
            'lowAttendanceCount' => $this->lowAttendanceCount(),
        ]);
    }

    private function averageAttendance(): float
    {
        $done = Activity::where('estado', 'realizada')->count();
        $activeAffiliates = Affiliate::where('status', 'activo')->count();

        if ($done === 0 || $activeAffiliates === 0) {
            return 0;
        }

        $valid = Attendance::where('estado', 'valido')
            ->whereNull('reverted_at')
            ->whereHas('activity', fn ($query) => $query->where('estado', 'realizada'))
            ->count();

        return round(($valid / ($done * $activeAffiliates)) * 100, 2);
    }

    private function lowAttendanceCount(): int
    {
        $done = Activity::where('estado', 'realizada')->count();

        if ($done === 0) {
            return 0;
        }

        $validCounts = Attendance::where('estado', 'valido')
            ->whereNull('reverted_at')
            ->whereHas('activity', fn ($query) => $query->where('estado', 'realizada'))
            ->selectRaw('affiliate_id, COUNT(*) as total')
            ->groupBy('affiliate_id')
            ->pluck('total', 'affiliate_id');

        return Affiliate::where('status', 'activo')->get()
            ->filter(fn (Affiliate $affiliate) => (($validCounts[$affiliate->id] ?? 0) / $done) < 0.5)
            ->count();
    }
}
