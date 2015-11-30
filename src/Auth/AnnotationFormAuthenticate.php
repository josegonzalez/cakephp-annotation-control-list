<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;
use Cake\Auth\FormAuthenticate;

class AnnotationFormAuthenticate extends FormAuthenticate
{
    use AnnotationParserTrait;
}
