<?php

namespace KGMBundle\Twig\TwigFunction;

use KGMBundle\Twig\TwigFunction;

/**
 * twig choice label stabilizer method class
 */
class TwigFunctionIs extends TwigFunction
{
	/**
	 * @var array $parameters parameter names
	 *
	 * @access protected
	 */
	protected $parameters = array(
		'value' => '',
		'value2' => '',
	);
	
	/**
	 * determines if value is ...
	 *
	 * @param array $params parameters
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function process($params)
	{
		$this->createParams($params);
		
		switch ($params['callString']) {
			case 'isArray' :
				return is_array($params['value']);
			case 'isBool' :
				return is_bool($params['value']);
			case 'isDir' :
				return is_dir($params['value']);
			case 'isDouble' :
				return is_double($params['value']);
			case 'isEmpty' :
				return empty($params['value']);
			case 'isFile' :
				return is_file($params['value']);
			case 'isFloat' :
				return is_float($params['value']);
			case 'isInt' :
				return is_int($params['value']);
			case 'isInteger' :
				return \GlobalFunction::isInteger($params['value'], $params['value2']);
			case 'isMatch' :
				return \GlobalFunction::isMatch($params['value'], $params['value2']);
			case 'isNull' :
				return is_null($params['value']);
			case 'isNumeric' :
				return is_numeric($params['value']);
			case 'isString' :
				return is_string($params['value']);
		}

		return false;
	}
}
