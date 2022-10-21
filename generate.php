<?php
ini_set('display_errors', 1);
header('Content-type: application/json');

$filename = substr(sha1($_POST['text']), 0, 8);
if (file_exists(dirname(__FILE__) . '/blender/files/' . $filename . '.stl')) {
    echo json_encode(['file' => $filename, 'result' => 'cached']);
    exit();
}


$recaptcha = checkResponse($_POST['recaptcha']);
if (is_null($recaptcha) || !isset($recaptcha['success']) || $recaptcha['success'] == false) {
    echo json_encode(['error' => 'Please verify that you are not a robot.']);
    exit();
}

if (!file_exists(dirname(__FILE__) . '/blender/trim.py')) {
    $dir = opendir(dirname(__FILE__) . '/static');
    while ($f = readdir($dir)) {
        if (!is_dir(dirname(__FILE__) . '/static/' . $f)) {
            copy(dirname(__FILE__) . '/static/' . $f, dirname(__FILE__) . '/blender/' . $f);
        }
    }
    $fp = fopen(dirname(__FILE__) . '/blender/files/texts.txt', 'w');
    fputs($fp, date("Y-m-d") . "\n");
}



file_put_contents(dirname(__FILE__) . '/blender/files/texts.txt', $filename . " " . $_POST['text'] . "\r\n", FILE_APPEND);

$f = file_get_contents(dirname(__FILE__) . '/blender/trim.py');
$f = str_replace(['###TEXT###', '###FILE###'], [$_POST['text'], $filename], $f);
file_put_contents(dirname(__FILE__) . '/blender/files/' . $filename . '.py', $f);


$out = [];
$cmd = 'docker run --rm -v /containermounts/candy/blender/:/media/ ikester/blender /media/trim.blend --python /media/files/' . $filename . '.py 2>&1';
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