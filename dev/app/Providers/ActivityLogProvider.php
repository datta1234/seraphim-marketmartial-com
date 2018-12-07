<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\ActivityLogObserver;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

class ActivityLogProvider extends ServiceProvider
{
    /**
     * The Log types.
     *
     * @var array
     */
    protected $log_types = [
        'single',
        'daily',
    ];

    /**
     * The Log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug'     => Logger::DEBUG,
        'info'      => Logger::INFO,
        'notice'    => Logger::NOTICE,
        'warning'   => Logger::WARNING,
        'error'     => Logger::ERROR,
        'critical'  => Logger::CRITICAL,
        'alert'     => Logger::ALERT,
        'emergency' => Logger::EMERGENCY,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // hook logging into models registered in config
        $activity_log_models = config('marketmartial.logging.models', []);
        foreach($activity_log_models as $model) {
            $model::observe(ActivityLogObserver::class);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ActivityLogger', function ($app) {
            
            $name = config('marketmartial.logging.name', 'activity-log');
            $max_days = config('marketmartial.logging.max_days', 0);
            $path = config('marketmartial.logging.path', storage_path().'/logs/activity/activity_log.log');

            $log_type = $this->parseLogType(config('marketmartial.logging.log_type', 'single'));
            $level = $this->parseLevel(config('marketmartial.logging.level', 'info'));

            // create a log channel
            $logger = new Logger($name);

            // switch based on log type
            if($log_type == 'daily') {
                $handler = new RotatingFileHandler($path, $max_days, $level);
            } else {
                $handler = new StreamHandler($path, $level);
            }
            $logger->pushHandler($handler);
            return $logger;
        });
    }

    /**
     * Parse the string type to ensure it is valid
     *
     * @param  string  $type
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseLogType($type)
    {
        if (in_array($type, $this->log_types)) {
            return $type;
        }

        throw new \InvalidArgumentException("[ActivityLogger] Invalid log type, valid values are: 'single', 'daily'");
    }

    /**
     * Parse the string level into a Monolog constant.
     *
     * @param  string  $level
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    protected function parseLevel($level)
    {
        if (isset($this->levels[$level])) {
            return $this->levels[$level];
        }

        throw new \InvalidArgumentException('[ActivityLogger] Invalid log level');
    }

}
