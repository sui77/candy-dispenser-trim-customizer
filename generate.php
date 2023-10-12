<?php
ini_set('display_errors', 1);
header('Content-type: application/json');
include 'config.php';

$validFiles = [
    'keytag' => [
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'keytag.blend',
        'py' => 'keytag.py',
    ],
    'trim' => [
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'trim.blend',
        'py' => 'trim.py',
    ],
    'ribbon500' => [
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'ribbon500.blend',
        'py' => 'ribbon.py',
    ],
    'ribbon450' => [
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'ribbon450.blend',
        'py' => 'ribbon.py',
    ],
    'letters' => [
         'image' => 'sui77/blender:3.5.1',
         'blend' => 'fold-customizer.blend',
         'py' => 'fold-customizer.py',
     ],
    'letters+' => [
         'image' => 'sui77/blender:3.5.1',
         'blend' => 'fold-customizer2.blend',
         'py' => 'fold-customizer.py',
     ],
];
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
$text = substr(str_replace('"', '\"', $_POST['text']), 0, 60);
$f = str_replace(['###TEXT###', '###TEXTUPPER###', '###FILE###'], [$text, strtoupper($text), $filename], $f);
file_put_contents(dirname(__FILE__) . '/blender/files/' . $filename . '.py', $f);



$queueNumber = $redis->lpush('mc:queue', json_encode([
    'image' => $modelfile['image'],
    'blend' => $modelfile['blend'],
    'filename' => $filename
]));
$fileNumber = $redis->hincrby("mc:queuedata", 'queued', 1);
$redis->hset("mc:{$filename}", 'status', 'queued');
$redis->hset("mc:{$filename}", 'number', $fileNumber);

echo json_encode(['status' => 'ok', 'filename' => $filename]);
