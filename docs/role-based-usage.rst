Role-based usage
================

The ``AnnotationControlList`` plugin has two modes of usage. The ``role`` mode requires no more configuration than an ``@roles`` annotation on your action.

Setup
-----

Setup your ``AuthComponent`` to use the ``AnnotationAuthorize`` and ``AnnotationFormAuthenticate`` classes:

.. code:: php

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Josegonzalez/AnnotationControlList.AnnotationForm' => [
                    'passwordHasher' => 'Blowfish',
                    'roleField' => 'role',  // `roleField` is `role` by default
                ]
            ],
            'authorize' => [
                'Josegonzalez/AnnotationControlList.Annotation',
                'roleField' => 'role',  // `roleField` is `role` by default
            ],
        ]);
    }

Requiring roles for a given action
----------------------------------

Annotate your methods with the roles you want to allow:

.. code:: php

    /**
     * @roles all
     */
    public function index() {}

    /**
     * @roles authenticated
     */
    public function add() {}

    /**
     * this is a list of roles
     * @roles anonymous, some_other_role
     */
    public function register() {}

    /**
     * this is also a list of roles
     * @roles ["admin", "a_special_role"]
     */
    public function admin() {}


You can specify one or more roles in any of the above formats. If no role is specified for an action, then no user will be allowed access.

Special Roles
-------------

The following roles have a special meaning:

- ``all``: All users will have this role
- ``anonymous``: Users that have not yet authenticated against your app will have this role
- ``authenticated``: Users that have been authenticated fall in this role

Available Classes
-----------------

The following classes are available for your convenience:

- ``AnnotationAuthorize``
- ``AnnotationBasicAuthenticate``
- ``AnnotationDigestAuthenticate``
- ``AnnotationFormAuthenticate``

These extend the core classes and override the following methods:

- ``isAuthorized``
- ``getActionRoles``
- ``getPrefixedAnnotations``
- ``getAnnotations``
- ``processRoles``
- ``authorize``
- ``unauthenticated``
- ``getController``
- ``prefix``

Custom Authenticate Classes
---------------------------

The ``AnnotationFormAuthenticate`` class extends ``FormAuthenticate`` to override the ``unauthorized()`` method, allowing us to use the annotations to define access even if the user has not yet authenticated. You can follow this pattern for any Authenticate class you create/use by adding the following to either your custom authenticate class or a subclass of one of the core classes:

.. code:: php

    use AnnotationParserTrait;
