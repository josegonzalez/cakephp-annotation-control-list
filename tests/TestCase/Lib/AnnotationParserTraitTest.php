<?php
namespace Josegonzalez\AnnotationControlList\Test\TestCase\Lib;

// $pluginPath = App::pluginPath('AnnotationControlList');
// App::build([
//    'Controller' => [$pluginPath . 'Test' . DS . 'test_app' . DS . 'Controller' . DS],
//    'Lib' => [$pluginPath . 'Test' . DS . 'test_app' . DS . 'Lib' . DS],
// ]);

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;
use Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait;
use Minime\Annotations\AnnotationsBag;
use ReflectionClass;

class TestAnnotationParserImpl
{
    use AnnotationParserTrait;

    public function __construct(Controller $Controller)
    {
        $this->_Controller = $Controller;
    }

    public function setController(Controller $Controller)
    {
        $this->_Controller = $Controller;
    }

    public function setCollection(ComponentRegistry $Collection)
    {
        $this->ComponentRegistry = $Collection;
    }
}

class TestAnnotationController extends Controller
{
    public function none()
    {
    }

    /**
     * @roles all
     */
    public function index()
    {
    }

    /**
     * @roles anonymous
     */
    public function anonymous()
    {
    }

    /**
     * @roles authenticated
     */
    public function view()
    {
    }

    /**
     * @roles admin
     */
    public function add()
    {
    }

    /**
     * @roles admin, manager
     */
    public function administrative()
    {
    }

    /**
     * @roles ["admin", "manager", "ceo"]
     */
    public function administrative_two()
    {
    }

    /**
     * @roles ["noprefix"]
     */
    public function action()
    {
    }

    /**
     * @some_prefix value
     * @some_prefix.roles ["prefix"]
     */
    public function prefix_action()
    {
    }
}

class AnnotationParserTraitTest extends TestCase
{

    public function assertPreConditions()
    {
        parent::assertPreConditions();
        $this->Controller = new TestAnnotationController(null, null);
        $this->traitObject = new TestAnnotationParserImpl($this->Controller);
    }

    public function assertPostConditions()
    {
        unset($this->traitObject);
        unset($this->Controller);
    }

    /**
     * @dataProvider isAuthorizedProvider
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::isAuthorized
     */
    public function testIsAuthorized($expected, $user, $action)
    {
        $this->assertEquals($expected, $this->traitObject->isAuthorized($user, $action));
    }

