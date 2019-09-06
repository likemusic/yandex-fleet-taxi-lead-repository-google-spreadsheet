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
     * @var RowToLeadConverter
     */
    private $rowToLeadConverter;

    public function __construct(GoogleSheetClient $googleSheetClient, $spreadsheetId, RowToLeadConverter $rowToLeadConverter)
    {
        $this->googleSheetClient = $googleSheetClient;
        $this->spreadsheetId = $spreadsheetId;
        $this->rowToLeadConverter = $rowToLeadConverter;
    }

    public function getNewLeads()
    {
        $spreadsheetId = $this->spreadsheetId;
        $headersRow = $this->getHeadersRow($spreadsheetId);
        $rows = $this->getNotProcessedRows($spreadsheetId);

        return $this->convertRowsToLeads($headersRow, $rows);
    }

    private function getNotProcessedRows($spreadsheetId)
    {
        return $this->googleSheetClient->getNotProcessedRows($spreadsheetId);
    }

    private function convertRowsToLeads(array $headersRow, array $rows)
    {
        $leads = [];

        foreach ($rows as $rowIndex => $row) {
            $leads[$rowIndex] = $this->convertRowToLead($headersRow, $row, $rowIndex);
        }

        return $leads;
    }

    private function getHeadersRow(string $spreadsheetId)
    {
        return $this->googleSheetClient->getHeadersRow($spreadsheetId);
    }

    private function convertRowToLead(array $headersRow, array $row, int $rowIndex)
    {
        return $this->rowToLeadConverter->convert($headersRow, $row, $rowIndex);
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
