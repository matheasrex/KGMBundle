<?php

namespace KGMBundle\Twig\TwigFilter;

use KGMBundle\Twig\TwigFilter;

/**
 * twig formatter method class
 */
class TwigFilterFormat extends TwigFilter
{
	/**
	 * @var array $parameters parameter names
	 *
	 * @access protected
	 */
	protected $parameters = array(
		'callString' => '',
		'value' => '',
	);
	
	/**
	 * return formatted string value based on callString
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
		
		$return = 'common.format.missing';
		
		if ($params['value'] instanceof \DateTime) {
			$params['value'] = $params['value']->format('U');
		}
		
		switch (lcfirst($params['callString'])) {
			case 'dateTimeFormat' :
			case 'dateTimeTzFormat' :
			case 'dateFormat' :
			case 'timeFormat' :
				$formatName = lcfirst($params['callString']);
				$format = $this->extension->getContainer()->get('framework.repository')->getConstants()->$formatName;
				$return = date($format, $params['value']);
				break;
			case 'longDateFormat' :
				$return = $this->strftime(
					$this->extension->getContainer()->get('framework.repository')->getConstants()->getLongDateFormat(),
					$params['value'], 
					$this->extension->getContainer()->get('framework.repository')->getConstants()->getDateLocale()
				);
				break;
		}
		
		return $return;
	}
	
	/**
	 * Return date as formatted string with unix formater string
	 *
	 * @param string $format   unix datetime formater string
	 * @param mixed  $datetime unix timestamp or DateTime object
	 * @param string $locale   location string
	 *
	 * @return string
	 *
	 * @access protected
	 */
	protected function strftime($format, $datetime = null, $locale = null)
	{
		if (isset($locale)) {
			$oldLocale = setlocale(LC_TIME, "0");
			setlocale(LC_TIME, $locale);
		}
		$return = "";
		if ($format) {
			if (isset($datetime)) {
				if ($datetime instanceof \DateTime) {
					$timestamp = $datetime->format('U');
				} else {
					$timestamp = $datetime;
				}
				$return = strftime($format, $timestamp);
			} else {
				$return = strftime($format);
			}
		}
		if (isset($locale)) {
			setlocale(LC_TIME, $oldLocale);
		}
		
		return $return;
	}
}
