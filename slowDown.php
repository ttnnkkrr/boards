<?php
/**
 * Created by PhpStorm.
 * User: Charlie (tank) Simmons
 * Date: 7/7/2019
 * Time: 3:09 PM
 */
/*
 * So this stupid fucker admits to automating the forum
 *
 * Fuck him
 */
if (IN_PHPBB !== true) exit;

/* @var $user \phpbb\user */
/* @var $config \phpbb\config\config */
$slowDownCounter = $request->variable($config['cookie_name'] . '_slowDown', 0, false, \phpbb\request\request_interface::COOKIE);
if ($slowDownCounter > 20)
    sleep(5);
$user->set_cookie('slowDown', $slowDownCounter+1, time()+(60*2),true); 