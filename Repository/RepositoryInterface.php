<?

namespace KGMBundle\Repository;

use KGMBundle\Handler\HandlerInterface;

/**
 * sceleton class for repository classes
 */
interface RepositoryInterface extends HandlerInterface
{
	/**
	 * sceleton for item existence check method
	 *
	 * @param string $key Item's key
	 *
	 * @return bool True if item exists
	 *
	 * @access public
	 */
	public function has($key);
	
	/**
	 * sceleton for non existing item checker method
	 *
	 * @param string $key Item's key
	 *
	 * @access public
	 */
	public function throwIfNotExists($key);
}
