<?php
declare(strict_types=1);
namespace Supr\Supr\Form\Element;

use Supr\Supr\Renderer\WidgetRenderer;
use Supr\Supr\Service\WidgetService;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageRendererResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Renders the widget preview element
 */
class SuprWidgetElement extends AbstractFormElement
{
    /**
     * @return array
     */
    public function render(): array
    {
        $this->getLanguageService()->includeLLFile('EXT:supr/Resources/Private/Language/locallang_widget.xlf');
        $resultArray = $this->initializeResultArray();

        try {
            if (empty($this->data['processedTca']['columns']['supr_widget_id']['config']['items'])) {
                throw new \RuntimeException('There are no widgets available', 1501222499);
            }

            if ($this->data['command'] === 'new') {
                $selectedWidgetId = $this->data['processedTca']['columns']['supr_widget_id']['config']['items'][0][1];
            } else {
                if (empty($this->data['databaseRow']['supr_widget_id'])) {
                    throw new \InvalidArgumentException('Unknown widget is set');
                }
                $selectedWidgetId = (int)array_shift($this->data['databaseRow']['supr_widget_id']);
            }

            $widgetService = GeneralUtility::makeInstance(WidgetService::class);
            $widget = $widgetService->getSelectedWidget($selectedWidgetId);

            $content = GeneralUtility::makeInstance(WidgetRenderer::class)->render($widget);
        } catch (\Exception $e) {
            $content = GeneralUtility::makeInstance(FlashMessageRendererResolver::class)->resolve()->render([
                GeneralUtility::makeInstance(FlashMessage::class, $e->getMessage(), '', FlashMessage::ERROR),
            ]);
        }

        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $this->getLanguageService()->getLL('tt_content.supr_widget_id.wizard.preview.message'),
            $this->getLanguageService()->getLL('tt_content.supr_widget_id.wizard.preview.title'),
            FlashMessage::INFO
        );

        $loadSpinner = GeneralUtility::makeInstance(IconFactory::class)
            ->getIcon('spinner-circle-dark', Icon::SIZE_LARGE)
            ->render();

        $markup = [];
        $markup[] = '<div class="formengine-supr-widget-wizard">';
        $markup[] =   '<div class="t3js-load-spinner hidden">' . $loadSpinner . '</div>';
        $markup[] =   GeneralUtility::makeInstance(FlashMessageRendererResolver::class)->resolve()->render([$flashMessage]);
        $markup[] =   '<div class="row">';
        $markup[] =     '<div class="col-sm-6 col-lg-4 t3js-formengine-supr-widget-preview">';
        $markup[] =       $content;
        $markup[] =     '</div>';
        $markup[] =   '</div>';
        $markup[] = '</div>';

        $resultArray['html'] = implode('', $markup);
        $resultArray['stylesheetFiles'][] = 'EXT:supr/Resources/Public/Css/Backend.css';
        $resultArray['requireJsModules'][] = 'TYPO3/CMS/Supr/Widget';

        return $resultArray;
    }

    /**
     * @param array $attributes
     * @return string
     */
    protected function generateStyleAttribute(array $attributes): string
    {
        $style = '';
        foreach ($attributes as $attribute => $value) {
            $style .= htmlspecialchars($attribute) . ': ' . htmlspecialchars($value) . ';';
        }

        return $style;
    }
}
