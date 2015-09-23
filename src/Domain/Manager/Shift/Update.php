<?php

namespace Spark\Project\Domain\Manager\Shift;

use Spark\Adr\DomainInterface;
use Spark\Payload;

/**
 * Class Update.
 */
class Update implements DomainInterface
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
            if (empty($input['shiftId']) || empty($input['shift']) || !is_array($input['shift'])) {
                $output['Input Error'] = 'You must supply shift data.';
            } else {
                $shiftId = $input['shiftId'];

                //TODO: validate shift data.
                $dbInput = $input['shift'];

                $now = new \DateTime();
                $dbInput['updated_at'] = $now->format(DATE_RFC2822);

                $query = $this->fpdo
                    ->update('shift')
                    ->set($dbInput)
                    ->where('id', $shiftId)
                    ->where('manager_id', $managerId)
                    ->execute();

                //$query is number of rows updated In our case it should always be 1 on success or 0 on failure or false on error.
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
