<?php
App::uses('AnnotationParserTrait', 'AnnotationControlList.Lib');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');

class TestAnnotationParserImpl {

	use AnnotationParserTrait;

	public function __construct(Controller $Controller) {
		$this->_Controller = $Controller;
	}

	public function setController(Controller $Controller) {
		$this->_Controller = $Controller;
	}

	public function setCollection(ComponentCollection $Collection) {
		$this->_Collection = $Collection;
	}

}
