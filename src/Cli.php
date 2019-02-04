<?php
namespace App;

use Codedungeon\PHPCliColors\Color;
use App\Exceptions\InvalidURLException;

class Cli
{

    private $fullUrl;

    /**
     * @var RegEx used to extract Card Id and validate URL
     */
    const URL_REGEX = '/^http(?:s)?:\/{2}trello.com\/c\/(.{8}).*$/';

    public function __construct(array $args)
    {
        $this->setUp($args);
    }

    /**
     * Extract the Card Id from URL
     * @param string $url
     * @return string The Card Id 
     * @throws InvalidURLException
     */
    public function getCardId(string $url = null): string
    {
        $value = $url ?? $this->fullUrl;
        $value = $this->validateUrl($value);
        $urlMatches = $this->urlMatches($value);

        if (count($urlMatches)) {
            return $urlMatches[1];
        }

        throw new InvalidURLException("URL de Card inválida");
    }

    /**
     * Configure the attributes used by system
     * @param array $args The value of native $argv
     */
    private function setUp(array $args)
    {
        if (count($args) <= 1) {
            print Color::red() . "Os comandos disponíveis são:" . Color::normal() . PHP_EOL;
            print Color::green() . "trello <card-url>" . Color::normal() . PHP_EOL;
            exit;
        } else {
            $this->setFullUrl($args[1]); // second parameter
        }
    }

    private function setFullUrl(string $url)
    {
        $this->fullUrl = $url;
    }

    private function urlMatches(string $url): array
    {
        $matches = [];
        preg_match(self::URL_REGEX, $url, $matches);
        return $matches;
    }

    private function validateUrl(string $url)
    {
        $decodedUrl = urldecode($url);

        if (count($this->urlMatches($decodedUrl))) {
            return $decodedUrl;
        }

        throw new InvalidURLException("URL de Card inválida");
    }
}
