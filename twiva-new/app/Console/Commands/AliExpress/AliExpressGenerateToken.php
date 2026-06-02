<?php

namespace App\Console\Commands\AliExpress;

use Exception;
use Illuminate\Console\Command;
use Modules\AliExpress\Services\AliExpressService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AliExpressGenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ali-express:generate-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call refresh token api to generate new access token';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Start...');

        $logger = new Logger('ali_express');
        $logger->pushHandler(new StreamHandler(storage_path('logs/ali_express_refresh_token_' . now()->format('Y_m_d') . '.log'), Logger::DEBUG));
        $ali_express_service = new AliExpressService($logger);

        $this->info('Initialize Done.');

        try {
            $this->info('Start Get New Token');
            $ali_express_service->refreshToken();
            $this->info('Progress completed!');
        } catch (Exception $exception) {
            $this->error('Progress Fail!');
            $this->info("Ali Express account doesn't have an access token yet, please refer to " . route('ali.express.user.authorize'));
        }
    }
}
