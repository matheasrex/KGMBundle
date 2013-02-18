<?php

namespace KGMBundle\Doctrine;

use Doctrine\DBAL\Driver\OCI8\Driver;
use Doctrine\DBAL\Driver\OCI8\OCI8Connection;

/**
 * inherited from OCI8Driver
 * used to init connection with custom queries
 * and work with custom platform settings
 */
class MyOCI8Driver extends Driver
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
	 * create oci8 connection and execute custom queries for initialization
	 * @param array  $params        params
	 * @param string $username      database username
	 * @param string $password      database password
	 * @param array  $driverOptions driver options
	 *
	 * @return OCI8Connection
	 *
	 * @access public
	 */
	public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
	{
		$params['charset'] = isset($params['charset']) ?: $this->container->get('framework.repository')->getConstants()->getConnectionCharset();
		$params['persistent'] = true;
		$this->connection = parent::connect($params, $username, $password);
		
		if (
			($initQueries = $this->container->get('framework.repository')->getConstants()->getConnectionInitializerQueries()) &&
			is_array($initQueries)
		) {
			foreach ($initQueries as $initQuery) {
				$this->connection->exec($initQuery);
			}
		}
		
		return $this->connection;
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
			$this->platform = new MyOraclePlatform(($this->container) ? $this->container->get('framework.repository') : null);
		}
		
		return $this->platform;
	}
}
