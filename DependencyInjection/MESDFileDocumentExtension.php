<?php

namespace MESD\File\DocumentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class MESDFileDocumentExtension extends Extension
{
    /**
     * Build the extension services
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load( array $configs, ContainerBuilder $container ) {
        $configuration = new Configuration();
        $config = $this->processConfiguration( $configuration, $configs );

        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        $loader->load( 'services.yml' );

        foreach ( $config['dirs'] as $label => $value ) {
                $dirs[$label] = ($value?$value.'/':'')
                .(( 'default' == $label || 'temp' == $label )?'':$label);
        }

        if ( isset( $dirs ) ) {
            $container->setParameter( "dirs", $dirs );
        } else {
            $container->setParameter( "dirs", null );
        }
    }
}
