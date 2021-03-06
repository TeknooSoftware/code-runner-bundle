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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\East\CodeRunner\Worker;

use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\RunnerInterface;

/**
 * Class AbstractRunnerTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractRunnerTest extends \PHPUnit_Framework_TestCase
{
    abstract public function builderRunner(): RunnerInterface;

    /**
     * @expectedException \Throwable
     */
    public function testComposerIsReadyBadCode()
    {
        $this->builderRunner()->composerIsReady(new \stdClass());
    }

    public function testComposerIsReady()
    {
        self::assertInstanceOf(
            RunnerInterface::class,
            $this->builderRunner()->composerIsReady(
                $this->createMock(CodeInterface::class)
            )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testCodeExecutedBadCode()
    {
        $this->builderRunner()->codeExecuted(new \stdClass(), $this->createMock(ResultInterface::class));
    }

    /**
     * @expectedException \Throwable
     */
    public function testCodeExecutedBadResult()
    {
        $this->builderRunner()->codeExecuted($this->createMock(CodeInterface::class), new \stdClass());
    }

    public function testCodeExecuted()
    {
        self::assertInstanceOf(
            RunnerInterface::class,
            $this->builderRunner()
                ->codeExecuted(
                    $this->createMock(CodeInterface::class),
                    $this->createMock(ResultInterface::class)
                )
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testErrorInCodedBadCode()
    {
        $this->builderRunner()->codeErrorInCoded(new \stdClass(), $this->createMock(ResultInterface::class));
    }

    /**
     * @expectedException \Throwable
     */
    public function testErrorInCodedBadResult()
    {
        $this->builderRunner()->codeErrorInCoded($this->createMock(CodeInterface::class), new \stdClass());
    }

    public function testErrorInCode()
    {
        self::assertInstanceOf(
            RunnerInterface::class,
            $this->builderRunner()
                ->errorInCode(
                    $this->createMock(CodeInterface::class),
                    $this->createMock(ResultInterface::class)
                )
        );
    }
}
