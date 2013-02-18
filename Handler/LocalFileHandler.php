<?

namespace KGMBundle\Handler;

use KGMBundle\Handler\HandlerInterface;

/**
 * class for Local File handler
 */
class LocalFileHandler extends \Memcached implements HandlerInterface
{
	/**
	 * read file content
	 *
	 * @param string $key filename
	 *
	 * @return file content
	 *
	 * @access public
	 */
	public function get($key, $cache_cb = '', &$cas_token = '')
	{
		return file_get_contents($key);
	}
	
	/**
	 * set item in memcache
	 *
	 * @param string $key  Filename
	 * @param mixed  $data File content
	 *
	 * @return bool True on success
	 *
	 * @access public
	 */
	public function set($key, $data, $expiration = '')
	{
		return file_put_contents($key, $data);
	}
	
	/**
	 * remove file
	 *
	 * @param string $key filename
	 *
	 * @return bool True on success
	 *
	 * @access public
	 */
	public function remove($key)
	{
		return unlink($key);
	}
}
