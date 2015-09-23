<?php

namespace Spark\Project\Domain\Manager\Shift;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class Add implements DomainInterface
{

    public function __construct(\FluentPDO $fluentPDO)
    {
        $this->fpdo = $fluentPDO;
    }

    public function __invoke(array $input)
    {
        $output = [];

        if (!empty($input['managerId'])) {
            $managerId = $input['managerId'];
            if (empty($input['shift']) || !is_array($input['shift'])) {
                $output['Input Error'] = 'You must supply shift data.';
            } else {
                $dbInput = $input['shift'];

                $dbInput['manager_id'] = $managerId;

                $now = new \DateTime();
                $dbInput['created_at'] = $now->format(DATE_RFC2822);
                $dbInput['updated_at'] = $now->format(DATE_RFC2822);

                $query = $this->fpdo->insertInto('shift',$dbInput)->execute();

                //$query is the inserted row's primary key, in our case, the shift id.
                $output['shift_id'] = $query;

            }
        } else {
            $output['Credential Error'] = 'You must supply your manager credentials.';
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($output);
    }
}
