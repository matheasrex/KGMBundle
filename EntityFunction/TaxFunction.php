<?php

namespace KGMBundle\EntityFunction;

use KGMBundle\EntityFunction;

/**
 * Entity related functions for Tax
 */
class TaxFunction extends EntityFunction
{
	/**
	 * string representation of tax
	 *
	 * @return string value of tax formatted with dashes
	 *
	 * @access public
	 */
	public function stringValue()
	{
		$retval = $this->entity->getSplit0();
		$retval .= "-".$this->entity->getSplit1();
		$retval .= "-".$this->entity->getSplit2();
		
		return $retval;
	}
}
