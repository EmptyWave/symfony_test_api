<?php

declare(strict_types=1);

namespace App\Service\Validators;

use App\Service\RequestValidator;
use Symfony\Component\HttpFoundation\Request;

class ApiRequestValidator extends RequestValidator
{
    public function validate(Request $request): ?bool
    {
        // TODO: Implement validate() method.
    }
}