<?php

$EM_CONF['supr'] = [
    'title' => 'Supr',
    'description' => 'TYPO3 integration of SUPR.com',
    'category' => '',
    'author' => 'WMDB Systems GmbH',
    'author_email' => 'info@wmdb.de',
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author_company' => '',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'fluid_styled_content' => '8.7.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
