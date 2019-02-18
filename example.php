<?php

require('bitdb.php');

print(json_encode(query_bitdb((object) [
    'v' => 3,
    'q' => [
        'find' => (object) [],
        'limit' => 10
    ]
])));

query_bitsocket((object) [
    'v' => 3,
    'q' => [
        'find' => (object) [],
        'limit' => 10
    ]
], function($data) {
	print(json_encode($data));
});
