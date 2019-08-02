<?php
return [
    "auth_bearer"=>env("SLACK_AUTH_BEARER"),
    "verification_token"=>env("SLACK_VERIFICATION_TOKEN"),
    "api_url"=>env("SLACK_API_URL"),
    "admin_id"=>env("SLACK_ADMIN_ID"),
    "admin_ref"=>env("SLACK_ADMIN_REF"),
    "admin_notify_channel"=>env("SLACK_ADMIN_NOTIFY_CHANNEL"),
    "trade_channel"=>env("SLACK_ADMIN_TRADES_CHANNEL")
];