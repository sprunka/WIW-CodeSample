<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;

/**
 * Class Shifts
 * @package Spark\Project\Domain\Employee
 */
class Shifts implements DomainInterface
{

    /**
     * @param \FluentPDO $fluentPDO
     */
    public function __construct(\FluentPDO $fluentPDO)
    {
        $this->fpdo = $fluentPDO;
    }

    /**
     * @param array $input
     * @return \Spark\Adr\PayloadInterface|Payload
     */
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
        } elseif (!empty($input['managerId']) && !empty($input['startTime']) && !empty($input['endTime'])) {

            $managerId = $input['managerId'];
            $startTime = urldecode($input['startTime']);
            $endTime = urldecode($input['endTime']);

            // Assumes inclusive time window, not exclusive. To use exclusive remove the "or equal" from the two where clauses.
            $query = $this->fpdo->from('shift')
                ->where('manager_id', $managerId)
                ->where('str_to_date(start_time,\'%a, %d %b %Y %T\') >= str_to_date(\''.$startTime.'\', \'%a, %d %b %Y %T\')')
                ->where('str_to_date(end_time,\'%a, %d %b %Y %T\') <= str_to_date(\''.$endTime.'\', \'%a, %d %b %Y %T\')')
            ;

            foreach ($query as $row)
            {
                $output[] = [
                    'shift_id' => $row['id'],
                    'start' => $row['start_time'],
                    'end' => $row['end_time'],
                    'break' => $row['break'],
                    'employee_id' => $row['employee_id']
                ];
            }

        } else {
            $output['Input Error'] = 'You must supply your Employee or Manager credentials to request shift information.';
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput(
                $output
            );
    }
}
