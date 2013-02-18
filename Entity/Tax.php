<?php

namespace KGMBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use KGMBundle\Entity;

/**
 * Class for Tax entity definitions
 *
 * @ORM\Entity
 */
class Tax extends Entity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type = "integer")
	 *
	 * @access protected
	 */
	protected $id;
	
	/**
	 * @Assert\Regex(pattern = "/^\d{8}$/D", message = "form.tax.format")
	 *
	 * @access protected
	 */
	protected $split0;

	/**
	 * @Assert\Regex(pattern = "/^\d{1}$/D", message = "form.tax.format")
	 *
	 * @access protected
	 */
	protected $split1;

	/**
	 * @Assert\Regex(pattern = "/^\d{2}$/D", message = "form.tax.format")
	 *
	 * @access protected
	 */
	protected $split2;
}
