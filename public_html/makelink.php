<?php
$target = __DIR__; // This gets the path to public_html
$link = dirname(__DIR__) . '/public'; // This sets the path for the new 'public' shortcut

if (file_exists($link)) {
    echo "The 'public' shortcut already exists! Please delete it in DirectAdmin first.";
} else {
    if (symlink($target, $link)) {
        echo "SUCCESS! The 'public' shortcut has been created. Moodle should now work.";
    } else {
        echo "FAILED. Your host has disabled the PHP symlink function.";
    }
}
?>