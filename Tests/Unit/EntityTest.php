<?php

namespace KGMBundle\Tests\Unit;

/**
 * class for testing entity
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * @var \KGMBundle\Entity $entity Entity object
	 */
	protected $entity;
	
	/**
	 * set up test environment, create entity object
	 */
	public function setUp()
	{
		$this->entity = new \KGMBundle\Entity();
	}
	
	/**
	 * test entity->__set
	 *
	 * @access public
	 *
	 * @expectedException \KGMBundle\Exception\Entity\MissingFieldException
	 */
	public function testEntitySetMissingField()
	{
		$this->entity->a = 2;
	}
	
	/**
	 * test entity->__set
	 *
	 * @access public
	 */
	public function testEntitySetField()
	{
		$this->entity->manager = new \StdClass();
		$this->entity->manager = null;
	}
	
	/**
	 * test entity->__get
	 *
	 * @access public
	 *
	 * @expectedException \KGMBundle\Exception\Entity\MissingFieldException
	 */
	public function testEntityGetMissingField()
	{
		$this->entity->a;
	}
	
	/**
	 * test entity->__get
	 *
	 * @access public
	 */
	public function testEntityGetField()
	{
		$this->assertNull($this->entity->manager);
	}
	
	/**
	 * test entity->__call
	 *
	 * @access public
	 */
	public function testEntityCallSetter()
	{
		$this->entity->__call('setManager', array(new \StdClass()));
		$this->entity->__set('manager', null);
	}
	
	/**
	 * test entity->__call
	 *
	 * @access public
	 */
	public function testEntityCallGetter()
	{
		$this->assertNull($this->entity->__call('getManager', null));
		$this->entity->manager = new \StdClass();
		$this->assertEquals($this->entity->__call('getManager', null), $this->entity->manager);
		$this->entity->manager = null;
	}
	
	/**
	 * test entity->__call
	 *
	 * @access public
	 *
	 * @expectedException \KGMBundle\Exception\Entity\MissingFunctionException
	 */
	public function testEntityCallMissing()
	{
		$this->entity->a();
	}
	
	/**
	 * test entity->__toString
	 *
	 * @access public
	 */
	public function testEntityToString()
	{
		$entityDump = (string)$this->entity;
		$this->assertContains("['PROTECTED']", $entityDump);
		$this->assertContains("['manager']", $entityDump);
	}
	
	/**
	 * test entity->getFunction
	 *
	 * @access public
	 */
	public function testEntityFunction()
	{
		$this->assertInstanceOf("\KGMBundle\EntityFunction", $this->entity->getFunction());
		$this->assertEquals($this->entity, $this->entity->getFunction()->getEntity());
	}
	
	/**
	 * test entity->testCall
	 *
	 * @access public
	 */
	public function testEntityTestCall()
	{
		$this->assertEquals(constant(get_class($this->entity).'::CALLABLE_PROPERTY'), $this->entity->testCall("getManager"));
		$this->assertEquals(constant(get_class($this->entity).'::CALLABLE_PROPERTY'), $this->entity->testCall("manager"));
		$this->assertEquals(constant(get_class($this->entity).'::CALLABLE_METHOD'), $this->entity->testCall("__toString"));
		$this->assertEquals(0, $this->entity->testCall("getEntity"));
	}
	
	/**
	 * test entity->search
	 *
	 * @access public
	 *
	 * @expectedException \KGMBundle\Exception\Entity\MissingEntityManagerException
	 */
	public function testEntitySearch()
	{
		$this->entity->search(array());
	}
	
	/**
	 * test entity->load
	 *
	 * @access public
	 *
	 * @expectedException \KGMBundle\Exception\Entity\MissingEntityManagerException
	 */
	public function testEntityLoad()
	{
		$this->entity->load(0);
	}
}
