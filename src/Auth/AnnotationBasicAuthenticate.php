<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Cake\Auth\BasicAuthenticate;
use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;

class AnnotationBasicAuthenticate extends BasicAuthenticate
{
    use AnnotationParserTrait;
}
