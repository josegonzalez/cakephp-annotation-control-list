Model-based usage
=================

The ``AnnotationControlList`` plugin has two modes of usage. The ``model`` mode requires more configuration than the ``role`` mode, but also allows you to extend access control to include information from your database records.

Setup
-----

Setup your ``AuthComponent`` to use the ``AnnotationAuthorize`` and ``AnnotationFormAuthenticate`` classes:

.. code:: php

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Josegonzalez/AnnotationControlList.ModelForm' => [
                    'passwordHasher' => 'Blowfish',
                    'roleField' => 'role',  // `roleField` is `role` by default
                ]
            ],
            'authorize' => [
                'Josegonzalez/AnnotationControlList.Model',
                'roleField' => 'role',  // `roleField` is `role` by default
            ],
        ]);
    }

Requiring roles for a given action
----------------------------------

Annotate your methods with the roles you want to allow:

.. code:: php

    /**
     * @isAuthorized.roles all
     */
    public function index() {}

    /**
     * @isAuthorized.roles authenticated
     */
    public function add() {}

    /**
     * this is a list of roles
     * @isAuthorized.roles anonymous, some_other_role
     */
    public function register() {}

    /**
     * this is also a list of roles
     * @isAuthorized.roles ["admin", "a_special_role"]
     */
    public function admin() {}

    /**
     * Only allows authenticated users access if the finder returns data
     * @isAuthorized.roles authenticated
     * @isAuthorized.table Post
     * @isAuthorized.find active
     */
    public function active_post() {
    }

    /**
     * Only allows authenticated users access if the Post.check_active()
     * method returns data
     *
     * @isAuthorized.roles authenticated
     * @isAuthorized.table Post
     * @isAuthorized.method check_active
     */
    public function active_post() {
    }

    /**
     * Only allows authenticated users access if the finder returns data
     *
     * If the authenticated user's "group" field is "admin", then they are
     * allowed access without further database checks
     *
     * @isAuthorized.roles authenticated
     * @isAuthorized.always ["group", "admin"]
     * @isAuthorized.table Post
     * @isAuthorized.find active
     */
    public function always_if_admin() {
    }

    /**
     * Only allows authenticated users access if the finder returns data
     *
     * If the authenticated user's "group" field is "admin", then they are
     * allowed access without further database checks
     *
     * If the user's "group" field matches the "Post.group_name", then they are
     * allowed access, otherwise they are denied access. You can have multiple
     * "if" conditions, and if any are true, then access is granted
     * @isAuthorized.roles authenticated
     * @isAuthorized.always ["group", "admin"]
     * @isAuthorized.table Post
     * @isAuthorized.find edit
     * @isAuthorized.conditions.if ["group", "Post.group_name"]
     */
    public function edit_post() {
    }

When a `Model::find()` is called, the current request parameters - as well as the ``user_id`` - are passed into the find as options. This can be used to further limit the data being retrieved. If an alternative model method is specified, then the current request parameters and ``user_id`` are passed in as the first argument.


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

- ``ModelAuthorize``
- ``ModelBasicAuthenticate``
- ``ModelDigestAuthenticate``
- ``ModelFormAuthenticate``

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
- ``performCheck``
- ``checkAlwaysRule``
- ``checkIfRules``
- ``getData``
- ``getFinder``
- ``missingFinder``
- ``ensureList``
- ``isAssoc``

Custom Authenticate Classes
---------------------------

The ``AnnotationFormAuthenticate`` class extends ``FormAuthenticate`` to override the ``unauthorized()`` method, allowing us to use the annotations to define access even if the user has not yet authenticated. You can follow this pattern for any Authenticate class you create/use by adding the following to either your custom authenticate class or a subclass of one of the core classes:

.. code:: php

    use ModelParserTrait;
