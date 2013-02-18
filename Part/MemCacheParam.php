<?

namespace KGMBundle\Part;

/**
 * class for advanced memcache setter parameter
 */
class MemCacheParam
{
	/**
	 * @var mixed $data Memcache data
	 *
	 * @access protected
	 */
	protected $data;
	
	/**
	 * @var int $flag Memcache data flag
	 *
	 * @access protected
	 */
	protected $flag;
	
	/**
	 * @var int $expire Memcache data expire
	 *
	 * @access protected
	 */
	protected $expire;
	
	/**
	 * public constructor
	 *
	 * @param mixed $data   Memcache data
	 * @param int   $expire Memcache data expire
	 * @param int   $flag   Memcache data flag
	 *
	 * @access public;
	 * @link {data}
	 * @link {flag}
	 * @link {expire}
	 */
	public function __construct($data, $expire = 0, $flag = 0)
	{
		$this->data = $data;
		$this->flag = $flag;
		$this->expire = $expire;
	}
	
	/**
	 * return memcache data
	 *
	 * @return mixed Memcache data
	 *
	 * @access public
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * return memcache data expire
	 *
	 * @return int Memcache data expire
	 *
	 * @access public
	 */
	public function getExpire()
	{
		return $this->expire;
	}
	
	/**
	 * return memcache data flag
	 *
	 * @return int Memcache data flag
	 *
	 * @access public
	 */
	public function getFlag()
	{
		return $this->flag;
	}
}
