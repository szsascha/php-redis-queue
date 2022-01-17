<?php

namespace Queue;

interface IQueueProvider {

    public function pop(string $queuename, int $timeout = 0): ?string;

    public function push(string $queuename, string $element): void;

    public function popPush(string $fromqueue, string $toqueue, int $timeout = 0): ?string;

    public function removePush(string $removequeue, string $removeelement, string $pushqueue, string $pushelement): void;

}