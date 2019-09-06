<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter;

use Likemusic\YandexFleetTaxi\LeadRepository\Lead;
use Likemusic\YandexFleetTaxi\LeadRepository\Contract\LeadInterface;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead\RowToDriverPostData as RowToDriverPostDataConverter;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead\RowToCarPostData as RowToCarPostDataConverter;

class RowToLead
{
    /**
     * @var RowToDriverPostDataConverter
     */
    private $rowToDriverPostDataConverter;

    /**
     * @var RowToCarPostDataConverter
     */
    private $rowToCarPostDataConverter;

    public function __construct(
        RowToDriverPostDataConverter $rowToDriverPostDataConverter,
        RowToCarPostDataConverter $rowToCarPostDataConverter
    ) {
        $this->rowToDriverPostDataConverter = $rowToDriverPostDataConverter;
        $this->rowToCarPostDataConverter = $rowToCarPostDataConverter;
    }


    public function convert($headersRow, array $row, $rowIndex, $parkId): LeadInterface
    {
        $lead = new Lead();

        $id = $rowIndex;
        $driverPostData = $this->getDriverPostDataByRow($headersRow, $row);
        $carPostData = $this->getCarPostDataByRow($headersRow, $row, $parkId);

        return $lead
            ->setId($id)
            ->setDriverPostData($driverPostData)
            ->setCarPostData($carPostData);
    }

    private function getDriverPostDataByRow($headersRow, $row)
    {
        return $this->rowToDriverPostDataConverter->convert($headersRow, $row);
    }

    private function getCarPostDataByRow($headersRow, $row, $parkId)
    {
        return $this->rowToCarPostDataConverter->convert($headersRow, $row, $parkId);
    }
}
