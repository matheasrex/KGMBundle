<?php

namespace KGMBundle\Twig;

/**
 * abstract class for twig filter methods
 */
abstract class TwigMethod
{
	/**
	 * @var array $parameters parameter names
	 *
	 * value is the default value
	 *
	 * @access protected
	 */
	protected $parameters = array();
	
	/**
	 * @var Twig_Extension $extension twig extension which calls the filter
	 *
	 * @access protected
	 */
	protected $extension;
	
	/**
	 * global constructor
	 *
	 * @param Twig_Extension $extension twig extension which calls the filter
	 *
	 * @access public
	 */
	public function __construct(\Twig_Extension $extension)
	{
		$this->extension = $extension;
		//fix parameters config, if developer forgot the callString param
		if (!in_array('callString', array_keys($this->parameters))) {
			$params = array_reverse($this->parameters);
			$params['callString'] = '';
			$this->parameters = array_reverse($params);
		}
	}
	
	/**
	 * object to string function
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function __toString()
	{
		return (string)\GlobalFunction::objectToString($this, get_object_vars($this));
	}
	
	/**
	 * method called by container
	 *
	 * @param array $params parameters
	 *
	 * @return mixed
	 *
	 * @access public
	 */
	public abstract function process($params);
	
	/**
	 * create asociative parameter array from indexed parameters
	 *
	 * @param array &$params parameters given by twig engine
	 *
	 * @access protected
	 */
	protected function createParams(&$params)
	{
		if (count($params) > count($this->parameters)) {
			$params = array_slice($params, 0, count($this->parameters));
		} elseif (count($params) < count($this->parameters)) {
			$params = array_merge(
				$params,
				array_slice(
					array_values($this->parameters),
					count($params),
					count($this->parameters) - count($params)
				)
			);
		}
		$params = array_combine(array_keys($this->parameters), $params);
	}
}
