<?php //strict

namespace LayoutCore\Services;

use Plenty\Modules\Category\Contracts\CategoryRepository;
use Plenty\Modules\Category\Models\Category;

use LayoutCore\Constants\CategoryType;
use LayoutCore\Constants\Language;

class NavigationService
{
	
	/**
	 * @var CategoryRepository
	 */
	private $categoryRepository;
	
	public function __construct(CategoryRepository $categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
	}
	
	/**
	 * Returns sitemap tree as array
	 * @param string $type Only return categories of given type
	 * @param string $lang The language to get sitemap tree for
	 * @return array
	 */
	public function getNavigationTree(string $type = "all", string $lang = "de"):array
	{
		return $this->categoryRepository->getSitemapTree($type, $lang);
	}
	
	/**
	 * Returns sitemap list as array
	 * @param string $type Only return categories of given type
	 * @param string $lang The language to get sitemap list for
	 * @return array
	 */
	public function getNavigationList(string $type = "all", string $lang = "de"):array
	{
		return $this->toArray($this->categoryRepository->getSitemapList($type, $lang));
	}
	
	// FIXME arrays of objects are not transformed to arrays of native types before passing to twig templates.
	private function toArray(array $categories):array
	{
		$result = [];
		foreach($categories as $category)
		{
			array_push($result, $category->toArray());
		}
		
		return $result;
	}
	
}
