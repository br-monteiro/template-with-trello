<?php
namespace App;

use App\Configuration as cfg;
use App\Exceptions\UnreadableFileException;

class Files
{

    /**
     * @var string The base path
     */
    private $basePath = '';

    public function __construct()
    {
        $this->basePath = str_replace(cfg::DISCARDED, '', cfg::REFERENCE);
    }

    /**
     * Load the file and returns the content
     * @param string $path The path to file (Based on root project) 
     * @param bool $asArray Return the content as Array
     * @return string|array The content File
     * @throws UnreadableFileException
     */
    public function load(string $path, bool $asArray = false)
    {
        $fullPath = $this->validateFile($path);

        if ($asArray) {
            return file($fullPath);
        }
        
        return file_get_contents($fullPath);
    }

    /**
     * Check if the file exists and is readable
     * @param string $path The path to file (Based on root project)
     * @return string The full path of file
     * @throws UnreadableFileException
     */
    private function validateFile(string $path): string
    {
        $fullPath = $this->basePath . $path;

        if (file_exists($fullPath) && !is_readable($fullPath)) {
            throw new UnreadableFileException("O arquivo {$fullPath} não pode ser lido");
        }

        return $fullPath;
    }
}
