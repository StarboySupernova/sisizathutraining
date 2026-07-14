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
$CFG->dboptions = array(
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

<style>

/* =====================================================================
   SISIZATHU TRAINING
   ULTRA GLASSMORPHIC GLOBAL SCROLLBAR
   Targets virtually every scrollbar in Moodle
   ===================================================================== */

/* Firefox */
*{
    scrollbar-width: thin !important;
    scrollbar-color:
        rgba(255,255,255,.40)
        rgba(12,12,12,.75) !important;
}

/* Chromium Browsers */
html::-webkit-scrollbar,
body::-webkit-scrollbar,
*::-webkit-scrollbar{
    width:16px !important;
    height:16px !important;
}

/* Track */

html::-webkit-scrollbar-track,
body::-webkit-scrollbar-track,
*::-webkit-scrollbar-track{

    background:
        linear-gradient(
            180deg,
            rgba(42,42,42,.70),
            rgba(12,12,12,.92)
        ) !important;

    border-radius:999px !important;

    border:1px solid rgba(255,255,255,.08) !important;

    box-shadow:
        inset 0 0 16px rgba(255,255,255,.03),
        inset 0 0 40px rgba(0,0,0,.65),
        0 0 15px rgba(0,0,0,.35);

    backdrop-filter:blur(22px) saturate(180%);
    -webkit-backdrop-filter:blur(22px) saturate(180%);
}

/* Thumb */

html::-webkit-scrollbar-thumb,
body::-webkit-scrollbar-thumb,
*::-webkit-scrollbar-thumb{

    background:
        linear-gradient(
            180deg,
            rgba(255,255,255,.48),
            rgba(170,170,170,.22)
        ) !important;

    border-radius:999px !important;

    border:3px solid rgba(18,18,18,.65) !important;

    background-clip:padding-box !important;

    backdrop-filter:blur(28px) saturate(220%);
    -webkit-backdrop-filter:blur(28px) saturate(220%);

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.75),
        inset 0 -1px 0 rgba(255,255,255,.20),
        0 0 18px rgba(255,255,255,.18),
        0 8px 30px rgba(0,0,0,.55);

    transition:all .25s ease;
}

/* Hover */

html::-webkit-scrollbar-thumb:hover,
body::-webkit-scrollbar-thumb:hover,
*::-webkit-scrollbar-thumb:hover{

    background:
        linear-gradient(
            180deg,
            rgba(255,255,255,.72),
            rgba(210,210,210,.38)
        ) !important;

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.95),
        0 0 25px rgba(255,255,255,.35),
        0 0 45px rgba(0,0,0,.60);

    transform:scale(1.04);
}

/* Active */

html::-webkit-scrollbar-thumb:active,
body::-webkit-scrollbar-thumb:active,
*::-webkit-scrollbar-thumb:active{

    background:
        linear-gradient(
            180deg,
            rgba(255,255,255,.90),
            rgba(255,255,255,.55)
        ) !important;
}

/* Corner */

::-webkit-scrollbar-corner{
    background:rgba(12,12,12,.92) !important;
}

/* Buttons */

::-webkit-scrollbar-button{
    display:none !important;
}

/* Force Moodle Containers */

#region-main,
#region-main-box,
.drawer,
.drawercontent,
.block,
.course-content,
.course-section,
.courseindex,
.secondary-navigation,
.primary-navigation,
.navbar,
.dropdown-menu,
.modal,
.modal-body,
.card,
.card-body,
.list-group,
.list-group-item,
.table-responsive,
.tab-content,
.popover,
.offcanvas,
.pre-scrollable,
pre,
code,
textarea,
iframe,
div,
section,
main,
article,
aside{

    scrollbar-width:thin !important;

    scrollbar-color:
        rgba(255,255,255,.40)
        rgba(12,12,12,.75) !important;
}

/* Individual Moodle WebKit Scrollbars */

#region-main::-webkit-scrollbar,
#region-main-box::-webkit-scrollbar,
.drawer::-webkit-scrollbar,
.drawercontent::-webkit-scrollbar,
.block::-webkit-scrollbar,
.course-content::-webkit-scrollbar,
.course-section::-webkit-scrollbar,
.courseindex::-webkit-scrollbar,
.secondary-navigation::-webkit-scrollbar,
.primary-navigation::-webkit-scrollbar,
.navbar::-webkit-scrollbar,
.dropdown-menu::-webkit-scrollbar,
.modal::-webkit-scrollbar,
.modal-body::-webkit-scrollbar,
.card::-webkit-scrollbar,
.card-body::-webkit-scrollbar,
.list-group::-webkit-scrollbar,
.table-responsive::-webkit-scrollbar,
.tab-content::-webkit-scrollbar,
.popover::-webkit-scrollbar,
.offcanvas::-webkit-scrollbar,
textarea::-webkit-scrollbar,
pre::-webkit-scrollbar,
code::-webkit-scrollbar,
iframe::-webkit-scrollbar,
div::-webkit-scrollbar{

    width:16px !important;
    height:16px !important;
}

/* Smooth Scrolling */

html{
    scroll-behavior:smooth;
}

/* Slight glow */

::-webkit-scrollbar-thumb{
    filter:brightness(1.08);
}

</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Forcefully overwrite Moodle\'s native Favicon tags
        var favicons = document.querySelectorAll("link[rel~=\'icon\']");
        favicons.forEach(function(fav) { fav.href = "' . $custom_favicon . '"; });
        
        // 2. Forcefully overwrite the Header Logos
        var logos = document.querySelectorAll(".navbar-brand img, .site-name img, .logo img, .navbar-brand .logo");
        logos.forEach(function(img) { 
            img.src = "' . $custom_logo . '"; 
            img.style.maxHeight = "50px"; /* Ensures the new logo fits perfectly in the navbar */
            img.style.width = "auto";
        });
    });
</script>';

require_once(__DIR__ . '/public/lib/setup.php');
