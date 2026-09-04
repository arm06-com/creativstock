<?php

namespace App\Livewire\Admin;

use App\Models\CommissionSetting;
use App\Models\ContributorEarning;
use Livewire\Component;

class CommissionShare extends Component
{
    public string $percentage = '50.00';

    public string $minimumPayout = '20.00';

    public array $paymentMethods = [];

    public function mount(): void
    {
        $this->percentage = (string) CommissionSetting::current()->percentage;
        $setting = CommissionSetting::current();
        $this->minimumPayout = (string) $setting->minimum_payout;
        $this->paymentMethods = $setting->payment_methods ?? ['paypal', 'bank_transfer'];
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $this->validate([
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'minimumPayout' => ['required', 'numeric', 'min:0.01'],
            'paymentMethods' => ['array', 'min:1'],
            'paymentMethods.*' => ['in:paypal,bank_transfer'],
        ]);

        CommissionSetting::current()->update([
            'percentage' => $this->percentage,
            'minimum_payout' => $this->minimumPayout,
            'payment_methods' => array_values($this->paymentMethods),
        ]);
        session()->flash('status', __('Commission and payout settings updated successfully.'));
    }

    public function render(): mixed
    {
        abort_unless(auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin(), 403);

        return view('livewire.admin.commission-share', [
            'contributorTotals' => ContributorEarning::query()
                ->selectRaw('contributor_id, COUNT(*) as download_count, SUM(amount) as total_earnings')
                ->with('contributor')
                ->groupBy('contributor_id')
                ->orderByDesc('total_earnings')
                ->get(),
        ]);
    }
}
