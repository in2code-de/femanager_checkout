<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "femanager"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'femanager checkout',
    'description' => 'Enables a checkout process for frontend users. During the registration users are redirected to a 
    paypal checkout page',
    'category' => 'plugin',
    'author' => 'Stefan Busemann',
    'author_email' => 'info@in2code.de',
    'author_company' => 'in2code.de - Wir leben TYPO3',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'php' => '7.0.0-7.2.99',
        ],
        'conflicts' => [],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'In2code\\FemanagerCheckout\\' => 'Classes'
        ]
    ],
];
