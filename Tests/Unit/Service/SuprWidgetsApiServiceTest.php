<?php

namespace Supr\Supr\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Supr\Supr\Service\SuprWidgetsApiService;

class SuprWidgetsApiServiceTest extends TestCase
{

    /**
     * @test
     */
    public function getAllAvailableWidgetReturnsArray()
    {
        // @todo this test is nonsense, for demo only!
        $apiService = new SuprWidgetsApiService();
        $result = $apiService->getAllAvailableWidgets();
        $this->assertCount(2, $result);
    }

}
