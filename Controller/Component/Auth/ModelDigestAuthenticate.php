<?php
App::uses('ModelParserTrait', 'AnnotationControlList.Lib');
App::uses('DigestAuthenticate', 'Controller/Component/Auth');

class ModelDigestAuthenticate extends DigestAuthenticate {

	use ModelParserTrait;

}
