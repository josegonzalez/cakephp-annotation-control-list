<?php
App::uses('ModelBlowfishAuthenticate', 'AnnotationControlList.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');

class ModelBlowfishAuthenticateTest extends CakeTestCase {

	public function testConstruct() {
		$ComponentCollection = $this->getMock('ComponentCollection', ['init']);
		new ModelBlowfishAuthenticate($ComponentCollection, []);
	}

}

