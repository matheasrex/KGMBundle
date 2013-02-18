<?php

namespace KGMBundle;

/**
 * global class for entities
 */
class Entity implements \IteratorAggregate
{
	/**
	 * @const callable property
	 */
	const CALLABLE_PROPERTY = 1;
	
	/**
	 * @const callable method
	 */
	const CALLABLE_METHOD = 2;
	
	/**
	 * @var EntityManager $manager entity manager for entity
	 *
	 * @access protected
	 */
	protected $manager;
	
	/**
	 * @var EntityFunction $entityFunction function library for this entity
	 *
	 * @access protected
	 */
	protected $entityFunction;
	
	/**
	 * @var array $modifiedFields Modified fields and previous content
	 *
	 * @access protected
	 */
	protected $modifiedFields;
	
	/**
	 * @var RepositoryClass $repositoryClass repository object
	 *
	 * @access protected
	 */
	protected $repositoryClass;
	
	/**
	 * global constructor
	 *
	 * @param 
	 *
	 */
	public function __construct($doctrine = null)
	{
		if ($doctrine) {
			if ($doctrine instanceOf \Doctrine\Bundle\DoctrineBundle\Registry) {
				$this->setEntityManager($doctrine->getEntityManager());
			}
		}
	}
	/**
	 * global getter
	 *
	 * @param string $name Prop name
	 *
	 * @return mixed
	 *
	 * @access public
	 */
	public function __get($name)
	{
		$func = 'get' . ucfirst($name);
		
		return $this->$func();
	}
	
