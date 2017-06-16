<?php

namespace Nitrapi\Services\CloudServers\Apps;

class DataMissingException extends \Exception {}

/**
 * Class App
 *
 * @package Nitrapi\Services\CloudServers\Apps
 *
 * @method string getAppName()
 * @method string getAppType()
 * @method string getDescription()
 * @method string getStatus()
 * @method string getSystemdPath()
 * @method string getSystemdConfig()
 * @method string getSystemdModified()
 * @method string getCmd()
 * @method string getParsedCmd()
 * @method array getParameters()
 * @method array getConfigurations()
 * @method array getPorts()
 *
 * @method App setAppName()
 * @method App setAppType()
 * @method App setDescription()
 * @method App setStatus()
 * @method App setSystemdPath()
 * @method App setSystemdConfig()
 * @method App setSystemdModified()
 * @method App setCmd()
 * @method App setParsedCmd()
 * @method App setParameters()
 * @method App setConfigurations()
 * @method App setPorts()
 */
class App {
    /**
     * @var AppManager $appManager
     */
    protected $appManager;
    public $data;

    public function __construct(AppManager $appManager, array $data) {
        $this->appManager = $appManager;
        $this->data = $data;

        if (!isset($this->data['ports'])) {
            $this->data['ports'] = [];
        }
    }

    /**
     * Implement the getter and setter methods to access the $data
     * array via getter and set the data via the setter.
     *
     * @example $app->setAppName('mc_server');
     * @example $app->getStatus();
     *
     * @param string $name The method name
     * @param array $args The args
     * @return mixed The resulting value from $data
     * @throws DataMissingException if the key in $data does not exist.
     * @throws \BadMethodCallException if the method does not exist.
     */
    public function __call($name, $args) {
        if (preg_match('/(get|set)(.+)/', $name) === 0) {
            throw new \BadMethodCallException("Method $name not found.");
        }
        $method = strtolower($name[3]) . substr($name, 4); // Remove "get..."
        $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $method));
        $prefix = substr($name, 0, 3);

        if ($prefix === 'get' && isset($this->data[$key])) {
            return $this->data[$key];
        }

        if ($prefix === 'set') {
            $this->data[$key] = $args[0];
            return $this;
        }

        throw new DataMissingException('The key ' . $key . ' does not exist. Please set first.');
    }

    /**
     * Saves all the changed data attributes
     * @return $this
     */
    public function persist() {
        $this->_api()->dataPut($this->_url($this->getAppName()), [
            'cmd' => $this->getCmd(),
            'parameters' => $this->data
        ]);
        return $this;
    }

    /**
     * Install the application
     *
     * @return $this
     */
    public function install() {
        $this->_api()->dataPost($this->_url(''), [
            'app_type' => $this->getAppType(),
            'app_name' => $this->getAppName(),
            'ports' => $this->getPorts()
        ]);

        /**
         * Update the $data array
         * @var $installedApps App[]
         */
        $installedApps = $this->appManager->getInstalledApps();
        foreach ($installedApps as $app) {
            if ($app->getAppType() === $this->getAppType() &&
                $app->getAppName() === $this->getAppName()) {
                $this->data = $app->data;
            }
        }

        return $this;
    }

    /**
     * Uninstall the application.
     *
     * @return $this
     */
    public function uninstall() {
        $this->_api()->dataDelete($this->_url($this->getAppName()));
        return $this;
    }

    /**
     * Update teh application.
     *
     * @return $this
     */
    public function update() {
        $this->_api()->dataPost($this->_url($this->getAppName() . '/update'));
        return $this;
    }

    /**
     * Restart the application.
     *
     * @return $this
     */
    public function restart() {
        $this->_api()->dataPost($this->_url('restart'));
        return $this;
    }

    /**
     * Stop the application.
     *
     * @return $this
     */
    public function stop() {
        $this->_api()->dataPost($this->_url('stop'));
        return $this;
    }

    private function _url($method) {
        return '/services/' . $this->appManager->service->getId() . '/cloud_servers/apps/' . $method;
    }

    private function _api() {
        return $this->appManager->service->getApi();
    }
}
