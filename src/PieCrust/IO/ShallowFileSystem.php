<?php

namespace PieCrust\IO;

use PieCrust\IPieCrust;
use PieCrust\PieCrustException;


/**
 * Describes a year PieCrust blog file-system - single depth where dir must be year and filename mm-dd_slug.
 */
class ShallowFileSystem extends FileSystem
{
    public function __construct($pagesDir, $postsDir)
    {
        FileSystem::__construct($pagesDir, $postsDir);
    }
    
    public function getPostFiles()
    {
        if (!$this->postsDir)
            return array();

        $years = array();
        $yearsIterator = new \DirectoryIterator($this->postsDir);
        foreach ($yearsIterator as $year)
        {
            if (preg_match('/^\d{4}$/', $year->getFilename()) == false)
                continue;
            
            $thisYear = $year->getFilename();
            $years[] = $thisYear;
        }
        rsort($years);
        
        $result = array();
        foreach ($years as $year)
        {
            $posts = array();
            $pathPattern = $this->postsDir . $year . '/' . '*.html';
            $paths = glob($pathPattern, GLOB_ERR);
            if ($paths === false)
            {
                throw new PieCrustException('An error occured while reading the posts directory.');
            }
            rsort($paths);
            
            foreach ($paths as $path)
            {
                $matches = array();
                
                if (preg_match('/(\d{4})\/(\d{2})-(\d{2})_(.*)\.html$/', $path, $matches) == false)
                    continue;
                
                $result[] = array(
                    'year' => $matches[1],
                    'month' => $matches[2],
                    'day' => $matches[3],
                    'name' => $matches[4],
                    'path' => $path
                );
            }
        }
        return $result;
    }
    
    public function getPostPathFormat()
    {
        return '%year%/%month%-%day%_%slug%.html';
    }
}
