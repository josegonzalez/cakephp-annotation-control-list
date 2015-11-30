<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;
use Cake\Auth\BasicAuthenticate;

class AnnotationBasicAuthenticate extends BasicAuthenticate
{
    use AnnotationParserTrait;
}
