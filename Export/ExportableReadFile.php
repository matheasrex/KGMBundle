<?php

namespace KGMBundle\Export;

/**
 * Class for exportable object created from existing file
 */
class ExportableReadFile implements \KGMBundle\Export\ExportableInterface
{
	/**
	 * @var string original filename
	 */
	protected $filename;
	
	/**
	 * Class constructor
	 * 
	 * @param string $filename original file name
	 * 
	 * @access public
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}
	
	/**
	 * returns content of file
	 * 
	 * @return string file content
	 * 
	 * @access public
	 */
	public function getContent()
	{
		return file_get_contents($this->filename);
	}
	
	/**
	 * Return file name (without directory)
	 * 
	 * @return string file name
	 * 
	 * @access public
	 */
	public function getFileName()
	{
		return basename($this->filename);
	}
	
	/**
	 * Return file's mime type
	 * 
	 * @return string mime type
	 * 
	 * @access public
	 */
	public function getMimeType()
	{
		$fi = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($fi, $this->filename);
		finfo_close($fi);
		
		return $mimeType;
	}
}
