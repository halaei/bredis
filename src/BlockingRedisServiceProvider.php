<?php

namespace Halaei\BRedis;

use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class BlockingRedisServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot(QueueManager $manager)
    {
        $manager->addConnector('bredis', function() {
            return new BlockingRedisConnector($this->app['redis']);
        });
    }
}
