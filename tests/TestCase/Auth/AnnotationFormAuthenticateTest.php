<?php
namespace Josegonzalez\AnnotationControlList\Test\TestCase\Auth;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;
use Josegonzalez\AnnotationControlList\Auth\AnnotationFormAuthenticate;

class AnnotationFormAuthenticateTest extends TestCase
{
    public function testConstruct()
    {
        $ComponentRegistry = $this->getMock('Cake\Controller\ComponentRegistry', ['init']);
        new AnnotationFormAuthenticate($ComponentRegistry, []);
    }
}
