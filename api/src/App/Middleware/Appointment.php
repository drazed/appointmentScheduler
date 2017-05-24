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

    public function post(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $appointment = $request->getParsedBody();
        try {
            $id = (int) $this->model->addAppointment($appointment);
        } catch (Exception $e) {
            return $response->withStatus(400);
        }
        $response = $response->withHeader( 'Location', $this->helper->generate('api.appointment.get', ['id' => $id]));
        return $response->withStatus(201);
    }

    public function patch(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $id = $request->getAttribute('id');
        try {
            $appointment = $this->model->updateAppointment($id, $request->getParsedBody());
        } catch (Exception $e) {
            return $response->withStatus(400);
        }
        if (! $appointment) {
            return $response->withStatus(404);
        }
        return new JsonResponse([ 'appointment' => $appointment ]);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $id = $request->getAttribute('id');
        $result = $this->model->deleteAppointment($id);
        if (! $result) {
            return $response->withStatus(404);
        }
        return new EmptyResponse();
    }
}
