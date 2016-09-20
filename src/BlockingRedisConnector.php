<?php

namespace Halaei\BRedis;

use Illuminate\Queue\Connectors\RedisConnector;
use Illuminate\Support\Arr;

class BlockingRedisConnector extends RedisConnector
{
    public function connect(array $config)
    {
        return new BlockingRedisQueue(
            $this->redis, $config['queue'],
            Arr::get($config, 'connection', $this->connection),
            Arr::get($config, 'retry_after', 90),
            Arr::get($config, 'timeout', 10)
        );
    }
}
