<?php

$css_dir = $plugin_uri.'Framework/Assets/css/';
$js_dir = $plugin_uri.'Framework/Assets/js/';

$frmwrk_cnf = [

    'assets' => [
       'frontend' => [],

       'admin' => [
           'styles' => [[
               'handle' => "ppm-main",
               'src' => $css_dir.'main.css',
               'version' => null,
               'dependencies' => [],
               'enqueue' => true
            ]],

           'scripts' => [[
                'handle' => "jquery3",
                'src' => $js_dir.'jquery.min.js',
                'version' => null,
                'dependencies' => [],
                'in_header' => false,
                'enqueue' => true
            ],[
                'handle' => "ppm-autosize",
                'src' => $js_dir.'autosize.min.js',
                'version' => null,
                'dependencies' => [],
                'in_header' => false,
                'enqueue' => true
            ],[
                'handle' => "ppm-main",
                'src' => $js_dir.'main.js',
                'version' => null,
                'dependencies' => ['jquery3','ppm-autosize'],
                'in_header' => false,
                'enqueue' => true
           ]],
       ],
       
       'both' => []
    ],
];