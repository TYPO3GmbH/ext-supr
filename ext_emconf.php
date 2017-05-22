<?php
/************************************************************************
 * Extension Manager/Repository config file for ext "supr".
 ************************************************************************/
$EM_CONF[$_EXTKEY] = [
    'title' => 'Supr Integration',
    'description' => 'Supr integration',
    'category' => 'extension',
    'constraints' => [
        'depends' => [
            'typo3' => '8.0.0-8.99.99'
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Supr\\Supr\\' => 'Classes'
        ],
    ],
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Anja Leichsenring',
    'author_email' => 'anja.leichsenring@typo3.com',
    'author_company' => 'TYPO3 GmbH',
    'version' => '0.0.1',
];
