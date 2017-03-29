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

    /**
     * PerformanceTracker constructor.
     */
    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * @param $key
     */
    public function trackRuntime($key)
    {
        $this->getLogger($key)->info('io runtime', microtime(true)-$this->startTime);
    }

    /**
     * @param $key
     * @param $duration
     */
    public function trackDuration($key, $duration)
    {
        $this->getLogger($key)->info('duration', number_format($duration, 3).' sec');
    }
}