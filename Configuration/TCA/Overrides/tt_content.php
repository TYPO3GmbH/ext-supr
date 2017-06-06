<?php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:supr/Resources/Private/Language/Tca.xlf:plugin.supr_widget',
        'supr_widget',
        'ctype-supr-widget'
    ],
    'CType',
    'supr'
);

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['supr_widget'] = 'ctype-supr-widget';

$columns = [
    'supr_widget_id' => [
        'label' => 'LLL:EXT:supr/Resources/Private/Language/Tca.xlf:tt_content.supr_widget_id',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    ''
                ]
            ],
            'itemsProcFunc' => \Supr\Supr\Repository\SuprWidgetRepository::class . '->getWidgetsForItemsProcFunc',
            'default' => '',
        ]
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $columns);

$GLOBALS['TCA']['tt_content']['types']['supr_widget'] = [
    'showitem' => '
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header,supr_widget_id,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,layout;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:layout_formlabel,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.extended,rowDescription,
        --div--;LLL:EXT:lang/locallang_tca.xlf:sys_category.tabs.category,categories
'
];
