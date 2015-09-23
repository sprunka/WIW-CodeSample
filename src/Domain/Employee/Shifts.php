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

            // TODO: create User Story clarification to detail what shift details are required.
            // Is this just for upcoming shifts, is this all shifts, all shifts not yet worked, etc?
            // What other info should be displayed? Should this return the manager as well?
            // If so, by name? by ID? With contact (see related User Story.)
            // Also, this could be used to additionally show coworkers (See relevant User Story)
            // This could additionally sum up the hours worked.
            // Current implementation displays all shifts in the db for teh given employee and only the start time, end time and break.
            $query = $this->fpdo->from('shift')
                ->where('employee_id', $employeeId);

            foreach ($query as $row)
            {
                $output[] = ['shift_id' => $row['id'], 'start'=>$row['start_time'], 'end'=>$row['end_time'], 'break' => $row['break']];
            }
        } else {
            $output['Input Error'] = 'You must supply your Employee credentials to request your shift information.';
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput(
                $output
            );
    }
}
