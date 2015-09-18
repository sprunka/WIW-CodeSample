<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use Spark\Project\DAL\User;

class Info implements DomainInterface
{
    public function __construct(\FluentPDO $fpdo)
    {
        $this->fpdo = $fpdo;
    }
    public function __invoke(array $input)
    {
        $output = [];
        if (!empty($input['managerId'])) {
            $managerId = $input['managerId'];
        } else {
            $output['Credential Error'] = 'You must supply your manager credentials.';
        }
        if (!empty($input['employeeId'])) {
            $employeeId = $input['employeeId'];
        } else {
            $output['Input Error'] = 'You must supply an Employee ID to request their contact information.';
        }

        // TODO: remove dependency or inject. Possibly remove separate DAL container object entirely, making FPDO calls here.
        // Alternately, fix DAL container object to allow for empty objects to be used via injection.
        $manager = new User($this->fpdo, $managerId);
        if ( $manager->isManager() ) {
            $user = new User($this->fpdo, $employeeId);
            $output['info'] = $user->getContactInfo();
        } else {
            $output['Credential Error'] = 'You must supply your manager credentials.';
        }


        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput(
                $output
            );
    }
}
