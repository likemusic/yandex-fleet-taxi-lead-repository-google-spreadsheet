<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Tests\Converter;


interface Fixture
{
    const HEADERS = [
        'Phone',
        'Phone_2',
        'gorod',
        'chasy-poias',
        'VU-Strana',
        'VU-Familia',
        'VU-Imia',
        'VU-Otchestvo',
        'prava-seria',
        'prava-nomer',
        'Date',
        'Date_2',
        'Date_3',
        'frs-sts-gosnomer',
        'frs-sts-avto-marka',
        'frs-sts-avto-model',
        'frs-sts-avto-god',
        'frs-sts-avto-cvet',
        'frs-sts-avto-vin',
        'frs-sts-avto-vin_2',
        'Брендирование_Яндекс_Такси_если_есть',
        'requestid',
        'sended',
        'referer',
        'status',
        'message'
    ];


    const ROW = [
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
