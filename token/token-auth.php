<?php
if(!empty($_REQUEST['code']))
{
    require_once __DIR__.'/../classes/google.class.php';
    $google =new Google();

    $config = $google->getConfig();

    if(!empty($config->refresh_token) && !empty($config->created) && !empty($config->expires_in) && !empty($config->access_token) && !empty($config->web->baseUrl))
        header('Location: ' . filter_var($config->web->baseUrl."?status=success", FILTER_SANITIZE_URL));
    else
        header('Location: ' . filter_var($config->web->baseUrl."?status=fail", FILTER_SANITIZE_URL));
}