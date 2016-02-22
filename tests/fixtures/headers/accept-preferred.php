<?php

return [
    [
        'header'    => 'application/json',
        'available' => 'application/json',
        'expected'  => 'application/json',
    ],
    [
        'header'    => '*/*',
        'available' => 'application/json',
        'expected'  => 'application/json',
    ],
    [
        'header'    => 'application/json;version=2',
        'available' => 'application/json;version=1',
        'expected'  => null,
    ],
    [
        'header'    => 'application/json',
        'available' => 'text/html, application/json',
        'expected'  => 'application/json',
    ],
    [
        'header'    => 'text/html;q=0.1, application/json',
        'available' => 'application/json',
        'expected'  => 'application/json',
    ],
    [
        'header'    => 'text/html, application/json',
        'available' => 'application/json',
        'expected'  => 'application/json',
    ],
    [
        'header'    => 'text/html;q=0.1, application/json;q=0.4',
        'available' => 'application/json',
        'expected'  => 'application/json',
    ],
    [
        'header'    => 'text/html, application/json, text/*',
        'available' => 'text/plain',
        'expected'  => 'text/plain',
    ],
    [
        'header'    => 'text/html, application/json',
        'available' => 'application/json, text/html',
        'expected'  => 'text/html',
    ],
    [
        'header'    => 'application/json',
        'available' => 'application/json;version=2',
        'expected'  => null,
    ],
    [
        'header'    => 'application/json;version=3, application/json',
        'available' => 'application/json;version=2, application/json',
        'expected'  => 'application/json',
    ],
    [
        'header'    => 'application/json;version=3, application/json',
        'available' => 'application/json;version=2, application/json;version=3',
        'expected'  => 'application/json;version=3',
    ],
];
