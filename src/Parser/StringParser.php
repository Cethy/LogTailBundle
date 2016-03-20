<?php
namespace Cethyworks\LogTailBundle\Parser;


class StringParser implements ParserInterface
{
    /**
     * @param int $lineNo
     * @param string $content
     *
     * @return mixed
     */
    public function extract($lineNo, $content)
    {
        return $content;
    }
}