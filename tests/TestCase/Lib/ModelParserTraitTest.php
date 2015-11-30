<?php
namespace Josegonzalez\AnnotationControlList\Test\TestCase\Lib;

// $pluginPath = App::pluginPath('AnnotationControlList');
// App::build([
//     'Controller' => [$pluginPath . 'Test' . DS . 'test_app' . DS . 'Controller' . DS],
//     'Lib' => [$pluginPath . 'Test' . DS . 'test_app' . DS . 'Lib' . DS],
// ]);

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Plugin;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use Exception;
use Josegonzalez\AnnotationControlList\Lib\ModelParserTrait;
use Minime\Annotations\AnnotationsBag;

class TestModelParserImpl
{
    use ModelParserTrait;

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

class TestModelAnnotationController extends Controller
{
    public function none()
    {
    }

    /**
     * @isAuthorized.roles all
     */
    public function index()
    {
    }

    /**
     * @isAuthorized.roles anonymous
     */
    public function anonymous()
    {
    }

    /**
     * @isAuthorized.roles authenticated
     */
    public function view()
    {
    }

    /**
     * @isAuthorized.roles admin
     */
    public function add()
    {
    }

    /**
     * @isAuthorized.roles admin, manager
     */
    public function administrative()
    {
    }

    /**
     * @isAuthorized.roles ["admin", "manager", "ceo"]
     */
    public function administrative_two()
    {
    }

    /**
     * @isAuthorized.roles authenticated
     */
    public function missing_finder()
    {
    }

    /**
     * @isAuthorized.roles authenticated
     * @isAuthorized.model Post
     * @isAuthorized.find first
     */
    public function has_finder()
    {
    }

    /**
     * @isAuthorized.roles authenticated
     * @isAuthorized.always ["group", "admin"]
     * @isAuthorized.model Post
     * @isAuthorized.find first
     */
    public function always_if_admin()
    {
    }

    /**
     * @isAuthorized.roles authenticated
     * @isAuthorized.always ["group", "admin"]
     * @isAuthorized.model Post
     * @isAuthorized.find first
     * @isAuthorized.conditions.if ["group", "group_name"]
     */
    public function if_conditions()
    {
    }
}

class ModelParserTraitTest extends TestCase
{
    public function assertPreConditions()
    {
        parent::assertPreConditions();
        $this->TraitObject = $this->getObjectForTrait('Josegonzalez\AnnotationControlList\Lib\ModelParserTrait');
        $this->Controller = new TestModelAnnotationController(null, null);
    }

