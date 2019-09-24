<?php

namespace Nitrapi\Services\TaskManager;

use Nitrapi\Services\Service;
use Nitrapi\Services\ServiceItem;

class Task extends ServiceItem
{
    /**
     * @var Service $service
     */
    protected $service;
    /**
     * @var TaskManager
     */
    protected $taskManager;

    protected $id = null;
    protected $service_id;
    protected $minute;
    protected $hour;
    protected $day;
    protected $month;
    protected $weekday;
    protected $next_run;
    protected $last_run;
    protected $timezone;
    protected $action_method;
    protected $action_data = null;

    public function __construct() {}

    public function setTaskManager(TaskManager &$taskManager, array &$data = []) {
        $this->taskManager = $taskManager;
        parent::__construct($taskManager->getService(), $data);
        $this->setService($taskManager->getService());
    }

    public function getId() {
        return (int)$this->id;
    }

    public function getMinute() {
        return $this->minute;
    }

    public function setMinute($minute) {
        $this->minute = $minute;

        return $this;
    }

    public function getHour() {
        return $this->hour;
    }

    public function setHour($hour) {
        $this->hour = $hour;

        return $this;
    }

    public function getDay() {
        return $this->day;
    }

    public function setDay($day) {
        $this->day = $day;

        return $this;
    }

    public function getMonth() {
        return $this->month;
    }

    public function setMonth($month) {
        $this->month = $month;

        return $this;
    }

    public function getWeekDay() {
        return $this->weekday;
    }

    public function setWeekday($weekday) {
        $this->weekday = $weekday;

        return $this;
    }

    public function getActionMethod() {
        return $this->action_method;
    }

    public function setActionMethod($actionMethod) {
        $this->action_method = $actionMethod;

        return $this;
    }

    public function getActionData() {
        return $this->action_data;
    }

    public function setActionData($actionData) {
        $this->action_data = $actionData;

        return $this;
    }

    public function getNextRun() {
        if (empty($this->next_run))
            return null;

        return (new \DateTime())->setTimestamp(strtotime($this->next_run));
    }

    public function getLastRun() {
        if (empty($this->last_run))
            return null;

        return (new \DateTime())->setTimestamp(strtotime($this->last_run));
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function reloadData() {
        if (!empty($this->id)) {
            $url = "services/" . $this->getService()->getId() . "/tasks";
            $_tasks = $this->getService()->getApi()->dataGet($url);

            foreach ($_tasks['tasks'] as $task) {
                if ($task['id'] == $this->getId()) {
                    $this->loadData($task);
                    break;
                }
            }
        }
    }
}
