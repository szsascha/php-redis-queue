<?php

require('vendor/autoload.php');

use Queue\QueueProviderFactory;

$uid = uniqid('', true); 
$queue = QueueProviderFactory::createQueueProvider();

while (true) {
    // Check for input and get it
    $element = $queue->popPush('input', 'processing', 5);
    if ($element == null) {
        continue;
    }

    // Process input
    sleep(10);

    // Remove entry from processing queue and send response into output queue
    $output = '"'.$element.'" processed by '.$uid.' at '.time();
    $queue->removePush('processing', $element, 'output', $output);
}
