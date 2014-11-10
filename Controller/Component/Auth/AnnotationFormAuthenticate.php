<?php
App::uses('AnnotationParserTrait', 'AnnotationControlList.Lib');
App::uses('FormAuthenticate', 'Controller/Component/Auth');

class AnnotationFormAuthenticate extends FormAuthenticate {

	use AnnotationParserTrait;

}
