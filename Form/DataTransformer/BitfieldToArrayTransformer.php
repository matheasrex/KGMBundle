<?php

namespace KGMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class to handle array - int transformations for bit fields
 */
class BitfieldToArrayTransformer implements DataTransformerInterface
{
	/**
	 * @var const MAX_BITFIELDS_32 Max bitfield size allowed
	 */
	const MAX_BITFIELDS_32 = 0x7FFFFFFF;
	
	/**
	 * Function to transform option sum in to array
	 *
	 * @param int $bits Bits set on entity
	 *
	 * @return array List of valid bits in array format
	 *
	 * @access public
	 */
	public function transform($bits)
	{
		$validBits = array();
		$currentBit = 1;

		while ($currentBit < BitfieldToArrayTransformer::MAX_BITFIELDS_32) {
			if ($currentBit & $bits) {
				$validBits[] = $currentBit;
			}

			$currentBit <<= 1;
		}

		return $validBits;
	}
	
	/**
	 * Function to transform option sum in to array
	 *
	 * @param array $array List of valid bits in array format
	 *
	 * @return int Sum of valid bits
	 *
	 * @access public
	 */
	public function reverseTransform($array)
	{
		$bits = 0;
		foreach ($array as $value) {
			$bits += $value;
		}

		return $bits;
	}
}
