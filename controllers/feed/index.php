<?php

requireLogin();

$currentUser = getCurrentUser();

require __DIR__ . '/../../views/feed/index.php';
