<?php
ini_set('display_errors', 1);
header('Content-type: application/json');
include 'config.php';

$modelfile = $_POST['modelfile'];
if (!array_key_exists($modelfile, $validFiles)) {
    echo json_encode(['error' => 'Invalid model.', 'result' => '']);
    exit();
}
$modelfile = $validFiles[$modelfile];


$filename = substr(sha1(json_encode($_POST)), 0, 8);
$status = $redis->hget('mc:' . $filename, 'status');
if ($status != '') {
    echo json_encode(['filename' => $filename]);
    exit();
}

$f = file_get_contents(dirname(__FILE__) . '/blender/' . $modelfile['py']);
$niceFilename = [$modelfile['nf']??'model'];
$f = str_replace(['###FILE###'], [$filename], $f);
for ($i=1; $i<4; $i++) {
    if (isset($_POST['text' . $i]) && $_POST['text' . $i] != '') {
        $text = substr(str_replace('"', '\"', $_POST['text' . $i]), 0, 60);
        if ($i==1) {
            $f = str_replace(['###TEXT###', '###TEXTUPPER###'], [$text, strtoupper($text)], $f);
        }
        $f = str_replace(['###TEXT' . $i . '###', '###TEXTUPPER' . $i . '###'], [$text, strtoupper($text)], $f);
        $niceFilename[] = preg_replace('/[^a-z0-9]/i', '_', $text);
    }
}
file_put_contents(dirname(__FILE__) . '/blender/files/' . $filename . '.py', $f);



$queueNumber = $redis->lpush('mc:queue', json_encode([
    'image' => $modelfile['image'],
    'blend' => $modelfile['blend'],
    'filename' => $filename
]));
$nf = implode('-', $niceFilename);
$redis->hset("mc:${filename}", 'nf', $nf);
$fileNumber = $redis->hincrby("mc:queuedata", 'queued', 1);
$redis->hset("mc:{$filename}", 'status', 'queued');
$redis->hset("mc:{$filename}", 'number', $fileNumber);

echo json_encode(['status' => 'ok', 'filename' => $filename, 'nf' => $nf]);
