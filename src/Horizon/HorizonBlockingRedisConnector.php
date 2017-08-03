<?php

namespace Halaei\BRedis\Horizon;

use Illuminate\Queue\Connectors\RedisConnector;
use Illuminate\Support\Arr;

class HorizonBlockingRedisConnector extends RedisConnector
{
    public function connect(array $config)
    {
        return new HorizonBlockingRedisQueue(
            $this->redis, $config['queue'],
            Arr::get($config, 'connection', $this->connection),
            Arr::get($config, 'retry_after', 90),
            Arr::get($config, 'timeout', 10)
        );
    }
}
