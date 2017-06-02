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
