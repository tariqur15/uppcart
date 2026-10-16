<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixOrderNumbers extends Command
{
    protected $signature = 'orders:fix-numbers';

    protected $description = 'Regenerate order numbers with random suffixes in ORD-DDMMYY-XXXXXX format';

    public function handle(): int
    {
        $used = [];
        $orders = DB::table('orders')->orderBy('id')->get();

        foreach ($orders as $order) {
            $date = Carbon::parse($order->created_at);
            $ddmmyy = $date->format('dmy');

            do {
                $random = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            } while (in_array($ddmmyy.'-'.$random, $used, true));

            $used[] = $ddmmyy.'-'.$random;
            $orderNumber = 'ORD-'.$ddmmyy.'-'.$random;

            DB::table('orders')->where('id', $order->id)->update(['order_number' => $orderNumber]);
            $this->line("Order #{$order->id} → {$orderNumber}");
        }

        $this->info('Done. '.count($orders).' orders updated.');

        return 0;
    }
}
