<?php

namespace KGMBundle\Configuration;

/**
 * class for defining global constants
 * date formats and connection handling queries
 */
class GlobalConstant
{
	/**
	 * @var KGMBundle\MyEntityManager
	 *
	 * @access protected
	 */
	protected $entityManager;
	
	/**
	 * @var string ORACLE DateTime format for Platform
	 *
	 * @access protected
	 */
	protected $oraclePlatformDateTimeFormat = 'Y-m-d H:i:s';
	
	/**
	 * @var string ORACLE DateTime (with timezone) format for Platform
	 *
	 * @access protected
	 */
	protected $oraclePlatformDateTimeTzFormat = 'Y-m-d H:i:sP';
	
	/**
	 * @var string ORACLE Date format for Platform
	 *
	 * @access protected
	 */
	protected $oraclePlatformDateFormat = 'Y-m-d 00:00:00';
	
	/**
	 * @var string ORACLE Time format for Platform
	 *
	 * @access protected
	 */
	protected $oraclePlatformTimeFormat = '1900-01-01 H:i:s';
	
	/**
	 * @var string ORACLE DateTime format for OCI Driver
	 *
	 * @access protected
	 */
	protected $oracleDriverDateTimeFormat = 'YYYY-MM-DD HH24:MI:SS';
	
	/**
	 * @var string ORACLE DateTime format
	 *
	 * @access protected
	 */
	protected $oracleDateTimeFormat = 'YYYY/MM/DD HH24:MI:SS';
	
	/**
	 * @var string ORACLE DateTime (with timezone) format
	 *
	 * @access protected
	 */
	protected $oracleDateTimeTzFormat = 'YYYY/MM/DD HH24:MI:SS'; //?
	
	/**
	 * @var string ORACLE Date format
	 *
	 * @access protected
	 */
	protected $oracleDateFormat = 'YYYY/MM/DD';
	
	/**
	 * @var string ORACLE Time format
	 *
	 * @access protected
	 */
	protected $oracleTimeFormat = 'HH24:MI:SS';
	
	/**
	 * @var string PHP DateTime format
	 *
	 * @access protected
	 */
	protected $dateTimeFormat = 'Y/m/d H:i:s';
	
	/**
	 * @var string PHP DateTime (with timezone) format
	 *
	 * @access protected
	 */
	protected $dateTimeTzFormat = 'Y/m/d H:i:sP';
	
	/**
	 * @var string PHP Date format
	 *
	 * @access protected
	 */
	protected $dateFormat = 'Y/m/d';
	
	/**
	 * @var string PHP Time format
	 *
	 * @access protected
	 */
	protected $timeFormat = 'H:i:s';
	
	/**
	 * @var string PHP DateTime (with timezone) format used by srftime()
	 *
	 * @access protected
	 */
	protected $longDateFormat = '%Y. %B %d.';
	
	/**
	 * @var string PHP datetime locale used by srftime()
	 *
	 * @access protected
	 */
	protected $dateLocale = 'hu_HU.ISO-8859-2';
	
	/**
	 * @var string Date format of form date dropdown
	 *
	 * @access protected
	 */
	protected $formDateFormat = 'yyyy-MMMM-d';
	
	/**
	 * @var array Connection initializer queries
	 *
	 * @access protected
	 */
	protected $connectionInitializerQueries = array();
	
	/**
	 * @var Adminuser entity of current developer
	 *
	 * @access protected
	 */
	protected $developer;
	
	/**
	 * @var string Mailer default sender mail address
	 *
	 * @access protected
	 */
	protected $defaultMailerSenderAddress = 'info@oszkar.com';
	
	/**
	 * @var string Mailer default charset
	 *
	 * @access protected
	 */
	protected $defaultMailerCharSet = 'ISO-8859-2';
	
	/**
	 * global getter
	 *
	 * @param string $name private or protected variable name
	 * 
	 * @return mixed
	 *
	 * @access public
	 */
	public function __get($name)
	{
		$field = "";
		if (preg_match('/^get[A-Z\d]/', $name)) {
			$field = lcfirst(substr($name, 3));
		} else {
			$field = lcfirst($name);
		}
		if (!property_exists($this, $field)) {
			throw new \Exception(get_class() . " has no ".$field." field");
		}
		
		return $this->$field;
	}
	
	/**
	 * global caller
	 *
	 * @param string $name  private or protected variable name
	 * @param mixed  $param parameter
	 *
	 * @return return the calue of called param
	 *
	 * @access public
	 */
	public function __call($name, $param)
	{
		return self::__get($name);
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
	 * set developer entity based on url and adminuser table data
	 *
	 * @access public
	 */
	public function initDeveloperMailAddress()
	{
		if (!isset($_SERVER['HTTP_HOST'])) {
			return;
		}
		$url = explode('.', $_SERVER['HTTP_HOST']);
		if (end($url) == 'office') {
			$developerUsername = $url[count($url) - 2];
			$developerMailChunk = substr($developerUsername, 0, -1).'.'.substr($developerUsername, -1, 1).'%@%';
			$adminuser = new \KGMBundle\Entity\Adminuser();
			$adminuser->setEntityManager($this->entityManager);
			$users = $adminuser->search(
				array(
					'where' => array(
						'email' => array($developerMailChunk, 'LIKE'),
						'costCenterId' => array(array(1), 'IN'),
					),
				)
			);
			if (is_array($users) && !empty($users)) {
				$this->developer = $users[0];
			}
		}
	}
	
	/**
	 * entity manager setter
	 *
	 * @param EntityManager $entityManager the entity manager object
	 *
	 * @return $this
	 *
	 * @access public
	 */
	public function setEntityManager($entityManager)
	{
		$this->entityManager = $entityManager;
		
		return $this;
	}
}
