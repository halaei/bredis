<?php

namespace Halaei\BRedis\Horizon;

use Illuminate\Queue\QueueManager;

class HorizonServiceProvider extends \Laravel\Horizon\HorizonServiceProvider
{
    /**
     * Register the custom queue connectors for Horizon.
     *
     * @return void
     */
    protected function registerQueueConnectors()
    {
        $this->app->resolving(QueueManager::class, function ($manager) {
            $manager->addConnector('bredis', function () {
                return new HorizonBlockingRedisConnector($this->app['redis']);
            });
        });
    }

}
