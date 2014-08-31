FileDocumentBundle
==================

The FileDocumentBundle contains entity code to persist file references to a
database and save files to specified directory.

Usage
-----

To configure a document/entity relation
add the association to the relevant orm file, e.g.:

.. code-block:: php

    MESD\ORMed\ORMedBundle\Entity\Transmission:
    ...
        oneToOne:
            document:
                targetEntity: MESD\File\DocumentBundle\Entity\Document
                joinColumn:
                    name: document_id


To write a file, set the paths and
call the relevant entity's write function, e.g.:

.. code-block:: php

    // controller call instances this with default directories
    // this replaces $file837 = new Transmission();
    $file837=$this->get('document');

    $file837->setFilename($filename);
    $file837->setCategory('837');

    // prepare the string to write ahead of time
    $file837->write($data);

    $em->persist($file837);

By default, the application config file specifies the path directory
based on file type:

.. code-block:: html

    mesd_file_document:
        dirs:
            default:  documents
            835:      uploads/835
            837:      downloads/837
            dumbidea: ~

In this case, type 835 and 837 files will be saved in the specified directory
which is peer-level with application source.  ~ puts the directory as in
application root. When no other file type is matched, the default directory
is used. e.g. when root is

.. code-block:: html

    /var/www/symfony/ormed/

then:

.. code-block:: html

    default is in /var/www/symfony/ormed/app/../documents
    837 is in /var/www/symfony/ormed/app/../uploads/837
    835 is in /var/www/symfony/ormed/app/../downloads/835
    dumbidea is in /var/www/symfony/ormed/app/../dumbidea

All file paths are mapped through 'realpath()' which removes interstitial '/./',
'//', '/../' and other crap.  Read php manual for more details on this function.
The above dirs thus become:

.. code-block:: html

    default is in /var/www/symfony/ormed/documents
    837 is in /var/www/symfony/ormed/documents/uploads/837
    835 is in /var/www/symfony/ormed/documents/downloads/835
    dumbidea is in /var/www/symfony/ormed/dumbidea


The save path may be specified manually for each file, e.g.:

.. code-block:: php

    $file837->setPath('some/other/path');

This new directory is also peer-level with application source.



Installation and Configuration
------------------------------

Include code for bandcamp require in composer.json:

.. code-block:: js

    "require": {
    ...
        "mesd/filedocumentbundle": "dev-master",
    ...
    },

Also register in the AppKernel.php, e.g.:

.. code-block:: php

    public function registerBundles()
    {
        $bundles = array(

        ...

            new MESD\File\DocumentBundle\MESDFileDocumentBundle(),

        ...

        );


Caution
-------

A similar named bundle, FileDocumentedEntityBundle is in progress designed
for mapped Superclass.  It is not finished.  Make sure the appropriate bundle is
chosen for your application.