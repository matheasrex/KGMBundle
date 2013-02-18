<?php

namespace KGMBundle\Tests\Unit;

use KGMBundle\DataRepositoryItem;

/**
 * class for testing data repository
 */
class DataRepositoryItemTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * test item->__toString
	 *
	 * @access public
	 */
	public function testItemToString()
	{
		$item = new DataRepositoryItem(null);
		$itemDump = (string)$item;
		$this->assertContains("['PROTECTED']", $itemDump);
		$this->assertContains("['data']", $itemDump);
	}
	
	/**
	 * test item->__construct
	 *
	 * @access public
	 */
	public function testItemInit()
	{
		$item = new DataRepositoryItem("ASDF");
		$itemDump = (string)$item;
		$this->assertContains("['PROTECTED']", $itemDump);
		$this->assertContains("['data']", $itemDump);
		$this->assertContains("ASDF", $itemDump);
	}
	
	/**
	 * test item->getData
	 *
	 * @access public
	 */
	public function testItemGet()
	{
		$item = new DataRepositoryItem("ASDF");
		$this->assertInternalType("string", $item->getData());
		$this->assertEquals("ASDF", $item->getData());
	}
	
	/**
	 * test item->setData
	 *
	 * @access public
	 */
	public function testItemSet()
	{
		$item = new DataRepositoryItem(null);
		$this->assertNull($item->getData());
		$item->setData(42);
		$this->assertInternalType("int", $item->getData());
		$this->assertEquals(42, $item->getData());
	}
}
