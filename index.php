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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <style>
        canvas {
            border: 0;
        }

    </style>

    <script src="https://cdn.jsdelivr.net/npm/three@0.118.3/build/three.min.js"></script>


    <script type="module">

        var objTypes = {
            keytag: {
                camera: [0, 9, 9],
                rotation: [0, 0 , 3.14],
                position: [-0.5, 0, 0],
                scale: [0.21, 0.21, 0.21]
            },
            trim: {
                camera: [0, 12, 6],
                rotation: [3.14, 0, 2],
                position: [0, 1, 4],
                scale: [0.16, 0.16, 0.16]
            },
            ribbon450: {
                camera: [0, 9, 7],
                rotation: [0, 0, 3.14/4 + 3.14*2.5],
                position: [-0.5, 0, 0],
                scale: [0.08, 0.08, 0.08]
            }
        }


        import * as THREE from './three/build/three.module.js';
        import {STLLoader} from './three/build/jsm/loaders/STLLoader.js';
        import {OrbitControls} from './three/build/jsm/controls/OrbitControls.min.js';

        var check;
        var container;
        var controls, camera, cameraTarget, scene, renderer;

        function init(file, type) {
            $('#model').html('');
            container = document.getElementById('model');
            camera = new THREE.PerspectiveCamera(70, 590 / 400, 1, 40);

            if (typeof objTypes[type] != 'undefined') {
                camera.position.set(objTypes[type]['camera'][0], objTypes[type]['camera'][1], objTypes[type]['camera'][2]);
            } else {
                camera.position.set(0, 9, 9);
            }

            cameraTarget = new THREE.Vector3(0, 0, 0);
            camera.up.set( 0, 0, 1 );
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
            loader.load(file, (geometry) => {
                var material = new THREE.MeshPhongMaterial({color: 0x55ff33, specular: 0x888888, shininess: 50});
                var mesh = new THREE.Mesh(geometry, material);
                console.log(type, objTypes);
                if (typeof objTypes[type] != 'undefined') {
                    console.log('defined');
                    mesh.rotation.set( objTypes[type]['rotation'][0], objTypes[type]['rotation'][1], objTypes[type]['rotation'][2]);
                    mesh.position.set(objTypes[type]['position'][0], objTypes[type]['position'][1], objTypes[type]['position'][2]);
                    mesh.scale.set(objTypes[type]['scale'][0], objTypes[type]['scale'][1], objTypes[type]['scale'][2]);
                } else {
                    mesh.rotation.set(0, 0, 0);
                    mesh.position.set(0, 0, 0);
                    mesh.scale.set(0.1, 0.1, 0.1);
                }
                mesh.castShadow = true;
                mesh.receiveShadow = true;
                scene.add(mesh);

                //const axesHelper = new THREE.AxesHelper( 5 );
                //scene.add( axesHelper );
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

            if ($('#modelfile').val() != '') {
                init('examples/' + $('#modelfile').val() + '.stl', $('#modelfile').val());
                animate();
            }

            $('#modelfile').change( function() {
                let mf = $(this).val();
                init('/examples/' +mf+ '.stl', mf);
                animate();
            });

            $('#xform').submit((e) => {
                e.preventDefault();
                $('input,select').attr('disabled', 'disabled');
                $('#status').html(`<p class="loading">submitting</p>` );
                let data = {
                    text: $('#text').val(),
                    modelfile: $('#modelfile').val(),
                }
                $('#model').html( $('') );
                $.ajax({
                    type: "POST",
                    url: './generate.php',
                    data: data,
                    success: (data, status) => {
                        if (typeof data.error != 'undefined') {
                            $('#status').html(`<span style="color:#f00">Error: ${data.error}</span>`);
                            $('input,select').removeAttr('disabled');
                            return;
                        }
                        check = setInterval( () => { checkStatus(data.filename) }, 1000);
                    }
                });
            });
        });

        function checkStatus(filename) {
            $.ajax({
                type: 'GET',
                url: './status.php?filename=' + filename,
                success: (data, status) => {
                    if (data.status != 'queued') {
                        clearInterval(check);
                    }
                    if (data.status == 'success') {
                        init('./blender/files/' + filename + '.stl', $('#modelfile').val());
                        $('#status').html(`<a href="/blender/files/${filename}.stl">Click here to download your stl file.</button></a>` );
                        $('input,select').removeAttr('disabled');
                    } else if (data.status == 'failed') {
                        $('#status').html(`<span style="color:#f00">${data.error}</span>`);
                    } else {
                        if (data.queueTurn == 0) {
                            $('#status').html(`<p class="loading">processing</p>` );
                        } else {
                            $('#status').html(`<p class="loading">waiting in line ${data.queueTurn}</p>` );
                        }
                    }

                    console.log("S", data, status);
                },
                failure: (data, status) => {
                    console.log(data, status);
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

<pre>

</body>
</html>

