<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet;

use Likemusic\YandexFleetTaxi\LeadRepository\Contract\LeadRepositoryInterface;

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
        $rows = $this->getNotProcessedRows($this->spreadsheetId);

        return $this->convertRowsToLeads($rows);
    }

    private function getNotProcessedRows($spreadsheetId)
    {
        return $this->googleSheetClient->getNotProcessedRows($spreadsheetId);
    }

    private function convertRowsToLeads($rows)
    {
        $leads = [];

        foreach ($rows as $row) {
            $leads[] = $this->convertRowToLead($row);
        }

        return $leads;
    }

    private function convertRowToLead($row)
    {
        return $this->rowToLeadConverter->convert($row);
    }

    public function updateLeadStatus(string $leadId, string $leadStatus, string $statusMessage = null)
    {
        $rowIndex = $this->getRowIndexByLeadId($leadId);
        $spreadsheetId = $this->spreadsheetId;
        $this->updateRowStatus($spreadsheetId, $rowIndex, $leadStatus, $statusMessage);
    }
}
