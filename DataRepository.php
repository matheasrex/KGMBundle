<?php

namespace KGMBundle;

use KGMBundle\Exception\DataRepository\MissingKeyException;

/**
 * datarepository class
 */
class DataRepository
{
	/**
	 * @var GlobalConstants
	 *
	 * @access protected
	 */
	protected $constants;
	
	/**
	 * @var array repository
	 *
	 * @access protected
	 */
	protected $repository;
	
	/**
	 * public contructor
	 *
	 * @param array $params Init params
	 *
	 * @access public
	 */
	public function __construct($params = array())
	{
		$this->constants = new \KGMBundle\Configuration\GlobalConstant();
		if (!empty($params)) {
			foreach ($params as $key => $item) {
				$this->addRepository(str_replace('_', '.', $key), new DataRepositoryItem($item));
			}
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
	 * data getter for twig templates
	 *
	 * @param string $name Repository key
	 *
	 * @return mixed Repository item value
	 *
	 * @access public
	 */
	public function __get($name)
	{
		try {
			$value = $this->getRepository(str_replace('_', '.', $name))->getData();
			if (is_array($value) && count($value) == 1) {
				$value = $value[0];
			}
			
			return $value;
		} catch (MissingKeyException $ex) {
			\ErrorHandler::logError('Repository item "'.$name.'" not found!');
		}
		
		return null;
	}
	
	/**
	 * wrapper for twig template getter
	 *
	 * @param string $name   Repository key
	 * @param mixed  $params Call parameters
	 *
	 * @return mixed Repository item value
	 *
	 * @access public
	 */
	public function __call($name, $params)
	{
		return $this->__get($name);
	}
	
	/**
	 * return constants
	 *
	 * @return GlobalConstants
	 *
	 * @access public
	 */
	public function getConstants()
	{
		return $this->constants;
	}
	
	/**
	 * check if repo exists
	 *
	 * @param string $key repo key
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function hasRepository($key)
	{
		return isset($this->repository[$key]);
	}
	
	/**
	 * add new repo data, throws exception if key exists
	 *
	 * @param string $key  repo key
	 * @param mixed  $data repo data
	 *
	 * @access public
	 */
	public function addRepository($key, DataRepositoryItem $data)
	{
		$this->checkRepository($key);
		$this->repository[$key] = $data;
	}
	
	/**
	 * get repo data, throws exception if key not exists
	 *
	 * @param string $key repo key
	 *
	 * @return DataRepositoryItem repo data
	 *
	 * @access public
	 */
	public function getRepository($key)
	{
		$this->checkNoRepository($key);
		
		return $this->repository[$key];
	}
	
	/**
	 * change repo data, throws exception if key not exists
	 *
	 * @param string $key  repo key
	 * @param mixed  $data repo data
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function changeRepository($key, DataRepositoryItem $data)
	{
		$this->checkNoRepository($key);
		
		return $this->repository[$key] = $data;
	}
	
	/**
	 * delete repo data, throws exception if key not exists
	 *
	 * @param string $key repo
	 *
	 * @access public
	 */
	public function deleteRepository($key)
	{
		$this->checkNoRepository($key);
		unset($this->repository[$key]);
	}
	
	/**
	 * move repo data, throws exception if key not exists, or newkey exists
	 *
	 * @param string $key    repo
	 * @param string $newkey repo
	 *
	 * @access public
	 */
	public function moveRepository($key, $newkey)
	{
		$this->addRepository($newkey, $this->getRepository($key));
		$this->deleteRepository($key);
	}
	
	/**
	 * clone repo data, throws exception if key not exists, or newkey exists
	 *
	 * @param string $key    repo
	 * @param string $newkey repo
	 *
	 * @access public
	 */
	public function cloneRepository($key, $newkey)
	{
		$this->addRepository($newkey, clone $this->getRepository($key));
	}
	
	/**
	 * check for repo, yields if found
	 *
	 * @param string $key repo
	 *
	 * @access protected
	 */
	protected function checkRepository($key)
	{
		if ($this->hasRepository($key)) {
			throw new \KGMBundle\Exception\DataRepository\ExistingKeyException(get_class($this)." has allready ".$key." repository");
		}
	}
	
	/**
	 * check for repo, yields if not found
	 *
	 * @param string $key repo
	 *
	 * @access protected
	 */
	protected function checkNoRepository($key)
	{
		if (!$this->hasRepository($key)) {
			throw new MissingKeyException(get_class($this)." has no ".$key." repository");
		}
	}
}
