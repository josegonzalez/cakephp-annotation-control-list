<?php
App::uses('ModelParserTrait', 'AnnotationControlList.Lib');
App::uses('BlowfishAuthenticate', 'Controller/Component/Auth');

class ModelBlowfishAuthenticate extends BlowfishAuthenticate {

	use ModelParserTrait;

}
