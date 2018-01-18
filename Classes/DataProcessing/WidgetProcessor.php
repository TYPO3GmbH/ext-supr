<?php
declare(strict_types=1);
namespace WMDB\Supr\DataProcessing;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use WMDB\Supr\Exception\InvalidWidgetException;
use WMDB\Supr\Service\WidgetService;

/**
 * Data processor for SUPR widgets
 */
class WidgetProcessor implements DataProcessorInterface, SingletonInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        GeneralUtility::makeInstance(PageRenderer::class)->addJsFooterFile('https://widget.supr.com/load.js');
    }

    /**
     * Process data for the CType "fs_slider"
     *
     * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $widgetService = GeneralUtility::makeInstance(WidgetService::class);
        try {
            $widget = $widgetService->getSelectedWidget($processedData['data']['supr_widget_id']);
        } catch (InvalidWidgetException $e) {
            $widget = [
                'product_id' => $processedData['data']['supr_widget_id'],
            ];
        }

        $widget['shop_slug'] = $widgetService->getShopSlug();

        $processedData['widget'] = $widget;

        return $processedData;
    }
}
