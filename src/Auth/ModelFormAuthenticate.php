<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Cake\Auth\FormAuthenticate;
use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;

class ModelFormAuthenticate extends FormAuthenticate
{
    use ModelParserTrait;
}
