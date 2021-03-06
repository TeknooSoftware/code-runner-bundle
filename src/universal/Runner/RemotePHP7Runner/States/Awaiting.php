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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\States;

use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Awaiting of RemotePHP7Runner to accept new task to execute on the runner.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @mixin RemotePHP7Runner
 */
class Awaiting implements StateInterface
{
    use StateTrait;

    private function doPrepareNextTask()
    {
        /*
         * {@inheritdoc}
         */
        return function (): RunnerInterface {
            return $this;
        };
    }

    private function doExecute()
    {
        /*
         * To execute the task on the runner
         */
        return function (RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface {
            $this->currentTask = $task;
            $this->updateStates();

            $this->taskProducer->publish(json_encode($task));

            return $this;
        };
    }
}
