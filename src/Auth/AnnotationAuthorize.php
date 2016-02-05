<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Cake\Auth\BaseAuthorize;
use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;

class AnnotationAuthorize extends BaseAuthorize
{
    use AnnotationParserTrait;
}
