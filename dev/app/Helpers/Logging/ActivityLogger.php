<?php

namespace App\Helpers\Logging;

use Dubture\Monolog\Reader\LogReader;
use Closure;

class ActivityLogger
{

    public static function getLogData($start, $end, $filter, $outputParser = false)
    {
        $start = new \DateTime( $start );
        $end = new \DateTime( $end );

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval ,$end);

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

        $lines = [];
        foreach($daterange as $date) {
            $filePath = storage_path()."/logs/system/activity-".$date->format("Y-m-d").".log";
            if(file_exists($filePath)) {
                self::getFilteredContent($filePath, $filter, $lines, $outputParser);
            }
        }
        return $lines;
    }

    public static function getFilteredContent($filePath, $filter, &$lines, $outputParser)
    {
        $reader = new LogReader($filePath);

        $sub = self::generateFilterPattern($filter);
        $pattern = '/\[(?P<date>.*)\] (?P<logger>[\w-\s]+).(?P<level>\w+): (?P<message>[^\[\{]+) (?P<context>[\[\{]'.$sub.'[\]\}]) (?P<extra>[\[\{].*[\]\}])/';

        $reader->getParser()->registerPattern('activity_log', $pattern);
        $reader->setPattern('activity_log');

        foreach($reader as $line) {
            if(!empty($line)) {
                $lines[] = ( $outputParser ? $outputParser->call(new self(), $line) : $line );
            }
        }
        return $lines;
    }

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
        if($filter['user_id'] != null) {
            if($filter['organisation_id'] != null) {
                return $filter['user_id'].'\,'.$filter['organisation_id'].'[\D.]*';
            }
            return $filter['user_id'].'\,.*';
        }
        if($filter['organisation_id'] != null) {
            return '.*\,'.$filter['organisation_id'].'[\D.]*';
        }
        return '.*';
    }

}