<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Cake\Auth\DigestAuthenticate;
use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;

class AnnotationDigestAuthenticate extends DigestAuthenticate
{
    use AnnotationParserTrait;
}
