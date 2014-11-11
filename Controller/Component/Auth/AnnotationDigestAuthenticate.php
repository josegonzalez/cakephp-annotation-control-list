<?php
App::uses('AnnotationParserTrait', 'AnnotationControlList.Lib');
App::uses('DigestAuthenticate', 'Controller/Component/Auth');

class AnnotationDigestAuthenticate extends DigestAuthenticate {

	use AnnotationParserTrait;

}
