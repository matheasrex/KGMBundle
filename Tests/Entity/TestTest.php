<?php

namespace KGMBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use KGMBundle\Entity\Test;

/**
 * class for testing Test entity
 */
class TestTest extends WebTestCase
{
	/**
	 * @var \KGMBundle\Entity\Test $entity Entity object
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
		$client = static::createClient(array(
			'environment' => 'test',
		));
		$entityManager = $client->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
		$this->entity = new Test();
		$this->entity->setEntityManager($entityManager);
	}
	
	/**
	 * test entity->set
	 *
	 * @access public
	 */
	public function testEntitySetFields()
	{
		$this->entity->id = 8;
		$this->entity->text = "setfield";
		$this->entity->value = 5;
		$this->entity->weight = 100;
		$this->entity->created = new \DateTime();
		
		$entityDump = (string)$this->entity;
		
		$this->assertContains('8', $entityDump);
		$this->assertContains('setfield', $entityDump);
		$this->assertContains('5', $entityDump);
		$this->assertContains('100', $entityDump);
		$this->assertContains('\DateTime', $entityDump);
	}
	
	/**
	 * test entity->save
	 *
	 * @access public
	 */
	public function testEntitySave()
	{
		$this->entity->text = "save";
		$this->entity->value = 5;
		$this->entity->weight = 100;
		$this->entity->created = new \DateTime();
		$this->entity->save();
		$this->assertFalse(is_null($this->entity->id));
	}
	
	/**
	 * test entity->load
	 *
	 * @access public
	 */
	public function testEntityLoad()
	{
		$this->entity->text = "load";
		$this->entity->value = 5;
		$this->entity->weight = 100;
		$this->entity->created = new \DateTime();
		$this->entity->save();
		$loadedEntity = $this->entity->load($this->entity->id);
		$this->assertFalse(is_null($loadedEntity));
	}
	
	/**
	 * test entity->delete
	 *
	 * @access public
	 */
	public function testEntityDelete()
	{
		$this->entity->text = "delete";
		$this->entity->value = 5;
		$this->entity->weight = 100;
		$this->entity->created = new \DateTime();
		$this->entity->save();
		$loadedEntity = $this->entity->load($this->entity->id);
		$this->assertFalse(is_null($loadedEntity));
		$loadedEntity->delete();
		$deletedEntity = $this->entity->load($this->entity->id);
		$this->assertNull($deletedEntity);
	}
	
	/**
	 * test entity->search
	 *
	 * @access public
	 */
	public function testEntitySearch()
	{
		$count = 5;
		$rand = mt_rand(1000, 9999);
		for ($i = 1; $i <= $count; $i++) {
			$entity = new Test();
			$entity->setEntityManager($this->entity->getEntityManager());
			$entity->text = "search ".$i;
			$entity->value = $rand;
			$entity->weight = $rand;
			$entity->created = new \DateTime();
			$entity->save();
		}
		$searchResult = $this->entity->search(array(
			'where' => array(
				'text' => array('search %', 'LIKE'),
				'value' => $rand,
				'weight' => $rand,
			)
		));
		$this->assertCount(5, $searchResult);
		foreach ($searchResult as $entity) {
			$entity->delete();
		}
	}
	
	/**
	 * test entity->fullValue
	 *
	 * @access public
	 */
	public function testEntityFullValue()
	{
		$this->entity->value = 5;
		$this->entity->weight = 100;
		$this->assertEquals(5, $this->entity->fullValue());
		$this->entity->value = 7;
		$this->assertEquals(7, $this->entity->fullValue());
		$this->entity->weight = 50;
		$this->assertEquals(3.5, $this->entity->fullValue());
		$this->entity->weight = 150;
		$this->entity->value = 5;
		$this->assertEquals(7.5, $this->entity->fullValue());
	}
	
	/**
	 * test entity->stringValue
	 *
	 * @access public
	 */
	public function testEntityStringValue()
	{
		$this->entity->text = "textValue";
		$this->entity->value = 5;
		$this->entity->weight = 150;
		$this->assertEquals('textValue (7.5)', $this->entity->stringValue());
	}
	
	/**
	 * test entity->prefixedText
	 *
	 * @access public
	 */
	public function testEntityPrefixedText()
	{
		$this->entity->text = "textValue";
		$this->assertEquals('prefixtextValue', $this->entity->prefixedText('prefix'));
	}
}
