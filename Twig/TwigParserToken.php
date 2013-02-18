<?php

namespace KGMBundle\Twig;

/**
 * base class for custom token parsers
 */
class TwigParserToken extends \Twig_TokenParser
{
	/**
	 * @var string $name Token name
	 *
	 * @access protected
	 */
	protected $name;
	
	/**
	 * @var TwigParserContainet $parserContainer Environment parser container
	 *
	 * @access protected
	 */
	protected $parserContainer;
	
	/**
	 * public constructor
	 *
	 * @param string              $name            Tag name
	 * @param TwigParserContainet $parserContainer Environment parser container
	 *
	 * @access protected
	 */
	public function __construct($name, $parserContainer)
	{
		$this->name = $name;
		$this->parserContainer = $parserContainer;
	}
	
	/**
	 * parse token
	 *
	 * @param Twig_Token $token Twig token object
	 *
	 * @return TwigParserNode Parsed token node
	 *
	 * @access public
	 */
	public function parse(\Twig_Token $token)
	{
		$lineno = $token->getLine();
		$header = '';
		if ($this->parser->getStream()->test(\Twig_Token::STRING_TYPE)) {
			$header = $this->parser->getStream()->expect(\Twig_Token::STRING_TYPE)->getValue();
		}

		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
		
		return new TwigParserNode($header, $body, $lineno, $this->getTag(), $this->parserContainer);
	}
	
	/**
	 * decide if end of current block reached
	 *
	 * @param Twig_Token $token Twig token object
	 *
	 * @return bool End of blocked is reached
	 *
	 * @access public
	 */
	public function decideBlockEnd(\Twig_Token $token)
	{
		return $token->test('end'.$this->name);
	}
	
	/**
	 * return Tag's name
	 *
	 * @return String Tag name
	 *
	 * @access public
	 */
	public function getTag()
	{
		return $this->name;
	}
}
