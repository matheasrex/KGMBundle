<?php

namespace KGMBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * tax type class - creates a tax field form
 */
class TaxFieldType extends AbstractType
{
	/**
	 * Form builder method
	 *
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder Some symfony related object
	 * @param array                                        $options default nedded array
	 *
	 * @access public
	 */
	public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
	{
		$builder->add(
			'split0',
			'number',
			array(
				'required' => false,
				'attr' => array(
					'maxlength' => 8,
					'forcelabel' => '',
					'loadJs' => '/Part/SplitField', //load it from common/js directory
					'split' => 'split0',
					'nextsplit' => 'split1',
				),
			)
		);
		$builder->add(
			'split1',
			'number',
			array(
				'required' => false,
				'attr' => array(
					'maxlength' => 1,
					'forcelabel' => '-',
					'split' => 'split1',
					'nextsplit' => 'split2',
					'prevsplit' => 'split0',
				),
			)
		);
		$builder->add(
			'split2',
			'number',
			array(
				'required' => false,
				'attr' => array(
					'maxlength' => 2,
					'forcelabel' => '-',
					'split' => 'split2',
					'prevsplit' => 'split1',
				),
			)
		);
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
		return 'taxfield';
	}

	/**
	 * Set defaults for form
	 *
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver OptionsResolverInterface odject
	 *
	 * @return array
	 */
	public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => '\KGMBundle\Entity\Tax',
		));
	}
}
