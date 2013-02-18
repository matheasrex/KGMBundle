<?

namespace KGMBundle\Handler;

use KGMBundle\Handler\HandlerInterface;

/**
 * class for Memcache handler
 */
class MemcacheHandler extends \Memcached implements HandlerInterface
{
	/**
	 * get item from memcache
	 *
	 * @param string $key Item's key
	 *
	 * @return mixed Data in memcache, null if missing
	 *
	 * @access public
	 */
	public function get($key, $cache_cb = '', &$cas_token = '')
	{
		return parent::get($key);
	}
	
	/**
	 * set item in memcache
	 *
	 * @param string $key  Item's key
	 * @param mixed  $data New item
	 *
	 * @return bool True on success
	 *
	 * @access public
	 */
	public function set($key, $data, $expiration = '')
	{
		if ($data instanceof \KGMBundle\Part\MemCacheParam) {
			return parent::set($key, $data->getData(), $data->getFlag(), $data->getExpire());
		}
		
		return parent::set($key, $data);
	}
	
	/**
	 * remove item from memcache
	 *
	 * @param string $key Item's key
	 *
	 * @return bool True on success
	 *
	 * @access public
	 */
	public function remove($key)
	{
		return parent::delete($key);
	}
}
