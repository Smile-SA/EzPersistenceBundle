<?php

namespace Smile\EzPersistenceBundle\Zeta;

use eZ\Publish\Core\Persistence\Legacy\EzcDbHandler;

/**
 * PersistentSession factory for ZetaCompoments PersistentObject
 * Uses an ugly hack in order to get the wrapped ezcDbHandler from the legacy ezcDbHandler
 *
 * @author <champijo@gmail.com>
 */
class SessionFactory
{
    /**
     * Create the PersistentSession service
     *
     * @param ezcDbHandler $db
     * @param ezcPersistentDefinitionManager $manager
     * @return \PersistentSession
     */
    public function getSession( EzcDbHandler $db, \ezcPersistentDefinitionManager $manager )
    {
        $handlerReflection = new \ReflectionProperty( 'eZ\Publish\Core\Persistence\Legacy\EzcDbHandler', 'ezcDbHandler' );
        $handlerReflection->setAccessible( true );
        $wrappedDbHandler = $handlerReflection->getValue( $db );

        return new \ezcPersistentSession( $wrappedDbHandler, $manager );
    }
}
