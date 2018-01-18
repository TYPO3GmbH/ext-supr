<?php
declare(strict_types=1);
namespace WMDB\Supr\Tests\Service;

use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use WMDB\Supr\Exception\InvalidWidgetException;
use WMDB\Supr\Exception\UnavailableContentInPayloadException;
use WMDB\Supr\Service\WidgetService;

/**
 * Test class for Service/WidgetService
 */
class WidgetServiceTest extends UnitTestCase
{
    /**
     * @var WidgetService|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\TestingFramework\Core\AccessibleObjectInterface
     */
    protected $subject;

    /**
     * @var string
     */
    protected $shopSlug = '';

    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(WidgetService::class, ['getCache'], [], '', false);

        $this->shopSlug = 'unittest_shop_' . StringUtility::getUniqueId();
        $this->subject->_set('shopSlug', $this->shopSlug);
    }

    /**
     * @test
     */
    public function shopSlugIsCorrectlyReturned()
    {
        static::assertSame($this->shopSlug, $this->subject->getShopSlug());
    }

    /**
     * @test
     */
    public function widgetRestApiUrlIsCorrectlyReturned()
    {
        $expected = 'https://supr.com/' . $this->shopSlug . '/rest/widgets';
        static::assertSame($expected, $this->subject->_call('getWidgetUrl'));
    }

    /**
     * @test
     */
    public function exceptionForBrokenPayloadIsThrown()
    {
        $this->expectException(UnavailableContentInPayloadException::class);
        $this->expectExceptionCode(1500901981);
        $this->expectExceptionMessageRegExp('/not_payload/');

        $expectedWidgets = $this->getExpectedWidgets();
        $responseContent = json_encode([
            'status' => true,
            'requires' => [],
            'payload' => [
                'widgets' => $expectedWidgets,
            ],
            'content' => [
                'not_payload',
            ],
            'messages' => [],
        ]);

        $this->mockRequestFactory($responseContent);

        $this->subject->_call('sendRequest', 'http://foo.bar/');
    }

    /**
     * @test
     */
    public function widgetPayloadIsReturned()
    {
        $expectedWidgets = $this->getExpectedWidgets();
        $responseContent = json_encode([
            'status' => true,
            'requires' => [],
            'payload' => [
                'widgets' => $expectedWidgets,
            ],
            'content' => [
                'widgets',
            ],
            'messages' => [],
        ]);

        $this->mockRequestFactory($responseContent);

        static::assertSame($expectedWidgets, $this->subject->fetchWidgets());
    }

    /**
     * @test
     */
    public function exceptionForInvalidWidgetIdIsThrown()
    {
        $this->expectException(InvalidWidgetException::class);
        $this->expectExceptionCode(1500972170);

        $expectedWidgets = $this->getExpectedWidgets();
        $responseContent = json_encode([
            'status' => true,
            'requires' => [],
            'payload' => [
                'widgets' => $expectedWidgets,
            ],
            'content' => [
                'widgets',
            ],
            'messages' => [],
        ]);

        $this->mockRequestFactory($responseContent);

        $this->subject->getSelectedWidget(10);
    }

    /**
     * @param string $content
     */
    protected function mockRequestFactory(string $content)
    {
        $streamProphecy = $this->prophesize(StreamInterface::class);
        $streamProphecy->getContents()->willReturn($content);

        $responseProphecy = $this->prophesize(ResponseInterface::class);
        $responseProphecy->getBody()->willReturn($streamProphecy->reveal());

        $requestFactoryProphecy = $this->prophesize(RequestFactory::class);
        $requestFactoryProphecy->request(Argument::cetera())->willReturn($responseProphecy->reveal());

        GeneralUtility::addInstance(RequestFactory::class, $requestFactoryProphecy->reveal());
    }

    /**
     * @return array
     */
    protected function getExpectedWidgets(): array
    {
        return [
            ['id' => 1, 'code' => '5f3668'],
            ['id' => 2, 'code' => 'b936be'],
            ['id' => 3, 'code' => 'b15836']
        ];
    }
}
