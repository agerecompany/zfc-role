<?php
namespace Agere\Role\Form;

use Zend\Form\Form,
	Zend\InputFilter\Factory as InputFactory,
	Zend\InputFilter\InputFilter;

class Role extends Form {

	public function __construct()
	{
		parent::__construct('Role');

		$this->setAttribute('method', 'post');


		$this->add([
			'name' => 'role',
			'attributes' => [
				'required' => 'required'
			],
		]);
		$this->add(['name' => 'resource']);


		// filters
		$inputFilter = new InputFilter();
		$factory = new InputFactory();

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'role',
			'required' => true,
			'filters' => array(
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 50
					)
				),
			)
		)));

		$inputFilter->add($factory->createInput(array(
			'name'	=> 'resource',
			'required' => true,
		)));


		$this->setInputFilter($inputFilter);
	}

}