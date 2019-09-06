<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests;

use Google_Exception;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests\Converter\Fixture;
use PHPUnit\Framework\TestCase;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\GoogleSheetClient;
use Google_Service_Sheets;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Google\AuthorizedClient;

class GoogleSheetClientTest extends TestCase
{
    const SPREADSHEET_ID = '1baxAq-otNeyKajWcIt7yQHq1oWyVDGHp54YbwRJszIw';

    /**
     * @throws Google_Exception
     * @doesNotPerformAssertions
     */
    public function testInstantiation()
    {
        $credentialsPath = 'credentials.json';
        $tokenPath = 'token.json';

        $authorizedClient = new AuthorizedClient($credentialsPath, $tokenPath);
        $googleServiceSheets = new Google_Service_Sheets($authorizedClient);
        $googleSheetClient = new GoogleSheetClient($googleServiceSheets);

        return $googleSheetClient;
    }

    /**
     * @param GoogleSheetClient $googleSheetClient
     * @depends testInstantiation
     */
    public function testGetHeadersRow(GoogleSheetClient $googleSheetClient)
    {
        $spreadsheetId = self::SPREADSHEET_ID;
        $headersRow = $googleSheetClient->getHeadersRow($spreadsheetId);
        $expectedHeadersRow = $this->getExpectedHeadersRow();

        $this->assertEquals($expectedHeadersRow, $headersRow);
    }

    /**
     * @param GoogleSheetClient $googleSheetClient
     * @depends testInstantiation
     */
    public function testGetNotProcessedRows(GoogleSheetClient $googleSheetClient)
    {
        $spreadsheetId = self::SPREADSHEET_ID;
        $notProcessedRows = $googleSheetClient->getNotProcessedRows($spreadsheetId);
        $expectedNotProcessedRows = $this->getExpectedNotProcessedRows();

        $this->assertEquals($expectedNotProcessedRows, $notProcessedRows);
    }

    public function getExpectedNotProcessedRows()
    {
        return [2 => Fixture::ROW, 5 => Fixture::ROW];
    }


    private function getExpectedHeadersRow()
    {
        return Fixture::HEADERS;
    }
}
