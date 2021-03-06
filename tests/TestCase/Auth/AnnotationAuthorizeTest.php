<?php
namespace Josegonzalez\AnnotationControlList\Test\TestCase\Auth;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;
use Josegonzalez\AnnotationControlList\Auth\AnnotationAuthorize;

class AnnotationAuthorizeTest extends TestCase
{
    public function testConstruct()
    {
        $ComponentRegistry = $this->getMock('Cake\Controller\ComponentRegistry', ['getController']);
        new AnnotationAuthorize($ComponentRegistry, []);
    }
}
