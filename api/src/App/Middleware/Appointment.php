<?php
namespace App\Middleware;

use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Model\Appointment as AppointmentModel;
use Zend\Expressive\Helper\UrlHelper;
use Exception;

class Appointment
{
    protected $model;
    protected $helper;

    use RestDispatchTrait;

    public function __construct(AppointmentModel $model, UrlHelper $helper)
    {
        $this->model = $model;
        $this->helper = $helper;
    }

    /**
     * Get an appointment by ID, or if ID not provided get ALL appointments
     *
     **/
    public function get(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $id = $request->getAttribute('id');
        if (null === $id) {
            $appointments = $this->model->getAll();
            return new JsonResponse(['appointments' => $appointments ]);
        }
        $appointment = $this->model->getAppointment($id);
        if (! $appointment) {
            return $response->withStatus(404);
        }
        return new JsonResponse(['appointment' => $appointment ]);
    }

    /**
     * Generate a new appointment and return its id
     **/
    public function post(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $data = $request->getParsedBody();

        // validate each input, remove if invalid
        foreach ($data as $key=>$value) {
            if (!$this->validateInput($key, $value)) {
                // invalid input provided, remove from input
                unset($data[$key]);
            }
        }

        try {
            // all invalid inputs removed above, make sure required ones are still present
            $required = ["name","reason","date","start","end"];
            if(count(array_intersect_key(array_flip($required), $data)) === count($required)) {
                $id = (int) $this->model->addAppointment($data);
            } else {
                throw new Exception("Missing required inputs");
            }
        } catch (Exception $e) {
            return $response->withStatus(400);
        }
        $response = $response->withHeader( 'Location', $this->helper->generate('api.appointment.get', ['id' => $id]));
        return $response->withStatus(201);
    }

    /**
     * Update an existing appointment
     *
     * currently not used in the angular interface side, but added here for completedness
     *
     **/
    public function patch(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $id = $request->getAttribute('id');
        $data = $request->getParsedBody();

        // validate each input, remove if invalid
        foreach ($data as $key=>$value) {
            if (!$this->validateInput($key, $value)) {
                // invalid input provided, remove from input
                unset($data[$key]);
            }
        }

        try {
            // updates require a non-empty id and at least one validated input
            if (empty($id) || empty($data)) {
                throw new Exception("Missing required inputs");
            }

            $appointment = $this->model->updateAppointment($id, $request->getParsedBody());
        } catch (Exception $e) {
            return $response->withStatus(400);
        }
        if (!$appointment) {
            return $response->withStatus(404);
        }
        return new JsonResponse([ 'appointment' => $appointment ]);
    }

    /**
     * Delete an appointment
     **/
    public function delete(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $id = $request->getAttribute('id');

        // check for valid id, checking #id == (int) $id is a crafty way to ensure the valus is
        // numeric and non-zero (empty also checks the valus is non-zero)
        if (!empty($id) && $id = (int) $id) {
            $result = $this->model->deleteAppointment($id);
            if (!$result) {
                return $response->withStatus(404);
            }
        } else {
            // invalid input provided
            return $response->withStatus(400);
        }

        // valid call and delete succeeded, return empty (but valid) response
        return new EmptyResponse();
    }

    /**
     * This should really be done via Zend Expressive validators?  But since I'm very new
     * to Zend Expressive and lack the time, validate here
     **/
    private function validateInput($name, $value)
    {
        // all inputs/keys must be non-empty
        if (empty($name) || empty($value)) { return false; }

        // capture all valid inputs
        switch ($name) {
        // date/time validations
        case 'date':
            // expected Y-m-d
            if (!$this->validateDate($value, 'Y-m-d')) { return false; }
        case 'start':
        case 'end':
            // 'date' falls through here, but don't want to re-validate it as time
            if ($name == 'start' || $name == 'end') {
                // expected H:i
                if (!$this->validateDate($value, 'H:i')) { return false; }
            }

        // these values just need to be non-empty, there is a list of reasons
        // ui-side, but api allows any non-empty string as reason
        case 'name':
        case 'reason':
            // valid return for all the above input types fall through here
            return true;
        }

        // unexpected input reached, invalid
        return false;
    }

    /**
     * validate date string against format
     *
     * @source http://php.net/manual/en/function.checkdate.php
     **/
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        // call DateTime from the root namespace
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
