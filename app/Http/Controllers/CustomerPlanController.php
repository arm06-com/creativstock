<?php

namespace App\Http\Controllers;

use App\Models\CustomerSubscription;
use App\Models\SubscriptionPlan;
use Http\Adapter\Guzzle7\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Paddle\SDK\Client;
use Paddle\SDK\Environment;
use Paddle\SDK\Options;

class CustomerPlanController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_id' => ['required', 'string', 'max:255'],
        ]);
        $user = $request->user();
        $customer = $user->customerAccount();

        abort_unless($user->isCustomer(), 403);

        $this->activateTransaction($validated['transaction_id'], $customer->id);

        return response()->json(['active' => true]);
    }

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $customer = $user->customerAccount();

        abort_unless($user->isCustomer(), 403);

        $transactionId = $request->string('_ptxn')->toString() ?: $request->string('transaction_id')->toString();

        if ($transactionId !== '' && ! str_contains($transactionId, '{')) {
            try {
                $this->activateTransaction($transactionId, $customer->id);
            } catch (\Throwable $exception) {
                Log::error('Unable to synchronize Paddle checkout transaction.', [
                    'transaction_id' => $transactionId,
                    'user_id' => $customer->id,
                    'exception' => $exception,
                ]);
            }
        }

        return view('customer.my-plan', [
            'activeSubscription' => $customer->customerSubscriptions()
                ->with('plan')
                ->where('status', 'active')
                ->latest()
                ->first(),
            'plans' => SubscriptionPlan::query()->orderBy('price')->get(),
        ]);
    }

    private function activateTransaction(string $transactionId, int $userId): void
    {
        $transaction = (new Client(
            config('paddle.api_key'),
            new Options(Environment::from(config('paddle.environment'))),
            GuzzleClient::createWithConfig(['verify' => config('paddle.verify_ssl')]),
        ))->transactions->get($transactionId);
        abort_unless(in_array($transaction->status->getValue(), ['paid', 'completed'], true), 422);

        $customData = $transaction->customData?->data ?? [];
        $plan = SubscriptionPlan::query()->find($customData['plan_id'] ?? null);

        abort_unless(($customData['user_id'] ?? null) == $userId && $plan, 403);

        DB::transaction(function () use ($transaction, $userId, $plan): void {
            CustomerSubscription::query()
                ->where('user_id', $userId)
                ->where('status', CustomerSubscription::STATUS_ACTIVE)
                ->update(['status' => CustomerSubscription::STATUS_CANCELED, 'ends_at' => now()]);

            CustomerSubscription::query()->updateOrCreate(
                ['paddle_transaction_id' => $transaction->id],
                [
                    'user_id' => $userId,
                    'subscription_plan_id' => $plan->id,
                    'paddle_subscription_id' => $transaction->subscriptionId,
                    'status' => CustomerSubscription::STATUS_ACTIVE,
                    'starts_at' => now(),
                    'ends_at' => null,
                ],
            );
        });
    }
}
