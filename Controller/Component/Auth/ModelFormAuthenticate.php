<?php
App::uses('ModelParserTrait', 'AnnotationControlList.Lib');
App::uses('FormAuthenticate', 'Controller/Component/Auth');

class ModelFormAuthenticate extends FormAuthenticate {

	use ModelParserTrait;

}
