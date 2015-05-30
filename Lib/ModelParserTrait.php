<?php

App::uses('AnnotationParserTrait', 'AnnotationControlList.Lib');
App::uses('Controller', 'Controller');
App::uses('DebugTimer', 'DebugKit.Lib');
App::uses('Hash', 'Utility');
App::uses('Model', 'Model');

use Minime\Annotations\AnnotationsBag;
use Minime\Annotations\Reader;

trait ModelParserTrait {

	use AnnotationParserTrait {
		isAuthorized as protected __isAuthorized;
	}

/**
 * Wrapper around _isAuthorized to allow for timing
 *
 * @param array $user an array of user data. Can also be null
 * @param string $action The action to check access for
 * @return bool whether or not the user is authorized for access
 */
	public function isAuthorized($user, $action) {
		$timerExists = Configure::read('debug') && class_exists('DebugTimer');
		if ($timerExists) {
			DebugTimer::start(get_called_class() . '->isAuthorized()');
		}

		$return = $this->performCheck($user, $action);

		if ($timerExists) {
			DebugTimer::stop(get_called_class() . '->isAuthorized()');
		}

		return $return;
	}

/**
 * Returns whether a user is allowed access to a given action
 *
 * @param array $user an array of user data. Can also be null
 * @param string $action The action to check access for
 * @return bool whether or not the user is authorized for access
 */
	public function performCheck($user, $action) {
		$this->prefix('isAuthorized');
		if (!$this->__isAuthorized($user, $action)) {
			return false;
		}

		$user = $user ?: [];
		$controller = $this->getController();
		$annotations = $this->getPrefixedAnnotations($action);

		list($modelName, $methodName, $findMethod, $findOptions) = $this->getFinder($controller, $annotations);

		$annotations = $this->getPrefixedAnnotations($action);
		if ($this->checkAlwaysRule($annotations, $user) === true) {
			return true;
		}

		if ($this->missingFinder($modelName, $methodName, $findMethod)) {
			$this->prefix('isAuthorized');
			$roles = $this->getActionRoles($action);
			return in_array('authenticated', $roles);
		}

		$findOptions = array_merge((array)$findOptions, $controller->request->params);
		$findOptions['user_id'] = Hash::get($user, 'id');

		$record = [];
		try {
			$Model = $controller->$modelName;
			$record = $this->getData($Model, $methodName, $findMethod, $findOptions);
		} catch (Exception $e) {
			CakeLog::debug($e->getMessage());
			return false;
		}

		$rules = $annotations->useNamespace('conditions')->get('if');
		if (empty($rules)) {
			return !empty($record);
		}

		$rules = $this->ensureList($rules);
		return $this->checkIfRules($rules, $user, $record);
	}

/**
 * Runs the specified `always` check from the AnnotationBag
 *
 * @param AnnotationsBag $annotations bag of annotations
 * @param array $user an array of user data. Can also be null
 * @return bool whether or not the user is authorized for access
 * @throws LogicException If isAuthorized.model or isAuthorized.find is not set
 */
	public function checkAlwaysRule($annotations, $user) {
		$user = $user ?: [];
		$always = $annotations->get('always');
		if (empty($always)) {
			return false;
		}

		if (!is_array($always)) {
			return false;
		}

		if (count($always) !== 2) {
			return false;
		}

		return Hash::get($user, $always[0], null) == $always[1];
	}

/**
 * Runs the specified `if` checks from the AnnotationBag
 *
 * @param array $rules an array of rules
 * @param array $user an array of user data. Can also be null
 * @param array $record an array of model data. If object, then a toArray()
 *                      method may be called to retrieve an array of data
 * @return bool whether or not the user is authorized for access
 * @throws LogicException If isAuthorized.model or isAuthorized.find is not set
 */
	public function checkIfRules($rules, $user, $record) {
		$user = $user ?: [];

		if (is_object($record) && method_exists($record, 'toArray')) {
			$record = $record->toArray();
		}

		foreach ($rules as $rule) {
			$userVal = Hash::get($user, $rule[0]);
			$recordVal = Hash::get($record, $rule[1]);
			if ($userVal == null || $recordVal == null) {
				continue;
			}

			if ($userVal == $recordVal) {
				return true;
			}
		}
		return false;
	}

/**
 * Checks whether a variable is an array
 *
 * @param Model $Model Instance of a model
 * @param string $methodName Name of method
 * @param string $findMethod Find method to use
 * @param array $findOptions array of options to pass to the model method
 * @return mixed
 **/
	public function getData($Model, $methodName, $findMethod, $findOptions) {
		if (empty($methodName)) {
			$methodName = 'find';
		}

		if (empty($findMethod)) {
			$findMethod = 'first';
		}

		if ($methodName == 'find') {
			return $Model->find($findMethod, $findOptions);
		}
		return $Model->$methodName($findOptions);
	}

/**
 * Retrieves finder information
 *
 * @param Controller $Controller Instance of a controller
 * @param AnnotationsBag $annotations a bag of annotations
 * @return array
 **/
	public function getFinder(Controller $Controller, AnnotationsBag $annotations) {
		$modelName = $annotations->get('model');
		$methodName = $annotations->get('method');
		$findMethod = $annotations->get('find');
		$findOptions = $annotations->useNamespace('options')->export();

		if (empty($modelName)) {
			$modelName = $Controller->modelClass;
		}

		return [$modelName, $methodName, $findMethod, $findOptions];
	}

/**
 * Checks whether a variable is an array
 *
 * @param string $modelName Name of model
 * @param string $methodName Name of method
 * @param string $findMethod Find method to use
 * @return bool
 **/
	public function missingFinder($modelName, $methodName, $findMethod) {
		if (empty($findMethod)) {
			return empty($modelName) || empty($methodName);
		}

		return empty($modelName);
	}

/**
 * Ensures an "array" of rules is list
 *
 * @param mixed $rules list of rules
 * @return array
 **/
	public function ensureList($rules) {
		if (count($rules) == 2 && !is_array($rules[0])) {
			return [[$rules[0], $rules[1]]];
		}
		return $rules;
	}

/**
 * Checks whether a variable is an array
 *
 * @param mixed $variable variable to be checked
 * @return bool
 **/
	public function isAssoc($variable) {
		if (!is_array($variable)) {
			return false;
		}

		return array_keys($variable) !== range(0, count($variable) - 1);
	}

}
