<?php

    return [

        [
            [
                'name'      => 'simple.png',
                'type'      => 'image/png',
                'tmp_name'  => FIXTURE_PATH . '/files/simple.png',
                'error'     => UPLOAD_ERR_OK,
                'size'      => 74,
            ],
            'png',
            true, // does the extension match
        ],
        [
            [
                'name'      => 'something.fake',
                'type'      => 'image/png',
                'tmp_name'  => FIXTURE_PATH . '/files/something.fake',
                'error'     => UPLOAD_ERR_OK,
                'size'      => 10,
            ],
            'fake',
            false,
        ],
        [
            [
                'name'      => 'no-extension',
                'type'      => 'application/octet-stream',
                'tmp_name'  => FIXTURE_PATH . '/files/tempname',
                'error'     => UPLOAD_ERR_OK,
                'size'      => 75,
            ],
            '',
            false,
        ],

    ];
