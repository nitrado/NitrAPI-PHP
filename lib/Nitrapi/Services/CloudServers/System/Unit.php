<?php

namespace Nitrapi\Services\CloudServers\System;

class DataMissingException extends \Exception {}

/**
 * Class Unit
 *
 * @package Nitrapi\Services\CloudServers\System
 *
 * @method string getObjectPath()
 * @method string getUnitState()
 * @method string getDescription()
 * @method string getJobId()
 * @method string getLoadState()
 * @method string getFilename()
 * @method string getJobType()
 * @method string getJobObjectPath()
 * @method string getName()
 * @method string getActiveState()
 * @method string getSubState()
 * @method string getLeader()
 *
 * @method array enable()
 * @method array disable()
 * @method array mask()
 * @method array unmask()
 *
 * @method int start()
 * @method int stop()
 * @method int restart()
 * @method int reload()
 */
class Unit {
    private $systemd;
    private $data;

    public function __construct(Systemd $systemd, array $data=[]) {
        $this->systemd = $systemd;
        $this->data = $data;
    }

    /**
     * Implement the getter methods for the $data array.
     *
     * @example $app->getJobType();
     *
     * @param string $name The method name
     * @param array $_ params, which are not used
     * @return mixed The resulting value from $data
     * @throws DataMissingException if the key in $data does not exist.
     * @throws \BadMethodCallException if the method does not exits.
     */
    public function __call($name, $_) {
        if (in_array($name, ['enable', 'disable', 'mask', 'umask'], true)) {
            /* @var array[][] $apiResponse */
            $apiResponse = $this->api()->dataPost($this->url('/' . $name));
            return $apiResponse['changes'];
        }

        if (in_array($name, ['start', 'stop', 'restart', 'reload'], true)) {
            /* @var array[][] $apiResponse */
            $apiResponse = $this->api()->dataPost($this->url('/' . $name));
            return $apiResponse['job'];
        }

        if (preg_match('/get(.+)/', $name) === 0) {
            throw new \BadMethodCallException("Method $name not found.");
        }
        $method = strtolower($name[3]) . substr($name, 4);
        $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $method));
        if (isset($this->data[$key])) return $this->data[$key];
        throw new DataMissingException('The key ' . $key . ' does not exist. Please set first.');
    }

    /**
     * Resets the failure state of a unit back to normal.
     *
     * @return void
     */
    public function resetFailed() {
        $this->api()->dataPost($this->url('/reset_failed'));
    }

    /**
     * Send a POSIX signal to the process(es) running in a unit.
     *
     * @param string $who which process to send the kill signal
     * @param int $signal which signal will be send
     * @return void
     */
    public function kill($who='all', $signal=15) {
        $this->api()->dataPost($this->url('/kill'), [
            'who' => $who,
            'signal' => $signal
        ]);
    }


    private function url($endpoint) {
        return '/services/' . $this->systemd->service->getId() . '/cloud_servers/system/units/' . $this->getName() . $endpoint;
    }

    private function api() {
        return $this->systemd->service->getApi();
    }
}

