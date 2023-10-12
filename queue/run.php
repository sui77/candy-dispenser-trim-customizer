<?php
include dirname(__FILE__) . '/../config.php';

$redisQ = new Redis();
$redisQ->connect($conf['redis_host'], 6379);

while (true) {
    $job = $redisQ->brpop('mc:queue', 30);
    if ($job != null) {
        $jobdata = json_decode($job[1], 1);

        $fileNumber = $redis->hincrby("mc:queuedata", 'processed', 1);

        $out = [];
        $cmd = "/usr/bin/docker run --rm -v {$conf['HOSTBDIR']}:/media/ {$jobdata['image']} /media/{$jobdata['blend']} --python /media/files/{$jobdata['filename']}.py 2>&1";
        echo "CMD:" . $cmd . "\n";
        exec($cmd, $out);
        echo implode("\n", $out);

        if (file_exists( dirname(__FILE__) . '/../blender/files/' . $jobdata['filename'] . '.stl')) {
            $redis->hset("mc:{$jobdata['filename']}", "status", "success");
            echo "Success\n";
        } else {
            $redis->hset("mc:{$jobdata['filename']}", "status", "failed");
            echo "Failed\n";
        }
    }


}