<?php

$conf = [
    'redis_host' => (getenv('REDIS_HOST') == '') ? '172.17.0.1' : getenv('REDIS_HOST'),
    'HOSTBDIR' =>  (getenv('HOSTBDIR') == '') ? '/tmp/modelcustomizer' : getenv('HOSTBDIR'),
];

$validFiles = [
    'housenumber' => [
        'title' => '[title] by @Whitey4405',
        'image' => 'sui77/blender:4.2.1',
        'blend' => 'house-number.blend',
        'py' => 'house-number.py',
        'nf' => 'HouseNumber',
        'view' => [
            'camera' => [0,-9, 9],
            'rotation' => [0, 0 , 0],
            'position' => [10, 0, 0],
            'scale' => [0.11, 0.11, 0.11],
            'title' => '[title]',
            'creator' => '@Whitey4405',
            'url' => 'https://www.printables.com/model/104691-contemporary-stand-off-house-numbers',
        ],
    ],
    'keytag' => [
        'title' => 'Stencil Type Keytag',
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'keytag.blend',
        'py' => 'keytag.py',
        'nf' => 'KeyTag',
        'view' => [
            'camera' => [0, 9, 9],
            'rotation' => [0, 0 , 3.14],
            'position' => [-0.5, 0, 0],
            'scale' => [0.21, 0.21, 0.21],
            'title' => 'Stencil Type Keytag',
            'creator' => '@sui77',
            'url' => 'https://www.printables.com/model/611327-stencil-style-key-tag-online-customizer',
        ],
    ],
    'keytag2' => [
        'title' => 'Minimalistic Flexible Key Tags by @Akio',
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'keytag2.blend',
        'py' => 'keytag.py',
        'nf' => 'KeyTag2',
        'view' => [
            'camera' => [0, 9, 9],
            'rotation' => [0, 0 , 3.14],
            'position' => [-0.5, 0, 0],
            'scale' => [0.21, 0.21, 0.21],
            'title' => 'Minimalistic Flexible Key Tags',
            'creator' => '@Akio',
            'url' => 'https://www.printables.com/model/615049-minimalistic-flexible-key-tags',
        ],
    ],

    'trim' => [
        'title' => 'Nutella Jar Candy Dispenser Trim',
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'trim.blend',
        'py' => 'trim.py',
        'nf' => 'Trim',
        'view' => [
            'camera' => [0, 12, 6],
            'rotation' => [3.14, 0, 2],
            'position' => [0, 1, 4],
            'scale' => [0.16, 0.16, 0.16],
            'title' => 'Nutella Jar Candy Dispenser',
            'creator' => '@sui77',
            'url' => 'https://www.printables.com/model/274646-nutella-jar-candy-dispenser',
        ],
    ],
    'ribbon450' => [
        'title' => 'Nutella 450g Jar Ribbon',
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'ribbon450.blend',
        'py' => 'ribbon.py',
        'nf' => 'Ribbon',
        'view' => [
            'camera' => [0, 9, 7],
            'rotation' => [0, 0, 3.14/4 + 3.14*2.5],
            'position' => [-0.5, 0, 0],
            'scale' => [0.08, 0.08, 0.08],
            'title' => 'Nutella Jar Candy Dispenser II',
            'creator' => '@sui77',
            'url' => 'https://www.printables.com/model/499766-nutella-jar-candy-dispenser-ii',
        ],
    ],
    'swatch' => [
        'title' => 'Filament Swatch by Yimir',
        'image' => 'sui77/blender:3.5.1',
        'blend' => 'filamentswatch.blend',
        'py' => 'filamentswatch.py',
        'nf' => 'FilamentSwatch',
        'view' => [
            'camera' => [0,3 , 5],
            'rotation' => [0, 0, 3.14/4 + 3.14*2.5],
            'position' => [-0.5, 0, 0],
            'scale' => [0.08, 0.08, 0.08],
            'textfields' => 'Manufacturer;Material;Filament Name',
            'title' => 'Filament Swatch Remix',
            'creator' => '@Yimir_326009',
            'url' => 'https://www.printables.com/model/263740-filament-swatch-remix',
        ],

     ]
];


$redis = new Redis();
$redis->connect($conf['redis_host'], 6379);

