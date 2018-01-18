<?php
declare(strict_types=1);
namespace WMDB\Supr\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WMDB\Supr\Renderer\WidgetRenderer;
use WMDB\Supr\Service\WidgetService;

/**
 * Controller for AJAX actions
 */
class AjaxController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function renderAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $widgetId = $request->getParsedBody()['widgetId'] ?? $request->getQueryParams()['widgetId'] ?? null;
        if ($widgetId === null) {
            $response = $response->withStatus(400);
        } else {
            $widgetService = GeneralUtility::makeInstance(WidgetService::class);
            $widget = $widgetService->getSelectedWidget((int)$widgetId);

            try {
                $response->getBody()->write(GeneralUtility::makeInstance(WidgetRenderer::class)->render($widget));
                $response = $response->withHeader('Content-Type', 'text/html; charset=utf-8');
            } catch (\Exception $e) {
                $response = $response->withStatus(500);
                $response->getBody()->write(json_encode([
                    'exception' => $e->getMessage()
                ]));
            }
        }

        return $response;
    }
}
