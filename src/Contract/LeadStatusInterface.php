<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Contract;

interface LeadStatusInterface
{
    const PROCESSING = 'processing';
    const ERROR = 'error';
    const REGISTERED = 'registered';
}
