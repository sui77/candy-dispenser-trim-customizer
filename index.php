<?php
    include 'config.php';

    $uri = $_SERVER['REQUEST_URI'];
    if (preg_match('#/download/([a-f0-9]{8})\.stl$#', $uri, $match)) {
        $path =  dirname(__FILE__) . '/blender/files/' . $match[1] . '.stl';
        if (file_exists($path)) {

            $data = $redis->hgetall("mc:{$match[1]}");
            $filename = ($data['nf'] ?? $match[1]) . '.stl';

            header('Content-type: application/octett-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $fp = fopen($path, 'r');
            fpassthru($fp);
            exit();
        } else {
            header('HTTP/1.0 404 Not Found', true, 404);
            echo 'Not found';
            exit();
        }
    }

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>sui77's 3d model customizer</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/skeleton.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/tingle.min.css">
    <script src="css/tingle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <style>
        canvas {
            border: 0;
        }

    </style>

    <script src="https://cdn.jsdelivr.net/npm/three@0.118.3/build/three.min.js"></script>


    <script type="module" src="script.js?3"></script>
    <script>
<?php $d=[]; foreach ($validFiles as $k => $v) {
        $d[$k] = $v['view'];
        $d[$k]['popup'] = false;

 } echo "objTypes=" . json_encode($d); ?>

    var liked = [];
    $(document).on('click', '.js-download', function() {
        let link = $(this).data('link');
        if (link != '') {

            if (!liked.includes(link)) {
                var modalTinyBtn = new tingle.modal({
                    footer: true
                });
                modalTinyBtn.setContent("Thanks for downloading! If you liked this model please hit the like button at its printables.com page, it means a lot to us creators!");
                modalTinyBtn.addFooterBtn('Visit Printables model page', 'tingle-btn tingle-btn--primary tingle-btn--pull-right orange', function () {
                    window.open( link);
                });
                modalTinyBtn.addFooterBtn('Cancel', 'tingle-btn tingle-btn--default tingle-btn--pull-right', function () {
                    modalTinyBtn.close();
                });
                window.setTimeout(() => {modalTinyBtn.open()}, 3000);
                liked.push(link);
            }
        }
    });
    </script>
</head>
<body>


<div class="container">

    <div class="row">
        <div class="twelve columns" style="margin-top:10px;margin-bottom:10px;">
            <h2>3d model customizer</h2>
            <hr>
        </div>
    </div>


    <div class="row">
        <div class="four columns">
            <form id="xform">

                <select id="modelfile" class="u-full-width">
                    <option value="">--- Select Model ---</option>
                    <?php foreach ($validFiles as $key => $data) { ?>
                    <option value="<?=$key?>" <?=($_SERVER['REQUEST_URI']=='/'.$key)?'selected':''?>><?=$data['title']?></option>
                    <?php } ?>
                </select>

                <input class="u-full-width js-textfield" type="text" id="text1" placeholder="">

                <input class="u-full-width js-textfield" type="text" id="text2" placeholder="">

                <input class="u-full-width js-textfield" type="text" id="text3" placeholder="">


                <input type="submit" value="Generate STL">

            </form>

            <div id="status"></div>
            <hr>
            <div id="modelinfo">

            </div>

        </div>

        <div class="eight columns">
            <div id="model" style="width:590px;height:400px;background-color:#EEEEFE;"></div>
        </div>
    </div>


</div>



</body>
</html>

