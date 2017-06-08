<?php

namespace IO\Services;

use IO\Constants\SessionStorageKeys;
use IO\Services\SessionStorageService;

/**
 * Class ItemLastSeenService
 * @package IO\Services
 */
class ItemLastSeenService
{
    const MAX_COUNT = 12;
    private $countConfig;
    private $sessionStorage;
    
    /**
     * ItemLastSeenService constructor.
     * @param \IO\Services\SessionStorageService $sessionStorage
     */
    public function __construct(SessionStorageService $sessionStorage)
    {
        /**
         * @var TemplateConfigService $templateConfigService
         */
        $templateConfigService = pluginApp(TemplateConfigService::class);

        $this->countConfig = $templateConfigService->get('item.lists.last_seen');
        $this->sessionStorage = $sessionStorage;
    }
    
    /**
     * @param int $variationId
     */
    public function setLastSeenItem(int $variationId)
    {
        if(is_null($this->countConfig))
        {
            $this->countConfig = self::MAX_COUNT;
        }
        
        $lastSeenItems = $this->sessionStorage->getSessionValue(SessionStorageKeys::LAST_SEEN_ITEMS);
    
        if(is_null($lastSeenItems))
        {
            $lastSeenItems = [];
        }
        
        if(!in_array($variationId, $lastSeenItems))
        {
            if(count($lastSeenItems) >= $this->countConfig)
            {
                array_pop($lastSeenItems);
            }
            
            array_unshift($lastSeenItems, $variationId);
            $this->sessionStorage->setSessionValue(SessionStorageKeys::LAST_SEEN_ITEMS, $lastSeenItems);
        }
    }
}