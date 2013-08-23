<?php

namespace MESD\File\DocumentBundle;

use Symfony\Component\Console\Application;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MESDFileDocumentBundle extends Bundle
{

    public function registerCommands(Application $application){
        parent::registerCommands($application);
    }

}
