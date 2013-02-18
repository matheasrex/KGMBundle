<?php

namespace KGMBundle\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * class for testing twig methods
 */
class TwigFilterToArrayTest extends WebTestCase
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
		$this->method = new \KGMBundle\Twig\TwigFilter\TwigFilterToArray($twigExtension);
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testToArrayDefaultDelimiter()
	{
		$array = $this->method->process(array('toArray', "asdf\nghjk\nqwertz"));
		$this->assertInternalType("array", $array);
		$this->assertContainsOnly("string", $array);
		$this->assertContains("ghjk", $array);
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testToArrayGivenDelimiter()
	{
		$array = $this->method->process(array('toArray', "asdf|ghjk|qwertz", "|"));
		$this->assertInternalType("array", $array);
		$this->assertContainsOnly("string", $array);
		$this->assertContains("ghjk", $array);
	}
}
