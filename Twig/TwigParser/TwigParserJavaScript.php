<?php

namespace KGMBundle\Twig\TwigParser;

use KGMBundle\Twig\TwigParser;

/**
 * twig entity fuinction caller method class
 */
class TwigParserJavaScript extends TwigParser
{
	/**
	 * @var array $parameters parameter names
	 *
	 * @access protected
	 */
	protected $parameters = array(
		'body' => null,
		'header' => '',
		'compiler' => null,
	);
	
	/**
	 * compile javascript block
	 *
	 * @param array $params parameters
	 *
	 * @access public
	 */
	public function process($params)
	{
		$this->createParams($params);
		
		$params['compiler']
			->write("echo '<script type=\"text/javascript\" ".$params['header'].">\n';\n")
			->write("echo '/*<![CDATA[*/\n';\n")
			->subcompile($params['body'])
			->write("echo '/*]]>*/\n';\n")
			->write("echo '</script>\n';\n");
	}
}
