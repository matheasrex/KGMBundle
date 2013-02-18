<?php

namespace KGMBundle\Export;

/**
 * Interface for exportable objects
 */
interface ExportableInterface
{
	/**
	 * Get exportable object content
	 */
	public function getContent();
	
	/**
	 * Get exportable object file name
	 */
	public function getFileName();
	
	/**
	 * Get exportable object mime type
	 */
	public function getMimeType();
}
