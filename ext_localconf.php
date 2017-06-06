<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {
        if (TYPO3_MODE === 'BE') {
            $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
            $icons = [
                'ctype-supr-widget' => 'EXT:supr/Resources/Public/Icons/ContentElements/supr_widget.svg',
            ];
            foreach ($icons as $iconIdentifier => $source) {
                $iconRegistry->registerIcon(
                    $iconIdentifier,
                    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                    ['source' => $source]
                );
            }
        }
    }
);
