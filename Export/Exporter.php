<?php

namespace KGMBundle\Export;

/**
 * Exporter class - exports an ExportableInterface object to screemn or file
 */
class Exporter
{
	/**
	 * @var \KGMBundle\Export\ExportableInterface exportable object
	 */
	protected $exportable;
	
	/**
	 * Exporter constructor
	 * 
	 * @param \KGMBundle\Export\ExportableInterface $exportable exportable object
	 * 
	 * @access public
	 */
	public function __construct(ExportableInterface $exportable)
	{
		$this->exportable = $exportable;
	}
	
	/**
	 * Returns exportable object as a Response (content + http headers)
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response exportable object as Response
	 * 
	 * @access public
	 */
	public function getResponse()
	{
		return new \Symfony\Component\HttpFoundation\Response($this->getOutput(), 200, $this->getHttpHeaders());
	}
	
	/**
	 * returns exportable object's content
	 * 
	 * @return string exportable object content
	 * 
	 * @access public
	 */
	public function getOutput()
	{
		return $this->exportable->getContent();
	}
	
	/**
	 * Returns exportable object's http headers
	 * 
	 * @return array Http headers
	 * 
	 * @access public
	 */
	public function getHttpHeaders()
	{
		return array(
			'Content-type' => $this->exportable->getMimeType(),
			'Content-disposition' => 'attachment; filename="'.$this->exportable->getFileName().'"',
		);
	}
	
	/**
	 * Save exportable object to file. Directory is given as parameter. Filename is ge from exportable object
	 * 
	 * @param string $dir directory without filename
	 * 
	 * @access public
	 */
	public function saveToFile($dir)
	{
		file_put_contents($dir.DIRECTORY_SEPARATOR.$this->exportable->getFileName(), $this->exportable->getContent());
	}
}
