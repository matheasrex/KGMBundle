<?php

namespace KGMBundle\Twig\TwigFilter;

use KGMBundle\Twig\TwigFilter;

/**
 * twig entity fuinction caller method class
 */
class TwigFilterEntityFunctionCall extends TwigFilter
{
	/**
	 * @var array $parameters parameter names
	 *
	 * @access protected
	 */
	protected $parameters = array(
		'callString' => '',
		'entity' => null,
		'implicitParamReplace' => true,
	);
	
	/**
	 * call function of entity, or if not exists, function of entityFunction
	 * call syntax:
	 * 1., entityParamStringFunctionName
	 * function name will be the SHORTEST existing callable string, the remainig is the param string
	 * 2., entityParamString_FunctionName
	 * function name will be the string after the LATEST '_' char, the remaining is the param string,
	 * including the other (optional) '_' chars
	 * in both case, if the param string is an existing callable string, it will be replaced by the entity's
	 * value for this call, to prevent this replace, add an additional parameter to the twig call, with false value
	 *
	 * @param array $params parameters
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function process($params)
	{
		$this->createParams($params);
		
		if (strpos($params['callString'], '_') === false) {
			$words = preg_split('/(?=[A-Z])/', $params['callString'], -1, PREG_SPLIT_NO_EMPTY);
			$function = "";
			$param = "";
			for ($i = count($words) - 1; $i >= 0; $i--) {
				$function = $words[$i].$function;
				if ($params['entity']->testCall($function)) {
					$param = "";
					for ($j = $i - 1; $j >= 0; $j--) {
						$param = $words[$j].$param;
					}
					if ($params['implicitParamReplace']) {
						$this->replaceParam($params['entity'], $param);
					}
					
					return $params['entity']->$function($param);
				}
			}
			throw new \Exception(get_class($params['entity']) . " has no field or function matched to ".$params['callString']);
		} else {
			$words = explode('_', $params['callString']);
			$function = end($words);
			unset($words[count($words) - 1]);
			$param = implode('_', $words);
			if ($params['implicitParamReplace']) {
				$this->replaceParam($params['entity'], $param);
			}
			
			return $params['entity']->$function($param);
		}
	}
	
	/**
	 * replace param (if can) with entity's callable value
	 *
	 * @param entity $entity Current entity
	 * @param string &$param Params
	 *
	 * @access protected
	 */
	protected function replaceParam($entity, &$param)
	{
		$callType = $entity->testCall($param);
		switch ($callType) {
			case $entity::CALLABLE_PROPERTY :
				$param = $entity->$param;
			break;
			case $entity::CALLABLE_METHOD :
				$param = $entity->$param();
			break;
		}
	}
}
