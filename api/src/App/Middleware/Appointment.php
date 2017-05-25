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
            return new JsonResponse([' appointments' => $appointments ]);
        }
        $appointment = $this->model->getAppointment($id);
        if (! $appointment) {
            return $response->withStatus(404);
        }
        return new JsonResponse(['appointment' => $appointment ]);
    }

    /**
     * Normally/ideally adding new objects in REST can/should be done by PUT rather
     * then POST as PUT is supposed to be idempotent, however since this skeleton
     * framework provides PATCH instead of PUT methos, I used POST for creation and
     * simple ensure idempotency on the request regarldess
     *
     * Additionally, since appointments are created with auto-incremented id rather
     * then passed in id, POST is actually the right method to handle creation
     **/
    public function post(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $data = $request->getParsedBody();

        try {
            // validate all inputs inside the try, as status 400 is proper for
            // invalid inputs if any invalid input is found throw Exception
            // and catch handles the rest
            $required = ["name","reason","date","start","end"];
            if(count(array_intersect_key(array_flip($required), $data)) === count($required)) {
                // All required keys exist, check each individually through validators

                // check each input through validators
                // TODO this should call Zend Expressive validators, but to save
                // time for now I am just validating in place here for now
                foreach ($valid as $key) {
                    if (isset($data[$key])) {
                        switch ($key) {
                        case 'name':
                            break;
                        case 'reason':
                            break;
                        case 'date':
                            break;
                        case 'start':
                            break;
                        case 'end':
                            break;
                        default:
                            // invalid input provided, it's safe to ignore it but want it removed
                            unset($data[$key]);
                        }
                    }
                }

                $id = (int) $this->model->addAppointment($appointment);
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
        try {
            // updates don't require ALL inputs, but do require valid inputs where present
            $valid = ["name","reason","date","start","end"];

            // check each input through validators
            // TODO this should call Zend Expressive validators, but to save
            // time for now I am just validating in place here for now
            foreach ($valid as $key) {
                if (isset($data[$key])) {
                    switch ($key) {
                    case 'name':
                        break;
                    case 'reason':
                        break;
                    case 'date':
                        break;
                    case 'start':
                        break;
                    case 'end':
                        break;
                    default:
                        // invalid input provided, it's safe to ignore it but want it removed
                        unset($data[$key]);
                    }
                }
            }

            $appointment = $this->model->updateAppointment($id, $request->getParsedBody());
        } catch (Exception $e) {
            return $response->withStatus(400);
        }
        if (! $appointment) {
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
}
