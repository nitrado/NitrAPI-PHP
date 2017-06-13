<?php

namespace Nitrapi\Services;

use Nitrapi\Nitrapi;
use Nitrapi\Services\CloudServers\Apps\AppManager;
use Nitrapi\Services\CloudServers\CloudServer;
use Nitrapi\Common\NitrapiObject;
use Nitrapi\Services\TaskManager\TaskManager;

abstract class Service extends NitrapiObject
{
    protected $api;

    protected $id;
    protected $location_id;
    protected $comment;
    protected $status;
    protected $user_id;
    protected $username;
    protected $delete_date;
    protected $suspend_date;
    protected $start_date;
    protected $details;
    protected $websocket_token;
    protected $roles;

    const SERVICE_STATUS_INSTALLING = 'installing';
    const SERVICE_STATUS_ACTIVE = 'active';
    const SERVICE_STATUS_SUSPENDED = 'suspended';
    const SERVICE_STATUS_DELETED = 'deleted';
    const SERVICE_STATUS_ADMINLOCKED = 'adminlocked';
    const SERVICE_STATUS_ADMINLOCKED_SUSPENDED = 'adminlocked_suspended';

    public function __construct(Nitrapi &$api, array &$data) {
        parent::__construct($api);
        $this->loadData($data);
    }

    /**
     * Returns the current location id
     *
     * @return int
     */
    public function getLocationId() {
        return $this->location_id;
    }

    /**
     * Returns the service status
     * 
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Returns if the service is currently installing.
     * @return bool
     */
    public function isInstalling() {
        return $this->getStatus() === self::SERVICE_STATUS_INSTALLING;
    }

    /**
     * Returns if the service is currently active.
     * @return bool
     */
    public function isActive() {
        return $this->getStatus() === self::SERVICE_STATUS_ACTIVE;
    }

    /**
     * Returns if the service is currently suspended.
     * @return bool
     */
    public function isSuspended() {
        return $this->getStatus() === self::SERVICE_STATUS_SUSPENDED;
    }

    /**
     * Returns if the service is currently deleted.
     * @return bool
     */
    public function isDeleted() {
        return $this->getStatus() === self::SERVICE_STATUS_DELETED;
    }

    /**
     * Returns if the service is currently admin locked.
     * @return bool
     */
    public function isAdminLocked() {
        return $this->getStatus() === self::SERVICE_STATUS_ADMINLOCKED;
    }

    /**
     * Returns if the service is currently admin locked and suspended.
     * @return bool
     */
    public function isAdminLockedSuspended() {
        return $this->getStatus() === self::SERVICE_STATUS_ADMINLOCKED_SUSPENDED;
    }

