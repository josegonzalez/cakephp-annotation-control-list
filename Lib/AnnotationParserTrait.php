<?php

use Minime\Annotations\Facade;

trait AnnotationParserTrait {

/**
 * Returns whether a user is allowed access to a given action
 *
 * @param array $user an array of user data. Can also be null
 * @param string $action The action to check access for
 * @return bool whether or not the user is authorized for access
 */
	public function isAuthorized($user, $action) {
		$roles = $this->getActionRoles($action);
		if (empty($roles)) {
			return false;
		}

		if (in_array('all', $roles)) {
			return true;
		}

		// if there is no user, only allow
		// anonymous access where specified
		if (empty($user)) {
			return in_array('anonymous', $roles);
		}

		// if the action allows all authenticated
		// users, allow access immediately
		if (in_array('authenticated', $roles)) {
			return true;
		}

		// allow only users who have the correct role
		return in_array($user['role'], $roles);
	}

/**
 * retrieve all the roles for the current controller action
 *
 * @param string $action name of action to get roles for
 * @return array list of roles attached to the specified action
 */
	public function getActionRoles($action) {
		$annotations = Facade::getMethodAnnotations($this->getController(), $action);
		return $this->processRoles($annotations->get('roles'));
	}

/**
 * Ensure that incoming roles are always in array form
 *
 * If the roles are specified as a string, the string list will be
 * transformed into an array by splitting by comma
 *
 * @param array|string $roles a list of roles
 * @return array list of roles
 */
	public function processRoles($roles) {
		if ($roles === null) {
			$roles = [];
		} elseif (is_string($roles)) {
			$roles = trim($roles);
			if (strlen($roles) === 0) {
				$roles = [];
			} else {
				$roles = array_map('trim', explode(',', $roles));
			}
		}

		return $roles;
	}

/**
 * Checks user authorization.
 *
 * @param array $user Active user data
 * @param CakeRequest $request The request needing authorization.
 * @return bool
 */
	public function authorize($user, CakeRequest $request) {
		return $this->isAuthorized($user, $request->param('action'));
	}

/**
 * Handle unauthenticated access attempt.
 *
 * @param CakeRequest $request A request object.
 * @param CakeResponse $response A response object.
 * @return mixed Either true to indicate the unauthenticated request has been
 *  dealt with and no more action is required by AuthComponent or void (default).
 */
	public function unauthenticated(CakeRequest $request, CakeResponse $response) {
		return $this->isAuthorized(null, $request->param('action'));
	}

/**
 * Get the controller associated with the current class
 *
 * @return Controller Controller instance
 */
	public function getController() {
		if (!empty($this->_Collection)) {
			return $this->_Collection->getController();
		}

		return $this->_Controller;
	}

}
