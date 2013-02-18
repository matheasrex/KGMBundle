<?

namespace KGMBundle\Exception;

/**
 * class for main KGM framework exceptions
 */
abstract class KGMException extends \Exception
{
	/**
	 * global constructor
	 *
	 * @param string $text Exception text
	 *
	 * @access public
	 */
	public function __construct($text)
	{
		parent::__construct($text);
	}
}
