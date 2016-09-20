# bredis queue driver for Laravel

[![Build Status](https://travis-ci.org/halaei/bredis.svg)](https://travis-ci.org/halaei/bredis)
[![Latest Stable Version](https://poser.pugx.org/halaei/bredis/v/stable)](https://packagist.org/packages/halaei/bredis)
[![Total Downloads](https://poser.pugx.org/halaei/bredis/downloads)](https://packagist.org/packages/halaei/bredis)
[![Latest Unstable Version](https://poser.pugx.org/halaei/bredis/v/unstable)](https://packagist.org/packages/halaei/bredis)
[![License](https://poser.pugx.org/halaei/bredis/license)](https://packagist.org/packages/halaei/bredis)

## When do you need bredis?

You need `bredis` when all of these are applied:

1. **You don't want your jobs to be delayed because your workers are currently sleeping.**
2. You don't want to run `queue:work --sleep=0` on current Redis queue driver because it will devour your CPU when there is no job.

Hence, you need your workers to idle-wait for a job and process them just when they arrive, with nearly no delay.
With `bredis` you can happily run `queue:work --sleep=0` without worrying about busy waiting and CPU overload.

## Installation

### 1. Install the package via compioser

    composer require halaei/bredis
    
### 2. Add the service provider to your config/app.php

    Halaei\BRedis\BlockingRedisServiceProvider::class

### 3. Add bredis connections to app/queue.php

    'bredis' => [
        'driver'      => 'bredis',
        'connection'  => 'default',
        'queue'       => 'default',
        'retry_after' => 90,
        'timeout'     => 10, //Maximum seconds to wait for a job
    ],

Please note that if you need to increase 'timeout' in the config array above, you should increase 'retry_after' in the array as well as --timeout in your `queue:work` commands.

**Warning**: bredis queue workers don't bother handling jobs that are delayed or reserved. So when using bredis workers, you have to have at least one redis worker as well.
**Note**: bredis queue driver is 100% compatile with redis driver. In other words, you may push the jobs using redis driver and pop them using bredis.