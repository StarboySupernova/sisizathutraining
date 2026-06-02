<?php
$configfile = __DIR__ . '/../config.php';
if (!file_exists($configfile)) {
    header("Location: install.php");
    die;
}
require($configfile);

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!