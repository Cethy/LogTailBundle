<?php
namespace Cethyworks\LogTailBundle\Viewer;

use Symfony\Component\Console\Output\OutputInterface;

class StringViewer implements ViewerInterface
{
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function displayLine($elements) {
        $this->output->writeln($elements);
        $this->output->writeln('');
    }
}