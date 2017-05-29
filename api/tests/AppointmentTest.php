<?php
error_reporting(E_ALL & ~E_USER_DEPRECATED & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
ini_set('display_errors', 1);

// This all needed to call our Zend Expressive app objects
chdir(dirname(__DIR__));
require 'vendor/autoload.php';
$container = require 'config/container.php';

use PHPUnit\Framework\TestCase;
use App\Model\Appointment;
use Zend\Expressive\Application;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;

$app = $container->get(Application::class);

/**
 * Appointment Model Unit Tests
 **/
class AppointmentTest extends TestCase
{
    protected $appointment = null;

    // these are static as they are kept between tests to assert things
    // it would be far nicer if phpunit keps the class instance between tests
    // but it re-instantiates a new AppointmentTest class for every test call
    protected static $id = null;
    protected static $data = null;

    /*
     * initialize appointment object
     **/
    public function setUp() {
        global $container;

        // needed to initialize an appointment model class
        $adapter = ($container->has(AdapterInterface::class))
            ? $container->get(AdapterInterface::class)
            : null;
        $this->appointment = new Appointment($adapter);

        // used to create known record, and assert it's existance
        self::$data = [
            "name"=>"Adrian Tester",
            "reason"=>"CheckUp",
            "date"=>"2017-06-01",
            "start"=>"13:15",
            "end"=>"13:45",
        ];
    }

    public function tearDown() {
        unset($this->appointment);
    }

    /**
     * Add an appointment and assert it was added properly
     **/
    public function testAddAppointment() {
        // returns insert id, store this in protected static so it can
        // be used to assert this specific input in followup tests
        self::$id = $this->appointment->addAppointment(self::$data);

        // checks id is not empty (not null, not false, not 0)
        $this->assertNotEmpty(self::$id);

        // duplicates are currently allowed, but will have a different id
        // and id should be autoincremented
        $id = $this->appointment->addAppointment(self::$data);
        $this->assertEquals($id, self::$id+1);
    }

    /**
     * Get appointment by id
     **/
    public function testGetAppointment() {
        $appointment = $this->appointment->getAppointment(self::$id);

        // make sure return is non-empty
        $this->assertNotEmpty($appointment);

        // make sure return contains our added object with the inserted id
        $this->assertEquals($appointment, array_merge(['id'=>self::$id],self::$data));
    }

    /**
     * Get all appointments
     **/
    public function testGetAppointments() {
        $appointments = $this->appointment->getAll(); 

        // since insert already happened (twice in fact) there should definitely be at least 1
        $this->assertNotEmpty($appointments);

        // should contain our inserted object with id
        $this->assertContains(array_merge(['id'=>self::$id],self::$data), $appointments);
    }

    /**
     * Delete appointment
     **/
    public function testDeleteAppointment() {
        // deleteAppointment return boolean, assert return true
        $this->assertTrue($this->appointment->deleteAppointment(self::$id));

        // delete returned true, but lets get all appointments and make sure
        $appointments = $this->appointment->getAll(); 
        $this->assertNotContains(array_merge(['id'=>self::$id],self::$data), $appointments);
    }
}
