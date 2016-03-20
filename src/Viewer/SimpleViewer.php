<?php
namespace Cethyworks\LogTailBundle\Viewer;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

class SimpleViewer implements ViewerInterface
{
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->configureOutput();
    }

    protected function configureOutput()
    {
        $styles = [
            'datetime' => new OutputFormatterStyle('cyan'),
            'channel'  => new OutputFormatterStyle(null, null, array('bold')),
            'level'    => new OutputFormatterStyle('red', null, array('bold')),
            'message'  => new OutputFormatterStyle(null, null, array('bold')),
            'context'  => new OutputFormatterStyle(),
            'extra'    => new OutputFormatterStyle(),

        ];

        foreach($styles as $name => $style) {
            $this->output->getFormatter()->setStyle($name, $style);
        }
    }
    
    public function displayLine($elements) {
        $this->displayHeader($elements);
        $this->displayContext($elements);
        $this->displayExtra($elements);

        $this->output->writeln('');
    }

    protected function displayHeader($elements) {
        $this->output->writeln(sprintf("<datetime>%s</datetime> | <channel>%s</channel>.<level>%s</level> %s\n<message>%s</message>",
            $elements['datetime']->format('Y-m-d H:i:s'),
            $elements['channel'],
            $elements['level'],
            ($elements['reduced'] ? '<error>[reduced]</error>' : ''),
            $elements['message']
        ));
    }

    protected function displayContext($elements) {
        $context = $elements['context'];

        // prettify json content
        if(is_array(json_decode($context, true))) {
            $context = json_encode(json_decode($context, true), JSON_PRETTY_PRINT);
        }

        if(strlen($elements['context'])) {
            $this->output->writeln(sprintf("<context>%s</context>",
                $context
            ));
        }
    }

    protected function displayExtra($elements) {
        $extra = $elements['extra'];

        // prettify stackTrace
        if('#0 ' == substr($extra, 0, 3)
            && '[] []' == substr($extra, -5)
        ) {
            $extra = substr(str_replace('#', "\n#", $extra), 1);
        }

        if(strlen($elements['extra']) && '[]' != $elements['extra']) {
            $this->output->writeln(sprintf("<extra>%s</extra>",
                $extra
            ));
        }
    }
}