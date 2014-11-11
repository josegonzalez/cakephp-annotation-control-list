<?php
$pluginPath = App::pluginPath('AnnotationControlList');
App::build([
	'Controller' => [$pluginPath . 'Test' . DS . 'test_app' . DS . 'Controller' . DS],
	'Lib' => [$pluginPath . 'Test' . DS . 'test_app' . DS . 'Lib' . DS],
]);

App::uses('ComponentCollection', 'Controller');
App::uses('TestAnnotationController', 'Controller');
App::uses('TestAnnotationParserImpl', 'Lib');

class AnnotationParserTraitTest extends CakeTestCase {

	public function assertPreConditions() {
		parent::assertPreConditions();
		$this->Controller = new TestAnnotationController(null, null);
		$this->traitObject = new TestAnnotationParserImpl($this->Controller);
	}

	public function assertPostConditions() {
		unset($this->traitObject);
		unset($this->Controller);
	}

/**
 * @dataProvider isAuthorizedProvider
 * @covers AnnotationParserTrait::isAuthorized
 */
	public function testIsAuthorized($expected, $user, $action) {
		$this->assertEquals($expected, $this->traitObject->isAuthorized($user, $action));
	}

/**
 * @dataProvider getActionRolesProvider
 * @covers AnnotationParserTrait::getActionRoles
 */
	public function testGetActionRoles($expected, $action) {
		$this->assertEquals($expected, $this->traitObject->getActionRoles($action));
	}

/**
 * @covers AnnotationParserTrait::getPrefixedAnnotations
 */
	public function testGetPrefixedAnnotations() {
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
 * @covers AnnotationParserTrait::getAnnotations
 */
	public function testGetAnnotations() {
		$anonymousAnnotations = $this->traitObject->getAnnotations('anonymous');
		$this->assertEquals('anonymous', $anonymousAnnotations->get('roles'));
		$this->assertEquals($anonymousAnnotations, $this->traitObject->getAnnotations('anonymous'));

		$adminAnnotations = $this->traitObject->getAnnotations('administrative');
		$this->assertNotEquals($anonymousAnnotations, $adminAnnotations);
		$this->assertEquals('admin, manager', $adminAnnotations->get('roles'));
	}

/**
 * @dataProvider processRolesProvider
 * @covers AnnotationParserTrait::processRoles
 */
	public function testProcessRoles($expected, $roles) {
		$this->assertEquals($expected, $this->traitObject->processRoles($roles));
	}

/**
 * @covers AnnotationParserTrait::authorize
 */
	public function testAuthorize() {
		$Request = $this->getMock('CakeRequest');

		$Request->action = 'anonymous';
		$this->assertTrue($this->traitObject->authorize([], $Request));

		$Request->action = 'administrative';
		$this->assertFalse($this->traitObject->authorize([], $Request));
	}

/**
 * @covers AnnotationParserTrait::unauthenticated
 */
	public function testUnauthenticated() {
		$Request = $this->getMock('CakeRequest');
		$Response = $this->getMock('CakeResponse');

		$Request->action = 'anonymous';
		$this->assertTrue($this->traitObject->unauthenticated($Request, $Response));

		$Request->action = 'administrative';
		$this->assertFalse($this->traitObject->unauthenticated($Request, $Response));
	}

/**
 * @covers AnnotationParserTrait::getController
 */
	public function testGetController() {
		$this->assertEquals($this->Controller, $this->traitObject->getController());

		$traitObject = new TestAnnotationParserImpl($this->Controller);
		$collection = new ComponentCollection();
		$collection->init($this->Controller);
		$traitObject->setCollection($collection);
		$this->assertEquals($this->Controller, $traitObject->getController());
	}

/**
 * @covers AnnotationParserTrait::prefix
 */
	public function testPrefix() {
		$this->assertNull($this->traitObject->prefix());
		$this->assertEquals('prefix', $this->traitObject->prefix('prefix'));
		$this->assertEquals('prefix', $this->traitObject->prefix());
	}

/**
 * @covers AnnotationParserTrait::roleField
 */
	public function testRoleField() {
		$this->assertEquals('role', $this->traitObject->roleField());

		$this->traitObject->settings = ['roleField' => 'group'];
		$this->assertEquals('group', $this->traitObject->roleField());

		$this->traitObject->settings = ['key' => 'value'];
		$this->assertEquals('role', $this->traitObject->roleField());
	}

	public function isAuthorizedProvider() {
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

	public function processRolesProvider() {
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

	public function getActionRolesProvider() {
		return [
			[['all'], 'index'],
			[['anonymous'], 'anonymous'],
			[['authenticated'], 'view'],
			[['admin'], 'add'],
			[['admin', 'manager'], 'administrative'],
			[['admin', 'manager', 'ceo'], 'administrative_two'],
		];
	}

}
