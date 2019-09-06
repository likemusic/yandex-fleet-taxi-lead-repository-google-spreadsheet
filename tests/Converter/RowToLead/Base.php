<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests\Converter\RowToLead;

use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead\RowToDriverPostData as RowToDriverPostDataConverter;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests\Converter\Fixture;
use PHPUnit\Framework\TestCase;

class Base extends TestCase
{
    protected function getTestHeaders()
    {
        return Fixture::HEADERS;
    }

    protected function getTestRow()
    {
        return Fixture::ROW;
    }
}
