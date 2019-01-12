<?php
namespace App;

use Trello\Manager;
use App\Cli;
use App\Files;
use App\Configuration as cfg;

class Template
{

    const SOLUTION_REGEX = '/@solution:\n/';
    const OBSERVATION_REGEX = '/@important:\n/';

    /**
     * @var \Trello\Model\Card 
     */
    private $card;

    /**
     * @var string
     */
    private $rawTemplate = '';

    /**
     * @param Cli $cli
     * @param Manager $manager
     * @param Files $files
     */
    public function __construct(Cli $cli, Manager $manager, Files $files)
    {
        $this->setUp($cli, $manager, $files);
    }

    /**
     * Get the card content and load template file
     * @param Cli $cli
     * @param Manager $manager
     * @param Files $files
     */
    private function setUp(Cli $cli, Manager $manager, Files $files)
    {
        $this->card = $manager->getCard($cli->getCardId());
        $this->rawTemplate = $files->load('template.md');
    }

    /**
     * Make the template contente and return it
     * @return string
     */
    public function run(): string
    {
        $template = $this->rawTemplate;

        $template = $this->buildCardName($template);
        $template = $this->buildCardUrl($template);
        $template = $this->buildCardMembers($template);
        $template = $this->buildCardDescription($template);
        $template = $this->buildCardSolution($template);
        $template = $this->buildCardObservation($template);

        return $this->buildLines() . $template . $this->buildLines();
    }

    /**
     * Make the separator lines
     * @return string
     */
    private function buildLines(): string
    {
        return PHP_EOL . str_repeat('-', 10) . PHP_EOL . PHP_EOL;
    }

    /**
     * Get the card name and replace on template
     * @param string $template
     * @return string
     */
    private function buildCardName(string $template): string
    {
        $value = $this->card->getName();
        return str_replace(cfg::CARD_NAME, $value, $template);
    }

    /**
     * Get the card URL and replace on template
     * @param string $template
     * @return string
     */
    private function buildCardUrl(string $template): string
    {
        $value = $this->card->getShortUrl();
        return str_replace(cfg::CARD_URL, $value, $template);
    }

    /**
     * Get the members and replace on template
     * @param string $template
     * @return string
     */
    private function buildCardMembers(string $template): string
    {
        $members = $this->card->getMembers();
        $value = [];

        foreach ($members as $member) {
            $value[] = "[{$member->getFullName()}]({$member->getUrl()})";
        }

        $value = implode(", ", $value) . PHP_EOL;

        return str_replace(cfg::CARD_MEMBERS, $value, $template);
    }

    /**
     * Get the description and replace on template
     * @param string $template
     * @return string
     */
    private function buildCardDescription(string $template): string
    {
        $value = $this->card->getDescription();

        return str_replace(cfg::CARD_DESCRIPTION, $value, $template);
    }

    /**
     * Get the solution and replace on template
     * @param string $template
     * @return string
     */
    private function buildCardSolution(string $template): string
    {
        $action = $this->card->getActions();
        $value = '';

        foreach ($action as $action) {
            $text = $action['data']['text'] ?? '';
            if (preg_match(self::SOLUTION_REGEX, $text)) {
                $value .= preg_replace(self::SOLUTION_REGEX, '', $text);
                break;
            }
        }

        return str_replace(cfg::CARD_SOLUTION, $value, $template);
    }

    /**
     * Get the observations and replace on template
     * @param string $template
     * @return string
     */
    private function buildCardObservation(string $template): string
    {
        $action = $this->card->getActions();
        $value = '';

        foreach ($action as $action) {
            $text = $action['data']['text'] ?? '';
            if (preg_match(self::OBSERVATION_REGEX, $text)) {
                $value .= '>' . preg_replace(self::OBSERVATION_REGEX, '', $text) . PHP_EOL . PHP_EOL;
            }
        }

        return str_replace(cfg::CARD_OBSERVATIONS, $value, $template);
    }
}
