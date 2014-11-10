<?php
$pluginPath = App::pluginPath('AnnotationControlList');
App::build([
	'Controller' => [$pluginPath . 'Test' . DS . 'test_app' . DS . 'Controller' . DS],
	'Lib' => [$pluginPath . 'Test' . DS . 'test_app' . DS . 'Lib' . DS],
]);

App::uses('TestAnnotationController', 'Controller');
App::uses('TestAnnotationParserImpl', 'Lib');

class AnnotationParserTraitTest extends CakeTestCase {

	public function assertPreConditions() {
		parent::assertPreConditions();
		$Controller = new TestAnnotationController(null, null);
		$this->traitObject = new TestAnnotationParserImpl($Controller);
	}

	public function assertPostConditions() {
		unset($this->traitObject);
	}

	public function testIsAuthorized() {
		$anonymousUser = [];
		$this->assertTrue($this->traitObject->isAuthorized($anonymousUser, 'index'));
		$this->assertTrue($this->traitObject->isAuthorized($anonymousUser, 'anonymous'));
		$this->assertFalse($this->traitObject->isAuthorized($anonymousUser, 'view'));
		$this->assertFalse($this->traitObject->isAuthorized($anonymousUser, 'add'));
		$this->assertFalse($this->traitObject->isAuthorized($anonymousUser, 'administrative'));
		$this->assertFalse($this->traitObject->isAuthorized($anonymousUser, 'administrative_two'));

		$authenticatedUser = ['id' => 1, 'role' => 'authenticated'];
		$this->assertTrue($this->traitObject->isAuthorized($authenticatedUser, 'index'));
		$this->assertFalse($this->traitObject->isAuthorized($authenticatedUser, 'anonymous'));
		$this->assertTrue($this->traitObject->isAuthorized($authenticatedUser, 'view'));
		$this->assertFalse($this->traitObject->isAuthorized($authenticatedUser, 'add'));
		$this->assertFalse($this->traitObject->isAuthorized($authenticatedUser, 'administrative'));
		$this->assertFalse($this->traitObject->isAuthorized($authenticatedUser, 'administrative_two'));

		$adminUser = ['id' => 1, 'role' => 'admin'];
		$this->assertTrue($this->traitObject->isAuthorized($adminUser, 'index'));
		$this->assertFalse($this->traitObject->isAuthorized($adminUser, 'anonymous'));
		$this->assertTrue($this->traitObject->isAuthorized($adminUser, 'view'));
		$this->assertTrue($this->traitObject->isAuthorized($adminUser, 'add'));
		$this->assertTrue($this->traitObject->isAuthorized($adminUser, 'administrative'));
		$this->assertTrue($this->traitObject->isAuthorized($adminUser, 'administrative_two'));

		$managerUser = ['id' => 1, 'role' => 'manager'];
		$this->assertTrue($this->traitObject->isAuthorized($managerUser, 'index'));
		$this->assertFalse($this->traitObject->isAuthorized($managerUser, 'anonymous'));
		$this->assertTrue($this->traitObject->isAuthorized($managerUser, 'view'));
		$this->assertFalse($this->traitObject->isAuthorized($managerUser, 'add'));
		$this->assertTrue($this->traitObject->isAuthorized($managerUser, 'administrative'));
		$this->assertTrue($this->traitObject->isAuthorized($managerUser, 'administrative_two'));

		$ceoUser = ['id' => 1, 'role' => 'ceo'];
		$this->assertTrue($this->traitObject->isAuthorized($ceoUser, 'index'));
		$this->assertFalse($this->traitObject->isAuthorized($ceoUser, 'anonymous'));
		$this->assertTrue($this->traitObject->isAuthorized($ceoUser, 'view'));
		$this->assertFalse($this->traitObject->isAuthorized($ceoUser, 'add'));
		$this->assertFalse($this->traitObject->isAuthorized($ceoUser, 'administrative'));
		$this->assertTrue($this->traitObject->isAuthorized($ceoUser, 'administrative_two'));
	}

	public function testGetActionRoles() {
		$this->assertEquals(
			['all'],
			$this->traitObject->getActionRoles('index')
		);
		$this->assertEquals(
			['anonymous'],
			$this->traitObject->getActionRoles('anonymous')
		);
		$this->assertEquals(
			['authenticated'],
			$this->traitObject->getActionRoles('view')
		);
		$this->assertEquals(
			['admin'],
			$this->traitObject->getActionRoles('add')
		);
		$this->assertEquals(
			['admin', 'manager'],
			$this->traitObject->getActionRoles('administrative')
		);
		$this->assertEquals(
			['admin', 'manager', 'ceo'],
			$this->traitObject->getActionRoles('administrative_two')
		);
	}

	public function testProcessRoles() {
		$this->assertEquals([], $this->traitObject->processRoles(null));
		$this->assertEquals([], $this->traitObject->processRoles(''));
		$this->assertEquals(['admin'], $this->traitObject->processRoles('admin'));
		$this->assertEquals(['admin'], $this->traitObject->processRoles(' admin '));

		$this->assertEquals(['admin', 'anonymous'],
			$this->traitObject->processRoles('admin,anonymous')
		);
		$this->assertEquals(['admin', 'anonymous'],
			$this->traitObject->processRoles('admin, anonymous')
		);

		$this->assertEquals(['admin', 'anonymous'],
			$this->traitObject->processRoles(['admin', 'anonymous'])
		);
		$this->assertEquals(['admin', 'anonymous', 'all'],
			$this->traitObject->processRoles(['admin', 'anonymous', 'all'])
		);
	}

}
