<?php
namespace Cethyworks\LogTailBundle\Parser;

/**
 * Interface ParserInterface
 *
 * Extract data from logfile line
 */
interface ParserInterface
{
    /**
     * @param int    $lineNo
     * @param string $content
     *
     * @return mixed
     */
    public function extract($lineNo, $content);
}