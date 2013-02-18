<?php

namespace KGMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use KGMBundle\Entity;

/**
 * job apply type class - creates a form apply form
 */
abstract class FormType extends AbstractType
{
	/**
	 * @var Symfony\Component\Form\Form $form representation of form type
	 *
	 * @access protected
	 */
	protected $form;
	
	/**
	 * @var KGMBundle\Controller $controller controller to access entityManager
	 *
	 * @access protected
	 */
	protected $controller;
	
	/**
	 * @var Entity $entity entity that represents the form
	 *
	 * @access protected
	 */
	protected $entity;
	
	/**
	 * @var string $mailTarget target address to send mail at successfull save
	 *
	 * @access protected
	 */
	protected $mailTarget = '';
	
	/**
	 * @var string $mailSource source address from send mail at successfull save
	 *
	 * @access protected
	 */
	protected $mailSource = '';
	
	/**
	 * @var string $mailTemplate template to use in mail
	 *
	 * @access protected
	 */
	protected $mailTemplate = '';
	
	/**
	 * @var string $mailCharSet charset of mail
	 *
	 * @access protected
	 */
	protected $mailCharSet = '';
	
	/**
	 * @var string $formNamePostfix Form name postFix
	 *
	 * @access protected
	 */
	protected $formNamePostfix = '';
	
	/**
	 * global constructor
	 *
	 * @param Controller $controller      Controller that created the form
	 * @param Entity     $entity          Entity that represent the form, if null, then empty instance is created
	 * @param string     $formNamePostfix Form name postFix - used if more form present from the same type
	 */
	public function __construct(\KGMBundle\Controller $controller, Entity $entity = null, $formNamePostfix = '')
	{
		$this->controller = $controller;
		if ($entity == null) {
			$entityClass = str_replace(
				array(
					'\\Form\\',
					'Type',
				),
				array(
					'\\Entity\\',
					'',
				),
				get_class($this)
			);
			$this->entity = new $entityClass();
		} else {
			$this->entity = $entity;
		}
		$this->entity->setEntityManager($controller->getDoctrine()->getEntityManager());
		$this->mailTemplate = $this->getName();
		$this->formNamePostfix = $formNamePostfix;
		$this->initOptions();
	}
	
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
	 * Set defaults for form
	 *
	 * @param OptionsResolverInterface $resolver OptionsResolverInterface odbect
	 *
	 * @return array
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'intention' => strtolower($this->getName()).'_item',
		));
	}
		
	/**
	 * returns representation form 
	 *
	 * @return Symfony\Component\Form\Form
	 *
	 * @access public
	 */
	public function getForm()
	{
		if (!$this->form) {
			$this->form = $this->controller->createForm($this, $this->entity);
		}
		
		return $this->form;
	}
	
	/**
	 * returns inner entity
	 *
	 * @return KGMBundle\Entity
	 *
	 * @access public
	 */
	public function getEntity()
	{
		return $this->entity;
	}
	
	/**
	 * if form data is valid, than save data, and send mail
	 *
	 * @param array &$result container to hold result variables and values
	 *
	 * @return bool
	 *
	 * @access public
	 */
	public function save(&$result)
	{
		if (!is_array($result)) {
			$result = array();
		}
		$data = $this->getForm()->getData();
		$result['data'] = $data;
		if ($this->form->isValid()) {
			$data->setEntityManager($this->controller->getDoctrine()->getEntityManager());
			
			$this->processUpload($data, $result);
			$this->sendMail($data);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Name definition
	 * 
	 * @return string Creates a name for current form - default current classname
	 * 
	 * @access public
	 */
	public function getName()
	{
		return lcfirst(str_replace('Type', '', get_class($this))).$this->formNamePostfix;
	}
	
	/**
	 * bind request values to inner form
	 *
	 * @param Request $request request object
	 *
	 * @access public
	 */
	public function bind(\Symfony\Component\HttpFoundation\Request $request)
	{
		$this->getForm()->bind($request);
	}
	
	/**
	 * Funtion to load entity
	 *
	 * @param int $id Loaded id
	 *
	 * @access public
	 */
	public function load($id)
	{
		$this->entity = $this->entity->find($id);
	}
	
	/**
	 * called at constructor's end to initialize values in extended classes
	 *
	 * @access protected
	 */
	protected function initOptions()
	{
	
	}
	
	/**
	 * save form data, called when form is valid
	 *
	 * @param Entity $entity  form data
	 * @param array  &$result container to hold additional datas
	 *
	 * @access protected
	 */
	protected function processUpload(Entity $entity, &$result)
	{
		$entity->save();
	}
	
	/**
	 * send mail about successfull saveing
	 *
	 * @param Entity $entity form data
	 *
	 * @access protected
	 */
	protected function sendMail(Entity $entity)
	{
		if ($this->mailTarget && $this->mailTemplate) {
			$mail = $this->controller->initMailer($this->mailTarget, $this->mailTemplate.'.html.twig');
			$mail->assign('data', $entity);
			if ($this->mailSource) {
				$mail->setFrom($this->mailSource);
			}
			if ($this->mailCharSet) {
				$mail->charset = $this->mailCharSet;
			}
			$mail->send();
		}
	}
	
	/**
	 * get list of entities
	 *
	 * @return array of Entity
	 *
	 * @access public
	 */
	public function getList()
	{
		return array();
	}
	
	/**
	 * get temp folder
	 * 
	 * @param string $place   local or shared
	 * @param type   $postfix subdirectory
	 * 
	 * @return string full absolute path
	 */
	public function getTempFolder($place = 'shared', $postfix = '')
	{
		if (!in_array($place, array('local', 'shared'))) {
			$place = 'shared';
		}
		$folder = __DIR__ . '/../../../../../' . $this->controller->get('framework.repository')->temp_folders[$place] . $postfix . '/';
		
		return $folder;
	}
}
