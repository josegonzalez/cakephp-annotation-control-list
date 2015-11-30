<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;
use Cake\Auth\BasicAuthenticate;

class ModelBasicAuthenticate extends BasicAuthenticate
{
    use ModelParserTrait;
}
