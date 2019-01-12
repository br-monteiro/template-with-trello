<?php
namespace App;

use App\Cli;
use App\Files;
use App\Template;
use Trello\Client;
use Trello\Manager;

class Generator
{

    private $template;

    /**
     * @param Cli $cli
     * @param Files $files
     */
    public function __construct(Cli $cli, Files $files)
    {
        $manager = $this->buildTrelloAPIManager($files->load('config.json', Files::AS_OBJECT));
        $this->template = new Template($cli, $manager, $files);
    }

    /**
     * Print the template according the Card informations
     */
    public function exec()
    {
        echo $this->template->run();
    }

    /**
     * Create a API Client and Buil the API Manager
     * @param \stdClass $configs
     * @return Manager
     */
    private function buildTrelloAPIManager(\stdClass $configs): Manager
    {
        $client = new Client();
        $client->authenticate($configs->key, $configs->token, Client::AUTH_URL_CLIENT_ID);
        return new Manager($client);
    }
}
