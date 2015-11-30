<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;
use Cake\Auth\BaseAuthorize;

class ModelAuthorize extends BaseAuthorize
{
    use ModelParserTrait;
}
