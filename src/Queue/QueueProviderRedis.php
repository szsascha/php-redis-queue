<?php

namespace Queue;

use Exception;
use Redis;

class QueueProviderRedis implements IQueueProvider {

    private const REDIS_HOST = 'php-redis-queue_redis_1';

    private const REDIS_PORT = 6379;

    private const REDIS_CONNECTION_RETRIES = 5;

    private const REDIS_CONNECTION_RETRY_INTERVAL = 30;

    private Redis $redis;

    public function __construct() {
        $this->redis = $this->initRedisDriver();
    }

    private function initRedisDriver(): object {
        if (!extension_loaded('redis')) {
            throw new Exception('Redis PHP Extension not loaded!');
        }

        $redis = new \Redis(); 
        $connected = false;
        $try = 0;

        do {
            if ($try > 0) sleep(self::REDIS_CONNECTION_RETRY_INTERVAL); 

            $connectresult = $redis->connect(self::REDIS_HOST, self::REDIS_PORT);
            if (!$connectresult) {
                $connected = false;
                $try++;
                continue;
            }

            $pingresult = $redis->ping();
            if (!$pingresult) {
                $connected = false;
                $try++;
                continue;
            }

            $connected = true;
        } while(!$connected && $try < self::REDIS_CONNECTION_RETRIES);

        if (!$connected) {
            throw new Exception('Couldn\'t connect to redis server!');
        }

        return $redis;
    }

    /*
    * Removes and returns the last elements of the queuename list.
    * Can be blocked by passing timeout.
    */
    public function pop(string $queuename, int $timeout = 0): ?string {
        $element = $this->redis->brPop(array($queuename), $timeout);

        if ($element === false) $element = null;

        if (is_array($element)) {
            $element = $element[1];
        }

        return $element;
    }

    /*
    * Insert element at the head of the queuename list.
    */
    public function push(string $queuename, string $element): void {
        $this->redis->lPush($queuename, $element);
    }

    /*
    * Atomically returns and removes the last element (tail) of the fromqueue list and 
    * pushes the element at the first element (head) of the toqueue list.
    * Can be blocked by passing timeout.
    */
    public function popPush(string $fromqueue, string $toqueue, int $timeout = 0): ?string {
        $element = null;
        if ($timeout > 0) {
            $element = $this->redis->bRPopLPush($fromqueue, $toqueue, $timeout);
        } else {
            $element = $this->redis->rPopLPush($fromqueue, $toqueue);
        }

        if ($element === false) $element = null;
        return $element;
    }

    /*
    * Removes the first count occurrences of elements in removequeue equal to removelement and 
    * insert pushelement at the head of the pushqueue list 
    */
    public function removePush(string $removequeue, string $removeelement, string $pushqueue, string $pushelement): void {
        $this->redis->multi()->lRem($removequeue, $removeelement, -1)->lPush($pushqueue, $pushelement)->exec();
    }

}