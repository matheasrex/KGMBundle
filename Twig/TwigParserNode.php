<?php

namespace KGMBundle\Twig;

/**
 * base class for custom token parser nodes
 */
class TwigParserNode extends \Twig_Node
{
	/**
	 * @var TwigParserContainer Environment parser container
	 *
	 * @access protected
	 */
	protected $parserContainer;
	
	/**
	 * public constructor
	 *
	 * @param string              $header          Tag html header
	 * @param object              $body            Tag body content
	 * @param int                 $lineno          Line number where the tag started
	 * @param string              $tag             Tag name
	 * @param TwigParserContainer $parserContainer Environment parser container
	 *
	 * @access public
	 */
	public function __construct($header, $body, $lineno, $tag, $parserContainer)
	{
		parent::__construct(array('body' => $body), array('header' => $header), $lineno, $tag);
		$this->parserContainer = $parserContainer;
	}
	
	/**
	 * compile tag
	 *
	 * @param Twig_Compiler $compiler Twig compiler
	 *
	 * @access public
	 */
	public function compile(\Twig_Compiler $compiler)
	{
		$compiler->addDebugInfo($this);
		$this->parserContainer->callParser(
			array(
				$this->tag,
				$this->getNode('body'),
				$this->getAttribute('header'),
				$compiler,
			)
		);
	}
}
