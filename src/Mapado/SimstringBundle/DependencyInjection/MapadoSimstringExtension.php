<?php

namespace Mapado\SimstringBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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

        $this->loadReader($config, $container);
        $this->loadWriter($config, $container);

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
                $database = $this->getDatabase($config, $reader['database']);

                // initialize the service
                unset($reader['database']);
                $container->register(
                    'mapado.simstring.' . $readerKey . '_reader',
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
                $container->register(
                    'mapado.simstring.' . $writerKey . '_writer',
                    'Mapado\SimstringBundle\Database\SimstringWriter'
                )
                ->addArgument($database)
                ->addArgument($writer);
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
        if (!isset($config['databases'][$databaseName])) {
            $msg = sprintf('The simstring database with name "%s" is not defined', $databaseName);
            throw new \InvalidArgumentException($msg);
        }
        return $config['databases'][$databaseName];
    }
}
