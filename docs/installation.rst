Installation
------------

Using `Composer <http://getcomposer.org/>`__
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

`View on
Packagist <https://packagist.org/packages/josegonzalez/cakephp-annotation-control-list>`__,
and copy the json snippet for the latest version into your project's
``composer.json``. Eg, v. 1.1.1 would look like this:

.. code:: json

    {
        "require": {
            "josegonzalez/cakephp-annotation-control-list": "0.1.0"
        }
    }

Because this plugin has the type ``cakephp-plugin`` set in its own ``composer.json`, Composer knows to install it inside your ``/Plugins`` directory, rather than in the usual `vendor` directory. It is recommended that you add `/Plugins/AnnotationControlList` to your .gitignore file. (Why? `read
this <http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md>`__.)


Manual
~~~~~~

-  Download this:
   http://github.com/josegonzalez/cakephp-annotation-control-list/zipball/master
-  Unzip that download.
-  Copy the resulting folder to ``app/Plugin``
-  Rename the folder you just copied to ``AnnotationControlList``

GIT Submodule
~~~~~~~~~~~~~

In your *app directory* type:

.. code:: bash

    git submodule add -b master git://github.com/josegonzalez/cakephp-annotation-control-list.git Plugin/AnnotationControlList
    git submodule init
    git submodule update

GIT Clone
~~~~~~~~~

In your ``Plugin`` directory type:

.. code:: bash

    git clone -b master git://github.com/josegonzalez/cakephp-annotation-control-list.git AnnotationControlList

Enable plugin
~~~~~~~~~~~~~

You need to enable the plugin in your ``app/Config/bootstrap.php`` file:

.. code:: php

    <?php
    CakePlugin::load('AnnotationControlList');

If you are already using ``CakePlugin::loadAll();``, then this is not
necessary.
