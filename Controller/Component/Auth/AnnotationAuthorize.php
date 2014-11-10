<?php
App::uses('AnnotationParserTrait', 'AnnotationControlList.Lib');
App::uses('BaseAuthorize', 'Controller/Component/Auth');

class AnnotationAuthorize extends BaseAuthorize {

	use AnnotationParserTrait;

}
