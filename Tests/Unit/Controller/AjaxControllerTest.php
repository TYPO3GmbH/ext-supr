<?php
declare(strict_types=1);
namespace Supr\Supr\Tests\Controller;

use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Supr\Supr\Controller\AjaxController;
use Supr\Supr\Renderer\WidgetRenderer;
use Supr\Supr\Service\WidgetService;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test class for Controller/AjaxController
 */
class AjaxControllerTest extends UnitTestCase
{
    /**
     * @test
     */
    public function responseContainsClientErrorOnMissingWidgetId()
    {
        $response = (new AjaxController())->renderAction(new ServerRequest(), new Response());
        static::assertSame(400, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function contentTypeOfResponseIsHtml()
    {
        $serverRequestProphecy = $this->prophesize(ServerRequestInterface::class);
        $serverRequestProphecy->getParsedBody()->willReturn([
            'widgetId' => 42,
        ]);

        $widgetServiceProphecy = $this->prophesize(WidgetService::class);
        $widgetServiceProphecy->getSelectedWidget(Argument::cetera())->willReturn(['id' => 42]);
        $widgetRendererProphecy = $this->prophesize(WidgetRenderer::class);
        $widgetRendererProphecy->render(Argument::cetera())->willReturn('<foo bar="baz">');

        GeneralUtility::setSingletonInstance(WidgetService::class, $widgetServiceProphecy->reveal());
        GeneralUtility::setSingletonInstance(WidgetRenderer::class, $widgetRendererProphecy->reveal());

        $response = (new AjaxController())->renderAction($serverRequestProphecy->reveal(), new Response());
        static::assertSame(['text/html; charset=utf-8'], $response->getHeader('Content-Type'));
    }
}