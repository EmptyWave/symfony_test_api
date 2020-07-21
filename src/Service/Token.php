<?php

declare(strict_types=1);

namespace App\Service;


class Token //TODO: create real token sys
{
    static public function isValid($token): bool
    {
        return $token > 10;
    }


}