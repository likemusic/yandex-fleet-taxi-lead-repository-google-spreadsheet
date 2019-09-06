<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Contract;

interface GoogleSheetClientInterface
{
    public function getHeadersRow(string $spreadsheetId);

    public function getNotProcessedRows(string $spreadsheetId);

    public function updateRowStatus(string $spreadsheetId, $rowIndex, $leadStatus, $statusMessage = null);
}
