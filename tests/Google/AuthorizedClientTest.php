<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests\Google;

use Google_Exception;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Google\AuthorizedClient;
use PHPUnit\Framework\TestCase;

class AuthorizedClientTest extends TestCase
{
    /**
     * @throws Google_Exception
     * @doesNotPerformAssertions
     */
    public function testInstantiation()
    {
        $credentialsPath = 'credentials.json';
        $tokenPath = 'token.json';
        $authorizedClient = new AuthorizedClient($credentialsPath, $tokenPath);
    }
}