	/**
	 * global setter
	 *
	 * @param string $name  Prop name
	 * @param mixed  $value Prop value
	 *
	 * @access public
	 * @link {$name}
	 */
	public function __set($name, $value)
	{
		$func = 'set' . ucfirst($name);
		$this->$func($value);
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
		if (\GlobalFunction::isMatch('/^[gs]et[A-Z\d]/', $name)) {
			$field = lcfirst(substr($name, 3));
			$prev = false;
			if (\GlobalFunction::isMatch('/^getPrevious[A-Z\d]/', $name)) {
				$field = lcfirst(str_replace('getPrevious', '', $name));
				$prev = true;
			}
			if (!property_exists($this, $field)) {
				throw new \KGMBundle\Exception\Entity\MissingFieldException(get_class() . " has no $field field");
			}
			if ($name[0] == 'g') {
				if ($prev) {
					if (isset($this->modifiedFields[$field])) {
						return $this->modifiedFields[$field];
					}
					if (array_search($field, array_keys($this->modifiedFields)) !== false) {
						return $this->modifiedFields[$field];
					}
				}
				
				return $this->$field;
			} else {
				if (count($args) < 1) {
					throw new \KGMBundle\Exception\Semantic\FunctionParamCountException("not enough parameters for setter");
				}
				$value = $args[0];
				
				if ($this->$field != $value) {
					$this->modifiedFields[$field] = $this->$field;
				}
				
				return ($this->$field = $value);
			}
		} elseif (\GlobalFunction::isMatch('/^is[A-Z\d]/', $name)) {
			if (method_exists($this->getFunction(), $name)) {
				return call_user_func_array(array($this->getFunction(), $name), $args);
			}
			if (\GlobalFunction::isMatch('/^is[A-Z\d].*Modified$/', $name)) {
				$field = lcfirst(substr(substr($name, 2), 0, -8));
				
				return (array_search($field, array_keys($this->modifiedFields)) !== false);
			} else {
				$constantClass = str_replace('\\Entity\\', '\\EntityConstant\\', get_class($this)).'Constant';
				if (class_exists($constantClass)) {
					$callString = substr($name, 2);
					$bitFields = $constantClass::getBitFields();
					if (isset($bitFields[$callString])) {
						return (bool)($this->$bitFields[$callString]['field'] & $bitFields[$callString]['value']);
					}
					$enumFields = $constantClass::getEnumFields();
					if (isset($enumFields[$callString])) {
						return (bool)($this->$enumFields[$callString]['field'] == $enumFields[$callString]['value']);
					}
					throw new \KGMBundle\Exception\Entity\MissingFieldPropertyException($callString . " field property does not exists");
				}
				throw new \KGMBundle\Exception\Entity\MissingConstantException($constantClass . " class does not exists");
			}
		} else {
			if (!property_exists($this, $name)) {
				if (method_exists($this->getFunction(), $name)) {
					return call_user_func_array(array($this->getFunction(), $name), $args);
				} elseif (method_exists($this->getRepositoryClass(), $name)) {
					return call_user_func_array(array($this->getRepositoryClass(), $name), $args);
				} else {
					throw new \KGMBundle\Exception\Entity\MissingFunctionException(get_class() . " has no $name function");
				}
			}
			
			return $this->$name;
		}
		throw new \KGMBundle\Exception\Entity\MissingFunctionException(get_class() . " has no $name function");
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
	 * determines if entity is new
	 * TODO: use reflection for @ ORM\ID field determination instead of hardlinked id field
	 *
	 * @return bool True if entity is new
	 * @throws \KGMBundle\Exception\Entity\MissingFieldException When entity does not have $id field
	 *
	 * @access public
	 */
	public function isNew()
	{
		return ($this->getId() == null);
	}
	
	/**
	 * determines if entity is modified
	 *
	 * @return bool True if entity is modified
	 *
	 * @access public
	 */
	public function isModified()
	{
		return (bool)count($this->modifiedFields);
	}
	
	/**
	 * function library getter
	 *
	 * @return EntityFunction function library for this entity
	 *
	 * @access public
	 * @link {entityFunction} When not set
	 */
	public function getFunction()
	{
		if (!$this->entityFunction) {
			$this->createEntityFunction();
		}
		
		return $this->entityFunction;
	}
	
	/**
	 * get all data fields of entity
	 *
	 * @return array Entity fields
	 *
	 * @access public
	 */
	public function getFields()
	{
		return $this->manager->getClassMetadata(get_class($this))->getFieldNames();
	}
	
	/**
	 * iterate trough entity's fields
	 *
	 * @return \ArrayIterator Iterator object
	 *
	 * @access public
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->getFields());
	}
	
	/**
	 * Get the repository class
	 *
	 * @return obj RepositoryClass function library for this entity
	 *
	 * @access public
	 * @link {entityFunction} When not set
	 */
	public function getRepositoryClass()
	{
		if (!$this->repositoryClass) {
			$this->createRepositoryClass();
		}
		
		return $this->repositoryClass;
		
		
	}
	
	/**
	 * test if $name is a callable property or method, and returns the call mode
	 *
	 * @param string $name param
	 *
	 * @return int
	 *
	 * @access public
	 */
	public function testCall($name)
	{
		if (preg_match('/^[gs]et[A-Z\d]/', $name)) {
			$field = lcfirst(substr($name, 3));
			if (property_exists($this, $field)) {
				return self::CALLABLE_PROPERTY;
			}
		} else {
			$name = lcfirst($name);
			if (property_exists($this, $name)) {
				return self::CALLABLE_PROPERTY;
			}
			if (method_exists($this->getFunction(), $name)) {
				return self::CALLABLE_METHOD;
			}
		}
		
		return 0;
	}
	
	/**
	 * Dependency injection; called from postLoad event handler
	 *
	 * @param EntityManager $manager entity manager
	 *
	 * @access public
	 * @link {$manager}
	 */
	public function setEntityManager(\Doctrine\ORM\EntityManager $manager)
	{
		$this->manager = $manager;
	}
	
	/**
	 * Entitymanager getter
	 *
	 * @return EntityManager $manager
	 *
	 * @access public
	 */
	public function getEntityManager()
	{
		return $this->manager;
	}
	
	/**
	 * do save
	 *
	 * @access public
	 */
	public function save()
	{
		$this->manager->persist($this);
		$this->manager->flush();
		//TODO: create change event logs
		$this->modifiedFields = array();
	}
	
	/**
	 * do delete
	 *
	 * @access public
	 */
	public function delete()
	{
		$this->manager->remove($this);
		$this->manager->flush();
		//TODO: create change event logs
		$this->modifiedFields = array();
	}
	
	/**
	 * revert entity to last saved state, if entity is new then reverted to default state
	 *
	 * @access public
	 */
	public function revert()
	{
		foreach ($this->modifiedFields as $field => $value) {
			$this->$field = $value;
		}
		$this->modifiedFields = array();
	}
	
	/**
	 * load entity by fthe field Id
	 * if the entity does not have id field, this function should be overriden
	 *
	 * @param int $id id of entity in DB
	 *
	 * @return Entity or null, if id not found
	 * @throws \KGMBundle\Exception\Entity\MissingEntityManagerException When EM not set
	 *
	 * @access public
	 */
	public function load($id)
	{
		return $this->find($id);
	}
	
	/**
	 * construct EntityFunction class
	 * related to entity if exists otherwise \KGMBundle\EntityFunction
	 *
	 * @access protected
	 * @link {entityFunction}
	 */
	protected function createEntityFunction()
	{
		$class = str_replace('\\Entity\\', '\\EntityFunction\\', get_class($this)).'Function';
		if (class_exists($class)) {
			$this->entityFunction = new $class($this);
		} else {
			$this->entityFunction = new \KGMBundle\EntityFunction($this);
		}
	}
	
	/**
	 * construct RepositoryClass object
	 * related to entity if exists otherwise \KGMBundle\Part\Entity\EntityRepository
	 *
	 * @access protected
	 * @link {repositoryClass}
	 */
	protected function createRepositoryClass()
	{
		$this->repositoryClass = $this->manager->getRepository(get_class($this));
	}
}
