<?

namespace KGMBundle\EntityRepository;

use KGMBundle\Part\Entity\EntityRepository;

/**
 * repository class for CustomerFeedback Entity
 */
class AdminuserRepository extends EntityRepository
{
	/**
	 * test function to ordered name finding
	 *
	 * @return array
	 */
	public function findByNameOrdered()
	{
		return $this->getEntityManager()
			->createQuery('SELECT a FROM '.$this->getCurrentEntity().' a ORDER BY a.username ASC')
			->setMaxResults(10)
			->getResult();
	}
}
