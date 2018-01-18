<?php
declare(strict_types=1);
namespace WMDB\Supr\Hooks;

use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageRendererResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WMDB\Supr\Renderer\WidgetRenderer;
use WMDB\Supr\Service\WidgetService;

/**
 * Layout hook for "Page" module
 */
class SuprWidgetPreviewRenderer implements PageLayoutViewDrawItemHookInterface
{
    /**
     * Pre-processes the preview rendering of a content element of type "supr_widget"
     *
     * @param PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     */
    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row)
    {
        if ($row['CType'] === 'supr_widget') {
            $headerContent .= $parentObject->linkEditContent('<strong>' . htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:supr/Resources/Private/Language/locallang_db.xlf:tt_content.CType.supr_widget')) . '</strong>', $row);

            try {
                $widgetService = GeneralUtility::makeInstance(WidgetService::class);
                $widget = $widgetService->getSelectedWidget($row['supr_widget_id']);
                $itemContent .=
                    '<ul class="list-unstyled text-monospace">'
                      . '<li>' . htmlspecialchars(sprintf($GLOBALS['LANG']->sL('LLL:EXT:supr/Resources/Private/Language/locallang_widget.xlf:supr.widget.code'), $widget['code'])) . '</li>'
                      . '<li>' . htmlspecialchars(sprintf($GLOBALS['LANG']->sL('LLL:EXT:supr/Resources/Private/Language/locallang_widget.xlf:supr.widget.product_id'), $widget['product_id'])) . '</li>'
                    . '</ul>';
                $itemContent .= GeneralUtility::makeInstance(WidgetRenderer::class)->render($widget);
            } catch (\Exception $e) {
                $itemContent .= GeneralUtility::makeInstance(FlashMessageRendererResolver::class)->resolve()->render([
                    GeneralUtility::makeInstance(FlashMessage::class, $e->getMessage(), '', FlashMessage::ERROR)
                ]);
            }

            $drawItem = false;
        }
    }
}