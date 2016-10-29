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
namespace Teknoo\Tests\East\CodeRunnerBundle\Worker;

use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Worker\Interfaces\ComposerConfiguratorInterface;
use Teknoo\East\CodeRunnerBundle\Worker\Interfaces\RunnerInterface;

abstract class AbstractComposerConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildConfigurator(): ComposerConfiguratorInterface;

    public function testResetReturn()
    {
        self::assertInstanceOf(
            ComposerConfiguratorInterface::class,
            $this->buildConfigurator()->reset()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testConfigureBadCode()
    {
        $this->buildConfigurator()->configure(new \stdClass(), $this->createMock(RunnerInterface::class));
    }

    /**
     * @expectedException \Throwable
     */
    public function testConfigureBadRunner()
    {
        $this->buildConfigurator()->configure($this->createMock(CodeInterface::class), new \stdClass());
    }

    public function testConfigure()
    {
        $code = $this->createMock(CodeInterface::class);
        $code->expects(self::any())->method('getNeededPackages')->willReturn(['foo'=>'2.3.4','bar'=>'*']);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('composerIsReady')->willReturnSelf();

        self::assertInstanceOf(
            ComposerConfiguratorInterface::class,
            $this->buildConfigurator()->configure(
                $code,
                $runner
            )
        );
    }
}