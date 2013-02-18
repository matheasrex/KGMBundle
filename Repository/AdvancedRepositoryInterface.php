<?

namespace KGMBundle\Repository;

/**
 * sceleton class for advanced repository classes
 */
interface AdvancedRepositoryInterface extends RepositoryInterface
{
	/**
	 * sceleton for key lister method
	 *
	 * @return array List of keys
	 *
	 * @access public
	 */
	public function keys();
	
	/**
	 * sceleton for new item adding method
	 *
	 * @param string $key  New item's key
	 * @param mixed  $data New item
	 *
	 * @access public
	 */
	public function add($key, $data);
	
	/**
	 * sceleton for existing item changer method
	 *
	 * @param string $key  Item's key
	 * @param mixed  $data New item
	 *
	 * @access public
	 */
	public function change($key, $data);
	
	/**
	 * sceleton for existing item mover method
	 *
	 * @param string $key    Item's key
	 * @param string $newKey Item's new key
	 *
	 * @access public
	 */
	public function move($key, $newKey);
	
	/**
	 * sceleton for existing item cloner method
	 *
	 * @param string $key    Item's key
	 * @param string $newKey New item key
	 *
	 * @access public
	 */
	public function copy($key, $newKey);
	
	/**
	 * sceleton for whole repository cleaner method
	 *
	 * @access public
	 */
	public function clear();
	
	/**
	 * sceleton for existing item checker method
	 *
	 * @param string $key Item's key
	 *
	 * @access public
	 */
	public function throwIfExists($key);
}
