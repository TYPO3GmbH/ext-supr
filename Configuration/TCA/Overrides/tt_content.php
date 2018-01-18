<?php

call_user_func(function () {
    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['supr_widget'] = 'content-supr-widget';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'tt_content',
        'CType',
        [
            'LLL:EXT:supr/Resources/Private/Language/locallang_db.xlf:tt_content.CType.supr_widget',
            'supr_widget',
            'content-supr-widget',
        ],
        'textmedia',
        'after'
    );

    $newFields = [
        'supr_widget_id' => [
            'label' => 'LLL:EXT:supr/Resources/Private/Language/locallang_db.xlf:tt_content.supr_widget_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [],
                'fieldWizard' => [
                    'suprwidgetelement' => [
                        'renderType' => 'SuprWidgetElement',
                    ],
                ],
            ],
        ],
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $newFields);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('tt_content', 'supr', 'supr_widget_id');


    $GLOBALS['TCA']['tt_content']['types']['supr_widget'] = [
        'showitem' =>
            '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,'
            . 'header;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header.ALT.html_formlabel,'
            . '--palette--;LLL:EXT:supr/Resources/Private/Language/locallang_db.xlf:tt_content.palette.supr;supr,'
            . '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,'
            . '--palette--;;language,'
            . '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,'
            . '--palette--;;hidden,'
            . '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,',
    ];
});
