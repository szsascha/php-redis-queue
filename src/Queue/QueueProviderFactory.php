<?php

namespace Queue;

class QueueProviderFactory {

    public static function createQueueProvider(): IQueueProvider {
        return new QueueProviderRedis();
    }

}