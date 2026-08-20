<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Festivos
    |--------------------------------------------------------------------------
    |
    | country_code  ISO-3166 alpha-2 del país cuyos festivos se consultan
    |               en línea por año (API pública Nager.Date). Por defecto CO.
    |
    | manual_dates  Fechas manuales (Y-m-d) como respaldo cuando no haya
    |               conexión. Se filtran por el año que se está consultando.
    |
    */
    'country_code' => env('HOLIDAYS_COUNTRY_CODE', 'CO'),

    'manual_dates' => [],

];
