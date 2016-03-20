<?php
namespace Cethyworks\LogTailBundle\Viewer;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

class LogRefViewer extends SimpleViewer
{
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->configureOutput();
    }

    protected function configureOutput()
    {
        parent::configureOutput();

        $styles = [
            'logref' => new OutputFormatterStyle('green'),
        ];

        foreach($styles as $name => $style) {
            $this->output->getFormatter()->setStyle($name, $style);
        }
    }

    protected function displayHeader($elements) {

        $this->output->writeln(sprintf("<datetime>%s</datetime> | <channel>%s</channel>.<level>%s</level> | <logref>%s</logref> %s\n<message>%s</message>",
            $elements['datetime']->format('Y-m-d H:i:s'),
            $elements['channel'],
            $elements['level'],
            $elements['logref'],
            ($elements['reduced'] ? '<error>[reduced]</error>' : ''),
            $elements['message']
        ));
    }
}