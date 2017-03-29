<?php

namespace IO\Middlewares;
use IO\Services\PerformanceTracker;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Middleware;

/**
 * Created by ptopczewski, 29.03.17 11:13
 * Class Performance
 * @package IO\Middlewares
 */
class Performance extends Middleware
{

    public function before(
        Request $request
    )
    {

    }

    public function after(
        Request $request,
        Response $response
    ): Response
    {
        /** @var PerformanceTracker $performanceTracker */
        $performanceTracker = pluginApp(PerformanceTracker::class);
        $performanceTracker->save();
        return $response;
    }
}