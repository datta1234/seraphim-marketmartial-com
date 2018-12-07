<?php
namespace App\Observers;

use ActivityLogger;

class ActivityLogObserver
{
    private $logger;

    /**
     * Constructor
     *
     * @param  \ActivityLogger $logger
     * @return void
     */
    public function __construct()
    {
        // set level
        $this->level = config('marketmartial.logging.level', 'info');
    }

    /**
     * Listen to the Model created event.
     *
     * @param  \App\Models\<mixed> $model
     * @return void
     */
    public function created($model)
    {
        $level = $this->level;
        $messages = $this->getMessages($model, "created");
        foreach($messages as $message) {
            ActivityLogger::{$level}($message, $this->getContext());
        }
    }

    /**
     * Listen to the Model updated event.
     *
     * @param  \App\Models\<mixed> $model
     * @return void
     */
    public function updated($model)
    {
        $level = $this->level;
        $messages = $this->getMessages($model, "updated");
        foreach($messages as $message) {
            ActivityLogger::{$level}($message, $this->getContext());
        }
    }

    /**
     * Listen to the Model deleting event.
     *
     * @param  \App\Models\<mixed> $model
     * @return void
     */
    public function deleted($model)
    {
        $level = $this->level;
        $messages = $this->getMessages($model, "deleted");
        foreach($messages as $message) {
            ActivityLogger::{$level}($message, $this->getContext());
        }
    }


    /**
     * get generated messages from activity
     *
     * @param  \App\Models\<mixed> $model
     * @param  string $context
     * @return string
     */
    public function getMessages($model, $context = "changed")
    {
        // if not then carry on
        $dirty = collect($model->getDirty());   
        $userString = $this->resolveUserString();
        $classHumanName = $this->resolveModelName($model);

        // first try from the model
        try {
            $return = $model->getLogMessages($context, $userString);
            if($return !== false && is_array($return)) {
                return $return;
            }
        } catch(\Exception $e) { 
            /* do nothing... */ 
            \Log::info($e);
        }

        $messages = $dirty->map(function($value, $key) use (&$model, $userString, $classHumanName, $context) {
                return implode(" ", [
                    $userString,
                    $context,
                    $classHumanName,
                    "Field:",
                    $key,
                    "(",
                    '"'.$model->getOriginal($key).'"',
                    "->",
                    '"'.$value.'"',
                    ")",
                ]);
            });
        return $messages;
    }

    /**
     * get the context for logs
     *
     * @return array
     */
    public function getContext()
    {
        $authUser = \Auth::user();
        if(!$authUser) {
            if(\App::runningInConsole()) {
                // background system activity
                return [
                    0,
                    null
                ];
            }
            // system activity
            return [
                null,
                null
            ];
        }
        return [
            $authUser->id,
            $authUser->organisation_id
        ];
    }

    /**
     * Listen to the Model deleting event.
     *
     * @return string
     */
    public function resolveUserString()
    {
        $authUser = \Auth::user();
        if(!$authUser) {
            if(\App::runningInConsole()) {
                return "(System)BackgroundTask";    
            }
            return "(System)ForegroundTask";
        }
        return "(".$authUser->role->title.")".$authUser->full_name;
    }

    /**
     * resolve the humanized name of the model
     *
     * @param  \App\Models\<mixed> $model
     * @return void
     */
    public function resolveModelName($model)
    {
        // try get humanized name from model
        try {
            return $model->getHumanizedLabel();
        } catch(\Exception $e) { 
            /* do nothing... */ 
        }

        // if not continue with class name
        return get_class($model);
    }

}