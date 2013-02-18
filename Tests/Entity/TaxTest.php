<?php

namespace KGMBundle\Tests\Entity;

/**
 * class for testing Tax entity
 */
class TaxTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \KGMBundle\Entity\Tax $entity Entity object
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
		$this->entity = new \KGMBundle\Entity\Tax();
	}
	
	/**
	 * test entity->set
	 *
	 * @access public
	 */
	public function testEntitySetFields()
	{
		$this->entity->id = 2;
		$this->entity->split0 = "12345678";
		$this->entity->split1 = "9";
		$this->entity->split2 = "01";
		
		$entityDump = (string)$this->entity;
		
		$this->assertContains('12345678', $entityDump);
		$this->assertContains('9', $entityDump);
		$this->assertContains('01', $entityDump);
	}
	
	/**
	 * test entity->stringValue
	 *
	 * @access public
	 */
	public function testEntityStringValue()
	{
		$this->entity->id = 2;
		$this->entity->split0 = "12345678";
		$this->entity->split1 = "9";
		$this->entity->split2 = "01";
		
		$this->assertEquals('12345678-9-01', $this->entity->stringValue());
	}
}
