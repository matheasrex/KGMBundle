<?
namespace KGMBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\ORM\Mapping as ORM;
use KGMBundle\Entity;

/**
 * Entity definition of admingroup
 *
 * @ORM\Entity
 * @ORM\Table(name="admingroup")
 */
class Group extends Entity implements RoleInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="admingroup_id")
	 * @ORM\GeneratedValue(strategy="SEQUENCE")
	 * @ORM\SequenceGenerator(sequenceName="seq_admingroup", allocationSize=1)
	 *
	 * @access protected
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="admingroup_name", type="string", length=30)
	 *
	 * @access protected
	 */
	protected $name;
	
	/**
	 * get name of group as role name
	 *
	 * @return string
	 */
	public function getRole()
	{
		return $this->name;
	}
}
