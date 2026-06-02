<?php

namespace App\Console\Commands;

use Apxcde\OnfonSms\OnfonChannel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\Buyer\Entities\Buyer;
use Modules\Notification\Notifications\Buyer\CartReminderNotification;
use Modules\Products\Services\LLMCaptionService;

class SendCartReminderCommand extends Command {
    protected $signature = 'reminders:send-cart';
    protected $description = 'Send reminders to buyers with incomplete carts';

    public function handle()
    {
        $buyersWithCarts = Buyer::whereNotNull('cart')
            ->where('cart_last_updated_date', '<', now()->subDays(7)->toDateTimeString())
            ->get();
        $this->generateEmaileMesseage();
        foreach ($buyersWithCarts as $buyer)
        {
            $buyer->notify(new CartReminderNotification());
            $buyer->update(
                [
                    'cart_last_updated_date' => null
                ]
            );
        }

        $this->info('Cart reminders sent successfully!');
    }

    public function generateEmaileMesseage()
    {
        $LLMCaptionService = new LLMCaptionService();
        $LLMCaptionService->setMessage2('Write a professional and engaging message to remind the customer to complete their purchase. The message should be modern and include a motivational element.');
        $LLMCaptionService->askToChatGpt();
        $LLMCaptionService->getAnswer();
    }
}
