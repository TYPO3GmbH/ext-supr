<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {
        if (TYPO3_MODE === 'BE') {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Supr.supr',
                'tools',
                'tx_supr',
                'top',
                [
                    'Backend' => 'index, widgets, suprWidget',
                ],
                [
                    'access' => 'admin',
                    'icon' => 'EXT:supr/Resources/Public/Icons/module-supr.svg',
                    'labels' => 'LLL:EXT:supr/Resources/Private/Language/locallang_mod.xlf'
                ]
            );
        }
    }
);

