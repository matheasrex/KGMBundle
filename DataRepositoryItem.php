<?php

namespace KGMBundle;

/**
 * class to prevent data cloning for non pointer based datatypes and arrays
 * should be tested if works!
 * usage:
 * $itm = new DataRepositoryItem($value);
 * $repo->addRepository($key, $itm);
 * $repo->getRepository($key)->setData($new_value);
 */
class DataRepositoryItem
{
	/**
	 * @var data of the item
	 *
	 * @access protected
	 */
	protected $data;
	
	/**
	 * global contructor
	 *
	 * @param mixed $initdata Initial data
	 *
	 * @access public
	 */
	public function __construct($initdata)
	{
		$this->setData($initdata);
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
	 * getter of data
	 *
	 * @return mixed data
	 *
	 * @access public
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * setter of data
	 *
	 * @param mixed $value data
	 *
	 * @access public
	 */
	public function setData($value)
	{
		$this->data = $value;
	}
}
