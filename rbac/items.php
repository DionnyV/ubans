<?php
return [
    'manageBans' => [
        'type' => 2,
        'description' => 'Управление банами',
    ],
    'manageContent' => [
        'type' => 2,
        'description' => 'Управление контентом',
    ],
    'manageUsers' => [
        'type' => 2,
        'description' => 'Управление пользователями',
    ],
    'manageSettings' => [
        'type' => 2,
        'description' => 'Управление настройками',
    ],
    'user' => [
        'type' => 1,
    ],
    'editor' => [
        'type' => 1,
        'children' => [
            'manageContent',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'manageBans',
        ],
    ],
    'deputy' => [
        'type' => 1,
        'children' => [
            'editor',
            'admin',
        ],
    ],
    'root' => [
        'type' => 1,
        'children' => [
            'manageSettings',
            'manageUsers',
            'deputy',
        ],
    ],
];
