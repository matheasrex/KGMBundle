<?

namespace KGMBundle\Part;

use \Symfony\Component\Console\Output\OutputInterface;

/**
 * class to handle text files
 */
class File implements OutputInterface
{
	/**
	 * @var const SAVE_AUTO Should save on every data change
	 */
	CONST SAVE_AUTO = 1;
	
	/**
	 * @var const SAVE_ON_DESTROY Should save on class destroy
	 */
	CONST SAVE_ON_DESTROY = 2;
	
	/**
	 * @var const LOAD_AUTO Should load on construct
	 */
	CONST LOAD_AUTO = 4;
	
	/**
	 * @var const SAVE_FORCE_UNMODIFIED Should save allways
	 */
	CONST SAVE_FORCE_UNMODIFIED = 8;
	
	/**
	 * @var string $path Path of text file
	 *
	 * @access protected
	 */
	protected $path = '';
	
	/**
	 * @var int $flag File settings
	 *
	 * @access protected
	 */
	protected $flag;
	
	/**
	 * @var string $content Content of text file
	 *
	 * @access protected
	 */
	protected $content = '';
	
	/**
	 * @var int $pointer Pointer for readln() method
	 *
	 * @access protected
	 */
	protected $pointer = 0;
	
	/**
	 * @var bool $modified True if content is modified since last save
	 *
	 * @access protected
	 */
	protected $modified;
	
	/**
	 * @var bool $enableAutoLoad Disable auto load at extended class contructors
	 *
	 * @access protected
	 */
	protected $enableAutoLoad = true;
	
	/**
	 * @var \KGMBundle\Handler\LocalFileHandler $fileHandler Handler object
	 *
	 * @access protected
	 */
	protected $fileHandler;
	
	/**
	 * public constructor
	 *
	 * @param string                                        $path    Path of text file
	 * @param \KGMBundle\Handler\HandlerInterface $handler Handler object
	 * @param int                                           $flag    Options
	 *
	 * @access public
	 * @link {path}
	 * @link {fileHandler}
	 * @link {flag}
	 */
	public function __construct($path, \KGMBundle\Handler\HandlerInterface $handler = null, $flag = self::LOAD_AUTO)
	{
		$this->path = $path;
		if ($handler == null) {
			$handler = new \KGMBundle\Handler\LocalFileHandler();
		}
		$this->fileHandler = $handler;
		$this->flag = $flag;
		
		if (
			($flag & self::LOAD_AUTO) &&
			$this->isReadable() &&
			$this->enableAutoLoad
		) {
			$this->loadFile();
		}
	}
	
	/**
	 * public destructor
	 * saves text file, if settings say
	 *
	 * @access public
	 */
	public function __destruct()
	{
		if ($this->flag & self::SAVE_ON_DESTROY) {
			$this->saveFile();
		}
	}
	
	/**
	 * object to string function
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function __toString()
	{
		return (string)\GlobalFunction::objectToString($this, get_object_vars($this));
	}
	
	/**
	 * determines if text file exists and is readable
	 *
	 * @return bool True, if readable
	 */
	public function isReadable()
	{
		return is_readable($this->path);
	}
	
	/**
	 * determines if text file is writeable
	 *
	 * @return bool True, if writeable
	 */
	public function isWriteable()
	{
		return is_writeable($this->path);
	}
	
	/**
	 * determines if text file is modified
	 *
	 * @return bool True, if modified since last save
	 */
	public function isModified()
	{
		return $this->modified;
	}
	
	/**
	 * load file content, overwrites previous state
	 *
	 * @access public
	 */
	public function load()
	{
		$this->loadFile();
		$this->modified = false;
	}
	
	/**
	 * save file content
	 *
	 * @access public
	 */
	public function save()
	{
		$this->saveFile();
	}
	
	/**
	 * return whole file content
	 * should call after $this->load(), or AUTO_LOAD
	 *
	 * @return string File content
	 *
	 * @access public
	 */
	public function read()
	{
		return $this->content;
	}
	
	/**
	 * return file content line by line
	 *
	 * @return string Actual file content line
	 *
	 * @access public
	 */
	public function readln()
	{
		if ($this->pointer >= strlen($this->content)) {
			return false;
		}
		
		$retval = "";
		if (($end = strpos($this->content, PHP_EOL, $this->pointer)) !== false) {
			$retval = substr($this->content, $this->pointer, $end - $this->pointer);
			$this->pointer = $end + strlen(PHP_EOL);
		} else {
			$retval = substr($this->content, $this->pointer);
			$this->pointer = strlen($this->content);
		}
		
		return $retval;
	}
	
	/**
	 * sets file pointer to the first line of file's content
	 *
	 * @access public
	 */
	public function reset()
	{
		$this->pointer = 0;
	}
	
	/**
	 * write message, or messages imploded with EOL
	 *
	 * @param mixed $messages String, or array of lines to write
	 * @param bool  $newline  Should write an EOL after the message(s)
	 * @param int   $type     Output type, should not use
	 *
	 * @access public
	 */
	public function write($messages, $newline = false, $type = OutputInterface::OUTPUT_NORMAL)
	{
		if (is_array($messages)) {
			$messages = implode(PHP_EOL, $messages);
		}
		$this->content .= $messages.($newline ? PHP_EOL : '');
		$this->autoSave();
	}
	
	/**
	 * write message, or messages imploded with EOL postfixed by an EOL
	 *
	 * @param mixed $messages String, or array of lines to write
	 * @param int   $type     Output type, should not use
	 *
	 * @access public
	 */
	public function writeln($messages, $type = OutputInterface::OUTPUT_NORMAL)
	{
		$this->write($messages, true, $type);
	}
	
	/**
	 * dummy OutputInterface method
	 *
	 * @param int $level Verbosity level
	 *
	 * @access public
	 */
	public function setVerbosity($level)
	{
	}
	
	/**
	 * dummy OutputInterface method
	 *
	 * @return int Verbosity level
	 *
	 * @access public
	 */
	public function getVerbosity()
	{
		return 0;
	}
	
	/**
	 * dummy OutputInterface method
	 *
	 * @param bool $decorated Is decorated
	 *
	 * @access public
	 */
	public function setDecorated($decorated)
	{
	}
	
	/**
	 * dummy OutputInterface method
	 *
	 * @return bool True if is decorated
	 *
	 * @access public
	 */
	public function isDecorated()
	{
		return false;
	}
	
	/**
	 * dummy OutputInterface method
	 *
	 * @param \Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter Formatter object
	 *
	 * @access public
	 */
	public function setFormatter(\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter)
	{
	
	}
	
	/**
	 * dummy OutputInterface method
	 *
	 * @return \Symfony\Component\Console\Formatter\OutputFormatterInterface Formatter object
	 *
	 * @access public
	 */
	public function getFormatter()
	{
		return null;
	}
	
	/**
	 * save text file, if settings say
	 *
	 * @access protected
	 */
	protected function autoSave()
	{
		$this->modified = true;
		if ($this->flag & self::SAVE_AUTO) {
			$this->saveFile();
		}
	}
	
	/**
	 * save text file
	 *
	 * @return bool True if save needed
	 *
	 * @access protected
	 */
	protected function saveFile()
	{
		if (
			$this->modified ||
			($this->flag & self::SAVE_FORCE_UNMODIFIED)
		) {
			$this->fileHandler->set($this->path, $this->content);
			$this->modified = false;
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * load text file
	 *
	 * @access protected
	 */
	protected function loadFile()
	{
		$this->content = $this->fileHandler->get($this->path);
		$this->modified = false;
	}
}
