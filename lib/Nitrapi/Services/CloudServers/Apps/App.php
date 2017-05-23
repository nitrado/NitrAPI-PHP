<?php

namespace Nitrapi\Services\CloudServers\Apps;

class DataMissingException extends \Exception {}

class App {
    /**
     * @var AppManager $appManager
     */
    protected $appManager;
    public $data;

    public function __construct(AppManager $appManager, array $data) {
        $this->appManager = $appManager;
        $this->data = $data;
    }

    /**
     * Implement the getter and setter methods to access the $data
     * array via getter and set the data via the setter.
     *
     * Examples:
     * $app->setAppName('mc_server');
     * $app->getStatus();
     *
     * Possible data keys:
     * - app_name
     * - status
     *
     *
     * @param string $name The method name
     * @param array $args The args
     * @return mixed The resulting value from $data
     * @throws DataMissingException if the key in $data does not exist.
     */
    public function __call($name, $args) {
        $method = strtolower($name[3]) . substr($name, 4); // Remove "get..."
        $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $method));
        $prefix = substr($name, 0, 3);

        if ($prefix === 'get' && isset($this->data[$key]))
            return $this->data[$key];

        if ($prefix === 'set') {
            $this->data[$key] = $args[0];
            return $this;
        }

        throw new DataMissingException('The key ' . $key . ' does not exist. Please set first.');
    }

    public function persist() {
        $this->_api()->dataPost($this->_url($this->getAppName() . '/update'), [
            'cmd' => $this->getCmd(),
            'parameters' => $this->data
        ]);
        return $this;
    }

    public function install() {
        $this->_api()->dataPut($this->_url(''), [
            'app_type' => $this->getAppType(),
            'app_name' => $this->getAppName()
        ]);

        // Update $data
        $installedApps = $this->appManager->getInstalledApps();
        foreach ($installedApps as $app)
            if ($app->getAppType() === $this->getAppType() &&
                $app->getAppName() === $this->getAppName())
                $this->data = $app->data;

        return $this;
    }

    public function uninstall() {
        $this->_api()->dataDelete($this->_url($this->getAppName()));
        return $this;
    }

    public function update() {
        $this->_api()->dataPost($this->_url($this->getAppName() . '/update'));
        return $this;
    }


    public function restart() {
        $this->_api()->dataPost($this->_url('restart'));
        return $this;
    }

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
