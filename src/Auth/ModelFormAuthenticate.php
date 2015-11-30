<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;
use Cake\Auth\FormAuthenticate;

class ModelFormAuthenticate extends FormAuthenticate
{
    use ModelParserTrait;
}
