<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use Spark\Project\DAL\User;

/**
 * Class Info
 * @package Spark\Project\Domain\Employee
 */
class Info implements DomainInterface
{

    /**
     * @param \FluentPDO $fpdo
     */
    public function __construct(\FluentPDO $fpdo)
    {
        $this->fpdo = $fpdo;
    }

    /**
     * @param array $input
     * @return \Spark\Adr\PayloadInterface|Payload
     */
    public function __invoke(array $input)
    {
        // TODO: remove dependency or inject. Possibly remove separate DAL container object entirely, making FPDO calls here.
        // Alternately, fix DAL container object to allow for empty objects to be used via injection.
        $output = [];
        if (!empty($input['managerId'])) {
            if (!empty($input['employeeId'])) {
                $employeeId = $input['employeeId'];
                $managerId = $input['managerId'];
                $manager = new User($this->fpdo, $managerId);
                if ( $manager->isManager() ) {
                    $user = new User($this->fpdo, $employeeId);
                    $output['info'] = $user->getContactInfo();
                } else {
                    $output['Credential Error'] = 'You must supply your manager credentials.';
                }
            } else {
                $output['Input Error'] = 'You must supply an Employee ID to request their contact information.';
            }
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
