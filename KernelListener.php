<?php

namespace KGMBundle;

/**
 * kernel listener class
 */
class KernelListener
{
	/**
	 * @var Symfony\Component\DependencyInjection\Container $container Service container
	 *
	 * @access protected
	 */
	protected $container;
	
	/**
	 * @var bool $wired Services has been wired
	 *
	 * @access protected
	 */
	protected $wired = false;
	
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
		$this->wireServices();
	}
	
	/**
	 * wire services if must
	 *
	 * @access public
	 */
	public function wireServices()
	{
		if ($this->wired) {
			return;
		}
		$entityManager = $this->container->get('doctrine.orm.default_entity_manager');
		if ($entityManager instanceOf \KGMBundle\MyEntityManager) {
			$this->container->get('doctrine.orm.default_entity_manager')->setContainer($this->container);
			$this->wired = true;
		}
	}
}
