<?php

namespace KGMBundle\Entity\Part;

/**
 * Entity related function collection
 */
class EntityFunction
{
	/**
	 * @var Entity $entity entity to run methods on
	 *
	 * @access protected
	 */
	protected $entity;
	
	/**
	 * global contructor
	 *
	 * @param Entity $entity The entity
	 *
	 * @access public
	 */
	public function __construct(\KGMBundle\Entity $entity)
	{
		if ($entity == null) {
			throw new \Exception(get_class() . " has no valid entity");
		}
		$this->entity = $entity;
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
	 * return entity
	 *
	 * @return Entity entity to run methods on
	 *
	 * @access public
	 */
	public function getEntity()
	{
		return $this->entity;
	}
}
