<?php

namespace KGMBundle\Doctrine;

use Doctrine\DBAL\Platforms\OraclePlatform;

/**
 * inherited from OraclePlatform
 * used to override platform settings
 */
class MyOraclePlatform extends OraclePlatform
{
	/**
	 * @var KGMBundle\Configuration\GlobalConstant constants
	 *
	 * @access protected
	 */
	protected $constant;
	
	/**
	 * global contructor
	 *
	 * @param object $constant global constant object
	 *
	 * @access public
	 */
	public function __construct($constant = null)
	{
		$this->constant = $constant;
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
	 * set constant object
	 *
	 * @param KGMBundle\Configuration\GlobalConstant $constant Global Constant
	 *
	 * @link {$constant}
	 * @access public
	 */
	public function setConstant($constant)
	{
		$this->constant = $constant;
	}
	
	/**
	 * datetime format getter
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function getDateTimeFormatString()
	{
		return $this->constant->getOraclePlatformDateTimeFormat();
	}
	
	/**
	 * datetime tz format getter
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function getDateTimeTzFormatString()
	{
		return $this->constant->getOraclePlatformDateTimeTzFormat();
	}
	
	/**
	 * date format getter
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function getDateFormatString()
	{
		return $this->constant->getOraclePlatformDateFormat();
	}
	
	/**
	 * time format getter
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function getTimeFormatString()
	{
		return $this->constant->getOraclePlatformTimeFormat();
	}
	
	/**
	 * doctrine type mapper
	 *
	 * @link {$doctrineTypeMapping}
	 *
	 * @access protected
	 */
	protected function initializeDoctrineTypeMappings()
	{
		$this->doctrineTypeMapping = array(
			'integer' => 'integer',
			'number' => 'integer',
			'pls_integer' => 'boolean',
			'binary_integer' => 'boolean',
			'varchar' => 'string',
			'varchar2' => 'string',
			'nvarchar2' => 'string',
			'char' => 'string',
			'nchar' => 'string',
			'date' => 'date',
			'timestamp' => 'datetime',
			'timestamptz' => 'datetimetz',
			'float' => 'float',
			'long' => 'string',
			'clob' => 'text',
			'nclob' => 'text',
			'raw' => 'text',
			'long raw' => 'text',
			'rowid' => 'string',
			'urowid' => 'string',
			'blob' => 'blob',
		);
	}
}
