<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter;

use Likemusic\YandexFleetTaxi\LeadRepository\Contract\Lead;
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


    public function convert(array $row): LeadInterface
    {
        $lead = new Lead();

        $id = $this->getIdByRow($row);
        $driverPostData = $this->getDriverPostDataByRow($row);
        $carPostData = $this->getCarPostDataByRow($row);

        return $lead
            ->setId($id)
            ->setDriverPostData($driverPostData)
            ->setCarPostData($carPostData);
    }

    private function getDriverPostDataByRow($row)
    {
        return $this->rowToDriverPostDataConverter->convert($row);
    }

    private function getCarPostDataByRow($row)
    {
        return $this->rowToCarPostDataConverter->convert($row);
    }
}
