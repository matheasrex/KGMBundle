<?php

namespace KGMBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * form type class for bitfield
 */
class BitFieldType extends AbstractType
{
	/**
	 * Set defaults for form
	 *
	 * @param OptionsResolverInterface $resolver OptionsResolverInterface odbect
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'expanded' => true,
			'multiple' => true,
		));
	}
	
	/**
	 * Form builder method
	 *
	 * @param Symfony\Component\Form\FormBuilderInterface $builder Some symfony related object
	 * @param array                                       $options default nedded array
	 *
	 * @access public
	 */
	public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer(new DataTransformer\BitfieldToArrayTransformer());
	}
	
	/**
	 * Parent definition
	 *
	 * @return object
	 *
	 * @access public
	 */
	
	public function getParent()
	{
		return new \Symfony\Component\Form\Extension\Core\Type\ChoiceType();
	}
	
	
	/**
	 * Name definition
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function getName()
	{
		return 'bitfield';
	}
}
