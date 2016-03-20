<?php
namespace Cethyworks\LogTailBundle\Viewer;

interface ViewerInterface
{
    public function displayLine($elements);
}