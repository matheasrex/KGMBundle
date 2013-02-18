<?

namespace KGMBundle\Handler;

/**
 * sceleton class for handler classes
 */
interface HandlerInterface
{
	/**
	 * sceleton for item getter method
	 *
	 * @param string $key Item's key
	 *
	 * @return mixed Item
	 *
	 * @access public
	 */
	public function get($key);
	
	/**
	 * sceleton for item setter method
	 *
	 * @param string $key  Item's key
	 * @param mixed  $data New item
	 *
	 * @access public
	 */
	public function set($key, $data);
	
	/**
	 * sceleton for existing item remover method
	 *
	 * @param string $key Item's key
	 *
	 * @access public
	 */
	public function remove($key);
}
