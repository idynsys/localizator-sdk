<?php

namespace Idynsys\Localizator\DTO\Requests\Languages;

use Idynsys\Localizator\Config\ConfigContract;
use Idynsys\Localizator\DTO\Requests\RequestData;
use Idynsys\Localizator\Enums\RequestMethod;

class ApplicationLanguagesRequestData extends RequestData
{
    public function __construct(?ConfigContract $config = null)
    {
        parent::__construct(RequestMethod::METHOD_GET, 'APPLICATION_LANGUAGES_URL', $config);
    }
}
