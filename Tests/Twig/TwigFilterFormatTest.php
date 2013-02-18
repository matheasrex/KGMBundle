<?php

namespace KGMBundle\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * class for testing twig methods
 */
class TwigFilterFormatTest extends WebTestCase
{
	/**
	 * @var \KGMBundle\Twig\TwigMethod $method Method object
	 */
	protected $method;
	
	/**
	 * @var int $timestamp Unix timestamp
	 */
	protected $timestamp;
	
	
	/**
	 * set up test environment, create filter object
	 */
	public function setUp()
	{
		$client = static::createClient(array(
			'environment' => 'test',
		));
		$twigExtension = $client->getKernel()->getContainer()->get('twig.extension.kgm');
		$this->method = new \KGMBundle\Twig\TwigFilter\TwigFilterFormat($twigExtension);
		$this->timestamp = mktime(12, 34, 56, 10, 20, 2013);
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testFormatMissing()
	{
		$this->assertEquals("common.format.missing", $this->method->process(array('missingFormat', $this->timestamp)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testFormatDateTime()
	{
		$this->assertEquals("2013/10/20 12:34:56", $this->method->process(array('dateTimeFormat', $this->timestamp)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testFormatDateTimeWithObject()
	{
		$dateTime = new \DateTime();
		$dateTime->setTimestamp($this->timestamp);
		$this->assertEquals("2013/10/20 12:34:56", $this->method->process(array('dateTimeFormat', $dateTime)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testFormatDateTimeTz()
	{
		$this->assertEquals("2013/10/20 12:34:56+02:00", $this->method->process(array('dateTimeTzFormat', $this->timestamp)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testFormatDate()
	{
		$this->assertEquals("2013/10/20", $this->method->process(array('dateFormat', $this->timestamp)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testFormatTime()
	{
		$this->assertEquals("12:34:56", $this->method->process(array('timeFormat', $this->timestamp)));
	}
	
	/**
	 * test method->process
	 *
	 * @access public
	 */
	public function testFormatLongDate()
	{
		$this->assertEquals("2013. október 20.", $this->method->process(array('longDateFormat', $this->timestamp)));
	}
}
