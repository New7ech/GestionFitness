<?php

return [
    'durations' => [15, 30],

    'media' => [
        'photo' => [
            'mimes' => ['jpeg', 'png', 'jpg', 'webp'],
            'max_kb' => 5120,
        ],
        'video' => [
            'mimes' => ['mp4', 'mov', 'webm'],
            'max_kb' => 102400,
        ],
    ],
];
