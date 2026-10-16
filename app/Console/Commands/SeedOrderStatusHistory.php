<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SeedOrderStatusHistory extends Command
{
    protected $signature = 'seed:order-status-history';

    protected $description = 'Seed existing orders with full status history';

    private const STEP_ORDER = ['pending', 'confirmed', 'processing', 'shipped', 'completed', 'delivered'];

    public function handle(): int
    {
        $orders = DB::table('orders')->get();
        $now = now();
        $count = 0;

        foreach ($orders as $order) {
            DB::table('order_status_histories')->where('order_id', $order->id)->delete();

            $statusToSeed = $order->status === 'delivered' ? 'completed' : $order->status;
            $targetIndex = array_search($statusToSeed, self::STEP_ORDER);

            if ($targetIndex === false) {
                DB::table('order_status_histories')->insert([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'changed_at' => $order->created_at,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $count++;

                continue;
            }

            $steps = array_slice(self::STEP_ORDER, 0, $targetIndex + 1);
            $createdAt = Carbon::parse($order->created_at);
            $updatedAt = Carbon::parse($order->updated_at);
            $totalSeconds = $createdAt->diffInSeconds($updatedAt);
            $interval = count($steps) > 1 ? intdiv($totalSeconds, count($steps)) : 0;

            foreach ($steps as $i => $step) {
                $changedAt = $createdAt->copy()->addSeconds($interval * $i);
                DB::table('order_status_histories')->insert([
                    'order_id' => $order->id,
                    'status' => $step,
                    'changed_at' => $changedAt,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $count++;
            }
        }

        $this->info("Seeded {$count} status history records for ".$orders->count().' orders.');

        return self::SUCCESS;
    }
}
