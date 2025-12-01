<?php

session_destroy();
session_start();
setFlash('success', 'You have been logged out successfully.');
redirect('/login');
