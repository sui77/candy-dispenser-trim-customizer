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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        canvas {
            border: 0;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/three@0.118.3/build/three.min.js"></script>


    <script type="module">

        import * as THREE from './three/build/three.module.js';
        import {STLLoader} from './three/build/jsm/loaders/STLLoader.js';
        import {OrbitControls} from './three/build/jsm/controls/OrbitControls.min.js';

        var container;

        var controls, camera, cameraTarget, scene, renderer;

        function init(file) {
            $('#model').html('');
            container = document.getElementById('model');
            camera = new THREE.PerspectiveCamera(70, 590 / 400, 1, 40);
            camera.position.set(0, 2, -9);
            cameraTarget = new THREE.Vector3(0, 0, 0);
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0xeeeeff);

            controls = new OrbitControls(camera, container);
            controls.enableDamping = true;
            controls.rotateSpeed = 1;
            controls.dampingFactor = 0.1;
            controls.enableZoom = true;
            controls.enablePan = true;
            controls.autoRotate = true;
            controls.autoRotateSpeed = .75;

            var loader = new STLLoader();
            loader.load('./blender/files/' + file + '.stl', function (geometry) {
                var material = new THREE.MeshPhongMaterial({color: 0x55ff33, specular: 0x888888, shininess: 100});
                var mesh = new THREE.Mesh(geometry, material);
                mesh.rotation.set(0, 0, 0);
                mesh.position.set(0, 0, 0);
                mesh.scale.set(0.1, 0.1, 0.1);

                mesh.castShadow = true;
                mesh.receiveShadow = true;


                scene.add(mesh);

            });


            // Lights

            scene.add(new THREE.HemisphereLight(0x443333, 0x111122));

            //	addShadowedLight( 1, 1, 1, 0xffffff, 1.35 );
            addShadowedLight(0.5, 1, -1, 0xffaa00, 1);
            // renderer

            renderer = new THREE.WebGLRenderer({antialias: true});
            renderer.setPixelRatio(window.devicePixelRatio);
            renderer.setSize(590, 400);
            renderer.outputEncoding = THREE.sRGBEncoding;
            container.appendChild(renderer.domElement);
        }

        function addShadowedLight(x, y, z, color, intensity) {

            var directionalLight = new THREE.DirectionalLight(color, intensity);
            directionalLight.position.set(x, y, z);
            scene.add(directionalLight);

            directionalLight.castShadow = true;

            var d = 1;
            directionalLight.shadow.camera.left = -d;
            directionalLight.shadow.camera.right = d;
            directionalLight.shadow.camera.top = d;
            directionalLight.shadow.camera.bottom = -d;

            directionalLight.shadow.camera.near = 1;
            directionalLight.shadow.camera.far = 4;

            directionalLight.shadow.bias = -0.002;

        }


        function animate() {

            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }


        $(() => {
            $('#xform').submit((e) => {
                e.preventDefault();
                $('#error').html('');
                let gcResponse = grecaptcha.getResponse();
                submitForm(gcResponse);
            });
        });

        function submitForm(token) {
            let data = {
                recaptcha: grecaptcha.getResponse(),
                text: $('#text').val(),
                modelfile: $('#modelfile').val(),
            }
            $('#model').html( $('<img src="ajax-loader.gif">') );
            $.ajax({
                type: "POST",
                url: './generate.php',
                data: data,
                success: (data, status) => {
                    grecaptcha.reset();
                    if (typeof data.error != 'undefined') {
                        $('#error').html(data.error);
                        $('#model').html('');
                    }
                    if (typeof data.file != 'undefined') {
                        init(data.file);
                        $('#download').attr('href', 'blender/files/' + data.file + '.stl');
                        $('#download').show();
                        animate();
                    }
                }
            });

        }


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
                    <option value="keytag">Stencil Type Keytag</option>
                    <option value="letters">Foldable Letters (flat)</option>
                    <option value="letters+">Foldable Letters (+one layer)</option>
                    <option value="trim">Nutella Jar Candy Dispenser Trim</option>
                    <option value="ribbon450">Nutella 450g Jar Ribbon</option>
                    <option value="ribbon500">Nutella 500g Jar Ribbon</option>

                </select>

                <input class="u-full-width" type="text" id="text" value="Your Text Here.">

                <div class="g-recaptcha" data-sitekey="6LcES7QZAAAAAPlejsQrKjZlYW6nSYzYPGmzGhNH"></div>
                <br>
                <input type="submit" value="Generate STL">


                <div id="error" style="color:#f00"></div>
            </form>
            <a id="download" style="display:none;" href="#">
                <button>Download STL</button>
            </a>
        </div>

        <div class="eight columns">
            <div id="model" style="width:590px;height:400px;"></div>
        </div>
    </div>


</div>

<pre>

</body>
</html>

