<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests\Converter\RowToLead;

use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Converter\RowToLead\RowToCarPostData as RowToCarPostDataConverter;
use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests\Converter\Fixture;
use PHPUnit\Framework\TestCase;

class RowToCarPostDataTest extends Base
{
    public function testConvert()
    {
        $testHeaders = $this->getTestHeaders();
        $testRow = $this->getTestRow();
        $converter = new RowToCarPostDataConverter();
        $carPostData = $converter->convert($testHeaders, $testRow);
        //$expectedDriverPostData = $this->getExpectedDriverPostData();

        $this->assertIsArray($carPostData);
        //$this->assertEquals($expectedDriverPostData, $driverPostData);
    }

}
