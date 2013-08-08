<?php

namespace Mapado\SimstringBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MapadoSimstringExtension extends Extension
{
    /** 
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadDatabase($config, $container);
        $this->loadReader($config, $container);
        $this->loadWriter($config, $container);
        $this->loadMainServices($config, $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * loadReader
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @access private
     * @return void
     */
    private function loadReader(array $config, ContainerBuilder $container)
    {
        if (!empty($config['reader'])) {
            foreach ($config['reader'] as $readerKey => $reader) {
                // get database name
                $databaseName = $reader['database'];
                $database = $this->getDatabase($config, $databaseName);

                // initialize the service
                unset($reader['database']);
                $clientServiceId = sprintf('mapado.simstring.%s_readerclient', $readerKey);
                $container->register(
                    $clientServiceId,
                    'Mapado\SimstringBundle\Database\SimstringClient'
                )
                ->addArgument($database)
                ->addArgument($reader);
            }
        }
    }

    /**
     * loadReader
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @access private
     * @return void
     */
    private function loadWriter(array $config, ContainerBuilder $container)
    {
        if (!empty($config['writer'])) {
            foreach ($config['writer'] as $writerKey => $writer) {
                // get database name
                $database = $this->getDatabase($config, $writer['database']);

                // initialize the service
                unset($writer['database']);
                $clientServiceId = sprintf('mapado.simstring.%s_writerclient', $writerKey);
                $container->register(
                    $clientServiceId,
                    'Mapado\SimstringBundle\Database\SimstringWriter'
                )
                ->addArgument($database)
                ->addArgument($writer);
            }
        }
    }

    /**
     * loadMainServices
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @access private
     * @return void
     */
    private function loadMainServices(array $config, ContainerBuilder $container)
    {
        // Readers
        if (!empty($config['reader'])) {
            foreach ($config['reader'] as $readerKey => $reader) {
                // get database name
                $databaseName = $reader['database'];
                $serviceId = sprintf('mapado.simstring.%s_reader', $readerKey);
                $clientServiceId = sprintf('mapado.simstring.%s_readerclient', $readerKey);

                // reader service
                $transformerId = sprintf('mapado.simstring.model_transformer.%s', $databaseName);
                if (!$container->has($transformerId)) {
                    $container->setAlias($serviceId, $clientServiceId);
                } else {
                    // ORM mapper
                    $container->register(
                        $serviceId,
                        'Mapado\SimstringBundle\Database\SimstringTransformerClient'
                    )
                    ->addArgument(new Reference($clientServiceId))
                    ->addArgument(new Reference(sprintf('mapado.simstring.model_transformer.%s', $databaseName)));
                }
            }
        }

        if (!empty($config['writer'])) {
            foreach ($config['writer'] as $writerKey => $writer) {
                $databaseName = $writer['database'];
                $serviceId = sprintf('mapado.simstring.%s_writer', $writerKey);
                $writerServiceId = sprintf('mapado.simstring.%s_writerclient', $writerKey);

                // writer service
                $transformerId = sprintf('mapado.simstring.model_transformer.%s', $databaseName);
                if (!$container->has($transformerId)) {
                    $container->setAlias($serviceId, $writerServiceId);
                } else {
                    // ORM mapper
                    $container->register(
                        $serviceId,
                        'Mapado\SimstringBundle\Database\SimstringTransformerWriter'
                    )
                    ->addArgument(new Reference($writerServiceId))
                    ->addArgument(new Reference(sprintf('mapado.simstring.model_transformer.%s', $databaseName)));
                }
            }
        }
    }

    /**
     * loadDatabase
     *
     * @param array $config
     * @access private
     * @return void
     */
    private function loadDatabase(array $config, ContainerBuilder $container)
    {
        foreach ($config['databases'] as $key => $database) {
            if (!empty($database['persistence'])) {
                $this->loadPersistence($key, $database['persistence'], $container);
            }
        }
    }

    /**
     * getDatabase
     *
     * @param array $config
     * @param mixed $databaseName
     * @access private
     * @return string
     */
    private function getDatabase(array $config, $databaseName)
    {
        if (!isset($config['databases'][$databaseName]['path'])) {
            $msg = sprintf('The simstring database with name "%s" is not defined', $databaseName);
            throw new \InvalidArgumentException($msg);
        }

        return $config['databases'][$databaseName]['path'];
    }

    /**
     * loadPersistence
     *
     * @param array $config
     * @access private
     * @return void
     */
    private function loadPersistence($database, $persistence, ContainerBuilder $container)
    {
        $driver = $persistence['driver'];

        $serviceId = sprintf('mapado.simstring.model_transformer.%s', $database);

        switch ($driver) {
            case 'orm':
                $className = 'Orm';
                $persistenceService = new Reference('doctrine');
                break;
            default:
                $msg = sprintf('The %s driver is not yet supported', $driver);
                throw new \InvalidArgumentException($msg);
                break;
        }

        $container->register(
            $serviceId,
            'Mapado\SimstringBundle\DataTransformer\\' . $className
        )
        ->addArgument($persistenceService)
        ->addArgument($persistence['model'])
        ->addArgument($persistence['field'])
        ->addArgument($persistence['options']);
    }
}
