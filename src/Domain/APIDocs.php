<?php

namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class APIDocs implements DomainInterface
{
    public function __invoke(array $input)
    {
        // TODO: Find/Research a method for auto-generating this information from Routes.
        // This method of documenting the API is *not* scalable.
        // TODO: Uncomment Endpoint descriptions as work is completed.

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput([
                'Employee' => [
                    'List Shifts'=>'GET /myshifts[/{employeeId}]',
                    'List Coworkers'=>'GET /mycoworkers[/{employeeId}]',
                    'List Hours Worked'=>'GET /myhours[/{employeeId}]',
                    'List Manager Contact'=>'GET /mymanagers[/{employeeId}]'
                ],
                'Manager' => [
                    'Create a shift'=>'POST to /create-shift passing $managerId and the $shift data',
                    'List Shifts by time frame'=>'GET /list-shifts[/{managerId}/{startTime}/{endTime}]',
                    //'Update a given shift'=>'PUT to /update-shift passing $managerId, $shiftId and new $shift data',
                    'Assign an existing shift to an employee'=>'PUT to /assign-shift passing $managerId, $shiftId, and new $employeeId]',
                    'List Employee Info'=>'GET /employee-data[/{managerId}/{employeeId}]'
                ],
                'NOTES' => 'the variable "shift" should be an array containing the details of the shift, including employee ID which may be null.'
            ]);
    }
}
