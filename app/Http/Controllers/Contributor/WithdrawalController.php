<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\ContributorPaymentMethod;
use App\Models\ContributorEarning;
use App\Models\ContributorWithdrawal;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function create(Request $request): View
    {
        abort_unless($request->user()->isContributor(), 403);

        return view('contributor.withdraw', [
            'paymentMethods' => $request->user()->paymentMethods()->latest()->get(),
            'availableBalance' => $this->availableBalance($request->user()->id),
            'payoutSettings' => CommissionSetting::current(),
        ]);
    }

    public function savePaymentMethod(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isContributor(), 403);

        $settings = CommissionSetting::current();
        $validated = $request->validate([
            'method' => ['required', 'in:'.implode(',', $settings->payment_methods ?? ['paypal', 'bank_transfer'])],
            'account_name' => ['required', 'string', 'max:255'],
            'account_details' => ['required', 'string', 'max:1000'],
        ]);

        $request->user()->paymentMethods()->update(['is_default' => false]);
        $request->user()->paymentMethods()->create($validated + ['is_default' => true]);

        return back()->with('status', __('Payment method saved successfully.'));
    }

    public function store(Request $request): RedirectResponse
    {
        $contributor = $request->user();
        abort_unless($contributor->isContributor(), 403);

        $validated = $request->validate(['amount' => ['required', 'numeric', 'min:0.01']]);
        $settings = CommissionSetting::current();
        $availableBalance = $this->availableBalance($contributor->id);
        $paymentMethod = $contributor->paymentMethods()->where('is_default', true)->first();

        abort_unless($paymentMethod, 422, __('Set up a payment method before requesting a withdrawal.'));
        abort_unless((float) $validated['amount'] >= (float) $settings->minimum_payout, 422, __('The minimum withdrawal amount is :amount.', ['amount' => number_format((float) $settings->minimum_payout, 2)]));
        abort_unless((float) $validated['amount'] <= $availableBalance, 422, __('The withdrawal amount exceeds your available balance.'));

        ContributorWithdrawal::create([
            'contributor_id' => $contributor->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => $validated['amount'],
            'status' => ContributorWithdrawal::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        return redirect()->route('contributor.earnings')->with('status', __('Withdrawal request submitted successfully.'));
    }

    private function availableBalance(int $contributorId): float
    {
        $total = (float) ContributorEarning::query()->where('contributor_id', $contributorId)->sum('amount');
        $reserved = (float) ContributorWithdrawal::query()->where('contributor_id', $contributorId)->whereIn('status', [ContributorWithdrawal::STATUS_PENDING, ContributorWithdrawal::STATUS_APPROVED, ContributorWithdrawal::STATUS_PAID])->sum('amount');

        return max($total - $reserved, 0);
    }
}
