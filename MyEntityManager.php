<?php

namespace KGMBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

/**
 * The EntityManager uses private properties instead of protected, so
 * we must redeclare some properties, and cannot use the private functions,
 * only the public ones.
 */
class MyEntityManager extends EntityManager
{
	/**
	 * @var \Doctrine\DBAL\Connection $connection The database connection used by the EntityManager.
	 *
	 * @access protected
	 */
	protected $connection;
	
	/**
	 * @var Symfony\Component\DependencyInjection\Container $container Service container
	 *
	 * @access protected
	 */
	protected $container;
	
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
	 * create new MyEntityManager
	 * must implement to work properly!
	 *
	 * @param Resource      $conn         Connection
	 * @param Configuration $config       Configuration
	 * @param EventManager  $eventManager Event manager
	 *
	 * @return MyEntityManager
	 *
	 * @access public
	 */
	public static function create($conn, \Doctrine\ORM\Configuration $config, \Doctrine\Common\EventManager $eventManager = null)
	{
		if (!$config->getMetadataDriverImpl()) {
			throw ORMException::missingMappingDriverImpl();
		}
		
		$container = null;
		foreach ($conn->getEventManager()->getListeners() as $listeners) {
			foreach ($listeners as $listener) {
				if ($container = $listener->getContainer()) {
					break(2);
				}
			}
		}

		if (is_array($conn)) {
			$conn = \Doctrine\DBAL\DriverManager::getConnection($conn, $config, ($eventManager ? : new EventManager()));
		} else if ($conn instanceof \Doctrine\DBAL\Connection) {
			if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
				throw ORMException::mismatchedEventManager();
			}
			//$conn->getDriver()->setContainer($container);
		} else {
			throw new \InvalidArgumentException("Invalid argument: " . $conn);
		}
		
		$entityManager = new MyEntityManager($conn, $config, $conn->getEventManager());
		$entityManager->setContainer($container);

		return $entityManager;
	}
	
	/**
	 * Gets the database connection object used by the MyEntityManager.
	 * Overridden from EntityManager
	 *
	 * @return \Doctrine\DBAL\Connection
	 *
	 * @access public
	 */
	public function getConnection()
	{
		if ($this->connection) {
			return $this->connection;
		}
		$this->connection = parent::getConnection();
		$this->container->get('framework.repository')->getConstants()->setEntityManager($this)->initDeveloperMailAddress();
		
		return $this->connection;
	}

	/**
	 * get container
	 *
	 * @return Symfony\Component\DependencyInjection\Container Service container
	 *
	 * @access public
	 */
	public function getContainer()
	{
		return $this->container;
	}
	
	/**
	 * set container
	 *
	 * @param Symfony\Component\DependencyInjection\Container $container Service container
	 *
	 * @return \KGMBundle\MyEntityManager
	 *
	 * @link {$container}
	 * @access public
	 */
	public function setContainer($container)
	{
		$this->container = $container;
		
		return $this;
	}
}
