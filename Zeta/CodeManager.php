<?php

namespace Smile\EzPersistenceBundle\Zeta;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * ZetaComponents CodeManager replacement
 * Fetches definitions from registered bundles
 * Allows Doctrine-like bundle aliases
 *
 * @author <champijo@gmail.com>
 */
class CodeManager extends \ezcPersistentDefinitionManager
{
    private $kernel;
    private $objectNamespaces;

    /**
     * Initialization
     */
    public function __construct( KernelInterface $kernel, array $objectNamespaces )
    {
        $this->kernel = $kernel;
        $this->objectNamespaces = $objectNamespaces;
    }

    /**
     * {@inheritdoc} Fetch definition in the "Resource/persistence" folders of bundles
     */
    public function fetchDefinition( $class )
    {
        $definition = null;

        $class = ltrim( $class, '\\' );

        $className = $class;

        // Check for namespace alias
        if ( strpos( $class, ':' ) !== false )
        {
            list( $namespaceAlias, $simpleClassName ) = explode( ':', $class );
            $className = $this->getFqcnFromAlias( $namespaceAlias, $simpleClassName );
        }

        foreach ( $this->objectNamespaces as $alias => $namespace )
        {
            if ( strpos( $className, $namespace ) === 0 )
            {
                $bundlePath = $this->kernel->getBundle( $alias )->getPath();
                $shortClassName = substr( $className, strlen( $namespace ) + 1 );

                $definition = require $bundlePath . '/Resources/config/persistence/' . $shortClassName . '.php';
            }
        }

        if ( !( $definition instanceof \ezcPersistentObjectDefinition ) )
        {
            throw new \ezcPersistentDefinitionNotFoundException( $class );
        }

        if ( $definition->idProperty === null )
        {
            throw new \ezcPersistentDefinitionMissingIdPropertyException( $class );
        }

        $definition = $this->setupReversePropertyDefinition( $definition );
        return $definition;
    }

    /**
     * Get class name from alias
     *
     * @param string $namespaceAlias
     * @param string $simpleClassName
     * @return string
     */
    protected function getFqcnFromAlias( $namespaceAlias, $simpleClassName )
    {
        return $this->getObjectNamespace( $namespaceAlias ) . '\\' . $simpleClassName;
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * @param string $objectNamespaceAlias
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getObjectNamespace( $objectNamespaceAlias )
    {
        if ( ! isset( $this->objectNamespaces[$objectNamespaceAlias] ) )
        {
            throw new \InvalidArgumentException( sprintf( 'Unknown namespace %s', $objectNamespaceAlias ) );
        }

        return trim( $this->objectNamespaces[$objectNamespaceAlias], '\\' );
    }
}
