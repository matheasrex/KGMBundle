<?php

namespace KGMBundle\Doctrine;

use Doctrine\DBAL\Driver\PDOMySql\Driver;
//use Doctrine\DBAL\Driver\PDOMySql\OCI8Connection;

/**
 * inherited from OCI8Driver
 * used to init connection with custom queries
 * and work with custom platform settings
 */
class MyPDOMySqlDriver extends Driver
{
	/**
	 * @var Symfony\Component\DependencyInjection\Container Service container
	 *
	 * @access protected
	 */
	protected $container;
	
	/**
	 * @var OCI8Connection $connection DB connection
	 *
	 * @access protected
	 */
	protected $connection;
	
	/**
	 * @var MyOraclePlatform $platform Oracle platform config
	 *
	 * @access protected
	 */
	protected $platform;
	
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
	 * set container
	 *
	 * @param Symfony\Component\DependencyInjection\Container $container Service container
	 *
	 * @link {$container}
	 * @access public
	 */
	public function setContainer($container)
	{
		$this->container = $container;
		$this->getDatabasePlatform()->setConstant($container->get('framework.repository')->getConstants());
	}

	/**
	 * return database platform user by the driver
	 *
	 * @return MyOraclePlatform Oracle Platform config
	 *
	 * @access public
	 */
	public function getDatabasePlatform()
	{
		if (!$this->platform) {
			$this->platform = new MyMySqlPlatform(($this->container) ? $this->container->get('framework.repository') : null);
		}
		
		return $this->platform;
	}
}
