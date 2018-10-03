<?php
return [
    'trade_start_time'   =>  env("TRADE_START"),
    'trade_end_time'   =>  env("TRADE_END"),
    'operation_start_time'   =>  env("OPERATION_START"),
    'operation_end_time'   =>  env("OPERATION_END"),
    'days_offline' => env("DAYS_OFFLINE"),
    'auto_on_hold_minutes' => env("AUTO_ON_HOLD_MINUTES",30)
];