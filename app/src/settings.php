<?php
/*
settings.php

This file simply returns an array that contains the settings for Slim Framework.
This is the way Slim Framework suggests we handle settings.
*/
    return [
        'settings' => [
            // Renderer settings
            'renderer' => [
                //Location of our templates. Should be absolute path instead.
                'template_path' => '../templates/',
            ],
        ],
    ];

?>