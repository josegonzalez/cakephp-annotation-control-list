<?php
App::uses('AnnotationAuthorize', 'AnnotationControlList.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');

class AnnotationAuthorizeTest extends CakeTestCase {

	public function testConstruct() {
		$ComponentCollection = $this->getMock('ComponentCollection', ['getController']);
		new AnnotationAuthorize($ComponentCollection, []);
	}

}
