<?

namespace KGMBundle\Controller;

use KGMBundle\Controller;

/**
 * abstract class for cron controllers
 */
abstract class CronController extends Controller
{
	/**
	 * @var \Symfony\Component\Console\Output\OutputInterface $output Output interface
	 *
	 * @access protected
	 */
	protected $output;
	
	/**
	 * set output interface
	 *
	 * @param \Symfony\Component\Console\Output\OutputInterface $output Output interface
	 *
	 * @access public
	 * @link {output}
	 */
	public function setOutput(\Symfony\Component\Console\Output\OutputInterface $output)
	{
		$this->output = $output;
	}
	
	/**
	 * write log to stdout
	 *
	 * @param string $message Log message
	 *
	 * @access public
	 */
	public function cronLog($message)
	{
		$this->output->writeln('> '.date('Ymd_His').'_'.microtime()."\t".$message);
	}
	
	/**
	 * write cron start message
	 *
	 * @param string $cronJob Controller action method
	 * @param array  $params  Cron job params
	 *
	 * @access public
	 */
	public function cronStart($cronJob = '', $params = array())
	{
		$this->cronLog(
			'JOB START '.__CLASS__
			.($cronJob ? '::'.$cronJob : '')
			.(empty($params) ? '' : ' WITH PARAMS')
		);
		foreach ((array)$params as $param) {
			$this->output->writeln("\t".$param);
		}
		$this->output->writeln('');
	}
	
	/**
	 * write cron end message
	 *
	 * @access public
	 */
	public function cronDone()
	{
		$this->output->writeln('');
		$this->cronLog('JOB DONE');
	}
	
	/**
	 * main action
	 *
	 * @param array $params Parameters
	 *
	 * @return bool True if everything is ok and cron finished
	 */
	public function pingAction(array $params = array())
	{
		$this->cronLog(__CLASS__);
		$this->cronLog(\ErrorHandler::varExport($params));
		
		$this->cronDone();
		
		return true;
	}
}
