<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Cake\Auth\BaseAuthorize;
use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;

class ModelAuthorize extends BaseAuthorize
{
    use ModelParserTrait;
}
