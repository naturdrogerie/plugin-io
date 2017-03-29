<?php //strict

namespace IO\Services;

use IO\Helper\Performance;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\Countries\Models\Country;
use Plenty\Modules\Frontend\Contracts\Checkout;

/**
 * Class CountryService
 * @package IO\Services
 */
class CountryService
{
    use Performance;

	/**
	 * @var CountryRepositoryContract
	 */
	private $countryRepository;

    /**
     * CountryService constructor.
     * @param CountryRepositoryContract $countryRepository
     */
	public function __construct(CountryRepositoryContract $countryRepository)
	{
		$this->countryRepository = $countryRepository;
	}

    /**
     * List all active countries
     * @return array
     */
	public function getActiveCountriesList($lang = 'de'):array
	{
        $this->start('getActiveCountriesList');
        $list = $this->countryRepository->getCountriesList(1, array('states'));

        $countriesList = array();
        foreach($list as $country)
        {
			$country->currLangName = $this->getCountryName($country->id, $lang);
            $countriesList[] = $country;
        }

        $this->track('getActiveCountriesList');
		return $countriesList;
	}

    /**
     * Get a list of names for the active countries
     * @param string $language
     * @return array
     */
	public function getActiveCountryNameMap(string $language):array
	{
		return $this->countryRepository->getActiveCountryNameMap($language);
	}

    /**
     * Set the ID of the current shipping country
     * @param int $shippingCountryId
     */
	public function setShippingCountryId(int $shippingCountryId)
	{
        $this->start('setShippingCountryId');
        pluginApp(Checkout::class)->setShippingCountryId($shippingCountryId);
        $this->track('setShippingCountryId');
    }

    /**
     * Get a specific country by ID
     * @param int $countryId
     * @return Country
     */
	public function getCountryById(int $countryId):Country
	{
        $this->start('getCountryById');
        $country = $this->countryRepository->getCountryById($countryId);
        $this->track('getCountryById');
        return $country;
	}

    /**
     * Get the name of specific country
     * @param int $countryId
     * @param string $lang
     * @return string
     */
	public function getCountryName(int $countryId, string $lang = "de"):string
	{
		$country = $this->countryRepository->getCountryById($countryId);
		if($country instanceof Country && count($country->names) != 0)
		{
			foreach($country->names as $countryName)
			{
				if($countryName->language == $lang)
				{
					return $countryName->name;
				}
			}
			return $country->names[0]->name;
		}
		return "";
	}
}
