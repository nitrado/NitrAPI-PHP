<?php

namespace Nitrapi\Services\TaskManager;

use Nitrapi\Services\Service;

class TaskManager
{
    protected $service;
    protected $tasks = [];

    public function __construct(Service &$service) {
        $this->service = $service;
        $this->reloadTasks();
    }

    public function getService() {
        return $this->service;
    }

    /**
     * Returns all available tasks
     *
     * @return array
     */
    public function getTasks() {
        return $this->tasks;
    }

    public function getTaskList() {
        $url = "services/" . $this->getService()->getId() . "/tasks/list";
        return $this->getService()->getApi()->dataGet($url)['tasks'];
    }

    /**
     * Persists a task
     *
     * @param Task $task
     * @return Task
     */
    public function persistTask(Task $task) {
        $id = $task->getId();

        if (empty($id)) {
            //create new task
            $url = "services/" . $this->getService()->getId() . "/tasks";
        } else {
            //save task
            $url = "services/" . $this->getService()->getId() . "/tasks/" . $id;
        }

        $this->getService()->getApi()->dataPost($url, [
            'minute' => $task->getMinute(),
            'hour' => $task->getHour(),
            'day' => $task->getDay(),
            'month' => $task->getMonth(),
            'weekday' => $task->getWeekDay(),
            'action_method' => $task->getActionMethod(),
            'action_data' => $task->getActionData(),
        ]);

        $task->reloadData();

        return $task;
    }

    /**
     * Deletes a task
     *
     * @param Task $task
     * @return bool
     */
    public function deleteTask(Task $task) {
        $id = $task->getId();

        if (empty($id)) {
            throw new TaskException("This is not a persistent task, no delete needed");
        }

        $url = "services/" . $this->getService()->getId() . "/tasks/" . $id;
        $this->getService()->getApi()->dataDelete($url);

        unset($this->tasks[$id]);

        return true;
    }


    public function reloadTasks() {
        $url = "services/" . $this->getService()->getId() . "/tasks";
        $_tasks = $this->getService()->getApi()->dataGet($url);

        $this->tasks = [];
        foreach ($_tasks['tasks'] as $task) {
            $_ = new Task();
            $_->setTaskManager($this, $task);
            $this->tasks[$task['id']] =  $_;
        }
    }
}