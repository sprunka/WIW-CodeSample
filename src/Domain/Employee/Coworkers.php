<?php

namespace Spark\Project\Domain\Employee;

use Spark\Adr\DomainInterface;
use Spark\Payload;

/**
 * Class Coworkers
 * @package Spark\Project\Domain\Employee
 */
class Coworkers implements DomainInterface
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
            // TODO: Swap with better validation
            $employeeId = intval($input['employeeId']);

            // TODO: merge with related User Story for shift info and/or Manager Info?
            // TODO: Show coworkers if shifts overlap at all?
            $query = $this->fpdo
                ->from('shift AS s1')
                //->disableSmartJoin()
                ->where('s1.employee_id', $employeeId)
                ->innerJoin('shift AS s2 ON s1.employee_id != s2.employee_id AND s1.start_time=s2.start_time AND s1.end_time=s2.end_time')
                ->innerJoin('user ON user.id = s2.employee_id')
                ->select('user.name');

            foreach ($query as $row)
            {
                $output[] = ['shift' => $row['id'], 'coworker'=>$row['name']];
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
