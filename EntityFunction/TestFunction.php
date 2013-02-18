<?php

namespace KGMBundle\EntityFunction;

use KGMBundle\EntityFunction;

/**
 * Entity related functions for Test entity
 */
class TestFunction extends EntityFunction
{
	/**
	 * string representation of test
	 *
	 * @return string value of test entity
	 *
	 * @access public
	 */
	public function stringValue()
	{
		return $this->entity->text.' ('.$this->entity->fullValue().')';
	}
	
	/**
	 * full value of test
	 *
	 * @return int weighted value of entity
	 *
	 * @access public
	 */
	public function fullValue()
	{
		return round($this->entity->value * ($this->entity->weight / 100), 2);
	}
	
	/**
	 * return prefixed text field
	 *
	 * @param string $prefix Prefix
	 *
	 * @return string prefix and text field
	 *
	 * @access public
	 */
	public function prefixedText(string $prefix)
	{
		return $prefix.$this->entity->text;
	}
}
