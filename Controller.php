<?php

namespace KGMBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

/**
 * global controller class for all controllert to be extended from
 */
class Controller extends BaseController
{
	/**
	 * list of template variables
	 *
	 * @var array
	 *
	 * @access protected
	 */
	protected $templateVariables = array();
	
	/**
	 * name of the current bundle
	 *
	 * @var array
	 *
	 * @access protected
	 */
	protected $currentBundle = array();
	
	/**
	 * current bundle name parts
	 * @var array
	 */
	protected $currentBundleNamePart = array();
	/**
	 * it is true when we are in a dev envirionment
	 *
	 * @var boolean
	 *
	 * @access protected
	 */
	protected $isDevEnvironment = null;
	
	/**
	 * public constructor
	 *
	 * @param object $container Container that implement ContainerInterface
	 *
	 * @access public
	 * @link {container}
	 */
	public function __construct($container = null)
	{
		$this->container = $container;
	}

	/**
	 * getter function
	 *
	 * @param string $name Name of prop to get
	 * 
	 * @return mixed
	 *
	 * @access public
	 */
	public function __get($name)
	{
		$func = 'get'.ucfirst($name);
		if (method_exists($this, $func)) {
			return $this->$func();
		} else {
			throw new \KGMBundle\Exception\Controller\MissingPropertyException("Controller has no $name property");
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
	 * function to simplyfy mail sending
	 * @param int    $userbaseId   Id of user
	 * @param string $templateName Name of tpl
	 *
	 * @return obj
	 */
	public function initMailer($userbaseId, $templateName)
	{
		$templateName = $this->getCurrentBundle(true).':Mail:'.$templateName;
		$mailerClass = '\\'.$this->getCurrentBundle().'\\Part\\Mailer';
		
		return new $mailerClass($this, $userbaseId, $templateName);
	}
	
	/**
	 * return true if we are in a development environment
	 * @return bool is dev environment
	 * @access public
	 */
	public function isDev()
	{
		if (is_null($this->isDevEnvironment)) {
			$this->isDevEnvironment = (bool)($this->get('framework.repository')->static_settings['is_development']);
		}
		
		return $this->isDevEnvironment;
	}
	
	/**
	 * funtion to get the name of current bundle
	 *
	 * @param bool $withPage With Bundle name or not
	 * 
	 * @return string
	 *
	 * @access public
	 */
	public function getCurrentBundle($withPage = false)
	{
		if (isset($this->currentBundle[$withPage])) {
			return $this->currentBundle[$withPage];
		}
		$bundleNameArray = $this->getBundleNameArray();
		$this->currentBundle = array(
			true => $bundleNameArray['group'].$bundleNameArray['bundle'],
			false => $bundleNameArray['bundle'],
		);
		
		return $this->currentBundle[$withPage];
	}
	
	/**
	 * Success result function stores the success traslator label in session and shows
	 * it on the next request
	 *
	 * @param string $urlName Route name
	 * @param string $label   Traslator label of success message
	 *
	 * @access protected
	 */
	protected function successRedirect($urlName, $label)
	{
		$this->getRequest()->getSession()->getFlashBag()->add('success', $label);
		return $this->redirect($this->generateUrl($urlName));
	}
	
	/**
	 * funtion to get db object - Doctrine\DBAL\Connection
	 *
	 * @return obj
	 *
	 * @access protected
	 */
	protected function getDb()
	{
		if (!isset($this->db)) {
			$this->db = $this->getDoctrine()->getEntityManager()->getConnection();
		}
		
		return $this->db;
	}
	
	/**
	 * merge all parameters into one array, or returns the first parameter, which is not array (tipically at redirect)
	 *
	 * @return mixed
	 *
	 * @access protected
	 */
	protected function mergeResult()
	{
		$mergeables = func_get_args();
		if (empty($mergeables)) {
			return array();
		}
		
		foreach ($mergeables as $mergeable) {
			if (!is_array($mergeable)) {
				return $mergeable;
			}
		}
		
		$result = array();
		foreach ($mergeables as $mergeable) {
			if (!empty($mergeable)) {
				$result = array_merge($result, $mergeable);
			}
		}
		
		return $result;
	}
	/**
	 * simple form method
	 * guesses form type and entity class from parameter, handles form save methods
	 *
	 * @param string                                    $name    Form name
	 * @param \Symfony\Component\HttpFoundation\Request $request Request obj
	 * @param array                                     $options Options for form generation and redirect
	 *
	 * @return $form obj
	 *
	 * @access protected
	 */
	protected function simpleForm($name, \Symfony\Component\HttpFoundation\Request $request, $options = array())
	{
		$typeClassName = '\\'.$this->getCurrentBundle().'\Form\\'.ucfirst($name).'Type';
		$entityClassName = '\\'.$this->getCurrentBundle().'\Entity\\'.ucfirst($name);
		
		$entity = new $entityClassName();
		$type = new $typeClassName($this, $entity);
		
		if (!empty($options['entity'])) {
			foreach ($options['entity'] as $field => $value) {
				$entity->$field = $value;
			}
		}
		
		if (isset($options['id'])) {
			$type->load($options['id']);
		}
		
		$result = array(
			'type' => $type,
			'entity' => $type->getEntity(),
		);
		
		if ($request->getMethod() == 'POST') {
			$type->bind($request);
			if ($type->save($result) && !isset($options['noredirect'])) {
				if (isset($options['flash'])) {
					$request->getSession()->getFlashBag()->add('success', $options['flash']);
				}
				return $this->redirect($this->createSuccessUrl($request, $options));
			}
		}
		
		$result['form'] = $type->getForm()->createView();
		
		return $result;
	}
	
	/**
	 * create success url
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request Request obj
	 * @param array                                     $options Options array
	 *
	 * @return string Success url
	 *
	 * @access protected
	 */
	protected function createSuccessUrl(\Symfony\Component\HttpFoundation\Request $request, $options = array())
	{
		if (isset($options['redirect'])) {
			return $options['redirect'];
		}
		
		$requestFormat = $request->getRequestFormat();
		$baseUrl = str_replace('.'.$requestFormat, '/', $request->getRequestUri());
		$successUrl = $baseUrl.'success';
		if ($baseUrl == $request->getRequestUri()) {
			return $successUrl.'/';
		}
		
		return $successUrl.'.'.$requestFormat;
	}
	
	/**
	 * gnerate url withing current bundle
	 *
	 * @param string $route      Short route name
	 * @param array  $urlPparams List of routing parameters
	 * 
	 * @return string
	 *
	 * @access protected
	 */
	protected function generateSelfUrl($route, $urlPparams = array())
	{
		$bundleNameArray = $this->getBundleNameArray();
		$params = array();
		foreach (array(
			'group',
			'site',
			'controller'
		) as $needed) {
			$params[] = $bundleNameArray[$needed];
		};
		
		return $this->generateUrl(
			implode('_', array_map('strtolower', $params)).'_'.$route,
			$urlPparams
		);
	}
	/**
	 * get the parts of bundle name
	 *
	 * @return array
	 *
	 * @link getBundleNameArray
	 *
	 * @access protected
	 */
	protected function getBundleNameArray()
	{
		if ($this->currentBundleNamePart === array()) {
			$this->generateBundleNameArray();
		}
		
		return $this->currentBundleNamePart;
	}
	
	/**
	 * gnerate parts of bundle name
	 * 
	 * @access protected
	 */
	protected function generateBundleNameArray()
	{
		$controller = $this->getRequest()->attributes->get('_controller');
		$splitController = explode('\\', $controller);
		$this->currentBundleNamePart = array(
			'group' => '',
			'bundle' => $splitController[0],
			'site' => str_replace('Bundle', '', $splitController[0]),
			'controller' => preg_replace("/Controller.*$/", '', $splitController[3]),
		);
	}
}
