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

namespace Teknoo\Tests\East\CodeRunner\Service;

use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Service\RabbitMQStatusConsumerService;
use Teknoo\East\CodeRunner\Task\Status;

class RabbitMQStatusConsumerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RemotePHP7Runner
     */
    private $remotePHP7Runner;

    /**
     * @var RunnerManagerInterface
     */
    private $runnerManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RemotePHP7Runner
     */
    public function getRemotePHP7Runner()
    {
        if (!$this->remotePHP7Runner instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->remotePHP7Runner = $this->createMock(RemotePHP7Runner::class);
        }

        return $this->remotePHP7Runner;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RunnerManagerInterface
     */
    public function getRunnerManager()
    {
        if (!$this->runnerManager instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->runnerManager = $this->createMock(RunnerManagerInterface::class);
        }

        return $this->runnerManager;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->logger instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->logger = $this->createMock(LoggerInterface::class);
        }

        return $this->logger;
    }

    /**
     * @return RabbitMQStatusConsumerService
     */
    public function buildService()
    {
        return new RabbitMQStatusConsumerService(
            $this->getRemotePHP7Runner(),
            $this->getRunnerManager(),
            $this->getLogger()
        );
    }

    public function testExecuteBadMessage()
    {
        $message = new AMQPMessage();

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertFalse($this->buildService()->execute($message));
    }

    public function testExecuteBadBehaviorOfManager()
    {
        $status = new Status('foo');
        $message = new AMQPMessage();
        $message->body = json_encode($status);

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushStatus')
            ->with($this->getRemotePHP7Runner(), $status)
            ->willThrowException(new \Exception());

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertFalse($this->buildService()->execute($message));
    }

    public function testExecute()
    {
        $status = new Status('foo');
        $message = new AMQPMessage();
        $message->body = json_encode($status);

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushStatus')
            ->with($this->getRemotePHP7Runner(), $status)
            ->willReturnSelf();

        $this->getLogger()->expects(self::never())->method('critical');

        self::assertTrue($this->buildService()->execute($message));
    }
}