<?php

use Carbon\Carbon;

function dateFormatted($date): string
{
    if (is_null($date)) { //JWT is absent
        return '';
    }
    return Carbon::create($date)->format('d/m/Y H:i:s');
}
