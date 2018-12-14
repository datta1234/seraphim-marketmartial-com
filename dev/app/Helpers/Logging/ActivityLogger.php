<?php

namespace App\Helpers\Logging;

use Dubture\Monolog\Reader\LogReader;
use Closure;

class ActivityLogger
{

    /**
    * retrieve filtered log data - $outputParser can be true,false or a Closure of the parser function to run on each line or false to disable
    *
    * @param String $start
    * @param String $end
    * @param Array $filter
    * @param Closure|Boolean $outputParser
    *
    * @return Array
    */
    public static function getLogData($start, $end, $filter, $outputParser = false)
    {
        // parse to datetimes
        $start = new \DateTime( $start );
        $end = new \DateTime( $end );

        // generate range from interval of 1 day
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval ,$end);

        // test if the default parser is required
        if($outputParser === true) {
            $outputParser = function($line) {
                return implode(' ', [
                    "[".$line['date']->format("Y-m-d H:i:s")."]",
                    $line['logger'].".".$line['level'].":",
                    $line['message'],
                    "[".implode(",", $line['context'])."]",
                    "[".implode(",", $line['extra'])."]",
                ]);
            };
        }

        // itterate over each date in the range and process the logs for that day
        $lines = [];
        $path = config('marketmartial.logging.path', storage_path()."/logs/system/activity.log");
        if(substr($path, -4) != ".log") {
            throw new \Exception("Log file does not have the '.log' extention");
        }
        $path = substr($path, 0, strlen($path)-4 );
        foreach($daterange as $date) {
            $filePath = $path.'-'.$date->format("Y-m-d").".log";
            if(file_exists($filePath)) {
                self::getFilteredContent($filePath, $filter, $lines, $outputParser);
            }
        }
        return $lines;
    }

    /**
    * filter out the lines of log based on the filter object - mutates the $lines parameter
    *
    * @param String $filePath
    * @param Array $filter
    * @param Array $lines
    * @param Closure|Boolean $outputParser
    */
    public static function getFilteredContent($filePath, $filter, &$lines, $outputParser)
    {
        $reader = new LogReader($filePath, 0);

        $sub = self::generateFilterPattern($filter);
        $pattern = '/\[(?P<date>.*)\] (?P<logger>[\w-\s]+).(?P<level>\w+): (?P<message>[^\[\{]+) (?P<context>[\[\{]'.$sub.'[\]\}]) (?P<extra>[\[\{].*[\]\}])/';
        // $reader->getParser()->registerPattern('activity_log', $pattern);
        // $reader->setPattern('activity_log');

        foreach($reader as $line) {
            if(!empty($line)) {
                $lines[] = ( $outputParser ? $outputParser->call(new self(), $line) : $line );
            }
        }
    }

    /**
    * generate rexex pattern for filter
    *
    * @param Array $filter
    */
    private static function generateFilterPattern($filter = [])
    {
        $user = ( 
            $filter['user_id'] != null 
                ? $filter['user_id'] 
                : '.+' 
        );
        $org = ( 
            $filter['organisation_id'] != null 
                ? $filter['organisation_id'] 
                : '.+' 
        );
        $act = ( 
            $filter['activity_type'] != null 
                ? '\"'.$filter['activity_type'].'\"'
                : '.+'
        );
        return $user.'\,'.$org.'\,'.$act;
    }

}