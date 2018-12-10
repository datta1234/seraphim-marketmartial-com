<?php

namespace App\Helpers\Logging;

use Dubture\Monolog\Reader\LogReader;

class ActivityLogger
{

    public function __construct()
    {

    }

    public function parseLogLineToString($line)
    {
        return implode(' ', [
            "[".$line['date']->format("Y-m-d H:i:s")."]",
            $line['logger'].".".$line['level'].":",
            $line['message'],
            "[".implode(",", $line['context'])."]",
            "[".implode(",", $line['extra'])."]",
        ]);
    }

    public function getLogData($start, $end, $filter, $parseToString = false)
    {
        $start = new \DateTime( $start );
        $end = new \DateTime( $end );

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval ,$end);

        $lines = [];
        foreach($daterange as $date) {
            $filePath = storage_path()."/logs/system/activity-".$date->format("Y-m-d").".log";
            if(file_exists($filePath)) {
                $this->getFilteredContent($filePath, $filter, $lines, $parseToString);
            }
        }
        return $lines;
    }

    public function getFilteredContent($filePath, $filter, &$lines, $parseToString)
    {
        $reader = new LogReader($filePath);

        $sub = $this->generateFilterPattern($filter['user_id'], $filter['organisation_id']);
        $pattern = '/\[(?P<date>.*)\] (?P<logger>[\w-\s]+).(?P<level>\w+): (?P<message>[^\[\{]+) (?P<context>[\[\{]'.$sub.'[\]\}]) (?P<extra>[\[\{].*[\]\}])/';

        $reader->getParser()->registerPattern('activity_log', $pattern);
        $reader->setPattern('activity_log');

        foreach($reader as $line) {
            if(!empty($line)) {
                $lines[] = ( $parseToString ? $this->parseLogLineToString($line) : $line );
            }
        }
        return $lines;
    }

    private function generateFilterPattern($userId = null, $organisationId = null)
    {
        if($userId != null) {
            if($organisationId != null) {
                return $userId.'\,'.$organisationId.'[\D.]*';
            }
            return $userId.'\,.*';
        }
        if($organisationId != null) {
            return '.*\,'.$organisationId.'[\D.]*';
        }
        return '.*';
    }

}