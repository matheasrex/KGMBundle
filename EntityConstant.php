<?php

namespace KGMBundle;

/**
 * default constant list
 */
class EntityConstant
{
	/**
	 * @var array $bitFields Bitfield type field call strings
	 *
	 * @access protected
	 */
	protected static $bitFields;
	
	/**
	 * @var array $enumFields Enumfield type field call strings
	 *
	 * @access protected
	 */
	protected static $enumFields;
	
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
	 * return bit values and call strings for bitfield type fields
	 *
	 * @return array Call strings and bit values
	 *
	 * @access public
	 */
	public static function getBitFields()
	{
		return self::$bitFields;
	}
	
	/**
	 * return enum values and call strings for enumfield type fields
	 *
	 * @return array Call strings and enum values
	 *
	 * @access public
	 */
	public static function getEnumFields()
	{
		return self::$enumFields;
	}
	
	/**
	 * create call string array from field settings
	 *
	 * @param array $fields
	 *
	 * @return array List of call strings and it's values
	 *
	 * @access protected
	 */
	protected static function getFieldCallStrings(array $fields)
	{
		$retval = array();
		
		foreach ($fields as $fieldName => $fieldValues) {
			foreach ($fieldValues as $valueName => $value) {
				$retval[$fieldName.$valueName] = array(
					'field' => $fieldName,
					'value' => $value
				);
			}
		}
		
		return $retval;
	}
}
