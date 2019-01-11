<?php
namespace App;

use App\Configuration as cfg;
use App\Exceptions\UnreadableFileException;

class Files
{

    const AS_STRING = 'string';
    const AS_ARRAY = 'array';
    const AS_OBJECT = 'object';

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
     * @param string $returnType The type of return content
     * @return string|array|\stdClass The content file
     * @throws UnreadableFileException
     */
    public function load(string $path, string $returnType = 'string')
    {
        $fullPath = $this->validateFile($path);

        if ($returnType === self::AS_ARRAY) {
            return file($fullPath);
        }

        $content = file_get_contents($fullPath);

        if ($returnType === self::AS_OBJECT) {
            return json_decode($content);
        }

        return $content;
    }

    /**
     * Check if the file exists and is readable
     * @param string $path The path to file (Based on root project)
     * @return string The full path of file
     * @throws UnreadableFileException
     */
    private function validateFile(string $path): string
    {
        $fullPath = $this->basePath . cfg::DS . $path;

        if (file_exists($fullPath) && !is_readable($fullPath)) {
            throw new UnreadableFileException("O arquivo {$fullPath} não pode ser lido");
        }

        return $fullPath;
    }
}
