<div>
    {{-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci --}}
<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-[#087f85] dark:text-[#8fe3e2]">{{ __('Super Admin') }}</p>
            <h1 class="text-2xl font-semibold text-zinc-950 dark:text-white">{{ __('Subscription Plans') }}</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Create and manage the plans available to your customers.') }}</p>
        </div>
        <flux:button wire:click="createPlan" icon="plus" variant="primary">{{ __('Create plan') }}</flux:button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($plans as $plan)
            <article wire:key="subscription-plan-{{ $plan->id }}" class="flex flex-col gap-5 rounded-xl border {{ $plan->is_popular ? 'border-[#0fa3ad] ring-2 ring-[#0fa3ad]/20' : 'border-zinc-200 dark:border-zinc-800' }} bg-white p-5 shadow-sm dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ $plan->name }}</h2>
                        @if ($plan->is_popular)<flux:badge color="teal">{{ __('Popular') }}</flux:badge>@endif
                    </div>
                    <span class="text-2xl font-semibold text-zinc-950 dark:text-white">{{ number_format((float) $plan->price, 2) }}</span>
                </div>
                <dl class="grid gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <div class="flex justify-between gap-4"><dt>{{ __('Downloads') }}</dt><dd class="font-medium">{{ $plan->download_limit ?? __('Unlimited') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>{{ __('Users') }}</dt><dd class="font-medium">{{ $plan->user_limit ?? __('Unlimited') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>{{ __('Price ID') }}</dt><dd class="max-w-40 truncate font-mono text-xs" title="{{ $plan->price_id }}">{{ $plan->price_id }}</dd></div>
                </dl>
                <ul class="flex flex-1 flex-col gap-2 border-t border-zinc-200 pt-4 text-sm text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                    @forelse ($plan->features ?? [] as $feature)<li class="flex gap-2"><flux:icon name="check" variant="mini" class="mt-0.5 text-[#0fa3ad]" />{{ $feature }}</li>@empty<li>{{ __('No features listed.') }}</li>@endforelse
                </ul>
                <div class="flex justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <flux:button wire:click="editPlan({{ $plan->id }})" icon="pencil" variant="ghost">{{ __('Edit') }}</flux:button>
                    <flux:button wire:click="deletePlan({{ $plan->id }})" wire:confirm="{{ __('Delete this subscription plan?') }}" icon="trash" variant="danger">{{ __('Delete') }}</flux:button>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-zinc-300 p-8 text-center text-sm text-zinc-500 dark:border-zinc-700">{{ __('No subscription plans have been created yet.') }}</div>
        @endforelse
    </div>

    <flux:modal wire:model="showPlanModal" class="w-full max-w-xl" @close="closePlanModal">
        <form wire:submit="savePlan" class="flex flex-col gap-5">
            <flux:heading size="lg">{{ $editingPlanId ? __('Edit subscription plan') : __('Create subscription plan') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field><flux:label>{{ __('Plan name') }}</flux:label><flux:input wire:model="name" /><flux:error name="name" /></flux:field>
                <flux:field><flux:label>{{ __('Plan price') }}</flux:label><flux:input type="number" min="0" step="0.01" wire:model="price" /><flux:error name="price" /></flux:field>
                <flux:field><flux:label>{{ __('Download limit') }}</flux:label><flux:input type="number" min="1" wire:model="downloadLimit" placeholder="{{ __('Unlimited') }}" /><flux:error name="downloadLimit" /></flux:field>
                <flux:field><flux:label>{{ __('User limit') }}</flux:label><flux:input type="number" min="1" wire:model="userLimit" placeholder="{{ __('Unlimited') }}" /><flux:error name="userLimit" /></flux:field>
            </div>
            <flux:field><flux:label>{{ __('Features') }}</flux:label><flux:textarea wire:model="features" rows="4" placeholder="{{ __('One feature per line') }}" /><flux:error name="features" /></flux:field>
            <flux:field><flux:label>{{ __('Paddle price ID') }}</flux:label><flux:input wire:model="priceId" placeholder="pri_..." /><flux:error name="priceId" /></flux:field>
            <flux:checkbox wire:model="isPopular" label="{{ __('Mark as popular plan') }}" />
            <div class="flex justify-end gap-3"><flux:button type="button" wire:click="closePlanModal" variant="ghost">{{ __('Cancel') }}</flux:button><flux:button type="submit" variant="primary">{{ $editingPlanId ? __('Update plan') : __('Create plan') }}</flux:button></div>
        </form>
    </flux:modal>
</div>
