<?php
namespace Cethyworks\LogTailBundle\Manager;

use Cethyworks\LogTailBundle\Parser\LogRefParser;
use Cethyworks\LogTailBundle\Parser\ParserInterface;
use Cethyworks\LogTailBundle\Parser\SimpleParser;
use Cethyworks\LogTailBundle\Parser\StringParser;
use Cethyworks\LogTailBundle\Viewer\LogRefViewer;
use Cethyworks\LogTailBundle\Viewer\SimpleViewer;
use Cethyworks\LogTailBundle\Viewer\StringViewer;
use Symfony\Component\Console\Output\OutputInterface;

class TailFileManager
{
    const TYPE_LOG_REF = 'logRef';
    const TYPE_SIMPLE  = 'simple';
    const TYPE_STRING  = 'string';

    protected $parsers = [];

    protected $viewers = [];

    protected $waitingInterval;

    public function __construct(OutputInterface $output, $waitingInterval = 1)
    {
        $this->waitingInterval = $waitingInterval;

        $this->parsers = [
            [self::TYPE_LOG_REF, new LogRefParser()],
            [self::TYPE_SIMPLE, new SimpleParser()],
            [self::TYPE_STRING, new StringParser()],
        ];

        $this->viewers = [
            self::TYPE_LOG_REF => new LogRefViewer($output),
            self::TYPE_SIMPLE  => new SimpleViewer($output),
            self::TYPE_STRING  => new StringViewer($output)
        ];
    }

    public function parse($logFilePath)
    {
        if(! file_exists($logFilePath)
            ||! is_readable($logFilePath)) {
            throw new \Exception(sprintf('file (%s) not found or not readable.', $logFilePath));
        }

        $logFile = new \SplFileObject($logFilePath);

        $line = $this->getStartLine($logFile);
        while(1) {
            $logFile->seek($line);

            if($logFile->eof()) {
                sleep($this->waitingInterval);
                continue;
            }

            list($type, $data) = $this->useParsers($line, $logFile->current());
            $this->display($type, $data);

            $line++;
        }
    }

    protected function useParsers($lineNo, $line)
    {
        /**
         * @var ParserInterface $parser
         */
        foreach($this->parsers as $parserA) {
            list($type, $parser) = $parserA;

            if($result = $parser->extract($lineNo, $line)) {
                return [$type, $result];
            }
        }

        throw new \Exception('wat R U doing here ?');
    }

    protected function display($type, $data)
    {
        if(! isset($this->viewers[$type])) {
            throw new \Exception(sprintf('viewer with type (%s) not found.', $type));
        }

        $this->viewers[$type]->displayLine($data);
    }

    /**
     * Return the starting line
     *
     * Start with last 5 lines
     *
     * @param \SplFileObject $file
     * @param int            $offset
     *
     * @return int
     */
    protected function getStartLine(\SplFileObject $file, $offset = 5)
    {
        $file->seek(PHP_INT_MAX);
        $lineNo = $file->key() - $offset;

        return max($lineNo, 0);
    }
}