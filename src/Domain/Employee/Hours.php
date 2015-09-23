<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class Hours implements DomainInterface
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

            // This query assumes that for a shift to be counted it must be already completed.
            // Also assumes breaks are paid and not deducted from hours worked.
            $query = $this->fpdo->from('shift')
                ->where('employee_id', $employeeId)
                ->where('str_to_date(shift.end_time,\'%a, %d %b %Y %T\') < now()');

            //initialize internal array.
            $hoursThisWeek = [];

            foreach ($query as $row)
            {
                $start = \DateTime::createFromFormat(DATE_RFC2822, $row['start_time']);
                $end = \DateTime::createFromFormat(DATE_RFC2822, $row['end_time']);
                $week = $end->format('W');

                $interval = $start->diff($end);
                //This only calculates the specific whole hours.
                $hoursThisShift =  $interval->format('%r%H');

                //To add in the partial hours, we must calculate the minutes and seconds independently.
                $minutesThisShift =  $interval->format('%r%I');
                $secondsThisShift =  $interval->format('%r%S');

                //Then add them in.
                $hoursThisShift += ($minutesThisShift / 60) + ($secondsThisShift/3600);

                if (!array_key_exists($week, $hoursThisWeek)){
                    $hoursThisWeek[$week] = $hoursThisShift;
                } else {
                    $hoursThisWeek[$week] += $hoursThisShift;
                }

                // TODO: clarify User Story. This total hours includes every second that was scheduled.
                $output[$week] = ['week' => $week, 'hours_worked' => $hoursThisWeek[$week]];

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
