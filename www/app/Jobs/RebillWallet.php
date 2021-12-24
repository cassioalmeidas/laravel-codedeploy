<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Traits\Functions;
use App\Models\Subscriptions;

class RebillWallet implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Functions;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $subscription = Subscriptions::where('ends_at', '<', now())
      ->whereRebillWallet('on')
      ->whereCancelled('no')
      ->get();

      if ($subscription) {

        foreach ($subscription as $key) {

          // Get price of creator
          $amount = $key->subscribed()->price;

          if ($key->user()->wallet >= $amount && $key->subscribed()->free_subscription == 'no') {

            // Admin and user earnings calculation
            $earnings = $this->earningsAdminUser($key->subscribed()->custom_fee, $amount, null, null);

            // Insert Transaction
            // $txnId, $userId, $subscriptionsId, $subscribed, $amount, $userEarning, $adminEarning, $paymentGateway, $type, $percentageApplied
            $this->transaction('subw_'.str_random(25), $key->user()->id, $key->id, $key->subscribed()->id, $amount, $earnings['user'], $earnings['admin'], 'Wallet', 'subscription', $earnings['percentageApplied']);

            // Subtract user funds
            $key->user()->decrement('wallet', $amount);

            // Add Earnings to User
            $key->subscribed()->increment('balance', $earnings['user']);

            $key->update([
      						'ends_at' => now()->addMonth(),
      					]);
          }
        }
      }

    }
}
