<?php

namespace KGMBundle\Tests\Unit;

/**
 * class for testing controller
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \KGMBundle\Controller $controller Controller object
	 */
	protected $controller;
	
	/**
	 * set up test environment, create controller object
	 */
	public function setUp()
	{
		$this->controller = new \KGMBundle\Controller();
	}
	
	/**
	 * test controller->__get
	 *
	 * @access public
	 *
	 * @expectedException \KGMBundle\Exception\Controller\MissingPropertyException
	 */
	public function testControllerGetMissingProperty()
	{
		$this->controller->a;
	}
	
	/**
	 * test controller->__toString
	 *
	 * @access public
	 */
	public function testControllerToString()
	{
		$controllerDump = (string)$this->controller;
		$this->assertContains("['PROTECTED']", $controllerDump);
		$this->assertContains("['currentBundle']", $controllerDump);
	}
}
