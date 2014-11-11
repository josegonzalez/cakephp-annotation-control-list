<?php
App::uses('AnnotationFormAuthenticate', 'AnnotationControlList.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');

class AnnotationFormAuthenticateTest extends CakeTestCase {

	public function testConstruct() {
		$ComponentCollection = $this->getMock('ComponentCollection', ['init']);
		new AnnotationFormAuthenticate($ComponentCollection, []);
	}

}
