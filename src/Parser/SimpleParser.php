<?php
namespace Cethyworks\LogTailBundle\Parser;

/**
 * Simple parser
 *
 * Extract data from logfile line
 */
class SimpleParser implements ParserInterface
{
    protected $pattern = '/^\[(?P<date>.*)\] (?P<channel>\w+).(?P<level>\w+): (?P<message>.*[^ ]+)( (?P<context>\{[^ ]+\}))?(?P<extra> \[\])?$/Usi';

    /**
     * @param int    $lineNo
     * @param string $content
     *
     * @return array
     */
    public function extract($lineNo, $content)
    {
        $reduced = false;
        preg_match($this->pattern, $content, $matches);

        if(PREG_BACKTRACK_LIMIT_ERROR == preg_last_error())
        {
            $reduced = true;
            $content = substr($content, 0, 2000);

            preg_match($this->pattern, $content, $matches);

        }
        if(! sizeof($matches)) {
            return null;
        }
        return $this->format($lineNo, $matches, $reduced);
    }

    protected function format($lineNo, array $matches, $reduced)
    {
        return [
            'lineNo'   => $lineNo,
            'datetime' => \DateTime::createFromFormat('Y-m-d H:i:s', $matches['date']),
            'channel'  => trim($matches['channel']),
            'level'    => trim($matches['level']),
            'message'  => trim($matches['message']),
            'context'  => isset($matches['context']) ? trim($matches['context']) : '',
            'extra'    => isset($matches['extra'])   ? trim($matches['extra']) : '',
            'reduced'  => $reduced
        ];
    }
}