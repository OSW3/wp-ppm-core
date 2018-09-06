<?php


$config = [

    'assets' => [
       'frontend' => [],

       'admin' => [
           'styles' => [[
               'handle' => "ppm-main",
               'src' => \Register\Assets::DIRECTORY_STYLES.'main.css',
               'version' => null,
               'dependencies' => [],
               'enqueue' => true
            ]],

           'scripts' => [[
                'handle' => "jquery3",
                'src' => \Register\Assets::DIRECTORY_SCRIPTS.'jquery.min.js',
                'version' => null,
                'dependencies' => [],
                'in_header' => false,
                'enqueue' => true
            ],[
                'handle' => "ppm-autosize",
                'src' => \Register\Assets::DIRECTORY_SCRIPTS.'autosize.min.js',
                'version' => null,
                'dependencies' => [],
                'in_header' => false,
                'enqueue' => true
            ],[
                'handle' => "ppm-main",
                'src' => \Register\Assets::DIRECTORY_SCRIPTS.'main.js',
                'version' => null,
                'dependencies' => ['jquery3','ppm-autosize'],
                'in_header' => false,
                'enqueue' => true
            ],[
                'handle' => "vue-js",
                'src' => 'http://vuejs.com/main.js',
                'version' => null,
                'dependencies' => [],
                'in_header' => false,
                'enqueue' => true
           ]],
       ],
       
       'both' => []
    ],
];