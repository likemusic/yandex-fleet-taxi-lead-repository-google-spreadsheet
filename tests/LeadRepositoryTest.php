<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests;

use Google_Exception;
use Likemusic\YandexFleetTaxi\LeadRepository\Contract\LeadRepositoryInterface;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\LeadRepository;
use PHPUnit\Framework\TestCase;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\GoogleSheetClient;
use Google_Service_Sheets;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Google\AuthorizedClient as AuthorizedGoogleClient;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead as RowToLeadConverter;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Contract\LeadStatusInterface;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead\RowToDriverPostData as RowToDriverPostDataConverter;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead\RowToCarPostData as RowToCarPostDataConverter;

class LeadRepositoryTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     * @throws Google_Exception
     * @doesNotPerformAssertions
     */
    public function testGetNewLeads()
    {
        $leadRepository = $this->getTestLeadRepository();
        $leadRepository->getNewLeads();

        return $leadRepository;
    }

    /**
     * @param LeadRepository $leadRepository
     * @depends testGetNewLeads
     * @doesNotPerformAssertions
     */
    public function testUpdateLeadStatus(LeadRepository $leadRepository)
    {
        $leadId = 2;
        $leadStatus = LeadStatusInterface::PROCESSING;
        $statusMessage = 'Test lead status message.';
        $leadRepository->updateLeadStatus($leadId, $leadStatus, $statusMessage);
    }

    /**
     * @return LeadRepositoryInterface
     * @throws Google_Exception
     */
    private function getTestLeadRepository(): LeadRepositoryInterface
    {
        $googleSheetClient = $this->getTestGoogleSheetClient();
        $spreadsheetId = '1baxAq-otNeyKajWcIt7yQHq1oWyVDGHp54YbwRJszIw';
        $rowToLeadConverter = $this->getTestRowToLeadConverter();
        $parkId = '8d40b7c41af544afa0499b9d0bdf2430';
        $leadRepository = new LeadRepository($googleSheetClient, $spreadsheetId, $rowToLeadConverter, $parkId);

        return $leadRepository;
    }

    private function getTestRowToLeadConverter()
    {
        $rowToDriverPostDataConverter = new RowToDriverPostDataConverter();
        $rowToCarPostDataConverter = new RowToCarPostDataConverter();

        return new RowToLeadConverter($rowToDriverPostDataConverter, $rowToCarPostDataConverter);
    }

    /**
     * @return GoogleSheetClient
     * @throws Google_Exception
     */
    private function getTestGoogleSheetClient()
    {
        $googleSheetService = $this->getTestGoogleSheetService();
        $googleSheetClient = new GoogleSheetClient($googleSheetService);

        return $googleSheetClient;
    }

    /**
     * @return Google_Service_Sheets
     * @throws Google_Exception
     */
    private function getTestGoogleSheetService()
    {
        $authorizedGoogleClient = $this->getTestAuthorizedGoogleClient();

        return new Google_Service_Sheets($authorizedGoogleClient);
    }

    /**
     * @throws Google_Exception
     */
    private function getTestAuthorizedGoogleClient()
    {
        $credentialsPath = 'credentials.json';
        $tokenPath = 'token.json';

        return new AuthorizedGoogleClient($credentialsPath, $tokenPath);
    }
}
