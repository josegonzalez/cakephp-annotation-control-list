<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Cake\Auth\FormAuthenticate;
use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;

class AnnotationFormAuthenticate extends FormAuthenticate
{
    use AnnotationParserTrait;
}
