<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Doctrine\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Debug\Stopwatch;
use Doctrine\DBAL\Logging\DebugStack;

/**
 * DbalLogger.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DbalLogger extends DebugStack
{
    protected $logger;
    protected $stopwatch;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger    A LoggerInterface instance
     * @param Stopwatch       $stopwatch A Stopwatch instance
     */
    public function __construct(LoggerInterface $logger = null, Stopwatch $stopwatch = null)
    {
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        parent::startQuery($sql, $params, $types);

        if (null !== $this->stopwatch) {
            $this->stopwatch->start('doctrine', 'doctrine');
        }

        if (null !== $this->logger) {
            $this->log($sql.' ('.json_encode($params).')');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        parent::stopQuery();

        if (null !== $this->stopwatch) {
            $this->stopwatch->stop('doctrine');
        }
    }

    /**
     * Logs a message.
     *
     * @param string $message A message to log
     */
    public function log($message)
    {
        $this->logger->debug($message);
    }
}
