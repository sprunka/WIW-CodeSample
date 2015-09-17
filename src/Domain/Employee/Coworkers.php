<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class Coworkers implements DomainInterface
{
    public function __invoke(array $input)
    {
        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput([
                'hello' => $name,
            ]);
    }
}
