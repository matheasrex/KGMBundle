<?php

namespace KGMBundle\Twig\TwigFilter;

use KGMBundle\Twig\TwigFilter;

/**
 * twig str to array methos class
 */
class TwigFilterToArray extends TwigFilter
{
	/**
	 * @var array $parameters parameter names
	 *
	 * @access protected
	 */
	protected $parameters = array(
		'callString' => '',
		'value' => '',
		'delimiter' => "\n",
	);
	
	/**
	 * explode string by delimiter
	 *
	 * @param array $params parameters
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function process($params)
	{
		$this->createParams($params);
		
		return explode($params['delimiter'], $params['value']);
	}
	
}
