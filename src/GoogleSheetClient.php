<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet;

use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleSheetClient
{
    /**
     * @var Google_Service_Sheets
     */
    private $googleServiceSheets;

    public function __construct(Google_Service_Sheets $googleServiceSheets)
    {
        $this->googleServiceSheets = $googleServiceSheets;
    }

    public function getNotProcessedRows(string $spreadsheetId)
    {
        $statusColumnCellsValues = $this->getStatusColumnCellsValues($spreadsheetId);

        return $this->getNotProcessedRowsByColumn($spreadsheetId, $statusColumnCellsValues);
    }

    public function updateRowStatus($spreadsheetId, $rowIndex, $leadStatus, $statusMessage = null)
    {
        $values = [
            [$leadStatus, $statusMessage]
        ];

        $range = "LeadsFromTilda!{$rowIndex}Y:{$rowIndex}Z";

        $this->setRangeValues($spreadsheetId, $range, $values);
    }

    private function setRangeValues($spreadsheetId, $range, $values)
    {
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $this->googleServiceSheets->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
    }

    private function getStatusColumnCellsValues($spreadsheetId)
    {
        $statusColumnRows = $this->getStatusColumnRows($spreadsheetId);

        return $this->columnRowsToCells($statusColumnRows);
    }

    private function columnRowsToCells($columnRows)
    {
        $cells = [];

        foreach ($columnRows as $columnRow) {
            $cells[] = $columnRow[0];
        }

        return $cells;
    }

    private function getStatusColumnRows($spreadsheetId)
    {
        $range = 'LeadsFromTilda!Y:Y';

        $response = $this->getRangeValues($spreadsheetId, $range);

        return $response->getValues();
    }

    private function getRangeValues($spreadsheetId, $range)
    {
        return $this->getRange($spreadsheetId, $range)->getValues();
    }

    private function getRange($spreadsheetId, $range)
    {
        return $this->googleServiceSheets->spreadsheets_values->get($spreadsheetId, $range);
    }

    private function getNotProcessedRowsByColumn($spreadsheetId, $statusColumnCellsValues)
    {
        $notProcessedRowsIndexes = $this->getNotProcessedRowsIndexed($statusColumnCellsValues);

        return $this->getRowsByRowsIndexes($spreadsheetId, $notProcessedRowsIndexes);
    }

    private function getRowsByRowsIndexes($spreadsheetId, $notProcessedRowsIndexes)
    {
        $rows = [];

        foreach ($notProcessedRowsIndexes as $rowIndex) {
            $rows[$rowIndex] = $this->getRowByIndex($spreadsheetId, $rowIndex);
        }

        return $rows;
    }

    private function getRowByIndex($spreadsheetId, $rowIndex)
    {
        $range = "LeadsFromTilda!{$rowIndex}:{$rowIndex}";

        return $this->getRangeValues($spreadsheetId, $range);
    }

    private function getNotProcessedRowsIndexed($statusColumnCellsValues)
    {
        $unprocessedRowsIndexes = [];

        $count = count($statusColumnCellsValues);

        for($i=0;  $i < $count; $i++) {
            $cellValue = $statusColumnCellsValues[$i];

            if (!$cellValue) {
                $unprocessedRowsIndexes[] = $i;
            }
        }

        return $unprocessedRowsIndexes;
    }
}
