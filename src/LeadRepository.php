<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet;

use Likemusic\YandexFleetTaxi\LeadRepository\Contract\LeadRepositoryInterface;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead as RowToLeadConverter;

class LeadRepository implements LeadRepositoryInterface
{
    /**
     * @var GoogleSheetClient
     */
    private $googleSheetClient;

    /**
     * @var string
     */
    private $spreadsheetId;

    /**
     * @var string
     */
    private $parkId;
    /**
     * @var RowToLeadConverter
     */
    private $rowToLeadConverter;

    public function __construct(GoogleSheetClient $googleSheetClient, $spreadsheetId, RowToLeadConverter $rowToLeadConverter, string $parkId)
    {
        $this->googleSheetClient = $googleSheetClient;
        $this->spreadsheetId = $spreadsheetId;
        $this->rowToLeadConverter = $rowToLeadConverter;
        $this->parkId = $parkId;
    }

    public function getNewLeads()
    {
        $spreadsheetId = $this->spreadsheetId;
        $parkId = $this->parkId;
        $headersRow = $this->getHeadersRow($spreadsheetId);
        $rows = $this->getNotProcessedRows($spreadsheetId);

        return $this->convertRowsToLeads($headersRow, $rows, $parkId);
    }

    private function getNotProcessedRows($spreadsheetId)
    {
        return $this->googleSheetClient->getNotProcessedRows($spreadsheetId);
    }

    private function convertRowsToLeads(array $headersRow, array $rows, string $parkId)
    {
        $leads = [];

        foreach ($rows as $rowIndex => $row) {
            $leads[$rowIndex] = $this->convertRowToLead($headersRow, $row, $rowIndex, $parkId);
        }

        return $leads;
    }

    private function getHeadersRow(string $spreadsheetId)
    {
        return $this->googleSheetClient->getHeadersRow($spreadsheetId);
    }

    private function convertRowToLead(array $headersRow, array $row, int $rowIndex, $parkId)
    {
        return $this->rowToLeadConverter->convert($headersRow, $row, $rowIndex, $parkId);
    }

    public function updateLeadStatus(string $leadId, string $leadStatus, string $statusMessage = null)
    {
        $rowNumber = $leadId;
        $spreadsheetId = $this->spreadsheetId;
        $this->updateRowStatus($spreadsheetId, $rowNumber, $leadStatus, $statusMessage);
    }

    private function updateRowStatus(
        string $spreadsheetId,
        int $rowNumber,
        string $leadStatus,
        string $statusMessage = null
    ) {
        $this->googleSheetClient->updateRowStatusAndMessage($spreadsheetId, $rowNumber, $leadStatus, $statusMessage);
    }
}
