<?php

namespace App\Http\Controllers;

use App\Models\CustomerSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Paddle\SDK\Entities\Event;
use Paddle\SDK\Notifications\Secret;
use Paddle\SDK\Notifications\Verifier;

class PaddleWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        // Build a PSR-7 request from the actual incoming PHP request.
        $request = ServerRequest::fromGlobals();

        // Get our Paddle notification destination secret.
        $secret = new Secret(
            config('paddle.webhook_secret')
        );

        // Verify the Paddle signature.
        $isValid = (new Verifier)->verify(
            $request,
            $secret
        );

        if (! $isValid) {
            Log::warning('Invalid Paddle webhook signature.');

            return response('Invalid signature', 400);
        }

        // Signature is valid, so we can safely read the event.
        $event = Event::fromRequest($request);

        Log::info('Verified Paddle webhook', [
            'event_id' => $event->eventId,
            'event_type' => $event->eventType,
        ]);

        if (in_array($event->eventType->getValue(), ['transaction.paid', 'transaction.completed'], true)) {
            $transaction = $event->data;
            $customData = $transaction->customData?->data ?? [];
            $user = User::query()->find($customData['user_id'] ?? null);
            $plan = SubscriptionPlan::query()->find($customData['plan_id'] ?? null);

            if ($user?->isCustomer() && $plan) {
                DB::transaction(function () use ($transaction, $user, $plan): void {
                    CustomerSubscription::query()
                        ->where('user_id', $user->id)
                        ->where('status', CustomerSubscription::STATUS_ACTIVE)
                        ->update(['status' => CustomerSubscription::STATUS_CANCELED, 'ends_at' => now()]);

                    CustomerSubscription::query()->updateOrCreate(
                        ['paddle_transaction_id' => $transaction->id],
                        [
                            'user_id' => $user->id,
                            'subscription_plan_id' => $plan->id,
                            'paddle_subscription_id' => $transaction->subscriptionId,
                            'status' => CustomerSubscription::STATUS_ACTIVE,
                            'starts_at' => now(),
                            'ends_at' => null,
                        ],
                    );

                    Log::info('Customer plan activated from Paddle transaction.', [
                        'event_id' => $event->eventId,
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                    ]);
                });
            } else {
                Log::warning('Paddle transaction missing customer plan metadata.', [
                    'event_id' => $event->eventId,
                    'transaction_id' => $transaction->id,
                    'custom_data' => $customData,
                ]);
            }

            Log::info('Paddle transaction completed.', [
                'event_id' => $event->eventId,
            ]);

            // We will process the payment here later.
        }

        return response('OK', 200);
    }
}
