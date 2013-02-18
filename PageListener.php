<?php

namespace KGMBundle;

use Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener;

/**
 * The PageListener class handles the Page annotation.
 *
 * @author	 Gyurus Mate <mgyurus@oszkar.com>
 */
class PageListener extends TemplateListener
{
	/**
	 * @var obj $event we need to save FilterControllerEvent event for onKernelView function
	 *
	 * @access protected
	 */
	protected $event;
	
	/**
	 * public contructor
	 *
	 * @param array $params Init params
	 *
	 * @access public
	 */
	public function __construct($params)
	{
		parent::__construct($params['service_container']);
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
	 * Guesses the template name to render and its variables and adds them to
	 * the request object.
	 *
	 * @param FilterControllerEvent $event A FilterControllerEvent instance
	 *
	 * @access public
	 */
	public function onKernelController(\Symfony\Component\HttpKernel\Event\FilterControllerEvent $event)
	{
		if (!is_array($controller = $event->getController())) {
			return;
		}

		$request = $event->getRequest();
		
		$this->event = $event;
		
		if (!$configuration = $request->attributes->get('_template')) {
			return;
		}
		
		$template = $this->guessTemplateName($controller, $request, $configuration->get('engine'));

		$request->attributes->set('_template', $template);
	}
	
	/**
	 * Renders the template and initializes a new response object with the
	 * rendered template content.
	 *
	 * @param GetResponseForControllerResultEvent $event A GetResponseForControllerResultEvent instance
	 *
	 * @return mixed
	 *
	 * @access public
	 */
	public function onKernelView(\Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event)
	{
		$request = $event->getRequest();
		if (!is_array($controller = $this->event->getController())) {
			return;
		}
		
		/**
		 * if the annotation tells it's not called as Page() (must be Template()) then parent function has to be called
		 */
		$r = new \ReflectionObject($controller[0]);
		if (!strstr($r->getMethod($controller[1])->getDocComment(), '@Page')) {
			return parent::onKernelView($event);
		}
		
		$parameters = $event->getControllerResult();
		if (null === $parameters) {
			if (!$vars = $request->attributes->get('_template_vars')) {
				if (!$vars = $request->attributes->get('_template_default_vars')) {
					return;
				}
			}

			$parameters = array();
			foreach ($vars as $var) {
				$parameters[$var] = $request->attributes->get($var);
			}
		}
		if (!is_array($parameters)) {
			return $parameters;
		}

		if (!$template = $request->attributes->get('_template')) {
			return $parameters;
		}
		
		/**
		 * if engine is twig lets render layouthelper.html.twig instead of the current template to avoid writing extends stuff
		 * (if extends is needed @Template() annotation should be used instead of @Page())
		 */
		if ($template->get('engine') == 'twig') {
			$parameters['currentTemplate'] = $template->__toString();
			$controller = $template->get('controller');
			$setController = '';
			if (strstr($controller, '\\')) {
				list($setController, $class) = explode('\\', $controller);
			}
			$template->set('controller', $setController);
			$template->set('name', 'layouthelper');
		}
		
		$response = new \Symfony\Component\HttpFoundation\Response($this->container->get('templating')->render($template, $parameters));
		if (isset($parameters['httpStatusCode']) && $parameters['httpStatusCode']) {
			$response->setStatusCode($parameters['httpStatusCode']);
		}
		$event->setResponse($response);
	}
}
