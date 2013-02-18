<?php

namespace KGMBundle\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * class for testing twig methods
 */
class TwigFilterTransStyleTest extends WebTestCase
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
		$this->method = new \KGMBundle\Twig\TwigFilter\TwigFilterTransStyle($twigExtension);
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testTransStyleMissing()
	{
		$this->assertEquals("mail.missingtranslabel", $this->method->process(array('transStyle', "mail.missingtranslabel")));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testTransStyle()
	{
		$this->assertFalse("mail.content" == $this->method->process(array('transStyle', "mail.content")));
	}
}
