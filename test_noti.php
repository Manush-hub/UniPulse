<?php
require 'app/Core/config.php';
require 'app/Core/Database.php';
require 'app/Core/Model.php';
require 'app/models/Notification.php';
var_dump(method_exists('Notification', 'sendNotification'));
