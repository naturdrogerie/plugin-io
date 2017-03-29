<?php

namespace IO\Helper;
use IO\Services\PerformanceTracker;

/**
 * Created by ptopczewski, 29.03.17 10:07
 * Class Performance
 * @package IO\Helper
 */
trait Performance
{
    /**
     * @param string $key
     */
    private function track($key)
    {
        /** @var PerformanceTracker $tracker */
        $tracker = pluginApp(PerformanceTracker::class);
        $tracker->trackRuntime(__CLASS__ . ' - '.$key);
    }
}