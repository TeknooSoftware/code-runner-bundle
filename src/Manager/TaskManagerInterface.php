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
namespace Teknoo\East\CodeRunnerBundle\Manager;

use Teknoo\East\CodeRunnerBundle\Task\TaskInterface;

interface TaskManagerInterface
{
    /**
     * To persist a task to execute and sent it to a register
     * @param TaskInterface $task
     * @return TaskManagerInterface
     */
    public function executeMe(TaskInterface $task): TaskManagerInterface;

    /**
     * To update in the persistent database the status of a task from runner push
     * @param TaskInterface $task
     * @return TaskManagerInterface
     */
    public function updateMyExecutionStatus(TaskInterface $task): TaskManagerInterface;

    /**
     * To register in the persistent database the status of a task from runner push
     * @param TaskInterface $task
     * @return TaskManagerInterface
     */
    public function setMyExecutionResult(TaskInterface $task): TaskManagerInterface;

    /**
     * To close a task to execute
     * @param TaskInterface $task
     * @return TaskManagerInterface
     */
    public function goodBye(TaskInterface $task): TaskManagerInterface;
}