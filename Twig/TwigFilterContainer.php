<?php

namespace KGMBundle\Twig;

/**
 * twig filter method container class
 */
class TwigFilterContainer extends TwigContainer
{
	/**
	 * return list of filter assigns and resolver class names
	 *
	 * @return array list of filters
	 *
	 * @access public
	 */
	public function getFilters()
	{
		//keys MUST be lowercase and in regexp format
		return array(
			'.*format' => '\KGMBundle\Twig\TwigFilter\TwigFilterFormat',
			'toarray' => '\KGMBundle\Twig\TwigFilter\TwigFilterToArray',
			'entity*' => '\KGMBundle\Twig\TwigFilter\TwigFilterEntityFunctionCall',
			'transstyle' => '\KGMBundle\Twig\TwigFilter\TwigFilterTransStyle',
		);
	}
	
	/**
	 * call filter based on callstring and filter config
	 *
	 * @param array $params parameters
	 *
	 * @return mixed
	 *
	 * @access public
	 */
	public function callFilter($params)
	{
		foreach ($this->getFilters() as $filterName => $filterClass) {
			if (\GlobalFunction::isMatch('/'.$filterName.'/', strtolower($params[0]))) {
				return $this->getMethod($filterClass)->process($params);
			}
		}
	}
}