    public function assertPostConditions()
    {
        unset($this->TraitObject);
        unset($this->Controller);
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::isAuthorized
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::performCheck
     */
    public function testPerformCheck()
    {
        $this->Controller->request = $this->getMock('stdClass');
        $this->Controller->Post = $this->getMock('stdClass', ['find']);
        $this->Controller->Post->expects($this->at(0))
            ->method('find')
            ->will($this->returnValue(null));
        $this->Controller->Post->expects($this->at(1))
            ->method('find')
            ->will($this->returnValue(true));
        $this->Controller->Post->expects($this->at(3))
            ->method('find')
            ->will($this->throwException(new Exception));
        $this->Controller->Post->expects($this->at(4))
            ->method('find')
            ->will($this->returnValue(['group_name' => 'troll']));
        $this->Controller->Post->expects($this->at(5))
            ->method('find')
            ->will($this->returnValue(['group_name' => 'not_troll']));
        $this->Controller->request->params = [];

        $TraitObject = new TestModelParserImpl($this->Controller);
        $this->assertFalse($TraitObject->isAuthorized([], 'none'));
        $this->assertTrue($TraitObject->isAuthorized(['id' => 1], 'missing_finder'));
        $this->assertFalse($TraitObject->isAuthorized(['id' => 1], 'has_finder'));
        $this->assertTrue($TraitObject->isAuthorized(['id' => 1], 'has_finder'));
        $this->assertTrue($TraitObject->isAuthorized(['id' => 1, 'group' => 'admin'], 'always_if_admin'));
        $this->assertFalse($TraitObject->isAuthorized(['id' => 1, 'group' => 'troll'], 'always_if_admin'));
        $this->assertFalse($TraitObject->isAuthorized(['id' => 1, 'group' => 'troll'], 'always_if_admin'));
        $this->assertTrue($TraitObject->isAuthorized(['id' => 1, 'group' => 'troll'], 'if_conditions'));
        $this->assertFalse($TraitObject->isAuthorized(['id' => 1, 'group' => 'troll'], 'if_conditions'));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::checkAlwaysRule
     */
    public function testCheckAlwaysRule()
    {
        $annotations = $this->getMock('AnnotationsBag', ['get']);
        $annotations->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue(null));
        $annotations->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue(null));
            $annotations->expects($this->at(2))
            ->method('get')
            ->will($this->returnValue('not-empty'));
        $annotations->expects($this->at(3))
            ->method('get')
            ->will($this->returnValue(['id' => 1]));
        $annotations->expects($this->at(4))
            ->method('get')
            ->will($this->returnValue(['id']));
        $annotations->expects($this->at(5))
            ->method('get')
            ->will($this->returnValue(['id', 1, 2]));
        $annotations->expects($this->at(6))
            ->method('get')
            ->will($this->returnValue(['id', 1]));

        $this->assertFalse($this->TraitObject->checkAlwaysRule($annotations, null));
        $this->assertFalse($this->TraitObject->checkAlwaysRule($annotations, ['id' => 1]));
        $this->assertFalse($this->TraitObject->checkAlwaysRule($annotations, ['id' => 1]));
        $this->assertFalse($this->TraitObject->checkAlwaysRule($annotations, ['id' => 1]));
        $this->assertFalse($this->TraitObject->checkAlwaysRule($annotations, ['id' => 1]));
        $this->assertFalse($this->TraitObject->checkAlwaysRule($annotations, ['id' => 1]));
        $this->assertTrue($this->TraitObject->checkAlwaysRule($annotations, ['id' => 1]));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::checkIfRules
     */
    public function testPerformIfCheck()
    {
        $rules = [
            ['id', 'user_id'],
            ['null', 'null'],
            ['group_id', 'group_id'],
        ];
        $record = [
            'id' => 1,
            'user_id' => 2,
            'group_id' => 7,
            'non_null' => 'non_null',
        ];

        $this->assertFalse($this->TraitObject->checkIfRules($rules, null, $record));
        $this->assertFalse($this->TraitObject->checkIfRules($rules, ['id' => 1], $record));
        $this->assertTrue($this->TraitObject->checkIfRules($rules, ['id' => 2], $record));
        $this->assertTrue($this->TraitObject->checkIfRules($rules, ['group_id' => 7], $record));

        $recordObject = $this->getMock('stdClass', ['toArray']);
        $recordObject->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($record));

        $this->assertFalse($this->TraitObject->checkIfRules($rules, null, $recordObject));
        $this->assertFalse($this->TraitObject->checkIfRules($rules, ['id' => 1], $recordObject));
        $this->assertTrue($this->TraitObject->checkIfRules($rules, ['id' => 2], $recordObject));
        $this->assertTrue($this->TraitObject->checkIfRules($rules, ['group_id' => 7], $recordObject));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::getData
     */
    public function testGetData()
    {
        $Model = $this->getMock('Cake\ORM\Table', ['find', 'retrieve']);
        $Model->expects($this->at(0))
            ->method('find')
            ->with('first', [])
            ->will($this->returnValue(true));
        $Model->expects($this->at(1))
            ->method('find')
            ->with('first', [])
            ->will($this->returnValue(true));
        $Model->expects($this->at(2))
            ->method('find')
            ->with('first', [])
            ->will($this->returnValue(true));
        $Model->expects($this->once())
            ->method('retrieve')
            ->will($this->returnValue(['id' => 1]));

        $methodName = '';
        $this->assertTrue($this->TraitObject->getData($Model, $methodName, 'first', []));

        $methodName = 'find';
        $this->assertTrue($this->TraitObject->getData($Model, $methodName, 'first', []));

        $methodName = 'find';
        $findMethod = '';
        $this->assertTrue($this->TraitObject->getData($Model, $methodName, $findMethod, []));

        $methodName = 'retrieve';
        $this->assertEquals(['id' => 1], $this->TraitObject->getData($Model, $methodName, 'first', []));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::getFinder
     */
    public function testGetFinder()
    {
        $AnnotationsBag = new AnnotationsBag([
            'model' => 'Post',
            'method' => 'find',
            'find' => 'first',
            'findOptions' => [],
        ]);

        $Controller = $this->getMock('\Cake\Controller\Controller');
        $this->assertEquals(
            ['Post', 'find', 'first', []],
            $this->TraitObject->getFinder($Controller, $AnnotationsBag)
        );

        $AnnotationsBag = new AnnotationsBag([
            'method' => 'find',
            'find' => 'first',
            'findOptions' => [],
        ]);

        $Controller = $this->getMock('\Cake\Controller\Controller');
        $Controller->modelClass = 'Post';

        $this->assertEquals(
            ['Post', 'find', 'first', []],
            $this->TraitObject->getFinder($Controller, $AnnotationsBag)
        );
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::missingFinder
     */
    public function testMissingFinder()
    {
        $this->assertTrue($this->TraitObject->missingFinder(null, null, null));
        $this->assertTrue($this->TraitObject->missingFinder(null, '', null));
        $this->assertTrue($this->TraitObject->missingFinder('Post', '', null));
        $this->assertTrue($this->TraitObject->missingFinder(null, 'find', null));
        $this->assertFalse($this->TraitObject->missingFinder('Post', 'find', null));
        $this->assertFalse($this->TraitObject->missingFinder('Post', 'getData', null));

        $this->assertTrue($this->TraitObject->missingFinder(null, null, 'first'));
        $this->assertTrue($this->TraitObject->missingFinder(null, '', 'first'));
        $this->assertFalse($this->TraitObject->missingFinder('Post', '', 'first'));
        $this->assertTrue($this->TraitObject->missingFinder(null, 'find', 'first'));
        $this->assertFalse($this->TraitObject->missingFinder('Post', 'find', 'first'));
        $this->assertFalse($this->TraitObject->missingFinder('Post', 'getData', 'first'));
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::ensureList
     */
    public function testEnsureList()
    {
        $this->assertEquals([['key', 'value']], $this->TraitObject->ensureList(['key', 'value']));
        $this->assertEquals([['key', 'value']], $this->TraitObject->ensureList([['key', 'value']]));
        $this->assertEquals(
            [['key', 'value'], ['another' => 'value']],
            $this->TraitObject->ensureList([['key', 'value'], ['another' => 'value']])
        );
    }

    /**
     * @covers Josegonzalez\AnnotationControlList\Lib\ModelParserTrait::isAssoc
     */
    public function testIsAssoc()
    {
        $this->assertFalse($this->TraitObject->isAssoc(null));
        $this->assertFalse($this->TraitObject->isAssoc(true));
        $this->assertFalse($this->TraitObject->isAssoc(0));
        $this->assertFalse($this->TraitObject->isAssoc(''));
        $this->assertFalse($this->TraitObject->isAssoc(['key']));
        $this->assertFalse($this->TraitObject->isAssoc(['key', 'value']));
        $this->assertTrue($this->TraitObject->isAssoc(['key' => 'value']));
        $this->assertTrue($this->TraitObject->isAssoc(['key' => 'value', 'another' => 'value']));
    }
}
