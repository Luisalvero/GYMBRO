<?php

// Home page - redirect based on auth status
if (isLoggedIn()) {
    redirect('/feed');
} else {
    redirect('/login');
}
