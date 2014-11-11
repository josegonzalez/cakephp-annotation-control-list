<?php
App::uses('AnnotationParserTrait', 'AnnotationControlList.Lib');
App::uses('BasicAuthenticate', 'Controller/Component/Auth');

class AnnotationBasicAuthenticate extends BasicAuthenticate {

	use AnnotationParserTrait;

}
