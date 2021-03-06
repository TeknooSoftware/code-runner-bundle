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

namespace Teknoo\East\CodeRunner\Worker\Interfaces;

use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;

/**
 * Interface ComposerConfiguratorInterface.
 * Interface to define in a PHP worker the service able to manage Composer and prepare its autoloader.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface ComposerConfiguratorInterface
{
    /**
     * To reinitialize the configuration of Composer.
     *
     * @return ComposerConfiguratorInterface
     */
    public function reset(): ComposerConfiguratorInterface;

    /**
     * To create the composer.json required by the task's code and initialize Composer and its autoloader.
     *
     * @param CodeInterface   $code
     * @param RunnerInterface $runner
     *
     * @return ComposerConfiguratorInterface
     */
    public function configure(CodeInterface $code, RunnerInterface $runner): ComposerConfiguratorInterface;
}
