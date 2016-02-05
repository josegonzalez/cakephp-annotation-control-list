<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Cake\Auth\DigestAuthenticate;
use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;

class ModelDigestAuthenticate extends DigestAuthenticate
{
    use ModelParserTrait;
}
