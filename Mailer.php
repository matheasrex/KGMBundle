<?php

namespace KGMBundle;

/**
 * class for sending mailer via swiftmailer
 */
abstract class Mailer
{
	/**
	 * @var \Swift_Message $message Swift_Message instance
	 *
	 * @access protected
	 */
	protected $message;
	
	/**
	 * @var Symfony\Bundle\FrameworkBundle\Controller\Controller $controller controller object
	 *
	 * @access protected
	 */
	protected $controller;
	
	/**
	 * @var array $templateParameters template parameters
	 *
	 * @access protected
	 */
	protected $templateParameters;
	
	/**
	 * @var string $senderEmail sender email address
	 *
	 * @access protected
	 */
	protected $senderEmail = '';
	
	/**
	 * @var string $template template to use
	 *
	 * @access protected
	 */
	protected $template = '';
	
	/**
	 * @var bool $isHtmlBody body is html or text, defaults to html
	 *
	 * @access protected
	 */
	protected $isHtmlBody = true;
	
	/**
	 * @var string $layout layout
	 *
	 * @access protected
	 */
	protected $layout = 'kgm';
	
	/**
	 * @var bool $sendToDeveloper if environment is dev, mail have to send to the developer's mail address
	 *
	 * @access protected
	 */
	protected $sendToDeveloper = true;
	
	/**
	 * @var string $originalMailTarget original mail target iof sendToDeveloper == true
	 *
	 * @access protected
	 */
	protected $originalMailTarget = '';

	/**
	 * global contructor
	 *
	 * @param Symfony\Bundle\FrameworkBundle\Controller\Controller $controller Controller class
	 * @param int                                                  $userbaseId User id
	 * @param string                                               $template   Template file name
	 *
	 * @access public
	 */
	public function __construct(Controller $controller, $userbaseId = null, $template = '')
	{
		$this->controller = $controller;
		$this->senderEmail = $controller->get('framework.repository')->getConstants()->getDefaultMailerSenderAddress();
		$this->message = \Swift_Message::newInstance()
			->setFrom($this->senderEmail)
			->setCharset($controller->get('framework.repository')->getConstants()->getDefaultMailerCharSet());
		if ($userbaseId) {
			$this->loadTarget($userbaseId);
		}
		$this->template = $template;		
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
	 * loads the user from database
	 *
	 * @param int $userbaseId User id
	 *
	 * @access public
	 */
	public function loadTarget($userbaseId)
	{
		if (
			$this->controller->isDev() &&
			$this->sendToDeveloper &&
			!is_null($developer = $this->controller->get('framework.repository')->getConstants()->getDeveloper()) &&
			($developerEmail = $developer->getEmail())
		) {
			$this->originalMailTarget = $userbaseId;
			$this->message->setTo($developerEmail);
		} elseif (\GlobalFunction::isInteger($userbaseId)) {
			$userData = $this->loadTargetById($userbaseId);
			if ($userData) {
				$this->message->setTo($userData->email);
				$templateParameters['userData'] = $userData;
			} else {
				\ErrorHandler::raiseError('Mailer target not found ('.$userbaseId.')');
			}
		} else {
			if (strpos($userbaseId, ',') !== false) {
				$userbaseId = explode(',', $userbaseId);
			}
			$this->message->setTo($userbaseId);
		}
	}
	
	/**
	 * load target from DB by target ID
	 *
	 * @param int $userbaseId Target ID
	 *
	 * @return Entity Target entity (must have email field)
	 *
	 * @access protected
	 */
	protected abstract function loadTargetById($userbaseId);
	
	/**
	 * loads the original mail target
	 * if don't want to send the mail directly to the developer
	 *
	 * @access public
	 */
	public function forceOriginalMailTarget()
	{
		$this->sendToDeveloper = false;
		$this->loadTarget($this->originalMailTarget);
	}
	
	/**
	 * assign parameter to the list
	 *
	 * @param string $name  Variable name
	 * @param mixed  $value Variable value
	 *
	 * @access public
	 */
	public function assign($name, $value = '')
	{
		if (is_array($name)) {
			$this->templateParameters = array_merge($this->templateParameters, $name);
		} else {
			$this->templateParameters[$name] = $value;
		}
	}
	
	/**
	 * setter function for Subject, From, To
	 *
	 * @param string $name  Prop name
	 * @param string $value Prop value
	 * 
	 * @return mixed
	 *
	 * @access protected
	 */
	public function __set($name, $value)
	{
		$func = 'set'.ucfirst($name);
		if (method_exists($this->message, $func)) {
			if ($name == 'charset') {
				$this->assign('layoutCharset', strtolower($value));
			}
			
			return $this->message->$func($value);
		} else {
			if (property_exists($this, lcfirst($name))) {
				$field = lcfirst($name);
				$this->$field = $value;
			} elseif (property_exists($this, 'is'.ucfirst($name))) {
				$field = 'is'.ucfirst($name);
				$this->$field = $value;
			} else {
				throw new \Exception("Mailer has no $name property");
			}
		}
	}
	
	/**
	 * forwards to getter/setter call, otherwise proxies would break
	 *
	 * @param string $name Prop name
	 * @param mixed  $args Props
	 *
	 * @return mixed
	 *
	 * @access public
	 */
	public function __call($name, $args)
	{
		if (preg_match('/^set[A-Z\d]/', $name)) {
			$this->__set(substr($name, 3), $args[0]);
		}
	}
	
	/**
	 * send message function
	 *
	 * @access public
	 */
	public function send()
	{
		$template = $this->template;
		$parameters = $this->templateParameters;
		if ($this->layout) {
			$template = $this->controller->getCurrentBundle(true).':Mail:'.strtolower($this->layout).".layout".(($this->isHtmlBody) ? '.html' : '.txt').".twig";
			$parameters['layoutRealTemplatePath'] = $this->template;
		}
		$body = $this->controller->renderView($template, $parameters);
		$bodyExp = explode("\n", $body);
		$subjectLine = "";
		foreach ($bodyExp as $lineNumber => $line) {
			if (strpos(trim($line), 'Subject:') === 0) {
				$subjectLine = $line;
				unset($bodyExp[$lineNumber]);
				break;
			}
		}
		if (!$subjectLine) {
			$subjectLine = $this->controller->get('translator')->trans('mailer.nosubject', array(), 'messages');
		}
		$this->message->setSubject(trim(str_replace('Subject:', '', $subjectLine)));
		if ($this->isHtmlBody) {
			$this->message->setBody(implode("\n", $bodyExp), 'text/html');
		} else {
			$this->message->setBody(implode("\n", $bodyExp));
		}
		$this->controller->get('mailer')->send($this->message);		
	}
	
	/**
	 * attach file to message
	 *
	 * @param string $filePath file path
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function attach($filePath)
	{
		return $this->message->attach(\Swift_Attachment::fromPath($filePath));
	}
	
}
