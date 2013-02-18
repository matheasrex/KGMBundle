<?

namespace KGMBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use KGMBundle\Part\File;

/**
 * class for run command
 */
abstract class RunCommand extends ContainerAwareCommand
{
	/**
	 * @var string $namePostfix Postfix of command name
	 *
	 * @access protected
	 */
	protected $namePostfix = "";
	
	/**
	 * @var string $controllerPrefix Prefix of controller class
	 *
	 * @access protected
	 */
	protected $controllerPrefix = "";
	
	/**
	 * @var string $controllerGroup Group of controller
	 *
	 * @access protected
	 */
	protected $controllerGroup = "";
	
	/**
	 * @var string $controllerName Name of controller
	 *
	 * @access protected
	 */
	protected $controllerName = "";
	
	/**
	 * @var string $controllerAction Name of controller action
	 *
	 * @access protected
	 */
	protected $controllerAction = "";
	
	/**
	 * @var string $controllerClass Class of controller
	 *
	 * @access protected
	 */
	protected $controllerClass = "";
	
	/**
	 * @var \KGMBundle\Controller $controllerObject Controller
	 *
	 * @access protected
	 */
	protected $controllerObject;
	
	/**
	 * @var string $controllerMethod Method name of controller action
	 *
	 * @access protected
	 */
	protected $controllerMethod = "Action";
	
	/**
	 * @var string \Symfony\Component\HttpFoundation\Request Request object for controller actions
	 *
	 * @access protected
	 */
	protected $requestObject;
	
