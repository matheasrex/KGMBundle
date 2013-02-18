<?php

namespace KGMBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Twig field type extensions for form widgeting
 */
class FieldTypeExtendedExtension extends AbstractTypeExtension
{
	/**
	 * form builder method
	 *
	 * @param FormBuilderInterface $builder form Builder method
	 * @param array                $options form option datalist
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		if (isset($options['label_prefix']) && $options['label_prefix']) {
			$builder->setAttribute('label_prefix', $options['label_prefix']);
		} elseif (isset($options['data_class'])) {
			$dataList = explode('\\', $options['data_class']);
			$builder->setAttribute('label_prefix', strtolower(end($dataList)));
		}
	}
	/**
	 * form view builder method
	 *
	 * @param FormView      $view    form view object
	 * @param FormInterface $form    form instance
	 * @param array         $options Option list
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$labelPrefix = $form->getRoot()->hasAttribute('label_prefix') ? $form->getRoot()->getConfig()->getAttribute('label_prefix').'.' : '';
		$view->vars = array_replace($view->vars, array(
			'label' => 'form.'.str_replace('_', '.', strtolower($view->vars['id']))
		));
	}
	/**
	 * extend type getter
	 *
	 * @return string
	 */
	public function getExtendedType()
	{
		return 'field';
	}
}
