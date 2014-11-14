<?php
App::uses('ModelBasicAuthenticate', 'AnnotationControlList.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');

class ModelBasicAuthenticateTest extends CakeTestCase {

	public function testConstruct() {
		$ComponentCollection = $this->getMock('ComponentCollection', ['init']);
		new ModelBasicAuthenticate($ComponentCollection, []);
	}

}

