<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Cake\Auth\BasicAuthenticate;
use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;

class ModelBasicAuthenticate extends BasicAuthenticate
{
    use ModelParserTrait;
}
