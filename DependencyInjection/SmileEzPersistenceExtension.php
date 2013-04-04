<?php

namespace Smile\EzPersistenceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Dependency injection extension
 *
 * @author <champijo@gmail.com>
 */
class SmileEzPersistenceExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load( array $configs, ContainerBuilder $container )
    {
        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        $loader->load( 'services.yml' );

        $aliasMap = array();
        foreach ( $container->getParameter( 'kernel.bundles' ) as $bundleName => $bundleClass )
        {
            $bundle = new \ReflectionClass( $bundleClass );
            $aliasMap[$bundleName] = $bundle->getNamespaceName().'\\'.'Persistence';
        }

        $container->getDefinition( 'smile.persistent_code_manager' )->addArgument( $aliasMap );
    }
}
