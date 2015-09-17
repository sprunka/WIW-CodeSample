<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class Shifts implements DomainInterface
{
    public function __invoke(array $input)
    {
        $name = 'world';

        if (!empty($input['name'])) {
            $name = $input['name'];
        } else {
            //Provide Form
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput([
                'hello' => $name,
            ]);
    }
}