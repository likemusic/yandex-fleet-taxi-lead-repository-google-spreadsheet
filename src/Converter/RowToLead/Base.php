<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead;


class Base
{
    protected function getValuesByRowNamesMapping($rowNames, $row, $mapping)
    {
        $ret = [];

        foreach ($mapping as $key => $columnName){
            $ret[$key] = $this->getValueByRowName($rowNames, $row, $columnName);
        }

        return $ret;
    }

    protected function getValueByRowName($rowNames, $row, $columnName)
    {
        $index = array_search($columnName, $rowNames);

        return $row[$index];
    }
}
