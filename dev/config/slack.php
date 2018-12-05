<?php
return [
    "preset_users"  =>  [
        "trade" =>  [
            "icon_emoji"=> ":money_with_wings:",
            "username"  => "Trade-BOT",
            
        ],
        "dispute" =>  [
            "icon_emoji"=> ":warning:",
            "username"  => "Dispute-BOT",
            "channel"   => env("SLACK_ADMIN_DISPUTES_CHANNEL")
            
        ],
        "timeout"   =>  [
            "icon_emoji"=> ":alarm_clock:",
            "username"  => "Timeout-BOT",
        ],
        "notify"    =>  [
            "icon_emoji"=> ":grey_exclamation:",
            "username"  => "Notification-BOT",
            "channel"   => env("SLACK_ADMIN_NOTIFY_CHANNEL")
        ]
    ]
];