	/**
	 * configure command environment
	 *
	 * @access protected
	 */
	protected function configure()
	{
		$this->setName('run:'.$this->namePostfix);
		$this->setDescription('Run controller action');
		$this->addArgument('group', InputArgument::REQUIRED, 'Controller group');
		$this->addArgument('controller', InputArgument::REQUIRED, 'Controller class');
		$this->addArgument('action', InputArgument::REQUIRED, 'Action method');
		$this->addArgument('params', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Action method');
	}
	
	/**
	 * execute command
	 *
	 * @param \Symfony\Component\Console\Input\InputInterface   $input  Input interface
	 * @param \Symfony\Component\Console\Output\OutputInterface $output Output interface
	 *
	 * @throws \KGMBundle\Exception\Cron\CronRunErrorException When cron controller returns false value
	 * @throws \KGMBundle\Exception\Controller\MissingControllerException When controller not found
	 * @throws \KGMBundle\Exception\Controller\MissingActionException When controller action not found
	 *
	 * @access protected
	 * @link {controllerObject}
	 * @link {requestObject}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$params = $this->initParams($input);
		
		$this->controllerObject = new $this->controllerClass($this->getContainer());
		
		if (!$this->runCronJob($params, $output)) {
			$this->requestObject = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
			$this->runAction($params, $output);
		}
	}
	
	/**
	 * load params from input
	 *
	 * @param \Symfony\Component\Console\Input\InputInterface $input Input interface
	 *
	 * @return array Controller params
	 * @throws \KGMBundle\Exception\Controller\MissingControllerException When controller not found
	 * @throws \KGMBundle\Exception\Controller\MissingActionException When controller action not found
	 *
	 * @access protected
	 * @link {controllerGroup}
	 * @link {controllerName}
	 * @link {controllerAction}
	 * @link {controllerClass}
	 * @link {controllerMethod}
	 */
	protected function initParams(InputInterface $input)
	{
		$this->controllerGroup = ucfirst($input->getArgument('group'));
		$this->controllerName = ucfirst(str_replace('Controller', '', $input->getArgument('controller')));
		$this->controllerAction = lcfirst(str_replace('Action', '', $input->getArgument('action')));
		$this->controllerClass = $this->getControllerClass();
		$this->controllerMethod = $this->getControllerAction();
		
		return $input->getArgument('params');
	}
	
	/**
	 * get controller class name from controller group and name
	 *
	 * @return string Controller Class name
	 * @throws \KGMBundle\Exception\Controller\MissingControllerException When controller not found
	 *
	 * @access protected
	 */
	protected function getControllerClass()
	{
		$retval = $this->controllerPrefix.'\\Controller\\'.$this->controllerGroup.'\\'.$this->controllerName.'Controller';
		
		if (!class_exists($retval)) {
			throw new \KGMBundle\Exception\Controller\MissingControllerException('\''.$retval.'\' class not found!');
		}
		
		return $retval;
	}
	
	/**
	 * get controller action method name
	 *
	 * @return string Controller Action name
	 * @throws \KGMBundle\Exception\Controller\MissingActionException When controller action not found
	 *
	 * @access protected
	 */
	protected function getControllerAction()
	{
		$retval = $this->controllerAction.$this->controllerMethod;
		
		if (!method_exists($this->controllerClass, $retval)) {
			throw new \KGMBundle\Exception\Controller\MissingActionException('\''.$this->controllerClass.'::'.$retval.'\' action not found!');
		}
		
		return $retval;
	}
	
	/**
	 * determines if controller is Cron Job
	 *
	 * @return bool True if controller is Cron Job
	 *
	 * @access protected
	 */
	protected function isCronJob()
	{
		return ($this->controllerObject instanceof \KGMBundle\Controller\CronController);
	}
	
	/**
	 * run cron job
	 *
	 * @param array                                             $params Cron params from Unix Bash
	 * @param \Symfony\Component\Console\Output\OutputInterface $output Output interface
	 *
	 * @return bool False if controller is not Cron Job
	 * @throws \KGMBundle\Exception\Cron\CronRunErrorException When cron controller returns false value
	 *
	 * @access protected
	 */
	protected function runCronJob(array $params, OutputInterface $output)
	{
		if (!$this->isCronJob()) {
			return false;
		}
		
		$this->controllerObject->setOutput($this->createOutput($params, $output));
		$this->controllerObject->cronStart($this->controllerMethod, $params);
		if (!$this->controllerObject->{$this->controllerMethod}($params)) {
			throw new \KGMBundle\Exception\Cron\CronRunErrorException('\''.$this->controllerClass.'::'.$this->controllerMethod.'\' cron run error!');
		}
		$this->controllerObject->cronDone();
		
		return true;
	}
	
	/**
	 * create cron job output
	 *
	 * @param array                                             &$params Cron params from Unix Bash
	 * @param \Symfony\Component\Console\Output\OutputInterface $output  Output interface
	 *
	 * @return OutputInterface Output interface
	 *
	 * @access protected
	 */
	protected function createOutput(array &$params, OutputInterface $output)
	{
		if (($stdoutKey = array_search('[STDOUT]', $params)) === false) {
			return new File($this->createCronLogPath($params), null, File::SAVE_AUTO | File::SAVE_ON_DESTROY);
		}
		unset($params[$stdoutKey]);
		
		return $output;
	}
	
	/**
	 * create cron log file path
	 *
	 * @param array $params Parameters
	 *
	 * @return string Cron log file path
	 *
	 * @access protected
	 */
	protected function createCronLogPath(array $params)
	{
		if (($stdoutKey = array_search('[CRONLOG]', $params)) !== false) {
			return $params[$stdoutKey + 1];
		}
		
		return $this->getBaseLogPath()
			.\GlobalFunction::linuxHostname().'/'
			.$this->controllerGroup.'_'.$this->controllerName.'_'
			.$this->controllerAction.'_'.date('Ymd_His').'.log'
		;
	}
	
	/**
	 * Get the base log path for cron logging
	 *
	 * @return string
	 */
	protected function getBaseLogPath()
	{
		return __DIR__.'/../../../app/logs/cron/'
	}
	
	/**
	 * run controller action, if controller is NOT Cron Job
	 *
	 * @param array                                             $params Controller params from Unix Bash
	 * @param \Symfony\Component\Console\Output\OutputInterface $output Output interface
	 *
	 * @return bool False if controller is Cron Job
	 *
	 * @access protected
	 */
	protected function runAction(array $params, OutputInterface $output)
	{
		if ($this->isCronJob()) {
			return false;
		}
		
		$shouldRender = $this->shouldRender($params);
		array_unshift($params, $this->requestObject);
		$output->writeln(
			$this->renderResult(
				$this->executeControllerAction($params),
				$shouldRender
			)
		);
		
		return true;
	}
	
	/**
	 * render result to string based on $shouldRender option
	 *
	 * @param mixed $result       Controller action result
	 * @param bool  $shouldRender If false, the result will be only exported, not rendered
	 *
	 * @return string Render result
	 *
	 * @access protected
	 */
	protected function renderResult($result, $shouldRender)
	{
		if ($shouldRender) {
			return $this->renderTwig($result);
		} else {
			return \ErrorHandler::varExport($result);
		}
	}
	
	/**
	 * render result with Twig templating
	 *
	 * @param mixed $result Controller action result
	  *
	 * @return string Render result
	 *
	 * @access protected
	 */
	protected function renderTwig($result)
	{
		$result['currentTemplate'] = $this->getControllerTemplate();
		$result['controller'] = $this->controllerGroup;
		$this->getContainer()->enterScope('request');
		$this->getContainer()->set('request', $this->requestObject, 'request');
		
		return $this->getContainer()->get('templating')->render($this->getBaseTemplate(), $result);
	}
	
	/**
	 * execute controller action
	 *
	 * @param array $params Action parameters
	 *
	 * @return mixed Result array or redirectResponse
	 *
	 * @access protected
	 */
	protected function executeControllerAction(array $params)
	{
		return call_user_func_array(
			array(
				$this->controllerObject,
				$this->controllerMethod,
			),
			$params
		);
	}
	
	/**
	 * determines, if twig should render output
	 * no render if [NORENDER] found in param array
	 *
	 * @param array &$params Parameters
	 *
	 * @return bool True if should render
	 *
	 * @access protected
	 */
	protected function shouldRender(&$params)
	{
		if (($renderKey = array_search('[NORENDER]', $params)) !== false) {
			unset($params[$renderKey]);
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * get twig path of controller action's template file
	 *
	 * @return string Controller Action's template path
	 *
	 * @access protected
	 */
	protected function getControllerTemplate()
	{
		return str_replace('\\', '', $this->controllerPrefix).':'.$this->controllerGroup.'\\'.$this->controllerName.':'.$this->controllerAction.'.html.twig';
	}
	
	/**
	 * get twig path of base template file
	 *
	 * @return string Base template path
	 *
	 * @access protected
	 */
	protected function getBaseTemplate()
	{
		return str_replace('\\', '', $this->controllerPrefix).':'.$this->controllerGroup.':layouthelper.html.twig';
	}
}
