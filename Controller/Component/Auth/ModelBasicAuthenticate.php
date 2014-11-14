<?php
App::uses('ModelParserTrait', 'AnnotationControlList.Lib');
App::uses('BasicAuthenticate', 'Controller/Component/Auth');

class ModelBasicAuthenticate extends BasicAuthenticate {

	use ModelParserTrait;

}
