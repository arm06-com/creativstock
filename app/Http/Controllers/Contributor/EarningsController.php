<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\ContributorEarning;
use App\Models\ContributorWithdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->isContributor(), 403);

        $contributor = $request->user();
        $earningsQuery = ContributorEarning::query()
            ->with(['product' => fn ($query) => $query->withCount('downloads')])
            ->where('contributor_id', $contributor->id);

        $from = $request->date('from')?->startOfDay() ?? now()->startOfMonth();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();
        $earnings = (clone $earningsQuery)->whereBetween('created_at', [$from, $to])->latest()->get();
        $totalEarnings = (float) ContributorEarning::query()->where('contributor_id', $contributor->id)->sum('amount');
        $todayEarnings = (float) (clone $earningsQuery)->whereDate('created_at', today())->sum('amount');
        $rangeEarnings = (float) $earnings->sum('amount');
        $withdrawnAmount = (float) ContributorWithdrawal::query()
            ->where('contributor_id', $contributor->id)
            ->whereIn('status', [ContributorWithdrawal::STATUS_APPROVED, ContributorWithdrawal::STATUS_PAID])
            ->sum('amount');
        $reservedAmount = (float) ContributorWithdrawal::query()
            ->where('contributor_id', $contributor->id)
            ->whereIn('status', [ContributorWithdrawal::STATUS_PENDING, ContributorWithdrawal::STATUS_APPROVED, ContributorWithdrawal::STATUS_PAID])
            ->sum('amount');

        return view('contributor.earnings', [
            'earnings' => $earnings,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'todayEarnings' => $todayEarnings,
            'rangeEarnings' => $rangeEarnings,
            'totalEarnings' => $totalEarnings,
            'withdrawnAmount' => $withdrawnAmount,
            'availableBalance' => max($totalEarnings - $reservedAmount, 0),
            'withdrawals' => ContributorWithdrawal::query()->where('contributor_id', $contributor->id)->latest()->limit(10)->get(),
        ]);
    }
}
