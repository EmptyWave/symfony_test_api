<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

abstract class RequestValidator
{
    /**
     * @var array
     */
    protected $rules;

    /**
     * RequestValidator constructor.
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param Request $request
     * @return bool|null
     */
    abstract public function validate(Request $request): ?bool;

}