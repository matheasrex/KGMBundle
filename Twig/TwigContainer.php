<?php

namespace KGMBundle\Twig;

/**
 * abstract class for twig method containers
 */
abstract class TwigContainer
{
	/**
	 * @var Twig_Extension $extension twig extension which calls the filter
	 *
	 * @access protected
	 */
	protected $extension;
	
	/**
	 * @var array $methods list of previously called methods
	 *
	 * @access protected
	 */
	protected $methods = array();
	
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
	 * return method from cache, if not exists, then creates it
	 *
	 * @param string $className name of callable class
	 *
	 * @return TwigMethod method
	 *
	 * @access protected
	 */
	protected function getMethod($className)
	{
		if (!isset($this->methods[$className])) {
			$this->methods[$className] = new $className($this->extension);
		}
		
		return $this->methods[$className];
	}
}
