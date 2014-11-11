<?php
App::uses('AnnotationBlowfishAuthenticate', 'AnnotationControlList.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');

class AnnotationBlowfishAuthenticateTest extends CakeTestCase {

	public function testConstruct() {
		$ComponentCollection = $this->getMock('ComponentCollection', ['init']);
		new AnnotationBlowfishAuthenticate($ComponentCollection, []);
	}

}

