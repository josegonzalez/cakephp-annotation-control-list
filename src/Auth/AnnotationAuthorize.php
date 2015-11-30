<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;
use Cake\Auth\BaseAuthorize;

class AnnotationAuthorize extends BaseAuthorize
{
    use AnnotationParserTrait;
}