    /**
     * @dataProvider getActionRolesProvider
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::getActionRoles
     */
    public function testGetActionRoles($expected, $action)
    {
        $this->assertEquals($expected, $this->traitObject->getActionRoles($action));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::getPrefixedAnnotations
     */
    public function testGetPrefixedAnnotations()
    {
        $anonymousAnnotations = $this->traitObject->getPrefixedAnnotations('action');
        $this->assertEquals(['noprefix'], $anonymousAnnotations->get('roles'));

        $anonymousAnnotations = $this->traitObject->getPrefixedAnnotations('prefix_action');
        $this->assertNull($anonymousAnnotations->get('roles'));

        $this->traitObject = new TestAnnotationParserImpl($this->Controller);
        $this->traitObject->prefix('some_prefix');
        $anonymousAnnotations = $this->traitObject->getPrefixedAnnotations('prefix_action');
        $this->assertNotNull($anonymousAnnotations->get('roles'));
        $this->assertEquals(['prefix'], $anonymousAnnotations->get('roles'));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::getAnnotations
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::reader
     */
    public function testGetAnnotations()
    {
        $anonymousAnnotations = $this->traitObject->getAnnotations('anonymous');
        $this->assertEquals('anonymous', $anonymousAnnotations->get('roles'));
        $this->assertEquals($anonymousAnnotations, $this->traitObject->getAnnotations('anonymous'));

        $adminAnnotations = $this->traitObject->getAnnotations('administrative');
        $this->assertNotEquals($anonymousAnnotations, $adminAnnotations);
        $this->assertEquals('admin, manager', $adminAnnotations->get('roles'));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::useNamespace
     */
    public function testGetFinder()
    {
        $AnnotationsBag = new AnnotationsBag([
            'some_prefix.table' => 'Post',
            'some_prefix.method' => 'find',
            'some_prefix' => 'value',
            'find' => 'first',
            'findOptions' => [],
        ]);

        $expected = new AnnotationsBag([
            'table' => 'Post',
            'method' => 'find',
        ]);
        $actual = $this->protectedMethodCall($this->traitObject, 'useNamespace', [$AnnotationsBag, 'some_prefix']);
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /**
     * @dataProvider processRolesProvider
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::processRoles
     */
    public function testProcessRoles($expected, $roles)
    {
        $this->assertEquals($expected, $this->traitObject->processRoles($roles));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::authorize
     */
    public function testAuthorize()
    {
        $Request = $this->getMock('Cake\Network\Request');

        $Request->action = 'anonymous';
        $this->assertTrue($this->traitObject->authorize([], $Request));

        $Request->action = 'administrative';
        $this->assertFalse($this->traitObject->authorize([], $Request));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::unauthenticated
     */
    public function testUnauthenticated()
    {
        $Request = $this->getMock('Cake\Network\Request');
        $Response = $this->getMock('Cake\Network\Response');

        $Request->action = 'anonymous';
        $this->assertTrue($this->traitObject->unauthenticated($Request, $Response));

        $Request->action = 'administrative';
        $this->assertFalse($this->traitObject->unauthenticated($Request, $Response));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::getController
     */
    public function testGetController()
    {
        $this->assertEquals($this->Controller, $this->traitObject->getController());

        $traitObject = new TestAnnotationParserImpl($this->Controller);
        $collection = new ComponentRegistry($this->Controller);
        $traitObject->setCollection($collection);
        $this->assertEquals($this->Controller, $traitObject->getController());
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::prefix
     */
    public function testPrefix()
    {
        $this->assertNull($this->traitObject->prefix());
        $this->assertEquals('prefix', $this->traitObject->prefix('prefix'));
        $this->assertEquals('prefix', $this->traitObject->prefix());
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\AnnotationParserTrait::roleField
     */
    public function testRoleField()
    {
        $this->assertEquals('role', $this->traitObject->roleField());

        $this->traitObject->settings = ['roleField' => 'group'];
        $this->assertEquals('group', $this->traitObject->roleField());

        $this->traitObject->settings = ['key' => 'value'];
        $this->assertEquals('role', $this->traitObject->roleField());
    }

    public function isAuthorizedProvider()
    {
        return [
            // anonymous
            [false, [], 'none'],
            [true, [], 'index'],
            [true, [], 'anonymous'],
            [false, [], 'view'],
            [false, [], 'add'],
            [false, [], 'administrative'],
            [false, [], 'administrative_two'],
            // authenticated
            [false, ['id' => 1, 'role' => 'authenticated'], 'none'],
            [true, ['id' => 1, 'role' => 'authenticated'], 'index'],
            [false, ['id' => 1, 'role' => 'authenticated'], 'anonymous'],
            [true, ['id' => 1, 'role' => 'authenticated'], 'view'],
            [false, ['id' => 1, 'role' => 'authenticated'], 'add'],
            [false, ['id' => 1, 'role' => 'authenticated'], 'administrative'],
            [false, ['id' => 1, 'role' => 'authenticated'], 'administrative_two'],
            // admin
            [false, ['id' => 1, 'role' => 'admin'], 'none'],
            [true, ['id' => 1, 'role' => 'admin'], 'index'],
            [false, ['id' => 1, 'role' => 'admin'], 'anonymous'],
            [true, ['id' => 1, 'role' => 'admin'], 'view'],
            [true, ['id' => 1, 'role' => 'admin'], 'add'],
            [true, ['id' => 1, 'role' => 'admin'], 'administrative'],
            [true, ['id' => 1, 'role' => 'admin'], 'administrative_two'],
            // manager
            [false, ['id' => 1, 'role' => 'manager'], 'none'],
            [true, ['id' => 1, 'role' => 'manager'], 'index'],
            [false, ['id' => 1, 'role' => 'manager'], 'anonymous'],
            [true, ['id' => 1, 'role' => 'manager'], 'view'],
            [false, ['id' => 1, 'role' => 'manager'], 'add'],
            [true, ['id' => 1, 'role' => 'manager'], 'administrative'],
            [true, ['id' => 1, 'role' => 'manager'], 'administrative_two'],
            // ceo
            [false, ['id' => 1, 'role' => 'ceo'], 'none'],
            [true, ['id' => 1, 'role' => 'ceo'], 'index'],
            [false, ['id' => 1, 'role' => 'ceo'], 'anonymous'],
            [true, ['id' => 1, 'role' => 'ceo'], 'view'],
            [false, ['id' => 1, 'role' => 'ceo'], 'add'],
            [false, ['id' => 1, 'role' => 'ceo'], 'administrative'],
            [true, ['id' => 1, 'role' => 'ceo'], 'administrative_two'],
        ];
    }

    public function processRolesProvider()
    {
        return [
            [[], null],
            [[], ''],
            [['admin'], 'admin'],
            [['admin'], ' admin '],
            [['admin', 'anonymous'], 'admin,anonymous'],
            [['admin', 'anonymous'], 'admin, anonymous'],
            [['admin', 'anonymous'], ['admin', 'anonymous']],
            [['admin', 'anonymous', 'all'], ['admin', 'anonymous', 'all']],
        ];
    }

    public function getActionRolesProvider()
    {
        return [
            [['all'], 'index'],
            [['anonymous'], 'anonymous'],
            [['authenticated'], 'view'],
            [['admin'], 'add'],
            [['admin', 'manager'], 'administrative'],
            [['admin', 'manager', 'ceo'], 'administrative_two'],
        ];
    }

    protected function protectedMethodCall(&$object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
