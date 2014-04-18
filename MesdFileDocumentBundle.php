<?php

namespace Mesd\FileDocumentBundle;

use Symfony\Component\Console\Application;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MesdFileDocumentBundle extends Bundle
{

    public function registerCommands(Application $application){
        parent::registerCommands($application);
    }

}
