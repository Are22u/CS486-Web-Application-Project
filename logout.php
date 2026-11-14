<?php
require_once __DIR__ . '/includes/init.php';

(new User())->logout();

session_start();               // fresh session just to carry the message
setFlash('success', 'You have been logged out.');
redirect('login.php');
