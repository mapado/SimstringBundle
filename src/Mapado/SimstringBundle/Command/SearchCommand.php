<?php

namespace Mapado\SimstringBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ExportCommand
 * 
 * @uses ContainerAwareCommand
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class SearchCommand extends ContainerAwareCommand
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
            ->setName('mapado:simstring:search')
            ->setDescription('Simstring search text')
            ->addArgument('reader', InputArgument::REQUIRED, 'The reader you want to search in')
            ->addArgument('query', InputArgument::REQUIRED, 'The text to search')
            ->addOption('threshold', 't', InputOption::VALUE_REQUIRED, 'Force the threshold');
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
        $query = $input->getArgument('query');
        $readerName = $input->getArgument('reader');
        $reader = $this->getContainer()->get('mapado.simstring.' . $readerName . '_reader');
        
        if ($input->getOption('threshold')) {
            $resultVector = $reader->find($query, $input->getOption('threshold'));
        } else {
            $resultVector = $reader->find($query);
        }

        foreach ($resultVector as $line) {
            $output->writeln($line);
        }
    }
}
