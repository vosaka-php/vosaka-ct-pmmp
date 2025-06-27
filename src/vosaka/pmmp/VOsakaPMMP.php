<?php

declare(strict_types=1);

namespace vosaka\pmmp;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use venndev\vosaka\VOsaka;

final class VOsakaPMMP
{
    public static int $periodProcessTask = 1;
    private static bool $isInited = false;
    private static ?TaskHandler $task = null;

    // The plugin last used this library's method.
    public static ?PluginBase $lastPlugin = null;

    // Init VOsaka come to PMMP
    public static function init(PluginBase $plugin): void
    {
        if (self::$isInited) {
            return;
        }

        self::initVOsaka();
        self::initTask($plugin);
    }

    // Use to reset the time to rerun VOsaka's event loop processing
    public static function setPeriodProcessTask(
        PluginBase $plugin,
        int $period
    ): void {
        self::$periodProcessTask = $period;
        self::$task->remove(); // Close task

        // Init again
        self::initTask($plugin);
    }

    private static function initVOsaka(): void
    {
        VOsaka::getLoop()->setIterationLimit(1);
    }

    private static function initTask(PluginBase $plugin): void
    {
        self::$lastPlugin = $plugin;

        $task = new ClosureTask(function () {
            VOsaka::run();
        });
        self::$task = $plugin
            ->getScheduler()
            ->scheduleRepeatingTask($task, self::$periodProcessTask);
    }
}
