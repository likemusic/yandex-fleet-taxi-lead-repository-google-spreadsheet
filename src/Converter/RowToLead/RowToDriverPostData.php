<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead;

use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Contract\ColumnNames\CreateDriver\DriverProfileInterface as DriverProfileColumnNameInterface;
use Likemusic\YandexFleetTaxiClient\Contracts\PostDataKey\CreateDriver\AccountsInterface;
use Likemusic\YandexFleetTaxiClient\Contracts\PostDataKey\CreateDriver\DriverProfile\DriverLicenceInterface;
use Likemusic\YandexFleetTaxiClient\Contracts\PostDataKey\CreateDriver\DriverProfileInterface;
use Likemusic\YandexFleetTaxiClient\Contracts\PostDataKey\CreateDriverInterface;
use Likemusic\YandexFleetTaxiClient\Contracts\References\DriverStatus\IdInterface as WorkStatusIdInterface;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Contract\ColumnNames\CreateDriver\DriverProfile\DriverLicenceInterface as DriverLicenceColumnInterface;

class RowToDriverPostData extends Base
{
    const DEFAULT_BALANCE_LIMIT = 5;

    public function convert(array $rowNames, array $row): array
    {
        return [
            CreateDriverInterface::ACCOUNTS => $this->getDriverPostDataAccounts(),
            CreateDriverInterface::DRIVER_PROFILE => $this->getDriverPostDataDriverProfile($rowNames, $row),
        ];
    }

    private function getDriverPostDataAccounts()
    {
        return [
            AccountsInterface::BALANCE_LIMIT => self::DEFAULT_BALANCE_LIMIT,
        ];
    }

    private function getDriverPostDataDriverProfile(array $rowNames, array $row)
    {
        $defaultValues = $this->getDefaultValues();
        $mappedValues = $this->getMappedValues($rowNames, $row);
        $calculatedValues = $this->getCalculatedValues($rowNames, $row);

        return array_replace_recursive($defaultValues, $mappedValues, $calculatedValues);
    }

    private function getDefaultValues()
    {
        return [
            DriverProfileInterface::ADDRESS => null,
            DriverProfileInterface::CAR_ID => null,
            DriverProfileInterface::CHECK_MESSAGE => null,
            DriverProfileInterface::COMMENT => null,
            DriverProfileInterface::DEAF => null,
            DriverProfileInterface::DRIVER_LICENSE => null,
            DriverProfileInterface::EMAIL => null,
            DriverProfileInterface::FIRE_DATE => null,
//            DriverProfileInterface::FIRST_NAME => null,
            DriverProfileInterface::HIRE_DATE => null,
//            DriverProfileInterface::LAST_NAME => null,
//            DriverProfileInterface::MIDDLE_NAME => null,
//            DriverProfileInterface::PHONES => null,
            DriverProfileInterface::PROVIDERS => null,
            DriverProfileInterface::WORK_RULE_ID => 'a6cb3fbe61a54ba28f8f8b5e35b286db',//todo
//            DriverProfileInterface::WORK_STATUS => WorkStatusIdInterface::WORKING,

            DriverProfileInterface::BANK_ACCOUNTS => [],
            DriverProfileInterface::EMERGENCY_PERSON_CONTACTS => [],
            DriverProfileInterface::IDENTIFICATIONS => [],
            DriverProfileInterface::PRIMARY_STATE_REGISTRATION_NUMBER => null,
            DriverProfileInterface::TAX_IDENTIFICATION_NUMBER => null,
        ];
    }

    private function getMappedValues(array $rowNames, array $row)
    {
        $mapping = [
//            DriverProfileInterface::ADDRESS => null,
//            DriverProfileInterface::CAR_ID => null,
//            DriverProfileInterface::CHECK_MESSAGE => null,
//            DriverProfileInterface::COMMENT => null,
//            DriverProfileInterface::DEAF => null,
//            DriverProfileInterface::DRIVER_LICENSE => $this->getDriverPostDataDriverProfileDriverLicence($row),
//            DriverProfileInterface::EMAIL => null,
//            DriverProfileInterface::FIRE_DATE => null,
            DriverProfileInterface::FIRST_NAME => DriverProfileColumnNameInterface::FIRST_NAME,
//            DriverProfileInterface::HIRE_DATE => null,
            DriverProfileInterface::LAST_NAME => DriverProfileColumnNameInterface::LAST_NAME,
            DriverProfileInterface::MIDDLE_NAME => DriverProfileColumnNameInterface::MIDDLE_NAME,
//            DriverProfileInterface::PHONES => $this->getDriverPostDataDriverProfilePhones($row),
//            DriverProfileInterface::PROVIDERS => $this->getDriverPostDataDriverProfileProviders($row),
//            DriverProfileInterface::WORK_RULE_ID => null,
//            DriverProfileInterface::WORK_STATUS => null,

        ];

        return $this->getValuesByRowNamesMapping($rowNames, $row, $mapping);
    }

