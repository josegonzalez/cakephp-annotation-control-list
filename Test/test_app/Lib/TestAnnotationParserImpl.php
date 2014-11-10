<?php
App::uses('AnnotationParserTrait', 'AnnotationControlList.Lib');
App::uses('Controller', 'Controller');

class TestAnnotationParserImpl {

	use AnnotationParserTrait;

	public function __construct(Controller $Controller) {
		$this->_Controller = $Controller;
	}

	public function getController() {
		return $this->_Controller;
	}

}
