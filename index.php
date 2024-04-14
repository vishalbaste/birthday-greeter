<?php
require_once __DIR__.'/classes/google.class.php';
$google =new Google(function($authUrl,$baseUrl)
{
    echo "<a href='{$authUrl}'><button>Google Auth</button></a>";die;
});

$google->getContact();