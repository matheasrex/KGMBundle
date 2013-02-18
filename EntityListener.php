<?php

namespace KGMBundle;

/**
 * entity listener class
 */
class EntityListener
{
	/**
	 * @var Symfony\Component\DependencyInjection\Container $container Service container
	 *
	 * @access protected
	 */
	protected $container;
	
	/**
	 * public contructor
	 *
	 * @param array $params Init params
	 *
	 * @access public
	 */
	public function __construct($params)
	{
		$this->container = $params['service_container'];
	}
	
	/**
	 * return container stored in class
	 *
	 * @return Symfony\Component\DependencyInjection\Container Service Container
	 *
	 * @access public
	 */
	public function getContainer()
	{
		return $this->container;
	}
	
	/**
	 * post load function
	 *
	 * @param LifecycleEventArgs $args event arguments
	 *
	 * @access public
	 */
	public function postLoad(\Doctrine\ORM\Event\LifecycleEventArgs $args)
	{
		$args->getEntity()->setEntityManager($args->getEntityManager());
	}
	
	/**
	 * post metadata of function
	 *
	 * @param LoadClassMetadataEventArgs $args event arguments
	 *
	 * @access public
	 */
	public function loadClassMetaData(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $args)
	{
		$args->getClassMetadata()->setChangeTrackingPolicy(\Doctrine\ORM\Mapping\ClassMetadataInfo::CHANGETRACKING_DEFERRED_EXPLICIT);
	}
}
