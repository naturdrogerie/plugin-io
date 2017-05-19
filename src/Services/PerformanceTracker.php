<?php

namespace IO\Services;
use Plenty\Plugin\Log\Loggable;

/**
 * Created by ptopczewski, 29.03.17 10:00
 * Class PerformanceTracker
 * @package IO\Services
 */
class PerformanceTracker
{
    use Loggable;

    private $startTime = 0;

    private $trackedMap = [];

    /**
     * PerformanceTracker constructor.
     */
    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * performance.trackRuntime('')
     * @param $key
     */
    public function trackRuntime($key)
    {
        $this->trackedMap[] = ['key' => $key, 'runtime' => microtime(true)-$this->startTime];
    }

    /**
     * @param $key
     * @param $duration
     */
    public function trackDuration($key, $duration)
    {
        $this->trackedMap[] = ['key' => $key, 'duration' => number_format($duration, 3).' sec'];
    }

    /**
     * 
     */
    public function save()
    {
        $this->getLogger('Performance')->info('IO::performance.result', $this->trackedMap);
    }
}