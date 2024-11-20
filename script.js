

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

function changeModel(modelfile) {
    let model = objTypes[modelfile];
    let texts = model.textfields;
    if (typeof texts == 'undefined') {
        texts = 'Enter your text here.';
    }

    let ta = texts.split(';');
    $('.js-textfield').hide();
    for (let s in ta) {
        $('#text' + (1*s+1)).attr('placeholder', ta[s]);
        $('#text' + (1*s+1)).show();
    }
    init('examples/' + modelfile + '.stl', modelfile);
    animate();

    $('#modelinfo').html(`
        <strong>Model:</title> <a href="${model.url}">${model.title}</a><br>
        <strong>Creator:</title> <a href="https://printables.com/${model.creator}">${model.creator}</a>
        
    `);

    window.history.pushState({"modelfile": modelfile },"", '/' + modelfile);

}

$(() => {


    if ($('#modelfile').val() != '') {
        let mf = $('#modelfile').val();
        changeModel(mf);
    }

    $('#modelfile').change( function() {
        let mf = $(this).val();
        changeModel(mf);
    });

    $('#xform').submit((e) => {
        e.preventDefault();
        $('input,select').attr('disabled', 'disabled');
        $('#status').html(`<p class="loading">submitting</p>` );
        let data = {
            text1: $('#text1').val(),
            text2: $('#text2').val(),
            text3: $('#text3').val(),
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
                let link = '';
                if (typeof objTypes[$('#modelfile').val()] != 'undefined') {
                    link = objTypes[$('#modelfile').val()].url;
                }
                let el = `<a class="js-download" data-link="${link}" href="/download/${filename}.stl">Click here to download your stl file.</button></a>`;

                $('#status').html(el);


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

