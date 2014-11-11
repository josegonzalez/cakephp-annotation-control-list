<?php
App::uses('AnnotationParserTrait', 'AnnotationControlList.Lib');
App::uses('BlowfishAuthenticate', 'Controller/Component/Auth');

class AnnotationBlowfishAuthenticate extends BlowfishAuthenticate {

	use AnnotationParserTrait;

}
