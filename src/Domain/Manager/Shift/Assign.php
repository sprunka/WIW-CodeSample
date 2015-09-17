<?php

namespace Spark\Project\Domain\Manager\Shift;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class Assign implements DomainInterface
{
    public function __invoke(array $input)
    {
        $name = 'world';

        if (!empty($input['name'])) {
            $name = $input['name'];
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput([
                'hello' => $name,
            ]);
    }
}
