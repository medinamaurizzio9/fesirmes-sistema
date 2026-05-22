<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\Audit;
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
        ]);
    }
}
