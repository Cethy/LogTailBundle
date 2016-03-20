<?php
namespace Cethyworks\LogTailBundle\Command;

use Cethyworks\LogTailBundle\Manager\TailFileManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class LogTailCommand extends ContainerAwareCommand
{
    const NAME = 'debug:log:tail';

    public function configure()
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Emulate tail -f app/logs/*.log')
            ->addOption('interval', 'i', InputOption::VALUE_OPTIONAL, 'Number of seconds to wait before checking the file again when EOF reached', 1)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var KernelInterface $kernel */
        $kernel  = $this->getContainer()->get('kernel');
        $logDir  = $this->getContainer()->getParameter('kernel.logs_dir');

        $logFilePath = sprintf('%s/%s.log', $logDir, $kernel->getEnvironment());

        $waitingInterval = $input->getOption('interval');

        $manager = new TailFileManager($output, $waitingInterval);
        $manager->parse($logFilePath);

        $output->writeln('');
        $output->writeln('end');
    }
}