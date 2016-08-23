<?php

/**
 * East CodeRunnerBundle.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\East\CodeRunnerBundle\Manager\TaskManager;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskUserInterface;
use Teknoo\States\Proxy\Integrated;

class TaskManager extends Integrated implements TaskManagerInterface, TaskUserInterface
{
    /**
     * @var TaskInterface[]
     */
    private $tasks = [];

    /**
     * {@inheritdoc}
     */
    public function registerTask(TaskInterface $task): TaskUserInterface
    {
        $this->tasks[\spl_object_hash($task)] = $task;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function executeMe(TaskInterface $task): TaskManagerInterface
    {
        $this->registerTask($task);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function taskStatusIsUpdated(TaskInterface $task, StatusInterface $status): TaskManagerInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function taskResultIsUpdated(TaskInterface $task, ResultInterface $result): TaskManagerInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMyExecutionStatus(TaskInterface $task): TaskManagerInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMyExecutionResult(TaskInterface $task): TaskManagerInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function forgetMe(TaskInterface $task): TaskManagerInterface
    {
        $taskHash = \spl_object_hash($task);
        if (isset($this->tasks[$taskHash])) {
            unset($this->tasks[$taskHash]);
        }

        return $this;
    }
}