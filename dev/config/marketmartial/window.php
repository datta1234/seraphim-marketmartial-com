<?php
return [
    'trade_start_time'   			=> env("TRADE_START",'09:00:00'),
    'trade_end_time'   				=> env("TRADE_END",'17:00:00'),
    'trade_start_time_display_only'	=> env("TRADE_START_DISPLAY",'09:00:00'),
    'trade_end_time_display_only'	=> env("TRADE_END_DISPLAY",'17:00:00'),
    'trade_view_start_time'			=> env("TRADE_VIEW_START",'09:00:00'),
    'trade_view_end_time' 			=> env("TRADE_VIEW_END",'17:00:00'),
    'operation_start_time'  		=> env("OPERATION_START"),
    'operation_end_time'   			=> env("OPERATION_END"),
    'days_offline' 					=> env("DAYS_OFFLINE"),
    'auto_on_hold_minutes' 			=> env("AUTO_ON_HOLD_MINUTES",30),
];