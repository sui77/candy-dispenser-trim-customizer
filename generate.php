<?php
ini_set('display_errors', 1);
header('Content-type: application/json');

$fp = fopen('/app/blender/log.txt', 'a');

$validFiles = [
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

];
$modelfile = $_POST['modelfile'] ?? 'trim';
if (!array_key_exists($modelfile, $validFiles)) {
    echo json_encode(['error' => 'Invalid model.', 'result' => '']);
    exit();
}
$modelfile = $validFiles[$modelfile];

$filename = substr(sha1(json_encode($_POST)), 0, 8);
if (file_exists(dirname(__FILE__) . '/blender/files/' . $filename . '.stl')) {
    echo json_encode(['file' => $filename, 'result' => 'cached']);
    exit();
}

if (true) {
$recaptcha = checkResponse($_POST['recaptcha']);
if (is_null($recaptcha) || !isset($recaptcha['success']) || $recaptcha['success'] == false) {
    echo json_encode(['error' => 'Please verify that you are not a robot.']);
    exit();
}
}

if (!file_exists(dirname(__FILE__) . '/blender/' . $modelfile['py'] )) {
    $dir = opendir(dirname(__FILE__) . '/static');
    @mkdir(dirname(__FILE__) . '/blender/files', 0777);
    while ($f = readdir($dir)) {
        if (!is_dir(dirname(__FILE__) . '/static/' . $f)) {
            copy(dirname(__FILE__) . '/static/' . $f, dirname(__FILE__) . '/blender/' . $f);
        }
    }
}



$f = file_get_contents(dirname(__FILE__) . '/blender/' . $modelfile['py']);
$text = substr(str_replace('"', '\"', $_POST['text']), 0, 20);
$f = str_replace(['###TEXT###', '###TEXTUPPER###', '###FILE###'], [$text, strtoupper($text), $filename], $f);
file_put_contents(dirname(__FILE__) . '/blender/files/' . $filename . '.py', $f);

fputs($fp, "lol4\n");

$out = [];
$cmd = 'docker run --rm -v /tmp/modelcustomizer:/media/ ' . $modelfile['image'] . ' /media/' . $modelfile['blend'] . ' --python /media/files/' . $filename . '.py 2>&1';
fputs($fp, $cmd . "\n");
exec($cmd, $out);



//unlink( dirname(__FILE__) . '/blender/files/' . $filename . '.py' );
if (file_exists(dirname(__FILE__) . '/blender/files/' . $filename . '.stl')) {
    echo json_encode(['file' => $filename, 'result' => implode("\n", $out)]);
} else {
    echo json_encode(['error' => 'Unknown error. Could not generate STL file.', 'result' => implode("\n", $out)]);
}
function checkResponse($token) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'whatever');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "secret=6LcES7QZAAAAAIeMqN5pJopmdiyqj5P_5hLMFzpj&response=" . $token . "");

    $body = curl_exec($ch);
    if ($body === false) {
        //  throw new \Exception('curl error');
    }
    return json_decode($body, 1);


}