    /**
     * Return the service comment
     *
     * @return mixed
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * Returns the suspend date
     *
     * @return \DateTime
     */
    public function getSuspendDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp(strtotime($this->suspend_date));
        return $datetime;
    }

    /**
     * Returns the delete date
     *
     * @return \DateTime
     */
    public function getDeleteDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp(strtotime($this->delete_date));
        return $datetime;
    }

    /**
     * Returns the start date
     *
     * @return \DateTime
     */
    public function getStartDate() {
        $datetime = new \DateTime();
        $datetime->setTimestamp(strtotime($this->start_date));
        return $datetime;
    }

    /**
     * Returns the service id
     *
     * @return int
     */
    public function getId() {
        return (int)$this->id;
    }

    /**
     * Returns the user id of the service
     *
     * @return int
     */
    public function getUserId() {
        return (int)$this->user_id;
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUsername() {
        return (string)$this->username;
    }

    /**
     * Returns the websocket token
     *
     * @return string
     */
    public function getWebsocketToken() {
        return (string)$this->websocket_token;
    }

    /**
     * Returns all service details
     *
     * @return array
     */
    public function getServiceDetails() {
        return (array)$this->details;
    }

    /**
     * Returns the roles of the service
     *
     * @return array
     */
    public function getRoles() {
        return (array)$this->roles;
    }

    /**
     * Returns the ddos history
     *
     * @return array
     */
    public function getDDoSHistory() {
        $url = "services/" . $this->getId() . "/ddos";
        return $this->getApi()->dataGet($url);
    }

    /**
     * Lists all changelog items, which are available for the service.
     * A changelog item consists of a message with additional information.
     * This items will be used to show game updates and other notification
     * type messages to the service. A changelog item has the following
     * attributes:
     *
     * category     Which category the item will be (default "Game")
     * created      When the item is created
     * status
     *      name    What status the item has (e.g. "Update")
     *      icon    Icon name to use for display purpose
     *      button_class CSS Class to use for display purpose
     * text         The actual message to display
     * game         The full game name
     * alert        If the message is an alert message
     *
     * If the first parameter is set to true, changelog items for all
     * games will be returned. With the second parameter you can
     * suppress all non alert messages.
     *
     * @param boolean true if you need items from all games
     * @param boolean true if only alerts should be shown
     *
     * @return array a list of changelog items
     */
    public function getChangelogItems($allGames = false, $onlyAlerts = false) {
        $changelogs = $this->getApi()->dataGet('/changelogs');
        if (!isset($changelogs['changelogs'])) return [];

        $filteredChangelogs = $changelogs['changelogs'];

        // Filter out all non current game related items
        if (!$allGames) {
            $details = $this->getServiceDetails();
            if (!isset($details['game'])) return [];
            foreach ($filteredChangelogs as $i => $changelog)
                if (!isset($changelog['game']) || $changelog['game'] != $details['game'])
                    unset($filteredChangelogs[$i]);
        }

        // Filter out all normal items (only alerts)
        if ($onlyAlerts)
            foreach ($filteredChangelogs as $i => $changelog)
                if (!$changelog['alert']) unset($filteredChangelogs[$i]);

        return $filteredChangelogs;
    }

    /**
     * Returns the last log entries. You can optionally
     * provide a page number.
     *
     * @param int $hours
     * @return array
     */
    public function getLogs($page = 1) {
        $url = "services/" . $this->getId() . "/logs";
        return $this->getApi()->dataGet($url, null, [
            'query' => [
                'page' => $page
            ]
        ]);
    }

    /**
     * Adds a new log entry to your service
     *
     * @param string $category
     * @param string $message
     * @return boolean
     */
    public function addLog($category, $message) {
        $url = "services/" . $this->getId() . "/logs";
        $this->getApi()->dataPost($url, [
            'category' => $category,
            'message' => $message
        ]);
        return true;
    }

    /**
     * This can be used to force delete a suspended service.
     *
     * @return boolean
     */
    public function doDelete() {
        $url = "services/" . $this->getId();
        $this->getApi()->dataDelete($url);
        return true;
    }

    /**
     * Returns the sale price for the specified service.
     *
     * @return integer
     */
    public function getSalePrice() {
        $url = "services/" . $this->getId() . "/sale_price";
        return $this->getApi()->dataGet($url)['sale_price']['price'];
    }

    /**
     * This can be used to force suspend a service.
     * The sale price will be added as credit to your account.
     *
     * @return boolean
     */
    public function doCancel() {
        $url = "services/" . $this->getId() . "/cancel";
        $this->getApi()->dataPost($url);
        return true;
    }

    /**
     * Returns the task manager
     *
     * @return TaskManager
     */
    public function getTaskManager() {
        return new TaskManager($this);
    }

    /**
     * Returns the app manager.
     *
     * @return AppManager|bool
     */
    public function getAppManager() {
        if ($this instanceof CloudServer)
            return new AppManager($this);
        return false;
    }

    /**
     * @param array $data
     */
    protected function loadData(array $data) {
        $reflectionClass = new \ReflectionClass($this);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            if (!isset($data[$property->getName()])) continue;
            if (!$property->isProtected()) continue;
            $value = $data[$property->getName()];
            if (empty($value)) continue;

            $property->setAccessible(true);
            $property->setValue($this, $value);
            $property->setAccessible(false);
        }
    }

    public function getSupportAuthorization() {
        return null;
    }

    public function createSupportAuthorization() {
        throw new NitrapiErrorException("Support Authorizations aren't supported for this service type.");
    }

    public function deleteSupportAuthorization() {
        throw new NitrapiErrorException("Support Authorizations aren't supported for this service type.");
    }
}