<?php
date_default_timezone_set("Asia/Dhaka");

class Scheduler
{
    private $tasks = [];

    public function everyMinute($file)
    {
        $this->tasks[] = [
            'file' => $file,
            'interval' => 1,
            'last_run_file' => __DIR__ . '/.' . basename($file) . '.last'
        ];
    }

    public function hourly($file)
    {
        $this->tasks[] = [
            'file' => $file,
            'interval' => 60,
            'last_run_file' => __DIR__ . '/.' . basename($file) . '.last'
        ];
    }

    public function dailyAt($time, $file)
    {
        $this->tasks[] = [
            'file' => $file,
            'time' => $time,
            'last_run_file' => __DIR__ . '/.' . basename($file) . '.last'
        ];
    }

    public function monthlyAt($time, $file)
    {
        $this->tasks[] = [
           'file' => $file,
            'time' => $time,
           'last_run_file' => __DIR__ . '/.' . basename($file) . '.last',
            'monthly' => true
        ];
    }

    public function run()
    {
        $now = time();
        $today = date('Y-m-d');

        foreach ($this->tasks as $task) {
            $lastRun = file_exists($task['last_run_file'])
                ? (int) file_get_contents($task['last_run_file'])
                : 0;

            /*-------- Interval-based tasks--------*/
            if (isset($task['interval'])) {
                $diffMinutes = ($now - $lastRun) / 60;
                if ($diffMinutes >= $task['interval']) {
                    $this->runTask($task, 'interval');
                }
                continue;
            }

            /*-------- MONTHLY TASK--------*/
            if (!empty($task['monthly'])) {

                if (date('d') !== '01') {
                    continue;
                }

                if (date('Y-m', $lastRun) === date('Y-m')) {
                    continue;
                }

                if (date('H:i') >= $task['time']) {
                    $this->runTask($task, 'monthly');
                }

                continue;
            }

            /*-------- DAILY TASK--------*/
            $runAt = strtotime($today . ' ' . $task['time']);

            if (
                $now >= $runAt &&
                date('Y-m-d', $lastRun) !== $today
            ) {
                $this->runTask($task, 'daily');
            }
        }
    }


    private function runTask($task, $type)
    {
        echo "Running ({$type}): {$task['file']} at " . date('Y-m-d H:i:s') . "\n";

        include $task['file'];

        $lastRunFile = $task['last_run_file'];

        /*----Check Folder----*/
        $dir = dirname($lastRunFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
            echo "Created directory: $dir\n";
        }

          /*----Make A File If Not Exists----*/
        if (!file_exists($lastRunFile)) {
            touch($lastRunFile);
            chmod($lastRunFile, 0664); 
            echo "Created last run file: $lastRunFile\n";
        }

        /*----Check Permission----*/
        if (!is_writable($lastRunFile)) {
            chmod($lastRunFile, 0664);
            echo "Fixed permission for: $lastRunFile\n";
        }

        file_put_contents($lastRunFile, time());
        echo "Successfully executed: {$task['file']}\n";
    }


}
