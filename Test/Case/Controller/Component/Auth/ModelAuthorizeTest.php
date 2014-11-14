<?php
App::uses('ModelAuthorize', 'AnnotationControlList.Controller/Component/Auth');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');

class ModelAuthorizeTest extends CakeTestCase {

	public function testConstruct() {
		$ComponentCollection = $this->getMock('ComponentCollection', ['getController']);
		new ModelAuthorize($ComponentCollection, []);
	}

}
