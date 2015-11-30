<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;
use Cake\Auth\DigestAuthenticate;

class ModelDigestAuthenticate extends DigestAuthenticate
{
    use ModelParserTrait;
}
