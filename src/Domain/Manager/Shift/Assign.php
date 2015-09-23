<?php

namespace Spark\Project\Domain\Manager\Shift;

use Spark\Adr\DomainInterface;
use Spark\Payload;

/**
 * Class Assign.
 */
class Assign implements DomainInterface
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
     *
     * @return \Spark\Adr\PayloadInterface|Payload
     *
     * @throws \Exception
     */
    public function __invoke(array $input)
    {
        $output = [];

        if (!empty($input['managerId'])) {
            $managerId = $input['managerId'];
            if (empty($input['shift_id']) || empty($input['employee_id'])) {
                $output['Input Error'] = 'You must supply shift id and employee id.';
            } else {

                //TODO: Clarify User Story: Can any manager update any shift? This implementation assumes no.
                //TODO: Add functionality to create new shift if shift to be updated does not exist? (Based on PUT's definition?)

                $shiftId = $input['shift_id'];
                $dbInput['employee_id'] = $input['employee_id'];

                $now = new \DateTime();
                $dbInput['updated_at'] = $now->format(DATE_RFC2822);

                $query = $this->fpdo
                    ->update('shift')
                    ->set($dbInput)
                    ->where('id', $shiftId)
                    ->where('manager_id', $managerId)
                    ->execute();

                //$query is number of rows updated In our case it should always be 1 on success or 0 on failure.
                $output['shift_id'] = $query;
            }
        } else {
            $output['Credential Error'] = 'You must supply your manager credentials.';
        }

        return (new Payload())
            ->withStatus(Payload::OK)
            ->withOutput($output);
    }
}
