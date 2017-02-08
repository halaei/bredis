<?php

use Halaei\BRedis\BlockingRedisQueue;
use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Arr;
use Mockery as m;
use Illuminate\Container\Container;

class BlockingRedisQueueTest extends PHPUnit_Framework_TestCase
{
    use InteractsWithRedis;

    public function setUp()
    {
        parent::setUp();

        $this->setUpRedis();
    }

    public function tearDown()
    {
        $this->tearDownRedis();
        m::close();

        parent::tearDown();
    }

    private function getQueue($timeout, $redis = null)
    {
        $queue = new BlockingRedisQueue($redis ?: $this->redis, 'default', null, 60, $timeout);
        $queue->setContainer(m::mock(Container::class));
        return $queue;
    }

    public function test_blocking_pop_nothing()
    {
        $queue = $this->getQueue(1);
        $this->assertNull($queue->pop());
    }

    public function test_blocking_pop_something()
    {
        $queue = $this->getQueue(0);
        $queue->push(new BlockingRedisQueueIntegrationTestJob(1));
        /** @var RedisJob $job */
        $job = $queue->pop();
        $this->assertInstanceOf(RedisJob::class, $job);
        $this->assertEquals(1, $this->redis->connection()->zcard('queues:default:reserved'));
        $this->assertEquals(1, Arr::get(json_decode($job->getReservedJob(), true), 'attempts'));
        $job->delete();
        $this->assertEquals(0, $this->redis->connection()->zcard('queues:default:reserved'));
        $this->assertEquals(0, $this->redis->connection()->zcard('queues:default:delayed'));
    }

    public function test_blocking_pop_waits()
    {
        $queue = $this->getQueue(0);
        if (!pcntl_fork()) {
            $host = getenv('REDIS_HOST') ?: '127.0.0.1';
            $port = getenv('REDIS_PORT') ?: 6379;
            $redis = new RedisManager('predis', [
                'cluster' => false,
                'default' => [
                    'host' => $host,
                    'port' => $port,
                    'database' => 5,
                    'timeout' => 0.5,
                ],
            ]);

            $queue = $this->getQueue(0, $redis);

            sleep(1);

            $queue->push(new BlockingRedisQueueIntegrationTestJob(1));
            die;
        }
        $job = $queue->pop();
        $this->assertInstanceOf(RedisJob::class, $job);
        $this->assertEquals(1, $this->redis->connection()->zcard('queues:default:reserved'));
        $job->delete();
        $this->assertEquals(0, $this->redis->connection()->zcard('queues:default:reserved'));
    }
}

class BlockingRedisQueueIntegrationTestJob
{
    public $i;

    public function __construct($i)
    {
        $this->i = $i;
    }

    public function handle()
    {
    }
}
