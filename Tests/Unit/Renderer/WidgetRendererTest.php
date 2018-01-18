<?php
declare(strict_types=1);
namespace WMDB\Supr\Tests\Renderer;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use WMDB\Supr\Exception\ProductNotAvailableException;
use WMDB\Supr\Renderer\WidgetRenderer;

/**
 * Test class for Renderer/WidgetRendererTest
 */
class WidgetRendererTest extends UnitTestCase
{
    /**
     * @var WidgetRenderer|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\TestingFramework\Core\AccessibleObjectInterface
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(WidgetRenderer::class, ['getLanguageService'], [], '', false);
    }

    /**
     * Data provider for styleAttributeStringIsBuiltCorrectly
     */
    public function stylesAttributes()
    {
        return [
            'single value' => [
                [
                    'color' => '#FF0000',
                ],
                'color:#FF0000;',
            ],
            'multiple values' => [
                [
                    'color' => '#FF0000',
                    'background-color' => '#00aa00'
                ],
                'color:#FF0000;background-color:#00aa00;',
            ],
            'with whitespaces' => [
                [
                    ' border-style' => 'solid',
                    'border-width' => ' 5px  ',
                    'border-color ' => '  white ',
                ],
                'border-style:solid;border-width:5px;border-color:white;',
            ],
            'with special characters' => [
                [
                    'content' => '"foo"',
                    'color' => '">let\'s try XSS"',
                ],
                'content:&quot;foo&quot;;color:&quot;&gt;let\'s try XSS&quot;;',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider stylesAttributes
     * @param array $attributes
     * @param string $expected
     */
    public function styleAttributeStringIsBuiltCorrectly(array $attributes, string $expected)
    {
        static::assertSame($expected, $this->subject->_call('generateStyleAttributeString', $attributes));
    }

    /**
     * @test
     */
    public function widgetWithoutProductThrowsException()
    {
        $this->expectException(ProductNotAvailableException::class);
        $this->expectExceptionCode(1503476562);

        $this->subject->expects(static::any())->method('getLanguageService')->willReturn(GeneralUtility::makeInstance(LanguageService::class));

        $widget = [
            'product' => null,
        ];
        $this->subject->render($widget);
    }
}
