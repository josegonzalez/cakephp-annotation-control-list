<?php
namespace Josegonzalez\AnnotationControlList\Lib;

use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Utility\Hash;
use Minime\Annotations\AnnotationsBag;
use Minime\Annotations\Reader;

trait AnnotationParserTrait
{
    protected $_actionAnnotations = [];

    protected $_prefix = null;

    protected $_reader = null;

    /**
     * Returns whether a user is allowed access to a given action
     *
     * @param array $user an array of user data. Can also be null
     * @param string $action The action to check access for
     * @return bool whether or not the user is authorized for access
     */
    public function isAuthorized($user, $action)
    {
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
        $roleField = $this->roleField();

        return in_array($user[$roleField], $roles);
    }

    /**
     * Retrieve all the processed roles for the current controller action
     *
     * @param string $action name of action to get roles for
     * @return array list of roles attached to the specified action
     */
    public function getActionRoles($action)
    {
        $annotations = $this->getPrefixedAnnotations($action);

        return $this->processRoles($annotations->get('roles'));
    }

    /**
     * Retrieve all the roles for the controller action with a set prefix
     *
     * @param string $action name of action to get roles for
     * @return array list of roles attached to the specified action
     */
    public function getPrefixedAnnotations($action)
    {
        $prefix = $this->prefix();
        $annotations = $this->getAnnotations($action);

        if (!empty($prefix)) {
            $annotations = $this->useNamespace($annotations, $prefix);
        }

        return $annotations;
    }

    /**
     * Isolates a given namespace of annotations.
     *
     * @param \Minime\Annotations\AnnotationsBag $annotations A bag of annotations
     * @param string $prefix namespace
     * @return \Minime\Annotations\AnnotationsBag A bag of annotations
     */
    protected function useNamespace(AnnotationsBag $annotations, $prefix)
    {
        $data = [];
        $consumer = '(' . implode('|', array_map('preg_quote', ['.'])) . ')';
        $namespacePattern = '/^' . preg_quote(rtrim($prefix, '.')) . $consumer . '/';

        foreach ($annotations->toArray() as $key => $value) {
            $newKey = preg_replace($namespacePattern, '', $key);
            if ($newKey === null || $newKey === $key) {
                continue;
            }
            $data[$newKey] = $value;
        }

        return new AnnotationsBag($data);
    }

    /**
     * Retrieve all the roles for the controller action
     *
     * @param string $action name of action to get roles for
     * @return array list of roles attached to the specified action
     */
    public function getAnnotations($action)
    {
        if (!isset($this->_actionAnnotations[$action])) {
            $this->_actionAnnotations[$action] = $this->reader()->getMethodAnnotations(
                $this->getController(),
                $action
            );
        }

        return $this->_actionAnnotations[$action];
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
    public function processRoles($roles)
    {
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
     * @param Cake\Network\Request $request The request needing authorization.
     * @return bool
     */
    public function authorize($user, Request $request)
    {
        return $this->isAuthorized($user, $request->action);
    }

    /**
     * Handle unauthenticated access attempt.
     *
     * @param Cake\Network\Request $request A request object.
     * @param Cake\Network\Response $response A response object.
     * @return mixed Either true to indicate the unauthenticated request has been
     *  dealt with and no more action is required by AuthComponent or void (default).
     */
    public function unauthenticated(Request $request, Response $response)
    {
        $response;

        return $this->isAuthorized(null, $request->action);
    }

    /**
     * Get the controller associated with the current class
     *
     * @return Controller Controller instance
     */
    public function getController()
    {
        if (!empty($this->_registry)) {
            return $this->_registry->getController();
        }

        return $this->_Controller;
    }

    /**
     * Getter and setter of the annotations
     *
     * @param string $prefix if set, the prefix that should be applied
     * @return array prefix for the annotations
     */
    public function prefix($prefix = null)
    {
        if ($prefix === null) {
            return $this->_prefix;
        }

        if (is_string($prefix)) {
            $prefix = $prefix;
        }

        return $this->_prefix = $prefix;
    }

    /**
     * Retrieves settings for the current object
     *
     * @return array settings for the current object
     */
    public function roleField()
    {
        if (empty($this->settings)) {
            $settings = [];
        } else {
            $settings = $this->settings;
        }

        $roleField = Hash::get($settings, 'roleField');
        if ($roleField === null) {
            return 'role';
        }

        return $roleField;
    }

    /**
     * Retrieves a reader object
     *
     * @return Minime\Annotations\Reader
     */
    protected function reader()
    {
        if ($this->_reader === null) {
            $this->_reader = Reader::createFromDefaults();
        }

        return $this->_reader;
    }
}
