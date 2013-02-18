<?php

namespace KGMBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * listener to cache exception with custom handler
 */
class ExceptionListener
{
	/**
	 * handle exceptions which reached the kernel
	 *
	 * @param GetResponseForExceptionEvent $event event data
	 *
	 * @access public
	 */
	public function onKernelException(\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event)
	{
		$exeption = $event->getException();
		if ($exeption instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
			$response = new \Symfony\Component\HttpFoundation\RedirectResponse('/404/', 301);
		} elseif ($exeption instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
			$response = new \Symfony\Component\HttpFoundation\RedirectResponse('/403/', 301);
		} else {
			\ErrorHandler::handleException($exeption);
			$response = new \Symfony\Component\HttpFoundation\Response('Exception reached kernel!', 500, array());
		}
		$event->setResponse($response);
	}
}
