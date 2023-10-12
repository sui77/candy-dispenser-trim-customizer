<?php

    header('Content-type: application/json');
    include 'config.php';

    $status = $redis->hgetall("mc:{$_REQUEST['filename']}");
    $status['queueTurn'] = $redis->hget("mc:queuedata", 'queued') - $redis->hget("mc:queuedata", "processed");
    echo json_encode($status);