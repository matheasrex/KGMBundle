<?

namespace KGMBundle\Part;

use KGMBundle\Repository\RepositoryInterface;

/**
 * class for cachefile
 */
class CacheFile extends File implements RepositoryInterface
{
	/**
	 * @var const STORAGE_LOCAL Sub path of local cache directory
	 */
	CONST STORAGE_LOCAL = 'local';
	
	/**
	 * @var const STORAGE_SHARED Sub path of shared cache directory
	 */
	CONST STORAGE_SHARED = 'shared';
	
	/**
	 * @var const STORAGE_BASEPATH Base path of cache directory
	 */
	CONST STORAGE_BASEPATH = 'logs/tmp/cache/';
	
	/**
	 * @var bool $isShared True if cache is shared
	 *
	 * @access protected
	 */
	protected $isShared = false;
	
	/**
	 * @var array $cache Cache content
	 *
	 * @access protected
	 */
	protected $cache = array();
	
	/**
	 * public constructor
	 *
	 * @param string                                        $path    Path of text file
	 * @param \KGMBundle\Handler\HandlerInterface $handler Handler object
	 * @param int                                           $flag    Options
	 *
	 * @access public
	 */
	public function __construct($path, \KGMBundle\Handler\HandlerInterface $handler = null, $flag = self::LOAD_AUTO)
	{
		$enableAutoLoad = $this->enableAutoLoad;
		$this->enableAutoLoad = false;
		parent::__construct($this->getCachePath($path), $handler, $flag);
		$this->enableAutoLoad = $enableAutoLoad;
		
		if (
			($this->flag & self::LOAD_AUTO) &&
			$this->isReadable() &&
			$this->enableAutoLoad
		) {
			$this->loadCache();
		}
	}
	
	/**
	 * public destructor
	 * saves cache, if settings say
	 *
	 * @access public
	 */
	public function __destruct()
	{
		if ($this->flag & self::SAVE_ON_DESTROY) {
			$this->saveCache();
		}
	}
	
	/**
	 * determines if cache file is shared
	 *
	 * @return bool True if is shared
	 *
	 * @access public
	 */
	public function isShared()
	{
		return $this->isShared;
	}
	
	/**
	 * set if cache file is shared
	 *
	 * @param bool $value True if is shared
	 *
	 * @access public
	 * @link {isShared}
	 */
	public function setIsShared($value)
	{
		$this->isShared = $value;
	}
	
	/**
	 * determines if item existst in cache file
	 *
	 * @param string $key Item's key
	 *
	 * @return bool True if item exists
	 *
	 * @access public
	 */
	public function has($key)
	{
		return (isset($this->cache[$key]));
	}
	
	/**
	 * add new item to cache file
	 *
	 * @param string $key  Item's key
	 * @param mixed  $data Item
	 *
	 * @access public
	 */
	public function set($key, $data)
	{
		$this->cache[$key] = $data;
		$this->autoSave();
	}
	
	/**
	 * return item
	 *
	 * @param string $key Item's key
	 *
	 * @return mixed Item
	 * @throws \KGMBundle\Exception\Repository\MissingKeyException When key does not exists
	 *
	 * @access public
	 */
	public function get($key)
	{
		$this->throwIfNotExists($key);
		
		return $this->cache[$key];
	}
	
	/**
	 * remove item at key
	 *
	 * @param string $key Item's key
	 *
	 * @throws \KGMBundle\Exception\Repository\MissingKeyException When key does not exists
	 *
	 * @access public
	 */
	public function remove($key)
	{
		$this->throwIfNotExists($key);
		unset($this->cache[$key]);
		$this->autoSave();
	}
	
	/**
	 * throws exception if key does not exists
	 *
	 * @param string $key repo
	 *
	 * @throws \KGMBundle\Exception\Repository\MissingKeyException When key does not exists
	 *
	 * @access public
	 */
	public function throwIfNotExists($key)
	{
		if (!$this->has($key)) {
			throw new \KGMBundle\Exception\Repository\MissingKeyException(get_class($this)." has no ".$key." repository");
		}
	}
	
	/**
	 * load cache file
	 *
	 * @access public
	 */
	public function load()
	{
		$this->loadCache();
	}
	
	/**
	 * save cache file
	 *
	 * @access public
	 */
	public function save()
	{
		$this->saveCache();
	}
	
	/**
	 * generate cache file path
	 *
	 * @param string $path     Cache path
	 * @param bool   $filePath Should generate File or Memcache path
	 *
	 * @return string Cache file path
	 *
	 * @access protected
	 */
	protected function getCachePath($path, $filePath = true)
	{
		return ($filePath ? __DIR__.'/../../../' : '').self::STORAGE_BASEPATH
			.($this->isShared ? self::STORAGE_SHARED : self::STORAGE_LOCAL).'/'.$path
			.($filePath ? '.ser' : '')
		;
	}
	
	/**
	 * save cache file, if settings say
	 *
	 * @access protected
	 */
	protected function autoSave()
	{
		if ($this->flag & self::SAVE_AUTO) {
			$this->saveCache();
		} else {
			$this->modified = true;
		}
	}
	
	/**
	 * save cache file
	 *
	 * @return bool True if save needed
	 *
	 * @access protected
	 */
	protected function saveCache()
	{
		$this->content = serialize($this->cache);
		
		return $this->saveFile();
	}
	
	/**
	 * load cache file
	 *
	 * @access protected
	 */
	protected function loadCache()
	{
		$this->loadFile();
		$this->cache = unserialize($this->content);
		$this->content = '';
	}
}
