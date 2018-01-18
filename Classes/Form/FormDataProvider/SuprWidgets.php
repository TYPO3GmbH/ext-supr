<?php
declare(strict_types=1);
namespace WMDB\Supr\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WMDB\Supr\Service\WidgetService;

/**
 * Resolve return Url if not set otherwise.
 */
class SuprWidgets implements FormDataProviderInterface
{
    /**
     * @param array $result
     * @return array
     */
    public function addData(array $result): array
    {
        if ($result['recordTypeValue'] === 'supr_widget') {
            $widgetService = GeneralUtility::makeInstance(WidgetService::class);
            $widgets = $widgetService->fetchWidgets();

            foreach ($widgets as $widget) {
                if ($widget['product'] === null) {
                    // Widget relates to a deleted product
                    continue;
                }
                $result['processedTca']['columns']['supr_widget_id']['config']['items'][] = [
                    sprintf('%s - %s (%s)', $widget['name'], $widget['product']['title'], $widget['code']),
                    (int)$widget['id'],
                ];
            }
        }

        return $result;
    }
}
