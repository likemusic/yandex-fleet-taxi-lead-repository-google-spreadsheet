<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Contract;

interface GoogleSheetClientInterface
{
    public function getHeadersRow(string $spreadsheetId);

    public function getNotProcessedRows(string $spreadsheetId);

    public function updateRowStatusAndMessage(string $spreadsheetId, $rowNumber, $leadStatus, $statusMessage = null);
}
