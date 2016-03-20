<?php
namespace Cethyworks\LogTailBundle\Parser;

/**
 * Simple parser with logRef support
 *
 * Extract data from logfile line
 */
class LogRefParser extends SimpleParser
{
    protected $pattern = '/^\[(?P<date>.*)\] (?P<channel>\w+).(?P<level>\w+): (?P<message>.*[^ ]+ )\[logref (?P<logref>\w+)\]\:( (?P<context>\{[^ ]+\}))?(?P<extra>.*+)?$/Usi';

    protected function format($lineNo, array $matches, $reduced)
    {
        return [
            'lineNo'   => $lineNo,
            'datetime' => \DateTime::createFromFormat('Y-m-d H:i:s', $matches['date']),
            'channel'  => $matches['channel'],
            'level'    => $matches['level'],
            'message'  => $matches['message'],
            'logref'   => $matches['logref'],
            'context'  => isset($matches['context']) ? trim($matches['context']) : '',
            'extra'    => isset($matches['extra'])   ? trim($matches['extra']) : '',
            'reduced'  => $reduced
        ];
    }
}