<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class Managers implements DomainInterface
{
    public function __construct(\FluentPDO $fluentPDO)
    {
        $this->fpdo = $fluentPDO;
    }

    public function __invoke(array $input)
    {
        $output = [];

        if (!empty($input['employeeId'])) {
            $employeeId = $input['employeeId'];

            //TODO: add shift ID and repeat manager info? ((FOr compatibility with other User Stories)
            $query = $this->fpdo->from('user')
                ->leftJoin('shift ON shift.manager_id = user.id')
                ->where('shift.employee_id', $employeeId)
                ->groupBy('user.id');

            foreach ($query as $row)
            {
                // TODO: Filter output to remove null data?
                $output[] = ['name' => $row['name'], 'phone' => $row['phone'], 'email' => $row['email']];
            }
        } else {
            $output['Input Error'] = 'You must supply your Employee credentials to request manager contact information.';
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput(
                $output
            );
    }
}
