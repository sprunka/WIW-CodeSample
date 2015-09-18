<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class Shifts implements DomainInterface
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
        } else {
            $output['Input Error'] = 'You must supply your Employee credentials to request your shift information.';
        }
        $query = $this->fpdo->from('shift')
            ->where('employee_id', $employeeId);

        foreach ($query as $row)
        {
            $output[] = ['start'=>$row['start_time'], 'end'=>$row['end_time'], 'break' => $row['break']];
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput(
                $output
            );
    }
}
