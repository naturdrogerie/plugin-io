<?php //strict

namespace IO\Extensions\Filters;

use IO\Extensions\AbstractFilter;

/**
 * Class ItemImagesFilter
 * @package IO\Extensions\Filters
 */
class ItemImagesFilter extends AbstractFilter
{
    /**
     * ItemImagesFilter constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getFilters():array
    {
        return [
            'itemImages'        => 'getItemImages',
            'firstItemImage'    => 'getFirstItemImage',
            'firstItemImageUrl' => 'getFirstItemImageUrl'
        ];
    }

    /**
     * @param $images
     * @param string $imageAccessor
     * @return array
     */
    public function getItemImages( $images, string $imageAccessor = 'url' ):array
    {
        $imageUrls = [];
        $imageObject = (empty( $images['variation'] ) ? 'all' : 'variation');

        foreach ($images[$imageObject] as $image)
        {
            $imageUrls[] = [
                "url" => $image[$imageAccessor],
                "position" => $image["position"]
            ];
        }

        return $imageUrls;
    }

    public function getFirstItemImage( $images, $imageAccessor = 'url' )
    {
        $images = $this->getItemImages( $images, $imageAccessor );
        $itemImage = [];
        foreach( $images as $image )
        {
            if ( !count( $itemImage ) || $itemImage['position'] > $image['position'] )
            {
                $itemImage = $image;
            }
        }

        return $itemImage;
    }

    public function getFirstItemImageUrl( $images, $imageAccessor = 'url' )
    {
        $itemImage = $this->getFirstItemImage( $images, $imageAccessor );
        if ( $itemImage !== null && $itemImage['url'] !== null )
        {
            return $itemImage['url'];
        };

        return '';
    }
}
