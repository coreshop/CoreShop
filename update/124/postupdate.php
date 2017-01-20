<?php

$db = \Pimcore\Db::get();

$db->query("INSERT INTO `users_permission_definitions` VALUES ('coreshop_permission_mail_rules');");