<?php
declare(strict_types=1);
namespace Supr\Supr\Service;

use Supr\Supr\Exception\EmptyContentException;
use Supr\Supr\Exception\InvalidCredentialsException;
use Supr\Supr\Exception\InvalidWidgetException;
use Supr\Supr\Exception\UnavailableContentInPayloadException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service class responsible for remote widgets
 */
class WidgetService implements SingletonInterface
{
    /**
     * @var int
     */
    const CACHE_LIFETIME = 300;

    /**
     * @var array
     */
    protected $authPayload = [];

    /**
     * @var string
     */
    protected $shopSlug = '';

    /**
     * @var FrontendInterface
     */
    protected $cache;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->authenticate();
        $this->determineShopSlug();
    }

    /**
     * @return self
     * @throws InvalidCredentialsException
     */
    protected function authenticate(): self
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['supr'])) {
            throw new InvalidCredentialsException('Authentication to SUPR failed due to unset credentials', 1518518231);
        }

        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['supr'], ['allowed_classes' => false]);

        try {
            $response = $this->sendRequest($this->getAuthenticationUrl(), 'POST', [
                'form_params' => [
                    'email' => $extensionConfiguration['email'],
                    'password' => $extensionConfiguration['password'],
                ],
            ]);

            $this->authPayload = $response['payload'];
        } catch (EmptyContentException $e) {
            // Authentication failed, throw correct exception
            throw new InvalidCredentialsException('Authentication to SUPR failed due to invalid credentials', 1518518065, $e);
        }

        return $this;
    }

    /**
     * @return self
     */
    protected function determineShopSlug(): self
    {
        $this->shopSlug = $this->authPayload['shop']['slug'];

        return $this;
    }

    /**
     * @return string
     */
    public function getShopSlug(): string
    {
        return $this->shopSlug;
    }

    /**
     * @return array
     */
    public function fetchWidgets(): array
    {
        $cache = $this->getCache();
        $cacheIdentifier = 'Widgets_' . sha1('Widgets_' . $this->shopSlug);
        $cachedContent = $cache->get($cacheIdentifier);
        if ($cachedContent !== false && $cachedContent !== null) {
            return $cachedContent;
        }

        $content = $this->sendRequest($this->getWidgetUrl());

        $fetchedWidgets = $content['payload']['widgets'];
        $cache->set($cacheIdentifier, $fetchedWidgets, [], static::CACHE_LIFETIME);

        return $fetchedWidgets;
    }

    /**
     * @param int $widgetId
     * @return array
     * @throws InvalidWidgetException
     */
    public function getSelectedWidget(int $widgetId): array
    {
        foreach ($this->fetchWidgets() as $widget) {
            if ((int)$widget['id'] === $widgetId) {
                return $widget;
            }
        }

        throw new InvalidWidgetException('Widget id ' . $widgetId . ' is unavailable', 1500972170);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @return array
     * @throws EmptyContentException
     * @throws UnavailableContentInPayloadException
     */
    protected function sendRequest(string $url, string $method = 'GET', array $options = []): array
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $response = $requestFactory->request($url, $method, $options);
        $bodyContent = $response->getBody()->getContents();

        if (empty($bodyContent)) {
            throw new EmptyContentException($url . ' returned an empty response', 1518517876);
        }

        $content = json_decode($bodyContent, true);
        $requestedContent = $content['content'];
        $availableContent = array_keys($content['payload']);
        $difference = array_diff($requestedContent, $availableContent);
        if (!empty($difference)) {
            throw new UnavailableContentInPayloadException(
                'The following requested content is not available in payload: ' . implode(', ', $difference),
                1500901981
            );
        }

        return $content;
    }

    /**
     * @return string
     */
    protected function getAuthenticationUrl(): string
    {
        return 'https://supr.com/rest/auth-token';
    }

    /**
     * Get the URL to fetch configured widgets
     *
     * @return string
     */
    protected function getWidgetUrl(): string
    {
        return 'https://supr.com/' . $this->shopSlug . '/rest/widgets';
    }

    /**
     * @return FrontendInterface
     */
    protected function getCache(): FrontendInterface
    {
        if ($this->cache === null) {
            $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('cache_hash');
        }

        return $this->cache;
    }
}
