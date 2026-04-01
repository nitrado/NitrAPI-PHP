<?php

namespace Nitrapi\Services\TaskManager;

use DateTime;
use Nitrapi\Common\Exceptions\NitrapiException;
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

    protected $id;
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
    protected $action_data;

    public function __construct() {}

    public function setTaskManager(TaskManager $taskManager, array &$data = []): void
    {
        $this->taskManager = $taskManager;
        parent::__construct($taskManager->getService(), $data);
        $this->setService($taskManager->getService());
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getMinute()
    {
        return $this->minute;
    }

    public function setMinute($minute): self
    {
        $this->minute = $minute;

        return $this;
    }

    public function getHour()
    {
        return $this->hour;
    }

    public function setHour($hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function setDay($day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function setMonth($month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getWeekDay()
    {
        return $this->weekday;
    }

    public function setWeekday($weekday): self
    {
        $this->weekday = $weekday;

        return $this;
    }

    public function getActionMethod()
    {
        return $this->action_method;
    }

    public function setActionMethod($actionMethod): self
    {
        $this->action_method = $actionMethod;

        return $this;
    }

    public function getActionData()
    {
        return $this->action_data;
    }

    public function setActionData($actionData): self
    {
        $this->action_data = $actionData;

        return $this;
    }

    public function getNextRun(): ?DateTime
    {
        if (empty($this->next_run)) {
            return null;
        }

        return (new DateTime())->setTimestamp(strtotime($this->next_run));
    }

    public function getLastRun(): ?DateTime
    {
        if (empty($this->last_run)) {
            return null;
        }

        return (new DateTime())->setTimestamp(strtotime($this->last_run));
    }

    public function getTimeZone()
    {
        return $this->timezone;
    }

    /**
     * @throws NitrapiException
     */
    public function reloadData(): void
    {
        if (!empty($this->id)) {
            $url = "services/" . $this->getService()->getId() . "/tasks";
            $_tasks = $this->getService()->getApi()->dataGet($url);

            foreach ($_tasks['tasks'] as $task) {
                if ($task['id'] === $this->getId()) {
                    $this->loadData($task);
                    break;
                }
            }
        }
    }
}
