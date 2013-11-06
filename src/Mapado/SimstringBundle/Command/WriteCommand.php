<?php

namespace Mapado\SimstringBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Mapado\SimstringBundle\Database\SimstringTransformerWriter;

/**
 * ExportCommand
 * 
 * @uses ContainerAwareCommand
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class WriteCommand extends ContainerAwareCommand
{
    /**
     * configure
     *
     * @access protected
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('mapado:simstring:write')
            ->setDescription('Simstring create database')
            ->addArgument('writer', InputArgument::REQUIRED, 'The writer you want to write in')
            ->addArgument('list', InputArgument::IS_ARRAY, 'The list of value to insert');
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @access protected
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $writerName = $input->getArgument('writer');
        $list = $input->getArgument('list');

        if (!empty($list)) {
            $writer = $this->getContainer()->get(sprintf('mapado.simstring.%s_writerclient', $writerName));
        } else {
            // manage empty list and mainly ORM
            $writer = $this->getContainer()->get(sprintf('mapado.simstring.%s_writer', $writerName));

            if ($writer instanceof SimstringTransformerWriter) {
                $list = $this->getContainer()
                            ->get(sprintf('mapado.simstring.model_transformer.%s', $writerName))
                            ->findAll();
            }
        }
       
        $writer->insert($list);
    }
}
