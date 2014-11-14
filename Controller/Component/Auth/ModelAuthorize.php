<?php
App::uses('ModelParserTrait', 'AnnotationControlList.Lib');
App::uses('BaseAuthorize', 'Controller/Component/Auth');

class ModelAuthorize extends BaseAuthorize {

	use ModelParserTrait;

}
