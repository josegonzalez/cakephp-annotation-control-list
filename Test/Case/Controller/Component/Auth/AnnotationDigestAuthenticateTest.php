<?php
App::uses('AnnotationDigestAuthenticate', 'AnnotationControlList.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');

class AnnotationDigestAuthenticateTest extends CakeTestCase {

	public function testConstruct() {
		$ComponentCollection = $this->getMock('ComponentCollection', ['init']);
		new AnnotationDigestAuthenticate($ComponentCollection, []);
	}

}

