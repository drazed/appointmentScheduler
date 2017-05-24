<?php
namespace App\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class Appointment
{
    protected $table;

    public function __construct(AdapterInterface $adapter)
    {
        $this->table  = new TableGateway('appointments', $adapter);
    }

    /**
     * Get all appointment
     *
     * @return array
     */
    public function getAll()
    {
        return $this->table->select()->toArray();
    }

    /**
     * Get a appointment by email
     *
     * @param $email string
     * @return bool|array
     */
    public function getAppointment($id)
    {
        $appointment = $this->table->select([ 'id' => $id ]);
        return $appointment ? (array) $appointment->current() : false;
    }

    /**
     * Add a appointment
     *
     * @param $data array
     * @return bool|integer
     */
    public function addAppointment(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $rows = $this->table->insert($data);
        return ($rows === 1) ? $this->table->lastInsertValue : false;
    }

    /**
     * Update a appointment
     *
     * @param integer $id
     * @param array $data
     * @return bool|array
     */
    public function updateAppointment($id, array $data)
    {
        $rows = $this->table->update($data, [ 'id' => $id ]);
        return ($rows === 1) ? $this->getAppointment($id) : false;
    }

    /**
     * Remove a appointment
     *
     * @param integer $id
     * @return bool
     */
    public function deleteAppointment($id)
    {
        $rows = $this->table->delete([ 'id' => $id ]);
        return ($rows === 1);
    }
}
