<?php

namespace KGMBundle\Part\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * Entity list related function collection
 * not related to a single entoty but a collection
 */
class EntityRepository extends BaseRepository
{


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
	 * search by criteria array
	 *
	 * @param array $params Criteria list
	 * @param bool  $or     Criteria implode by AND or OR
	 *
	 * @return array of class type
	 *
	 * @access public
	 */
	public function search($params = array(), $or = false)
	{
		$queryBuilder = $this->getEntityManager()->createQueryBuilder('e');
		$queryBuilder->select(array('e'))->from($this->getCurrentEntityName(), 'e');
		$where = array();
		if (isset($params['where'])) {
			$where = $params['where'];
		}
		if (is_array($where) && !empty($where)) {
			foreach ($where as $field => $condition) {
				$conditionField = "e.".$field;
				$conditionOperand = "=";
				$conditionValue = ":".$field;
				$bindValue = null;
				if (is_array($condition)) {
					/**
					 * handle bit_and and bit_or functions
					 *
					 * to call: 'field' => array(2, 'bit_and', 6, '<>')
					 * where 
					 * * field   Is the entity field name
					 * * 2       Is the expected result
					 * * bit_and Is the function
					 * * 6       Is the bit to check
					 * * <>      Is the optional operator, if you dont want the default equivalence
					 * this examle is translazted to: bit_and(field, 6) <> 2 (means: can be 0 or 4 or 6)
					 */					 
					if (isset($condition[2]) && in_array($condition[1], array('bit_and', 'bit_or'))) {
						$conditionField = $condition[1].'('.$conditionField.', :examined)';
						$queryBuilder->setParameter('examined', $condition[2]);
						if (isset($condition[3]) && $condition[3] != '=') {
							$conditionOperand = $condition[3];
						}
					} elseif (isset($condition[1])) {
						$conditionOperand = $condition[1];
					}
					$bindValue = $condition[0];
				} else {
					$bindValue = $condition;
				}
				if (is_array($bindValue)) {
					if (in_array(trim(strtoupper($conditionOperand)), array('IN', 'NOT IN'))) {
						$conditionValue = "(".$conditionValue.")";
					}
				}
				$conditionString = $conditionField." ".$conditionOperand." ".$conditionValue;
				if ($or) {
					$queryBuilder->where($conditionString);
				} else {
					$queryBuilder->andWhere($conditionString);
				}
				$queryBuilder->setParameter($field, $bindValue);
			}
		}
		if (isset($params['orderby'])) {
			$orderby = $params['orderby'];
			if (is_array($orderby)) {
				if (isset($orderby[1])) {
					$queryBuilder->orderBy('e.'.$orderby[0], $orderby[1]);
				} else {
					$queryBuilder->orderBy('e.'.$orderby[0]);
				}
			} else {
				$queryBuilder->orderBy('e.'.$orderby);
			}
		}
		$result = $queryBuilder->getQuery()->execute();
		if (!is_array($result)) {
			return array();
		}
		
		return $result;
	}
	
	/**
	 * return current entity name 
	 *
	 * @return string entity to run methods on
	 *
	 * @access public
	 */
	public function getCurrentEntityName()
	{
		return $this->_entityName;
	}
}
