<?php
$req = \Illuminate\Http\Request::create('/submit', 'POST', [], [], [], [
    'HTTP_ACCEPT' => 'application/json',
    'HTTP_X-Requested-With' => 'XMLHttpRequest'
]);
$response = app()->handle($req);
dump($response->getStatusCode());
dump($response->getContent());
