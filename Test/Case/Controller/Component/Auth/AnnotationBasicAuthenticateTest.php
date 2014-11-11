<?php
App::uses('AnnotationBasicAuthenticate', 'AnnotationControlList.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');

class AnnotationBasicAuthenticateTest extends CakeTestCase {

	public function testConstruct() {
		$ComponentCollection = $this->getMock('ComponentCollection', ['init']);
		new AnnotationBasicAuthenticate($ComponentCollection, []);
	}

}

