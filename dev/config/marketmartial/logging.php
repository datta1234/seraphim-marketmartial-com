<?php
/**
*   Models to have activity logged
*
*   NOTE: models can implement 3 functions to aid in log formatting
*
*       getLogMessages($context = "changed", $userString) : array<string>
*           This must return an array of messages to be logged for the respective additions/deletions/changes made
*
*       getHumanizedLabel() : string
*           This must return the human readable label for this model instance
*
*       getActivityType($context = "changed") : string
*           This must return the goruping/type of the change being logged specific to this model
*/
return [
    "name" => "activity_log",
    "log_type" => "daily",
    "max_days" => 180,
    "path" => storage_path()."/logs/system/activity.log",
    "level" => "info",
    "activity_types" =>  [
        "default" => "trade",
        "list" => [
            "trade" => "Trade Activity",
            "dispute" => "Dispute Activity"
        ]
    ],
    "models" => [

        /*
        *   ApiIntegration
        */
        // App\Models\ApiIntegration\JseIntergration::class,
        // App\Models\ApiIntegration\JseTradeIntergration::class,
        // App\Models\ApiIntegration\SlackIntegration::class,

        /*
        *   Market
        */
        App\Models\Market\MarketNegotiation::class,
        App\Models\Market\UserMarket::class,
        // App\Models\Market\UserMarketSubscription::class,
        // App\Models\Market\UserMarketVolatility::class,

        /*
        *   MarketRequest
        */
        App\Models\MarketRequest\UserMarketRequest::class,
        // App\Models\MarketRequest\UserMarketRequestGroup::class,
        // App\Models\MarketRequest\UserMarketRequestItem::class,
        // App\Models\MarketRequest\UserMarketRequestTradable::class,

        /*
        *   StatsUploads
        */
        // App\Models\StatsUploads\OpenInterest::class,
        // App\Models\StatsUploads\SafexTradeConfirmation::class,

        /*
        *   StructureItems
        */
        // App\Models\StructureItems\Item::class,
        // App\Models\StructureItems\ItemType::class,
        // App\Models\StructureItems\Market::class,
        // App\Models\StructureItems\MarketType::class,
        // App\Models\StructureItems\SafexExpirationDate::class,
        // App\Models\StructureItems\Stock::class,
        // App\Models\StructureItems\TradeStructure::class,
        // App\Models\StructureItems\TradeStructureGroup::class,
        // App\Models\StructureItems\TradeStructureGroupType::class,

        /*
        *   Trade
        */
        App\Models\Trade\Rebate::class,
        App\Models\Trade\TradeNegotiation::class,
        // App\Models\Trade\TradeNegotiationStatus::class,

        /*
        *   TradeConfirmations
        */
        // App\Models\TradeConfirmations\BookedTrade::class,
        App\Models\TradeConfirmations\Distpute::class,
        // App\Models\TradeConfirmations\DistputeStatus::class,
        App\Models\TradeConfirmations\TradeConfirmation::class,
        App\Models\TradeConfirmations\TradeConfirmationGroup::class,
        App\Models\TradeConfirmations\TradeConfirmationItem::class,
        App\Models\TradeConfirmations\TradeConfirmationStatus::class,

        /*
        *   UserManagement
        */
        // App\Models\UserManagement\DefaultLabel::class,
        // App\Models\UserManagement\Email::class,
        // App\Models\UserManagement\Interest::class,
        // App\Models\UserManagement\InterestUser::class,
        // App\Models\UserManagement\Organisation::class,
        // App\Models\UserManagement\Role::class,
        // App\Models\UserManagement\Session::class,
        // App\Models\UserManagement\TradingAccount::class,
        // App\Models\UserManagement\User::class,
        // App\Models\UserManagement\UserNotification::class,
        // App\Models\UserManagement\UserNotificationType::class,
        // App\Models\UserManagement\UserOtp::class,

    ]
];