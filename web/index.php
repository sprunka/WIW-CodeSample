<?php
// Require .env for db config.
if (!is_file($env = __DIR__ . '/../.env')) {
    throw new RuntimeException('Please create a .env file before starting the app!');
}

require __DIR__ . '/../vendor/autoload.php';

//Load ENV vars.
$env = (new josegonzalez\Dotenv\Loader($env))->parse()->toArray();

//Create Injector for PDO db connection.
$injector = new \Auryn\Injector;

$injector->define('PDO', [
    ':dsn'      => 'mysql:host=localhost;dbname=' . $env['db_database'],
    ':username' => $env['db_username'],
    ':passwd' => $env['db_password']
]);

$injector->share('PDO');
$injector->share('FluentPDO');

$app = Spark\Application::boot($injector);

$app->setMiddleware([
    'Relay\Middleware\ResponseSender',
    'Spark\Handler\ExceptionHandler',
    'Spark\Handler\RouteHandler',
    'Spark\Handler\ActionHandler',
]);

$app->addRoutes(function(Spark\Router $r) {
    $r->get('/', 'Spark\Project\Domain\APIDocs');
    // TODO: Implement a login/logout and pass around tokens instead of just the user's ID every call.
    /*
     * Notes on Auth Layer:
     * This API currently *only* covers the User Stories listed in the assignment
     * As such it does not currently handle any validation of the user being who they claim to be.
     * It does verify that the userId (employee or manager) is valid for the type of request issued.
     * The current userId field should be replaced with a validated token when validation is added.
     * I did not add an authentication layer, as the user stories do not reference how the login would occur
     * We might do oauth or direct API via a POST to create a login token, etc.
     */

    //Employees can only GET (read) information.
    $r->get('/myshifts[/{employeeId}]', 'Spark\Project\Domain\Employee\Shifts');
    $r->get('/mycoworkers[/{employeeId}]', 'Spark\Project\Domain\Employee\Coworkers');
    $r->get('/myhours[/{employeeId}]', 'Spark\Project\Domain\Employee\Hours');
    $r->get('/mymanagers[/{employeeId}]', 'Spark\Project\Domain\Employee\Managers');

    //Managers have expanded access
    //the variable "shift" should be an object/array containing the details of the shift
    $r->post('/create-shift[/{managerId}/{employeeId}/{shift}]', 'Spark\Project\Domain\Manager\Shift\Add');
    $r->get('/list-shifts[/{managerId}/{startTime}/{endTime}]', 'Spark\Project\Domain\Employee\Shifts');
    $r->put('/update-shift[/{managerId}/{shiftId}/{shift}]', 'Spark\Project\Domain\Manager\Shift\Update');
    $r->put('/assign-shift[/{managerId}/{shiftId}/{employeeId}]', 'Spark\Project\Domain\Manager\Shift\Assign');
    $r->get('/employee-data[/{managerId}/{employeeId}]', 'Spark\Project\Domain\Employee\Info');
});

$app->run();
