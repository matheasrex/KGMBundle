<?php

namespace KGMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use KGMBundle\Entity;

/**
 * Entity definition of test
 *
 * @ORM\Entity
 * @ORM\Table(name="symfony_entity_test")
 */
class Test extends Entity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="set_id")
	 * @ORM\GeneratedValue(strategy="SEQUENCE")
	 * @ORM\SequenceGenerator(sequenceName="seq_symfony_entity_test", allocationSize=1)
	 *
	 * @access protected
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="set_text", length=10)
	 *
	 * @access protected
	 */
	protected $text;

	/**
	 * @ORM\Column(type="integer", name="set_value")
	 *
	 * @access protected
	 */
	protected $value;

	/**
	 * @ORM\Column(type="integer", name="set_weight")
	 *
	 * @access protected
	 */
	protected $weight;

	/**
	 * @ORM\Column(type="date", name="set_created")
	 *
	 * @access protected
	 */
	protected $created;
}
