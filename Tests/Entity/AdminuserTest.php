<?php

namespace KGMBundle\Tests\Entity;

/**
 * class for testing Adminuser entity
 */
class AdminuserTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \KGMBundle\Entity\Adminuser $entity Entity object
	 *
	 * @protected
	 */
	protected $entity;
	
	/**
	 * set up test environment, create entity object
	 *
	 * @access public
	 */
	public function setUp()
	{
		$this->entity = new \KGMBundle\Entity\Adminuser();
	}
	
	/**
	 * test entity->set
	 *
	 * @access public
	 */
	public function testEntitySetFields()
	{
		$this->entity->id = 2;
		$this->entity->name = "asdf";
		$this->entity->phone = "003678936789";
		$this->entity->email = "a@b.c";
		$this->entity->login = "qwert";
		$this->entity->password = "pass";
		$this->entity->deleted = 1;
		$this->entity->costCenterId = 5;
		$this->entity->parent = 4;
		
		$entityDump = (string)$this->entity;
		
		$this->assertContains('asdf', $entityDump);
		$this->assertContains('003678936789', $entityDump);
		$this->assertContains('a@b.c', $entityDump);
		$this->assertContains('qwert', $entityDump);
		$this->assertContains("pass", $entityDump);
		$this->assertContains('1', $entityDump);
		$this->assertContains('2', $entityDump);
		$this->assertContains('5', $entityDump);
		$this->assertContains('4', $entityDump);
	}
}
