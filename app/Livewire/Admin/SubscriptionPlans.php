<?php

namespace App\Livewire\Admin;

use App\Models\SubscriptionPlan;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SubscriptionPlans extends Component
{
    public bool $showPlanModal = false;

    public ?int $editingPlanId = null;

    public string $name = '';

    public string $price = '';

    public string $downloadLimit = '';

    public string $userLimit = '';

    public string $features = '';

    public string $priceId = '';

    public bool $isPopular = false;

    public function createPlan(): void
    {
        $this->resetEditor();
        $this->showPlanModal = true;
    }

    public function editPlan(int $planId): void
    {
        $plan = SubscriptionPlan::findOrFail($planId);

        $this->editingPlanId = $plan->id;
        $this->name = $plan->name;
        $this->price = (string) $plan->price;
        $this->downloadLimit = $plan->download_limit === null ? '' : (string) $plan->download_limit;
        $this->userLimit = $plan->user_limit === null ? '' : (string) $plan->user_limit;
        $this->features = implode(PHP_EOL, $plan->features ?? []);
        $this->priceId = $plan->price_id;
        $this->isPopular = $plan->is_popular;
        $this->showPlanModal = true;
    }

    public function savePlan(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'downloadLimit' => ['nullable', 'integer', 'min:1'],
            'userLimit' => ['nullable', 'integer', 'min:1'],
            'features' => ['nullable', 'string', 'max:2000'],
            'priceId' => ['required', 'string', 'max:255', Rule::unique('subscription_plans', 'price_id')->ignore($this->editingPlanId)],
            'isPopular' => ['boolean'],
        ]);

        $plan = SubscriptionPlan::updateOrCreate(
            ['id' => $this->editingPlanId],
            [
                'name' => $this->name,
                'price' => $this->price,
                'download_limit' => $this->downloadLimit === '' ? null : $this->downloadLimit,
                'user_limit' => $this->userLimit === '' ? null : $this->userLimit,
                'features' => collect(preg_split('/\r\n|\r|\n/', $this->features) ?: [])
                    ->map(fn (string $feature): string => trim($feature))
                    ->filter()
                    ->values()
                    ->all(),
                'price_id' => $this->priceId,
                'is_popular' => $this->isPopular,
            ],
        );

        if ($plan->is_popular) {
            SubscriptionPlan::query()
                ->where('id', '!=', $plan->id)
                ->update(['is_popular' => false]);
        }

        $this->closePlanModal();
    }

    public function deletePlan(int $planId): void
    {
        SubscriptionPlan::findOrFail($planId)->delete();
    }

    public function closePlanModal(): void
    {
        $this->resetEditor();
        $this->showPlanModal = false;
        $this->resetValidation();
    }

    private function resetEditor(): void
    {
        $this->reset(['editingPlanId', 'name', 'price', 'downloadLimit', 'userLimit', 'features', 'priceId', 'isPopular']);
    }

    public function render(): mixed
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        return view('livewire.admin.subscription-plans', [
            'plans' => SubscriptionPlan::query()->orderByDesc('is_popular')->orderBy('price')->get(),
        ]);
    }
}
