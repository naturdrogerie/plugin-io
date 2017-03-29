<?php // strict

namespace IO\Providers;

use IO\Extensions\TwigIOExtension;
use IO\Extensions\TwigServiceProvider;
use IO\Middlewares\Performance;
use IO\Services\ItemLoader\Contracts\ItemLoaderFactory;
use IO\Services\ItemLoader\Extensions\TwigLoaderPresets;
use IO\Services\ItemLoader\Factories\ItemLoaderFactoryES;
use IO\Services\NotificationService;
use IO\Services\PerformanceTracker;
use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Templates\Twig;

/**
 * Class IOServiceProvider
 * @package IO\Providers
 */
class IOServiceProvider extends ServiceProvider
{
    /**
     * Register the core functions
     */
    public function register()
    {
        $this->addGlobalMiddleware(Performance::class);
        $this->getApplication()->singleton(PerformanceTracker::class);
        $this->getApplication()->register(IORouteServiceProvider::class);

        $this->getApplication()->singleton('IO\Helper\TemplateContainer');

        $this->getApplication()->bind('IO\Builder\Item\ItemColumnBuilder');
        $this->getApplication()->bind('IO\Builder\Item\ItemFilterBuilder');
        $this->getApplication()->bind('IO\Builder\Item\ItemParamsBuilder');

        $this->getApplication()->singleton('IO\Services\CategoryService');

        $this->getApplication()->singleton(NotificationService::class);

        //TODO check ES ready state
        $this->getApplication()->bind(ItemLoaderFactory::class, ItemLoaderFactoryES::class);
    }

    /**
     * boot twig extensions and services
     * @param Twig $twig
     * @param PerformanceTracker $performanceTracker
     */
    public function boot(Twig $twig, PerformanceTracker $performanceTracker)
    {

        $twig->addExtension(TwigServiceProvider::class);
        $twig->addExtension(TwigIOExtension::class);
        $twig->addExtension('Twig_Extensions_Extension_Intl');
        $twig->addExtension(TwigLoaderPresets::class);

        $performanceTracker->trackRuntime(__CLASS__.' after boot');
    }
}
