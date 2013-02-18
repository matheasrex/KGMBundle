<?php

namespace KGMBundle;

use Symfony\Bundle\TwigBundle\TwigEngine as BaseEngine;

/**
 * twig engine extender class
 */
class TwigEngine extends BaseEngine
{
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
	 * render a twig engine
	 *
	 * @param string $name          Tpl name
	 * @param array  $parameters    Tpl params
	 * @param bool   $sourceProcess Process or render
	 *
	 * @return render obj
	 *
	 * @access public
	 */
	public function render($name, array $parameters = array(), $sourceProcess = true)
	{		
		$extension = null;
		try {
			$extension = $this->environment->getExtension('main');
			$parameters['repository'] = $extension->getContainer()->get('framework.repository');
		} catch (\Exception $e) {
			null;
		}
		$parameters['layout'] = $this->loadLayoutFromName(
			$name,
			($extension != null && $extension->getContainer()->get('request')->isXmlHttpRequest())
		);
		
		if (strstr($name, '.json.')) {
			$parameters['json_data'] = $parameters;
			foreach (array('currentTemplate', 'layout') as $unneadKey) {
				unset($parameters['json_data'][$unneadKey]);
			}
		}
		
		$source = parent::render($name, $parameters);
		if ($sourceProcess) {
			if ($extension != null) {
				$blocksToReplace = array();
				
				$bundleLoader = $this->environment->getLoader();
				$this->environment->setLoader(new \Twig_Loader_Filesystem(__DIR__.'/Resource/view'));
				
				try {
					$blocksToReplace['<!-- '.$extension->twigConstant('JS_LOAD_BLOCK').' -->'] =
						$this->render(
							'Part/loadjs.html.twig',
							array(
								'js_list' => $extension->getLoadedJS(),
							),
							false
						);
				} catch (\Exception $ex) {
					null;
				}
				try {
					$blocksToReplace['<!-- '.$extension->twigConstant('CSS_LOAD_BLOCK').' -->'] =
						$this->render(
							'Part/loadcss.html.twig',
							array(
								'css_list' => $extension->getLoadedCSS(),
							),
							false
						);
				} catch (\Exception $ex) {
					null;
				}
				
				$this->environment->setLoader($bundleLoader);
				
				$source = str_replace(
					array_keys($blocksToReplace),
					array_values($blocksToReplace),
					$source
				);
			}
		}
		
		return $source;
	}
	
	/**
	 * get layout name from templatename
	 *
	 * @param string $templateName Tpl name
	 * @param bool   $isAjax       Ajax call
	 *
	 * @return string layout name
	 *
	 * @access protected
	 */
	protected function loadLayoutFromName($templateName, $isAjax = false)
	{
		$exploded = explode(':', $templateName);
		if (!isset($exploded[1])) {
			$exploded[1] = '';
		}
		
		$namePrefix = '';
		if ($isAjax) {
			$namePrefix = 'ajax';
		}
		
		return $exploded[0].':'.$exploded[1].':'.$namePrefix.'layout.html.twig';
	}
}

