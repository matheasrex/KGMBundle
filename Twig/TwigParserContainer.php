<?php

namespace KGMBundle\Twig;

use KGMBundle\Twig\TwigContainer;

/**
 * twig parser method container class
 */
class TwigParserContainer extends TwigContainer
{
	/**
	 * return list of function assigns and resolver class names
	 *
	 * @return array list of functions
	 *
	 * @access public
	 */
	public function getParsers()
	{
		//keys MUST be lowercase
		return array(
			'javascript' => '\KGMBundle\Twig\TwigParser\TwigParserJavaScript'
		);
	}
	
	/**
	 * call parser based on callstring and parser config
	 *
	 * @param array $params parameters
	 *
	 * @access public
	 */
	public function callParser($params)
	{
		$parsers = $this->getParsers();
		if (isset($parsers[$params[0]])) {
			$this->getMethod($parsers[$params[0]])->process($params);
		} else {
			\ErrorHandler::raiseError('Token '.$params[0].' not found!');
		}
	}
}
