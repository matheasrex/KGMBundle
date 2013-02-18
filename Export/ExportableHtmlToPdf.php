<?php

namespace KGMBundle\Export;

/**
 * Class for creating exportable pdf file via TCPDF from HTML template
 */
class ExportableHtmlToPdf extends \TCPDF implements \KGMBundle\Export\ExportableInterface
{
	/**
	 * @var string HTML template name
	 */
	protected $templateName;
	
	/**
	 * @var \KGMBundle\Controller controller
	 */
	protected $controller;
	
	/**
	 * @var array variables passed to template
	 */
	protected $templateParameters;
	
	/**
	 * Class constructor
	 * 
	 * @param \KGMBundle\Controller $controller         controller
	 * @param string                          $templateName       html template filename
	 * @param array                           $templateParameters template variables
	 * 
	 * @access public
	 */
	public function __construct(\KGMBundle\Controller $controller, $templateName, $templateParameters)
	{
		$this->controller = $controller;
		$this->templateName = $templateName;
		$this->templateParameters = $templateParameters;
	}
	
	/**
	 * Return PDF output
	 * 
	 * @return string output
	 * 
	 * @access public
	 */
	public function getContent()
	{
		parent::__construct('P', 'mm', 'A4', true, 'UTF-8');
		$this->AddPage();
		$this->SetFont('freesans', '', 8);
		$templateOutput = $this->controller->renderView($this->templateName, $this->templateParameters);
		$this->writeHTML($templateOutput);
		$entity = $this->entity;
		$output = $this->Output('', 'S');
		$this->entity = $entity;
		
		return $output;
	}
	
	/**
	 * Return file name
	 * 
	 * @return sting file name
	 * 
	 * @access public
	 */
	public function getFileName()
	{
		return 'export.pdf';
	}
	
	/**
	 * Return mime type
	 * 
	 * @return string mime type
	 * 
	 * @access public
	 */
	public function getMimeType()
	{
		return 'application/pdf';
	}
}
