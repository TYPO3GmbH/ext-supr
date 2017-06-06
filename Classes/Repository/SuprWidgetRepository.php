<?php
declare(strict_types=1);
namespace Supr\Supr\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use Supr\Supr\Service\SuprWidgetsApiService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
  * Data Handling for Supr Widgets
  */
class SuprWidgetRepository {

    /**
     * Provide content element dropdown for CE integration
     *
     * @param array $configuration
     */
    public function getWidgetsForItemsProcFunc(array &$configuration)
    {
        $suprWidgetApiService = GeneralUtility::makeInstance(SuprWidgetsApiService::class);
        $widgets = $suprWidgetApiService->getAllAvailableWidgets();
        foreach ($widgets as $key => $widget) {
            $fieldName = $widget;
            $value = $key;
            $configuration['items'][] = [$fieldName, $value];
        }
    }

}
