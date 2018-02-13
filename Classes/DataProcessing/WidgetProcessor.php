<?php
declare(strict_types=1);
namespace Supr\Supr\DataProcessing;

use Supr\Supr\Service\WidgetService;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

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
     * Process data for the CType "supr_widget"
     *
     * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $widget = [];
        try {
            $widgetService = GeneralUtility::makeInstance(WidgetService::class);
            $widget['shop_slug'] = $widgetService->getShopSlug();
            $widget = array_merge($widget, $widgetService->getSelectedWidget($processedData['data']['supr_widget_id']));
        } catch (\Exception $e) {
            $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
            $logger->error(
                $e->getMessage(),
                [
                    'table' => $cObj->getCurrentTable(),
                    'uid' => $processedData['data']['uid'],
                    'exception' => $e,
                ]
            );
        }

        $processedData['widget'] = $widget;

        return $processedData;
    }
}
