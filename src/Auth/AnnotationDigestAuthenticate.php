<?php
namespace Josegonzalez\AnnotationControlList\Auth;

use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;
use Cake\Auth\DigestAuthenticate;

class AnnotationDigestAuthenticate extends DigestAuthenticate
{
    use AnnotationParserTrait;
}
