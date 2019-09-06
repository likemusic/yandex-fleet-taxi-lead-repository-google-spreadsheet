<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet;

use Google_Service_Sheets;
use Google_Service_Sheets_Sheet;
use Google_Service_Sheets_ValueRange;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Contract\GoogleSheetClientInterface;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Contract\ColumnNames\ProcessingInterface as ProcessingColumnNamesInterface;

class GoogleSheetClient implements GoogleSheetClientInterface
{
    /**
     * @var Google_Service_Sheets
     */
    private $googleServiceSheets;


    public function __construct(Google_Service_Sheets $googleServiceSheets)
    {
        $this->googleServiceSheets = $googleServiceSheets;
    }

    public function getHeadersRow(string $spreadsheetId)
    {
        $range = 'LeadsFromTilda!1:1';

        return $this->getRangeValues($spreadsheetId, $range)[0];
    }

    public function getNotProcessedRows(string $spreadsheetId, $headersRow = null)
    {
        if (!$headersRow) {
            $headersRow = $this->getHeadersRow($spreadsheetId);
        }

        $statusColumnCellsValues = $this->getStatusColumnCellsValues($spreadsheetId, $headersRow);

        return $this->getNotProcessedRowsByStatusColumnCellsValues($spreadsheetId, $statusColumnCellsValues);
    }

    public function updateRowStatusAndMessage(string $spreadsheetId, $rowNumber, $leadStatus, $statusMessage = null, $headersRow = null)
    {
        if (!$headersRow) {
            $headersRow = $this->getHeadersRow($spreadsheetId);
        }

        $statusColumnNumber = $this->getStatusColumnNumber($headersRow);
        $statusMessageColumnNumber = $this->getStatusMessageColumnNumber($headersRow);

        $sheetId = 'LeadsFromTilda';
        $this->setCellValue($spreadsheetId, $sheetId, $rowNumber, $statusColumnNumber, $leadStatus);

        //todo: update by batch
        if ($statusMessage) {
            $this->setCellValue($spreadsheetId, $sheetId, $rowNumber, $statusMessageColumnNumber, $statusMessage);
        }
    }

    private function getStatusMessageColumnNumber($headersRow)
    {
        $statusColumnHeader = ProcessingColumnNamesInterface::STATUS_MESSAGE;

        return $this->getColumnNumber($headersRow, $statusColumnHeader);
    }

    private function setCellValue($spreadsheetId, $sheetId, $rowNumber, $columnNumber, $value)
    {
        $cellRange = $this->getCellRange($sheetId, $rowNumber, $columnNumber);
        $this->setRangeValues($spreadsheetId, $cellRange, [[$value]]);
    }

    public function getCellRange($sheetId, $rowNumber, $columnNumber)
    {
        return "{$sheetId}!R{$rowNumber}C{$columnNumber}";
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

    private function getStatusColumnCellsValues($spreadsheetId, $headersRow): array
    {
        $statusColumnRowsValues = $this->getStatusColumnRows($spreadsheetId, $headersRow);

        if (!$statusColumnRowsValues) {
            return [];
        }

        return $this->columnRowsValuesToCellsValues($statusColumnRowsValues);
    }

    private function columnRowsValuesToCellsValues($columnRowsValues)
    {
        $cellsValues = [];

        foreach ($columnRowsValues as $columnRow) {
            $cellsValues[] = isset($columnRow[0]) ? $columnRow[0]: null;
        }

        return $cellsValues;
    }

    private function getStatusColumnRows($spreadsheetId, $headersRow)
    {
        $statusColumnIndex = $this->getStatusColumnIndex($headersRow);
        $statusColumnIndex++;
        $range = "LeadsFromTilda!R2C{$statusColumnIndex}:C{$statusColumnIndex}";

        return $this->getRangeValues($spreadsheetId, $range);
    }

    private function getStatusColumnIndex($headersRow): int
    {
        $statusColumnHeader = ProcessingColumnNamesInterface::STATUS;

        return $this->getColumnIndex($headersRow, $statusColumnHeader);
    }

    private function getColumnNumber($headersRow, $columnName)
    {
        $columnIndex = $this->getColumnIndex($headersRow, $columnName);

        return ++$columnIndex;
    }

    private function getColumnIndex($headersRow, $columnName)
    {
        return array_search($columnName, $headersRow);
    }

    private function getStatusColumnNumber($headersRow): int
    {
        $statusColumnHeader = ProcessingColumnNamesInterface::STATUS;

        return $this->getColumnNumber($headersRow, $statusColumnHeader);
    }

    private function getStatusMessageColumnIndex($headersRow): int
    {
        $statusColumnHeader = ProcessingColumnNamesInterface::STATUS_MESSAGE;

        return $this->getColumnIndex($headersRow, $statusColumnHeader);
    }

    private function getRangeValues($spreadsheetId, $range): array
    {
        $values = $this->getRange($spreadsheetId, $range)->getValues();

        return $values ?? [];
    }

    private function getRange($spreadsheetId, $range): Google_Service_Sheets_ValueRange
    {
        return $this->googleServiceSheets->spreadsheets_values->get($spreadsheetId, $range);
    }

    private function getNotProcessedRowsByStatusColumnCellsValues($spreadsheetId, $statusColumnCellsValues)
    {
        $sheetId = 'LeadsFromTilda';
        $dataRowsCount = $this->getDataRowsCount($spreadsheetId, $sheetId);

        $notProcessedRowsNumbers = $this->getNotProcessedRowsNumbers($dataRowsCount, $statusColumnCellsValues);

        if (!$notProcessedRowsNumbers) {
            return [];
        }

        return $this->getRowsByRowsIndexes($spreadsheetId, $notProcessedRowsNumbers);
    }

    private function getDataRowsCount(string $spreadsheetId, string $sheetId)
    {
        $range = "{$sheetId}!A2:A";
        $values = $this->getRangeValues($spreadsheetId, $range);

        return count($values);
    }

    private function getRowsByRowsIndexes($spreadsheetId, $notProcessedRowsNumbers)
    {
        $rows = [];

        foreach ($notProcessedRowsNumbers as $rowNumber) {
            $rows[$rowNumber] = $this->getRowByNumber($spreadsheetId, $rowNumber);
        }

        return $rows;
    }

    private function getRowByNumber($spreadsheetId, $rowNumber)
    {
        $range = "LeadsFromTilda!{$rowNumber}:{$rowNumber}";

        return $this->getRangeValues($spreadsheetId, $range)[0];
    }

    private function getNotProcessedRowsNumbers($dataRowsCount, $statusColumnCellsValues)
    {
        $unprocessedRowsNumbers = [];

        for ($i=0; $i < $dataRowsCount; $i++) {
            if (!isset($statusColumnCellsValues[$i])) {
                $unprocessedRowsNumbers[] = $i+2;//Numbers is 1-based + 1 header row
            }
        }

        return $unprocessedRowsNumbers;
    }
}
