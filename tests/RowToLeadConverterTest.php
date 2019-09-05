<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests;

use Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\RowToLeadConverter;
use PHPUnit\Framework\TestCase;
use Likemusic\YandexFleetTaxi\LeadRepository\Contract\Lead;

class RowToLeadConverterTest extends TestCase
{
    public function testConvert()
    {
        $row = $this->getTestRow();
        $converter = new RowToLeadConverter();
        $lead = $converter->convert($row);
        $expectedLead = $this->getExpectedLead();

        $this->assertEquals($expectedLead, $lead);
    }

    private function getExpectedLead()
    {
        $lead = new Lead();
    }

    private function getTestRow()
    {
        return [
            '+7 (753) 330-12-95',
            '+7 (753) 330-12-95',
            'Borisov',
            '+0 Москва',
            'Россия',
            'Иващенко',
            'Валерий',
            'Игоревич',
            'OA',
            '32132132',
            '28.07.2019',
            '07.11.2019',
            '01.02.1985',
            'A001AA78',
            'альфа ромео',
            'альфа ромео 2',
            '2008',
            'Серый',
            '000000000000000000000',
            'AA321354654654',
            'Lightbox; Наклейки',
            '1589609:362937272',
            '2019-09-02 18:10:31',
            'http://project1589609.tilda.ws/',
        ];
    }
}
