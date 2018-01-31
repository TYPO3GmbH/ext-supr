<?php
declare(strict_types=1);
namespace Supr\Supr\Renderer;

use Supr\Supr\Exception\ProductNotAvailableException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Service class that renders parts of the backend wizard
 */
class WidgetRenderer implements SingletonInterface
{
    protected $widget = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->getLanguageService()->includeLLFile('EXT:supr/Resources/Private/Language/locallang_widget.xlf');
    }

    /**
     * Renders the widget
     *
     * @param array $widget
     * @return string
     * @throws ProductNotAvailableException
     */
    public function render(array $widget): string
    {
        $this->widget = $widget;
        if (!isset($this->widget['product'])) {
            throw new ProductNotAvailableException(
                'The product linked to widget "' . $this->widget['code'] . '" is not available.',
                1503476562
            );
        }

        $panelAttributes = [];
        if ($this->widget['container_background'] === 'show') {
            $panelAttributes['background-color'] = $this->widget['container_background_color'];
        }

        if ($this->widget['container_border'] === 'show') {
            $panelAttributes['border-style'] = 'solid';
            $panelAttributes['border-color'] = $this->widget['container_border_color'];
            $panelAttributes['border-width'] = '1px';
        }

        $content = [];
        $content[] = '<div class="panel panel-default" style="' . $this->generateStyleAttributeString($panelAttributes) . '">';
        $content[] =   '<div class="panel-body">';
        $content[] =     $this->renderImage();
        $content[] =     $this->renderHeadline();
        $content[] =     $this->renderDescription();
        $content[] =     $this->renderVariants();
        $content[] =     '<div class="text-right">';
        $content[] =       $this->renderPrice();
        $content[] =       $this->renderPriceAdditions();
        $content[] =     '</div>';
        $content[] =     '<div class="row footer-input">';
        $content[] =       $this->renderButton();
        $content[] =     '</div>';
        $content[] =   '</div>';
        $content[] = '</div>';

        return implode('', $content);
    }

    /**
     * @return string
     */
    protected function renderImage(): string
    {
        if ($this->widget['image'] === 'show' && !empty($this->widget['product']['images'])) {
            $productImage = $this->widget['product']['images'][0]['url'];
            if (GeneralUtility::isValidUrl($productImage)) {
                return '<img src="' . $productImage . '" class="img-responsive supr-element-' . htmlspecialchars($this->widget['image_style']) . '" />';
            }
        }

        return '';
    }

    /**
     * @return string
     */
    protected function renderHeadline(): string
    {
        if ($this->widget['title'] === 'show') {
            $attributes = [
                'font-family' => $this->widget['title_font_face'],
                'font-weight' => $this->widget['title_font_weight'],
                'color' => $this->widget['title_font_color'],
            ];

            return '<h4 style="' . $this->generateStyleAttributeString($attributes) . '">' . htmlspecialchars($this->widget['product']['title']) . '</h4>';
        }

        return '';
    }

    /**
     * @return string
     */
    protected function renderDescription(): string
    {
        if ($this->widget['description'] === 'show') {
            $attributes = [
                'font-family' => $this->widget['description_font_face'],
                'font-weight' => $this->widget['description_font_weight'],
                'color' => $this->widget['description_font_color'],
            ];

            return '<p style="' . $this->generateStyleAttributeString($attributes) . '">' . $this->widget['product']['excerpt'] . '</p>';
        }

        return '';
    }

    /**
     * @return string
     */
    protected function renderVariants(): string
    {
        $select = [];
        if ($this->widget['product']['hasVariants']) {
            $select[] = '<select class="form-control">';

            foreach ($this->widget['product']['variants'] as $variant) {
                $select[] = '<option>' . htmlspecialchars($variant['variantText']) . ' | ' . number_format((float)$variant['amount'], 2) . htmlspecialchars($variant['currency']) . '</option>';
            }

            $select[] = '</select>';
        }

        return implode('', $select);
    }

    /**
     * @return string
     */
    protected function renderPrice(): string
    {
        $content = [];
        if ($this->widget['price'] === 'show') {
            $content[] = '<p>';

            if ((float)$this->widget['product']['price_discount'] > 0) {
                $attributes = [
                    'font-family' => $this->widget['price_font_face'],
                    'color' => $this->widget['price_font_color'],
                ];
                $content[] = '<s style="' . $this->generateStyleAttributeString($attributes) . '">' . number_format((float)$this->widget['product']['price_discount'], 2) . $this->widget['product']['currency'] . '</s>&nbsp;';
            }

            $attributes = [
                'font-family' => $this->widget['price_font_face'],
                'font-weight' => $this->widget['price_font_weight'],
                'color' => $this->widget['price_font_color'],
            ];

            $content[] = '<span class="lead" style="' . $this->generateStyleAttributeString($attributes) . '">' . number_format((float)$this->widget['product']['price'], 2) . $this->widget['product']['currency'] . '</span>';
            $content[] = '</p>';
        }

        return implode('', $content);
    }

    /**
     * @return string
     */
    protected function renderPriceAdditions(): string
    {
        $content = [];
        if ($this->widget['price'] === 'show') {
            $attributes = [
                'font-family' => $this->widget['price_addition_font_face'],
                'color' => $this->widget['price_addition_font_color'],
            ];

            $content[] = '<ul class="list-unstyled">';

            if ((int)$this->widget['product']['shipping_free'] === 0) {
                $content[] = '<li><small style="' . $this->generateStyleAttributeString($attributes) . '">' . htmlspecialchars($this->getLanguageService()->getLL('tt_content.supr_widget_id.wizard.price.additional_shipping_costs')) . '</small></li>';
            }

            if ((float)$this->widget['product']['vat_percentage'] > 0) {
                $content[] = '<li><small style="' . $this->generateStyleAttributeString($attributes) . '">' . htmlspecialchars(sprintf($this->getLanguageService()->getLL('tt_content.supr_widget_id.wizard.price.incl_vat'), (float)$this->widget['product']['vat_percentage']))  . '</small></li>';
            }

            $content[] = '<li><small style="' . $this->generateStyleAttributeString($attributes) . '">' . htmlspecialchars(sprintf($this->getLanguageService()->getLL('tt_content.supr_widget_id.wizard.price.delivery_time'), $this->widget['product']['deliveryTimeText']))  . '</small></li>';
            $content[] = '</ul>';
        }

        return implode('', $content);
    }

    /**
     * @return string
     */
    protected function renderButton(): string
    {
        $content = [];
        $showQuantityInput = $this->widget['quantity_input'] === 'show';
        if ($showQuantityInput) {
            $content[] = '<div class="col-sm-5">';
            $content[] = '<input type="number" class="form-control supr-element-' . htmlspecialchars($this->widget['quantity_input_style']) . '" value="1" min="1" style="min-width: 0;">';
            $content[] = '</div>';
        }

        $content[] = '<div class="col-sm-' . ($showQuantityInput ? '7' : '12') . '">';
        $attributes = [
            'font-family' => $this->widget['button_font_face'],
            'background-color' => $this->widget['button_background_color'],
            'color' => $this->widget['button_font_color'],
            'width' => '100%'
        ];
        $content[] = '<button type="button" class="btn text-uppercase supr-element-' . htmlspecialchars($this->widget['button_style']) . '" style="' . $this->generateStyleAttributeString($attributes) . '">' . htmlspecialchars($this->widget['button_buy_text'])  . '</button>';
        $content[] = '</div>';

        return implode('', $content);
    }

    /**
     * @param array $attributes
     * @return string
     */
    protected function generateStyleAttributeString(array $attributes): string
    {
        $style = '';
        foreach ($attributes as $attribute => $value) {
            $style .= htmlspecialchars(trim($attribute)) . ':' . htmlspecialchars(trim($value)) . ';';
        }

        return $style;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