    private function getCalculatedValues($rowNames, $row): array
    {
        return [
//            DriverProfileInterface::ADDRESS => null,
//            DriverProfileInterface::CAR_ID => null,
//            DriverProfileInterface::CHECK_MESSAGE => null,
//            DriverProfileInterface::COMMENT => null,
//            DriverProfileInterface::DEAF => null,
            DriverProfileInterface::DRIVER_LICENSE => $this->getDriverLicencePostData($rowNames, $row),
//            DriverProfileInterface::EMAIL => null,
//            DriverProfileInterface::FIRE_DATE => null,
//            DriverProfileInterface::FIRST_NAME => null,
//            DriverProfileInterface::HIRE_DATE => null,
//            DriverProfileInterface::LAST_NAME => null,
//            DriverProfileInterface::MIDDLE_NAME => null,
            DriverProfileInterface::PHONES => $this->getDriverPostDataDriverProfilePhones($rowNames, $row),
//            DriverProfileInterface::PROVIDERS => $this->getDriverPostDataDriverProfileProviders($row),
//            DriverProfileInterface::WORK_RULE_ID => null,
//            DriverProfileInterface::WORK_STATUS => null,
        ];
    }

    private function getDriverPostDataDriverProfilePhones($rowNames, $row)
    {
        return [
            $this->getValueByRowName($rowNames, $row, DriverProfileColumnNameInterface::PHONE),
//            $this->getValueByRowName($rowNames, $row, DriverProfileColumnNameInterface::PHONE_2),
//TODO: Нужно ли добавлять второй номер к номерам яндекса? Через админку на данный момент можно добавить только один,
//но api позволяет добавить несколько.
        ];
    }

    private function getDriverLicencePostData($rowNames, $row)
    {
        return [
            DriverLicenceInterface::BIRTH_DATE => null,
            DriverLicenceInterface::COUNTRY => 'rus',//$this->getDriverLicenceCountry($rowNames, $row),
            DriverLicenceInterface::EXPIRATION_DATE => $this->getExpirationDate($rowNames, $row),
            DriverLicenceInterface::ISSUE_DATE => $this->getIssueDate($rowNames, $row),
            DriverLicenceInterface::NUMBER => $this->getDriverLicenceNumber($rowNames, $row),
        ];
    }

    private function getExpirationDate(array $rowNames, array $row): string
    {
        $sheetValue = $this->getValueByRowName($rowNames, $row, DriverLicenceColumnInterface::EXPIRATION_DATE);

        return $this->getClientDateByTildaDate($sheetValue);
    }

    private function getIssueDate(array $rowNames, array $row): string
    {
        $sheetValue = $this->getValueByRowName($rowNames, $row, DriverLicenceColumnInterface::ISSUE_DATE);

        return $this->getClientDateByTildaDate($sheetValue);
    }

    private function getClientDateByTildaDate(string $sheetDate):string
    {
        $chunks = explode('.', $sheetDate);

        $chunks = array_reverse($chunks);

        return implode('-', $chunks);
    }

    private function getDriverLicenceCountry($rowNames, $row)
    {
        //todo
        $countryRu = $this->getValueByRowName($rowNames, $row, DriverLicenceColumnInterface::COUNTRY);

        return $this->getDriverLicenceCountryCodeByCountryRussianName($countryRu);
    }

    private function getDriverLicenceNumber($rowNames, $row)
    {
        $series = $this->getValueByRowName($rowNames, $row, DriverLicenceColumnInterface::SERIES);
        $number = $this->getValueByRowName($rowNames, $row, DriverLicenceColumnInterface::NUMBER);

        return "{$series}{$number}";
    }

    private function getDriverPostDataDriverProfileProviders($row)
    {
        return [
            'yandex',//todo
        ];
    }
}
