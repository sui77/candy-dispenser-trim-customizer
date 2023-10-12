<!DOCTYPE html>
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


    <script type="module" src="script.js"></script>
    <script>
    var liked = [];
    $(document).on('click', '.js-download', function() {
        let link = $(this).data('link');
        if (link != '') {

            if (!liked.includes(link)) {
                var modalTinyBtn = new tingle.modal({
                    footer: true
                });
                modalTinyBtn.setContent("If you liked this model please hit the like button at its printables.com page, it means a lot to me!");
                modalTinyBtn.addFooterBtn('Visit Printables model page', 'tingle-btn tingle-btn--primary tingle-btn--pull-right orange', function () {
                    window.open( link);
                });
                modalTinyBtn.addFooterBtn('Cancel', 'tingle-btn tingle-btn--default tingle-btn--pull-right', function () {
                    modalTinyBtn.close();
                });
                modalTinyBtn.open();
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
            Find my 3d models at <a href="https://www.printables.com/@sui77">printables.com/@sui77</a>
            <hr>
        </div>
    </div>


    <div class="row">
        <div class="four columns">
            <form id="xform">

                <select id="modelfile" class="u-full-width">
                    <option value="">--- Select Model ---</option>
                    <option value="keytag" <?=($_SERVER['REQUEST_URI']=='/keytag')?'selected':''?>>Stencil Type Keytag</option>
                    <option value="trim" <?=($_SERVER['REQUEST_URI']=='/trim')?'selected':''?>>Nutella Jar Candy Dispenser Trim</option>
                    <option value="ribbon450" <?=($_SERVER['REQUEST_URI']=='/ribbon450')?'selected':''?>>Nutella 450g Jar Ribbon</option>
                </select>

                <input class="u-full-width" type="text" id="text" placeholder="Enter your text here.">


                <input type="submit" value="Generate STL">

            </form>

            <div id="status"></div>

        </div>

        <div class="eight columns">
            <div id="model" style="width:590px;height:400px;background-color:#EEEEFE;"></div>
        </div>
    </div>


</div>



</body>
</html>

