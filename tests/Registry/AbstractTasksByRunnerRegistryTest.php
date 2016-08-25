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
namespace Teknoo\Tests\East\CodeRunnerBundle\Registry;

use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskUserInterface;
use Teknoo\Tests\East\CodeRunnerBundle\Task\TaskUserTestTrait;

abstract class AbstractTasksByRunnerRegistryTest extends \PHPUnit_Framework_TestCase
{
    use TaskUserTestTrait;

    public function buildTaskUserInstance(): TaskUserInterface
    {
        return $this->buildRegistry();
    }

    abstract public function buildRegistry(): TasksByRunnerRegistryInterface;

    /**
     * @exceptedException \InvalidArgumentException
     */
    public function testOffsetExistsInvalidArgument()
    {
        return isset($this->buildRegistry()[new \stdClass()]);
    }

    /**
     * @exceptedException \InvalidArgumentException
     */
    public function testOffsetGetInvalidArgument()
    {
        return $this->buildRegistry()[new \stdClass()];
    }

    /**
     * @exceptedException \InvalidArgumentException
     */
    public function testOffsetSetInvalidArgument()
    {
        $this->buildRegistry()[new \stdClass()] = $this->createMock(TaskInterface::class);
    }

    /**
     * @exceptedException \InvalidArgumentException
     */
    public function testOffsetUnsetInvalidArgument()
    {
        unset($this->buildRegistry()[new \stdClass()]);
    }

    public function testArrayAccessBehavior()
    {
        $task1 = $this->createMock(TaskInterface::class);
        $task2 = $this->createMock(TaskInterface::class);
        $task3 = $this->createMock(TaskInterface::class);

        $runner1 = $this->createMock(RunnerInterface::class);
        $runner2 = $this->createMock(RunnerInterface::class);
        $runner3 = $this->createMock(RunnerInterface::class);

        $registry = $this->buildRegistry();

        self::assertFalse(isset($registry[$runner1]));
        self::assertFalse(isset($registry[$runner2]));
        self::assertFalse(isset($registry[$runner3]));

        self::assertNull($registry[$runner1]);
        self::assertNull($registry[$runner2]);
        self::assertNull($registry[$runner3]);

        $registry[$runner1] = $task1;
        $registry[$runner2] = $task2;
        $registry[$runner3] = $task3;

        self::assertTrue(isset($registry[$runner1]));
        self::assertTrue(isset($registry[$runner2]));
        self::assertTrue(isset($registry[$runner3]));

        self::assertEquals($task1, $registry[$runner1]);
        self::assertEquals($task2, $registry[$runner2]);
        self::assertEquals($task3, $registry[$runner3]);

        unset($registry[$runner2]);
        $registry[$runner3] = $task1;

        self::assertTrue(isset($registry[$runner1]));
        self::assertFalse(isset($registry[$runner2]));
        self::assertTrue(isset($registry[$runner3]));

        self::assertEquals($task1, $registry[$runner1]);
        self::assertNull($registry[$runner2]);
        self::assertEquals($task1, $registry[$runner3]);
    }

    public function testClearAll()
    {
        $registry = $this->buildRegistry();

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::any())
            ->method('getIdentifier')
            ->willReturn('fooBar');

        $task = $this->createMock(TaskInterface::class);

        self::assertFalse(isset($registry[$runner]));

        $registry[$runner] = $task;

        self::assertTrue(isset($registry[$runner]));

        self::assertInstanceof(
            TasksManagerByTasksRegistryInterface::class,
            $registry->clearAll()
        );

        self::assertFalse(isset($registry[$runner]));
    }
}