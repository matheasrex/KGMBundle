<?php

namespace KGMBundle\Twig;

use KGMBundle\Twig\TwigContainer;

/**
 * twig function method container class
 */
class TwigFunctionContainer extends TwigContainer
{
	/**
	 * return list of function assigns and resolver class names
	 *
	 * @return array list of functions
	 *
	 * @access public
	 */
	public function getFunctions()
	{
		//keys MUST be lowercase and in regexp format
		return array(
			'is.*' => '\KGMBundle\Twig\TwigFunction\TwigFunctionIs',
		);
	}
	
	/**
	 * call function based on callstring and function config
	 *
	 * @param array $params parameters
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function callFunction($params)
	{
		foreach ($this->getFunctions() as $functionName => $functionClass) {
			if (\GlobalFunction::isMatch('/'.$functionName.'/', strtolower($params[0]))) {
				return $this->getMethod($functionClass)->process($params);
			}
		}
	}
}
