<?php

namespace KGMBundle\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * class for testing twig methods
 */
class TwigFunctionIsTest extends WebTestCase
{
	/**
	 * @var \KGMBundle\Twig\TwigMethod $method Method object
	 */
	protected $method;
	
	/**
	 * set up test environment, create filter object
	 */
	public function setUp()
	{
		$client = static::createClient(array(
			'environment' => 'test',
		));
		$twigExtension = $client->getKernel()->getContainer()->get('twig.extension.kgm');
		$this->method = new \KGMBundle\Twig\TwigFunction\TwigFunctionIs($twigExtension);
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsArray()
	{
		$this->assertTrue($this->method->process(array('isArray', array())));
		$this->assertFalse($this->method->process(array('isArray', 3)));
		$this->assertFalse($this->method->process(array('isArray', "asdf")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsBool()
	{
		$this->assertTrue($this->method->process(array('isBool', true)));
		$this->assertFalse($this->method->process(array('isBool', 0)));
		$this->assertFalse($this->method->process(array('isBool', "")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsDir()
	{
		$this->assertTrue($this->method->process(array('isDir', __DIR__)));
		$this->assertFalse($this->method->process(array('isDir', __FILE__)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsDouble()
	{
		$this->assertTrue($this->method->process(array('isDouble', 1.0)));
		$this->assertFalse($this->method->process(array('isDouble', 1)));
		$this->assertFalse($this->method->process(array('isDouble', "1.0")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsEmpty()
	{
		$this->assertTrue($this->method->process(array('isEmpty', array())));
		$this->assertTrue($this->method->process(array('isEmpty', 0)));
		$this->assertTrue($this->method->process(array('isEmpty', null)));
		$this->assertTrue($this->method->process(array('isEmpty', false)));
		$this->assertTrue($this->method->process(array('isEmpty', "")));
		$this->assertFalse($this->method->process(array('isEmpty', array(1))));
		$this->assertFalse($this->method->process(array('isEmpty', 1)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsFile()
	{
		$this->assertTrue($this->method->process(array('isFile', __FILE__)));
		$this->assertFalse($this->method->process(array('isFile', __DIR__)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsFloat()
	{
		$this->assertTrue($this->method->process(array('isFloat', (float)1.0)));
		$this->assertFalse($this->method->process(array('isFloat', 1)));
		$this->assertFalse($this->method->process(array('isFloat', "1.0")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsInt()
	{
		$this->assertTrue($this->method->process(array('isInt', 1)));
		$this->assertFalse($this->method->process(array('isInt', 1.0)));
		$this->assertFalse($this->method->process(array('isInt', "1")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsInteger()
	{
		$this->assertTrue((bool)$this->method->process(array('isInteger', 1)));
		$this->assertTrue((bool)$this->method->process(array('isInteger', "123")));
		$this->assertTrue((bool)$this->method->process(array('isInteger', 1.0)));
		$this->assertFalse((bool)$this->method->process(array('isInteger', "123f")));
		$this->assertFalse((bool)$this->method->process(array('isInteger', "f")));
		$this->assertFalse((bool)$this->method->process(array('isInteger', ".")));
		$this->assertFalse((bool)$this->method->process(array('isInteger', "-1")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsIntegerNegative()
	{
		$this->assertTrue((bool)$this->method->process(array('isInteger', -1, true)));
		$this->assertTrue((bool)$this->method->process(array('isInteger', "-123", true)));
		$this->assertTrue((bool)$this->method->process(array('isInteger', -1.0, true)));
		$this->assertFalse((bool)$this->method->process(array('isInteger', "-123f", true)));
		$this->assertFalse((bool)$this->method->process(array('isInteger', "-f", true)));
		$this->assertFalse((bool)$this->method->process(array('isInteger', "-.", true)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsMatch()
	{
		$this->assertTrue((bool)$this->method->process(array('isMatch', "/^.*[a-f].*$/D", "rutkuztmftrzi")));
		$this->assertFalse((bool)$this->method->process(array('isMatch', "/^.*[a-f].*$/D", "rutkuztmtrzi")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsNull()
	{
		$this->assertTrue($this->method->process(array('isNull', null)));
		$this->assertFalse($this->method->process(array('isNull', 0)));
		$this->assertFalse($this->method->process(array('isNull', false)));
		$this->assertFalse($this->method->process(array('isNull', "")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsNumeric()
	{
		$this->assertTrue($this->method->process(array('isNumeric', 1)));
		$this->assertTrue($this->method->process(array('isNumeric', "1")));
		$this->assertTrue($this->method->process(array('isNumeric', "1.0")));
		$this->assertTrue($this->method->process(array('isNumeric', "0xadf")));
		$this->assertFalse($this->method->process(array('isNumeric', "adf")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testIsString()
	{
		$this->assertTrue($this->method->process(array('isString', "1")));
		$this->assertTrue($this->method->process(array('isString', "")));
		$this->assertFalse($this->method->process(array('isString', 1)));
	}
}
