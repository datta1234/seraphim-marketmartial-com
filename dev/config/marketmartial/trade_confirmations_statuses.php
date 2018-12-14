<?php
return [  
	[
        'id' => 1,
        'title' => 'Pending: Initiate Confirmation'
	],
	[
        'id' => 2,
        'title' => 'Pending: Reciever Confirmation'
	],
	[
        'id' => 3,
        'title' => 'Disiputed: By Reciever'
	],
	[
        'id' => 4,
        'title' => 'Confirmed'
	],
	[
        'id' => 5,
        'title' => 'Disiputed: By Sender'
	],
    [
        'id' => 6,
        'title' => 'Updated By Sender' // state of limbo between being recieved and being updated before dispute
    ],
    [
        'id' => 7,
        'title' => 'Updated By Reciever' // state of limbo between being recieved and being updated before dispute
    ],
];
