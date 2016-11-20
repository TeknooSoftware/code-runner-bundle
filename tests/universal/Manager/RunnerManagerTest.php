<?php

/**
 * East CodeRunner.
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

namespace Teknoo\Tests\East\CodeRunner\Manager;

use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Manager\RunnerManager\RunnerManager;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksStandbyRegistryInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;

/**
 * Test RunnerManagerTest.
 *
 * @covers \Teknoo\East\CodeRunner\Manager\RunnerManager\RunnerManager
 * @covers \Teknoo\East\CodeRunner\Manager\RunnerManager\States\Running
 * @covers \Teknoo\East\CodeRunner\Manager\RunnerManager\States\Selecting
 */
class RunnerManagerTest extends AbstractRunnerManagerTest
{
    /**
     * @var TasksByRunnerRegistryInterface
     */
    private $tasksByRunner;

    /**
     * @var TasksManagerByTasksRegistryInterface
     */
    private $tasksManagerByTasks;

    /**
     * @var TasksStandbyRegistryInterface
     */
    private $tasksStandbyRegistry;

    /**
     * @return TasksByRunnerRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksByRunnerMock(): TasksByRunnerRegistryInterface
    {
        if (!$this->tasksByRunner instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->tasksByRunner = $this->createMock(TasksByRunnerRegistryInterface::class);

            $repository = [];
            $this->tasksByRunner
                ->expects(self::any())
                ->method('offsetExists')
                ->willReturnCallback(function ($name) use (&$repository) {
                    return isset($repository[$name]);
                });

            $this->tasksByRunner
                ->expects(self::any())
                ->method('offsetGet')
                ->willReturnCallback(function ($name) use (&$repository) {
                    return $repository[$name];
                });

            $this->tasksByRunner
                ->expects(self::any())
                ->method('offsetSet')
                ->willReturnCallback(function ($name, $value) use (&$repository) {
                    $repository[$name] = $value;
                });

            $this->tasksByRunner
                ->expects(self::any())
                ->method('offsetUnset')
                ->willReturnCallback(function ($name) use (&$repository) {
                    unset($repository[$name]);
                });
        }

        return $this->tasksByRunner;
    }

    /**
     * @return TasksManagerByTasksRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksManagerByTasksMock(): TasksManagerByTasksRegistryInterface
    {
        if (!$this->tasksManagerByTasks instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->tasksManagerByTasks = $this->createMock(TasksManagerByTasksRegistryInterface::class);

            $repository = [];
            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('offsetExists')
                ->willReturnCallback(function ($name) use (&$repository) {
                    return isset($repository[$name]);
                });

            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('offsetGet')
                ->willReturnCallback(function ($name) use (&$repository) {
                    return $repository[$name];
                });

            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('offsetSet')
                ->willReturnCallback(function ($name, $value) use (&$repository) {
                    $repository[$name] = $value;
                });

            $this->tasksManagerByTasks
                ->expects(self::any())
                ->method('offsetUnset')
                ->willReturnCallback(function ($name) use (&$repository) {
                    unset($repository[$name]);
                });
        }

        return $this->tasksManagerByTasks;
    }

    /**
     * @return TasksStandbyRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksStandbyRegistryMock(): TasksStandbyRegistryInterface
    {
        if (!$this->tasksStandbyRegistry instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->tasksStandbyRegistry = $this->createMock(TasksStandbyRegistryInterface::class);

            $tasksStandbyRegistry = $this->tasksStandbyRegistry;
            $queue = [];
            $this->tasksStandbyRegistry
                ->expects(self::any())
                ->method('enqueue')
                ->willReturnCallback(function (RunnerInterface $runner, TaskInterface $task) use (&$queue, $tasksStandbyRegistry) {
                    $queue[$runner->getIdentifier()][] = $task;

                    return $tasksStandbyRegistry;
                });

            $tasksStandbyRegistry = $this->tasksStandbyRegistry;
            $this->tasksStandbyRegistry
                ->expects(self::any())
                ->method('dequeue')
                ->willReturnCallback(function (RunnerInterface $runner) use (&$queue, $tasksStandbyRegistry) {
                    if (empty($queue[$runner->getIdentifier()])) {
                        return null;
                    }

                    return array_shift($queue[$runner->getIdentifier()]);
                });
        }

        return $this->tasksStandbyRegistry;
    }

    /**
     * @return RunnerManagerInterface|RunnerManager
     */
    public function buildManager(): RunnerManagerInterface
    {
        return new RunnerManager(
            $this->getTasksByRunnerMock(),
            $this->getTasksManagerByTasksMock(),
            $this->getTasksStandbyRegistryMock()
        );
    }

    public function testRegisterMeMustRememberToRunnerItsCurrentlyTask()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('rememberYourCurrentTask')->with($task)->willReturnSelf();
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $this->getTasksByRunnerMock()['abc'] = $task;

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $this->buildManager()->registerMe($runner)
        );
    }

    public function testManagerCanYouExecuteOnBusyRunnerMustNotCallExecute()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('rememberYourCurrentTask')->with($task)->willReturnSelf();
        $runner->expects(self::never())->method('execute');
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $this->getTasksByRunnerMock()['abc'] = $task;

        $runner->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner) {
                $manager->taskAccepted($runner, $task);

                return $runner;
            });

        $this->getTasksStandbyRegistryMock()
            ->expects(self::once())
            ->method('enqueue')
            ->willReturnSelf();

        $manager = $this->buildManager();
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe($runner)
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );
    }

    public function testManagerCanYouExecuteOnBusyRunnerMustNotCallExecuteCanCallLoadNextTaskForAfter()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('rememberYourCurrentTask')->with($task)->willReturnSelf();
        $runner->expects(self::once())->method('execute');
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $this->getTasksByRunnerMock()['abc'] = $task;

        $runner->expects(self::any())
            ->method('canYouExecute')
            ->willReturnCallback(function (RunnerManagerInterface $manager, TaskInterface $task) use ($runner) {
                $manager->taskAccepted($runner, $task);

                return $runner;
            });

        $this->getTasksStandbyRegistryMock()
            ->expects(self::once())
            ->method('enqueue')
            ->willReturnSelf();

        $manager = $this->buildManager();
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe($runner)
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->executeForMeThisTask(
                $this->createMock(TaskManagerInterface::class),
                $task
            )
        );

        unset($this->getTasksByRunnerMock()['abc']);

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->loadNextTaskFor($runner)
        );
    }

    public function testBashMethodsLoadNextTasks()
    {
        $task = $this->createMock(TaskInterface::class);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('execute');
        $runner->expects(self::any())->method('getIdentifier')->willReturn('abc');

        $this->getTasksStandbyRegistryMock()->enqueue($runner, $task);

        $manager = $this->buildManager();
        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->registerMe($runner)
        );

        self::assertInstanceOf(
            RunnerManagerInterface::class,
            $manager->loadNextTasks()
        );
    }
}