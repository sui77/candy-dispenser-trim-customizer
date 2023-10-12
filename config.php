<?php

$conf = [
    'redis_host' => (getenv('REDIS_HOST') == '') ? '172.17.0.1' : getenv('REDIS_HOST'),
    'HOSTBDIR' =>  (getenv('HOSTBDIR') == '') ? '/tmp/modelcustomizer' : getenv('HOSTBDIR'),
];

$redis = new Redis();
$redis->connect($conf['redis_host'], 6379);


