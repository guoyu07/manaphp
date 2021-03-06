<?php
namespace ManaPHP\Cli;

use ManaPHP\Component;

/**
 * Class ManaPHP\Cli\Controller
 *
 * @package controller
 *
 * @property \ManaPHP\Http\ClientInterface       $httpClient
 * @property \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
 * @property \ManaPHP\CounterInterface           $counter
 * @property \ManaPHP\CacheInterface             $cache
 * @property \ManaPHP\DbInterface                $db
 * @property \ManaPHP\Security\CryptInterface    $crypt
 * @property \ManaPHP\Http\Session\BagInterface  $persistent
 * @property \ManaPHP\Di|\ManaPHP\DiInterface    $di
 * @property \ManaPHP\LoggerInterface            $logger
 * @property \Application\Configure              $configure
 * @property \ManaPHP\Cache\AdapterInterface     $viewsCache
 * @property \ManaPHP\FilesystemInterface        $filesystem
 * @property \ManaPHP\Security\RandomInterface   $random
 * @property \ManaPHP\Message\QueueInterface     $messageQueue
 * @property \ManaPHP\Cli\ConsoleInterface       $console
 * @property \ManaPHP\Cli\ArgumentsInterface     $arguments
 * @property \ManaPHP\Text\CrosswordInterface    $crossword
 * @property \ManaPHP\Cli\RouterInterface        $cliRouter
 * @property \Redis                              $redis
 * @property \MongoDB\Client                     $mongodb
 * @property \Elasticsearch\Client               $elasticsearch
 */
abstract class Controller extends Component implements ControllerInterface
{
    public function helpCommand()
    {
        $parts = explode('\\', get_class($this));
        $controller = strtolower(basename(end($parts), 'Controller'));

        foreach (get_class_methods($this) as $method) {
            if (preg_match('#^.*Command$#', $method) !== 1) {
                continue;
            }

            $command = $controller . ':' . basename($method, 'Command');
            $params = [];
            $rm = new \ReflectionMethod($this, $method);
            $lines = explode("\n", $rm->getDocComment());
            foreach ($lines as $line) {
                $line = trim($line, ' \t*');
                $parts = explode(' ', $line, 2);
                if (count($parts) !== 2) {
                    continue;
                }
                list($tag, $description) = $parts;

                if ($tag === '@CliCommand') {
                    $command = $controller . ':' . basename($method, 'Command') . ' ' . trim($description);
                } elseif ($tag === '@CliParam') {
                    $parts = explode(' ', $description, 2);
                    $params[trim($parts[0])] = trim($parts[1]);
                }
            }

            $this->console->writeLn($command);

            if (count($params) !== 0) {
                $maxLength = max(array_map('strlen', array_keys($params)));
                foreach ($params as $name => $value) {
                    $this->console->writeLn('  ' . str_pad($name, $maxLength + 1) . ' ' . $value);
                }
            }
        }
    }
}