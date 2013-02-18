<?php

namespace KGMBundle\Twig\Extension;

use KGMBundle\Part\MemCacheFile;

/**
 * Twig extension class
 */
class MainExtension extends \Twig_Extension
{
	/**
	 * @var obj $loader Loader class
	 *
	 * @access protected
	 */
	protected $loader;
	
	/**
	 * @var array $loadedJs List of Js loadings
	 *
	 * @access protected
	 */
	protected $loadedJs = array();
	
	/**
	 * @var array $loadedCss List of Css loadings
	 *
	 * @access protected
	 */
	protected $loadedCss = array();
	
	/**
	 * @var const Js_LOAD_BLOCK Substitute name for Js block
	 */
	const JS_LOAD_BLOCK = 'JS_LOAD_BLOCK';
	
	/**
	 * @var const Css_LOAD_BLOCK Substitute name for Css block
	 */
	const CSS_LOAD_BLOCK = 'CSS_LOAD_BLOCK';
	
	/**
	 * @var TwigFilterContainer $filterContainer Filter container
	 *
	 * @access protected
	 */
	protected $filterContainer;
	
	/**
	 * @var TwigFunctionContainer $functionContainer Function container
	 *
	 * @access protected
	 */
	protected $functionContainer;
	
	/**
	 * @var TwigParserContainer $parserContainer Parser container
	 *
	 * @access protected
	 */
	protected $parserContainer;
	
	/**
	 * @var TwigEngine $environment Environment variable to reach twig from twig
	 *
	 * @access protected
	 */
	protected $environment;
	
	/**
	 * @var Symfony\Component\DependencyInjection\Container $container Service container
	 *
	 * @access protected
	 */
	protected $container;
	
	/**
	 * @var \KGMBundle\Part\MemCacheFile $jsCache Cache for js file existence check
	 *
	 * @access protected
	 */
	protected $jsCache;
	
	/**
	 * @var \KGMBundle\Part\MemCacheFile $cssCache Cache for css file existence check
	 *
	 * @access protected
	 */
	protected $cssCache;
	
	/**
	 * global contructor
	 *
	 * @param array $params Fileloader and Container params
	 *
	 * @access public
	 */
	public function __construct($params)
	{
		$this->loader = $params['loader'];
		$this->container = $params['service_container'];
		$filterContainer = '\\'.$params['filter_container'];
		$this->filterContainer = new $filterContainer($this);
		$functionContainer = '\\'.$params['function_container'];
		$this->functionContainer = new $functionContainer($this);
		$parserContainer = '\\'.$params['parser_container'];
		$this->parserContainer = new $parserContainer($this);
		
		$this->jsCache = new MemCacheFile('static_content/js', $this->container->get('memcache'));
		$this->cssCache = new MemCacheFile('static_content/css', $this->container->get('memcache'));
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
	 * environment getter
	 *
	 * @return Twig_Environment
	 *
	 * @access public
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}
	
	/**
	 * current name getter
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function getName()
	{
		return 'main';
	}
	
	/**
	 * get container
	 *
	 * @return Symfony\Component\DependencyInjection\ContainerInterface Service container
	 *
	 * @access public
	 */
	public function getContainer()
	{
		return $this->container;
	}
	
	/**
	 * filter list getter
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function getFilters()
	{
		$filters = array();

		$filters['*'] = new \Twig_Filter_Method($this, 'callTwigFilter');

		return $filters;
	}
	
	/**
	 * call extension filter based on call string
	 *
	 * @return mixed
	 *
	 * @access public
	 */
	public function callTwigFilter()
	{
		return $this->filterContainer->callFilter(func_get_args());
	}
	
	/**
	 * function list getter
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function getFunctions()
	{
		$functions = array();
		
		$functions['loadJs'] = new \Twig_Function_Method($this, 'loadJs');
		$functions['loadCss'] = new \Twig_Function_Method($this, 'loadCss');
		$functions['twigConstant'] = new \Twig_Function_Method($this, 'twigConstant');
		
		$functions['*'] = new \Twig_Function_Method($this, 'callTwigFunction');
		
		return $functions;
	}
	
	/**
	 * call extension function based on call string
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function callTwigFunction()
	{
		return $this->functionContainer->callFunction(func_get_args());
	}
	
	/**
	 * token parser list getter
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function getTokenParsers()
	{
		$parsers = array();
		
		foreach (array_keys($this->parserContainer->getParsers()) as $token) {
			$parsers[$token] = new \KGMBundle\Twig\TwigParserToken($token, $this->parserContainer);
		}
		
		return $parsers;
	}
	
	/**
	 * Get twig constant
	 *
	 * @param string $name Name of constant
	 *
	 * @return const
	 *
	 * @access public
	 */
	public function twigConstant($name)
	{
		$name = 'self::'.strtoupper($name);
		
		return constant($name);
	}
	
	/**
	 * Js loader
	 *
	 * @param string $loadable Name of Js file
	 *
	 * @return void
	 *
	 * @access public
	 */
	public function loadJs($loadable)
	{
		if (isset($this->loadedJs[$loadable])) {
			return;
		}
		$path = $this->createPath($loadable, 'js');
		if ($this->checkPath($path, 'js')) {
			$this->loadedJs[$loadable] = $path;
		}
	}
	
	/**
	 * Get list of loaded Js files
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function getLoadedJs()
	{
		return $this->loadedJs;
	}
	
	/**
	 * Css loader
	 *
	 * @param string $loadable Name of Css file
	 *
	 * @return void
	 *
	 * @access public
	 */
	public function loadCss($loadable)
	{
		if (isset($this->loadedCss[$loadable])) {
			return;
		}
		$path = $this->createPath($loadable, 'css');
		if ($this->checkPath($path, 'css')) {
			$this->loadedCss[$loadable] = $path;
		}
	}
	
	/**
	 * Get list of loaded Css files
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function getLoadedCss()
	{
		return $this->loadedCss;
	}
	
	
	protected function checkPath($path, $type)
	{
		$path = str_replace(
			array(
				'|common/'.$type.'/',
				'|'.$type.'/',
			),
			array(
				'common::',
				'local::',
			),
			'|'.$path
		);
		$cache = ($type == 'css') ? $this->cssCache : $this->jsCache;
		
		return ((bool)$this->container->get('framework.repository')->static_settings['is_development'] || $cache->has($path));
	}
	
	/**
	 * Runtime initialization  of extension
	 *
	 * @param Twig_Environment $environment Twig environment object
	 *
	 * @link {$environment}
	 * @access public
	 */
	public function initRuntime(\Twig_Environment $environment)
	{
		$this->environment = $environment;
	}
	
	/**
	 * Create file path
	 *
	 * @param string $loadable Name of Css file
	 * @param string $type     Name of Css file
	 *
	 * @return string
	 *
	 * @access protected
	 */
	protected function createPath($loadable, $type)
	{
		$loadable = str_replace('\\', ':', $loadable);
		$return = $loadable;
		if (strpos($return, ':') !== false) {		
			$template = explode(':', str_replace('::', ':', $loadable));
			$return = implode('/', $template);
			unset($template);
		}
		$return = str_replace(
			array(
				'.html.twig',
				'.twig',
				'.html.tpl',
				'.tpl',
			), 
			'',
			$return
		);
		if ($return{0} == '/') {
			$return = 'common/'.$type.$return;
		} else {
			$return = $type.'/'.$return;
		}
		
		return $return.'.'.$type;
	}
	
}
