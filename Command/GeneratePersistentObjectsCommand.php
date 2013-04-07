<?php

namespace Smile\EzPersistenceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;

class GeneratePersistentObjectsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName( 'persistence:generate:objects' )
            ->setDescription( 'Generate a persistent object' )
            ->addOption( 'bundle', null, InputOption::VALUE_REQUIRED, 'The target bundle (short notation)' )
            ->setHelp(
<<<EOT
The <info>persistence:generate-object</info> task generates PersistentObjects inside a bundle:

<info>php ezpublish/console persistence:generate:objects --bundle=eZDemoBundle --path=path/to/definition.file</info>

The above command would initialize new objects in the following namespace: <info>EzSystems\DemoBundle\Persistence</info>.
EOT
            );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $dialog = $this->getHelperSet()->get( 'dialog' );

        if ( $input->isInteractive() )
        {
            if ( ! $dialog->askConfirmation( $output, '<question>This will overwrite any existing class/definition. Do you confirm generation ? (y/n)</question>', 'y' ) )
            {
                $output->writeln( '<error>Command aborted</error>' );
                return 1;
            }
        }

        $bundle = $this->getContainer()->get( 'kernel' )->getBundle( $input->getOption( 'bundle' ) );
        $bundlePath = $bundle->getPath();
        $definitionsPath = $bundlePath . '/Resources/config/persistence';
        $objectsPath = $bundlePath . '/Persistence';
        $schemaFilePath = $definitionsPath . '/schema.xml';

        if ( ! file_exists( $schemaFilePath ) )
        {
            $output->writeln( sprintf( '<error>Cannot find schema file : %s</error>', $schemaFilePath ) );
            return 1;
        }

        if ( ! file_exists( $definitionsPath ) )
        {
            mkdir( $definitionsPath, 0775, true );
        }

        if ( ! file_exists( $objectsPath ) )
        {
            mkdir( $objectsPath, 0775, true );
        }

        $generator = new Process(
            'php ezpublish_legacy/lib/ezc/PersistentObjectDatabaseSchemaTiein/src/rungenerator.php' .
            ' -o' .
            ' -t' .
            ' -tp ' . escapeshellarg( __DIR__ . '/../Resources/templates/' ) .
            ' -p ' . escapeshellarg( $bundle->getNamespace() . '\\Persistence' ) .
            ' -s ' . escapeshellarg( $schemaFilePath ) .
            ' -f xml' .
            ' ' . escapeshellarg( $definitionsPath ) .
            ' ' . escapeshellarg( $objectsPath )
        );

        $generator->run();

        $output->write( $generator->getOutput() );
    }
}
