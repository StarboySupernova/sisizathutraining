<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'zathutrain_mo828';
$CFG->dbuser    = 'zathutrain_mo828';
$CFG->dbpass    = '4hS0.poF1(';
$CFG->prefix    = 'mdl5n_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);

$CFG->wwwroot   = 'https://www.sisizathutraining.com';
$CFG->dataroot  = '/home/zathutrain/moodledata';
$CFG->dirroot   = __DIR__ . '/public';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

// --- TASK 1 & 3: GLOBAL LOGO AND FAVICON OVERRIDE ---
// Note the Capital 'S' in the logo filename and the '?v=2' cache-busters!
$custom_favicon = 'https://www.sisizathutraining.com/sisizathulogo.png?v=2';
$custom_logo = 'https://www.sisizathutraining.com/Sisizathutrainingglassmorphiclogo.png?v=2';

$CFG->additionalhtmlhead = '
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Forcefully overwrite Moodle\'s native Favicon tags
        var favicons = document.querySelectorAll("link[rel~=\'icon\']");
        favicons.forEach(function(fav) { fav.href = "'.$custom_favicon.'"; });
        
        // 2. Forcefully overwrite the Header Logos
        var logos = document.querySelectorAll(".navbar-brand img, .site-name img, .logo img, .navbar-brand .logo");
        logos.forEach(function(img) { 
            img.src = "'.$custom_logo.'"; 
            img.style.maxHeight = "50px"; /* Ensures the new logo fits perfectly in the navbar */
            img.style.width = "auto";
        });
    });
</script>';

require_once(__DIR__ . '/public/lib/setup.php');