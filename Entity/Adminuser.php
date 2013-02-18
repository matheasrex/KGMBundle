<?php

namespace KGMBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Mapping as ORM;
use KGMBundle\Entity;

/**
 * Entity definition of adminuser
 *
 * @ORM\Entity
 * @ORM\Table(name="adminuser")
 * @ORM\Entity(repositoryClass="KGMBundle\EntityRepository\AdminuserRepository")
 */
class Adminuser extends Entity implements AdvancedUserInterface, \Serializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="adminuser_id")
	 * @ORM\GeneratedValue(strategy="SEQUENCE")
	 * @ORM\SequenceGenerator(sequenceName="seq_adminuser", allocationSize=1)
	 *
	 * @access protected
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="adminuser_name", length=40)
	 *
	 * @access protected
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string", name="adminuser_phone", length=40)
	 *
	 * @access protected
	 */
	protected $phone;

	/**
	 * @ORM\Column(type="string", name="adminuser_email", length=50)
	 *
	 * @access protected
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string", name="adminuser_login", length=40)
	 *
	 * @access protected
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string", name="adminuser_password", length=32)
	 *
	 * @access protected
	 */
	protected $password;

	/**
	 * @ORM\Column(type="integer", name="adminuser_deleted")
	 *
	 * @access protected
	 */
	protected $deleted;

	/**
	 * @ORM\Column(type="integer", name="adminuser_cost_center_id")
	 *
	 * @access protected
	 */
	protected $costCenterId;

	/**
	 * @ORM\Column(type="integer", name="adminuser_parent")
	 *
	 * @access protected
	 */
	protected $parent;
	
	/**
	 *	  
	 * @ORM\ManyToMany(targetEntity="Group")
	 * @ORM\JoinTable(name="adminusergroup",
	 *	  joinColumns={@ORM\JoinColumn(name="adminusergroup_adminuser_id", referencedColumnName="adminuser_id")},
	 *	  inverseJoinColumns={@ORM\JoinColumn(name="adminusergroup_admingroup_id", referencedColumnName="admingroup_id")}
	 * )
	 *
	 */
	private $groups;
	
	/**
	 * Global contructor
	 * initialises groups to be a Doctrine arraycollection
	 */
	public function __construct()
	{
		$this->groups = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	/**
	 * account is not expired
	 *
	 * @return bool
	 */
	public function isAccountNonExpired()
	{
		return true;
	}
	/**
	 * account is not locked
	 *
	 * @return bool
	 */
	public function isAccountNonLocked()
	{
		return true;
	}
	/**
	 * credential is not expired
	 *
	 * @return bool
	 */
	public function isCredentialsNonExpired()
	{
		return true;
	}
	/**
	 * account is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return !(bool)$this->deleted;
	}
	/**
	 * get user roles
	 *
	 * @return array
	 */
	public function getRoles()
	{
		return $this->groups->toArray();
	}
	/**
	 * get user password
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}
	/**
	 * get key salt
	 *
	 * @return string
	 */
	public function getSalt()
	{
		return '';
	}
	/**
	 * get User name
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}
	/**
	 * delete credentioal data
	 */
	public function eraseCredentials()
	{
	}
	/**
	 * @see \Serializable::serialize()
	 *
	 * @return string Serialized id
	 */
	public function serialize()
	{
		return serialize(array(
			$this->id,
		));
	}
	/**
	 * @param string $serialized Serialized id
	 *
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized)
	{
		list (
			$this->id,
		) = unserialize($serialized);
	}
}
