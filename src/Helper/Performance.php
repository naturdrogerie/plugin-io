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
    private $trackedKeys = [];

    /**
     * @param string $key
     */
    private function trackRuntime($key)
    {
        /** @var PerformanceTracker $tracker */
        $tracker = pluginApp(PerformanceTracker::class);
        $tracker->trackRuntime(__CLASS__ . ' - '.$key);
    }

    private function start($key)
    {
        $this->trackedKeys[$key] = microtime(true);
    }

    /**
     * @param $key
     */
    private function track($key)
    {
        /** @var PerformanceTracker $tracker */
        $tracker = pluginApp(PerformanceTracker::class);
        $tracker->trackDuration(__CLASS__ . ' - '.$key, microtime(true)-$this->trackedKeys[$key]);
    }
}