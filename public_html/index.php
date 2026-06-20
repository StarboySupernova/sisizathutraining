<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle frontpage.
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!file_exists('./config.php')) {
    header('Location: install.php');
    die;
}

require_once('config.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');

redirect_if_major_upgrade_required();

// Redirect logged-in users to homepage if required.
$redirect = optional_param('redirect', 1, PARAM_BOOL);

$urlparams = array();
if (!empty($CFG->defaulthomepage) &&
        ($CFG->defaulthomepage == HOMEPAGE_MY || $CFG->defaulthomepage == HOMEPAGE_MYCOURSES) &&
        $redirect === 0
) {
    $urlparams['redirect'] = 0;
}
$PAGE->set_url('/', $urlparams);
$PAGE->set_pagelayout('frontpage');

// --- SISIZATHU MODIFICATION --- 
// We comment out 'limitedwidth' so Moodle allows our design to span the whole screen
// $PAGE->add_body_class('limitedwidth'); 
// ------------------------------

$PAGE->set_other_editing_capability('moodle/course:update');
$PAGE->set_other_editing_capability('moodle/course:manageactivities');
$PAGE->set_other_editing_capability('moodle/course:activityvisibility');

// Prevent caching of this page to stop confusion when changing page after making AJAX changes.
$PAGE->set_cacheable(false);

require_course_login($SITE);

$hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance());

// If the site is currently under maintenance, then print a message.
if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
    print_maintenance_message();
}

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());

if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
}

// If site registration needs updating, redirect.
\core\hub\registration::registration_reminder('/index.php');

$homepage = get_home_page();
if ($homepage != HOMEPAGE_SITE) {
    if (optional_param('setdefaulthome', false, PARAM_BOOL)) {
        set_user_preference('user_home_page_preference', HOMEPAGE_SITE);
    } else if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_MY) && $redirect === 1) {
        redirect($CFG->wwwroot .'/my/');
    } else if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_MYCOURSES) && $redirect === 1) {
        redirect($CFG->wwwroot .'/my/courses.php');
    } else if ($homepage == HOMEPAGE_URL) {
        redirect(get_default_home_page_url());
    } else if (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_USER)) {
        $frontpagenode = $PAGE->settingsnav->find('frontpage', null);
        if ($frontpagenode) {
            $frontpagenode->add(
                get_string('makethismyhome'),
                new moodle_url('/', array('setdefaulthome' => true)),
                navigation_node::TYPE_SETTING);
        } else {
            $frontpagenode = $PAGE->settingsnav->add(get_string('frontpagesettings'), null, navigation_node::TYPE_SETTING, null);
            $frontpagenode->force_open();
            $frontpagenode->add(get_string('makethismyhome'),
                new moodle_url('/', array('setdefaulthome' => true)),
                navigation_node::TYPE_SETTING);
        }
    }
}

// Trigger event.
course_view(context_course::instance(SITEID));

$PAGE->set_pagetype('site-index');
$PAGE->set_docs_path('');
$editing = $PAGE->user_is_editing();
$PAGE->set_title(get_string('home'));
$PAGE->set_heading($SITE->fullname);
$PAGE->set_secondary_active_tab('coursehome');

$siteformatoptions = course_get_format($SITE)->get_format_options();
$modinfo = get_fast_modinfo($SITE);
$modnamesused = $modinfo->get_used_module_names();

include_course_ajax($SITE, $modnamesused);

$courserenderer = $PAGE->get_renderer('core', 'course');

if ($hassiteconfig) {
    $editurl = new moodle_url('/course/view.php', ['id' => SITEID, 'sesskey' => sesskey()]);
    $editbutton = $OUTPUT->edit_button($editurl);
    $PAGE->set_button($editbutton);
}

echo $OUTPUT->header();

// =====================================================================
// --- START OF SISIZATHU CUSTOM MODERN LMS HTML ---
// =====================================================================
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

<style>
  /* CSS Breakout: Force the dark theme to cover all Moodle white borders */
  body, #page.drawers, #page-wrapper, #region-main, .bg-white {
      background-color: #18204D !important;
  }
  #page.drawers .main-inner, #region-main {
      padding: 0 !important;
      margin: 0 !important;
      border: none !important;
  }

  /* Scoped CSS Wrapper */
  #sisi-modern-wrapper {
    --primary: #F37021; 
    --secondary: #D45D1A; 
    --svg-dark-blue: #18204D;
    --glass-bg-light: rgba(255, 255, 255, 0.08);
    --glass-border: rgba(255, 255, 255, 0.15);
    --white-1: #F8FAFC;
    --gray: #CBD5E1;
    
    font-family: 'Inter', sans-serif;
    color: var(--white-1);
    background-color: var(--svg-dark-blue);
    position: relative;
    overflow: hidden;
    
    /* Breakout formula to fill 100vw regardless of Moodle containers */
    width: 100vw;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    padding: 4rem 10vw; 
    box-sizing: border-box;
  }

  /* Background Wave */
  .sisi-wave-bg {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    z-index: 0; object-fit: cover; pointer-events: none;
  }

  .sisi-content-z { position: relative; z-index: 10; max-width: 1400px; margin: 0 auto; }

  /* Typography */
  #sisi-modern-wrapper h2 { 
      font-family: 'Poppins', sans-serif; font-size: 3rem; font-weight: 800; 
      margin-bottom: 1.5rem; color: #fff; letter-spacing: -1px; line-height: 1.2;
  }
  #sisi-modern-wrapper h2 span { 
      background: linear-gradient(90deg, var(--primary), #ff914d); 
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; 
  }
  #sisi-modern-wrapper p { font-size: 1.1rem; line-height: 1.7; color: var(--white-1); margin-bottom: 1.5rem; }

  /* Buttons */
  .sisi-btn {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 1rem 2rem; border-radius: 30px; font-family: 'Poppins', sans-serif;
      font-weight: 600; text-decoration: none; transition: all 0.3s ease;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      color: white; border: none; box-shadow: 0 10px 20px rgba(243, 112, 33, 0.3);
  }
  .sisi-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(243, 112, 33, 0.5); color: white;}

  /* Badge */
  .sisi-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(90deg, rgba(243, 112, 33, 0.2), rgba(212, 93, 26, 0.05));
    color: white; padding: 0.5rem 1rem; border-radius: 30px; font-size: 0.85rem;
    font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
    border: 1px solid rgba(243, 112, 33, 0.5); margin-bottom: 1.5rem;
  }

  /* Interactive Stacked Cards */
  .sisi-hero { display: flex; align-items: center; gap: 4rem; margin-bottom: 5rem; flex-wrap: wrap; }
  .sisi-hero-text { flex: 1; min-width: 300px; }
  .card-stack-container { flex: 0 0 350px; height: 250px; position: relative; perspective: 1000px; cursor: pointer; margin: 0 auto;}
  
  .stacked-card {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    border-radius: 20px; padding: 1.5rem;
    backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
    border: 1px solid var(--glass-border); transition: all 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2); display: flex; flex-direction: column; justify-content: center;
  }
  .stack-1 { z-index: 5; transform: translate(-30px, 10px) rotate(-4deg); background: linear-gradient(135deg, rgba(67, 22, 219, 0.7), rgba(0, 207, 253, 0.4)); }
  .stack-2 { z-index: 4; transform: translate(30px, -10px) scale(0.95) rotate(0deg); background: linear-gradient(135deg, rgba(243, 112, 33, 0.6), rgba(212, 93, 26, 0.4)); opacity: 1; }
  .stack-3 { z-index: 3; transform: translate(90px, -30px) scale(0.9) rotate(4deg); background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.05)); opacity: 1; }
  
  .card-stack-container:hover .stack-1 { transform: translate(0, 0) rotate(0); z-index: 3; }
  .card-stack-container:hover .stack-2 { transform: translate(30px, -20px) scale(0.95) rotate(0); opacity: 0.9; z-index: 2; }
  .card-stack-container:hover .stack-3 { transform: translate(60px, -40px) scale(0.9) rotate(0); background: rgba(255, 255, 255, 0.1); opacity: 0.6; z-index: 1; }
  
  .stacked-card h4, .stacked-card h3, .stacked-card h5 { 
      font-family: 'Poppins', sans-serif; color: white; display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 1.1rem;
  }
  .stacked-card svg { width: 20px; height: 20px; fill: white; flex-shrink: 0; }
  .faux-line { height: 6px; background: rgba(255,255,255,0.3); border-radius: 10px; margin-bottom: 8px; }
  .w-80 { width: 80%; } .w-60 { width: 60%; } .w-40 { width: 40%; }

  /* Glass Grid */
  .sisi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 5rem; }
  .glass-card {
    background: var(--glass-bg-light); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
    border: 1px solid var(--glass-border); border-radius: 24px; padding: 2rem;
    transition: all 0.4s ease; display: flex; flex-direction: column; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }
  .glass-card:hover {
    transform: translateY(-8px); background: rgba(255, 255, 255, 0.12);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 0 1px var(--primary);
  }
  .glass-card h3 { color: white; font-family: 'Poppins', sans-serif; font-size: 1.4rem; margin-bottom: 1rem; }
  .glass-card p { font-size: 1rem; }
  .glass-icon { width: 40px; height: 40px; fill: var(--primary); margin-bottom: 1.5rem; }

  /* ============================================================== */
  /* MOODLE NATIVE ELEMENTS - GLASSMORPHISM STYLING OVERRIDE        */
  /* ============================================================== */
  
  /* Text and headers */
  .sisi-moodle-dynamic-content { color: var(--white-1); margin-top: 3rem;}
  .sisi-moodle-dynamic-content h2, .sisi-moodle-dynamic-content h3 { color: white; font-family: 'Poppins', sans-serif; font-weight: 700; }
  .sisi-moodle-dynamic-content a { color: white; text-decoration: none; font-weight: 600; transition: color 0.3s; }
  .sisi-moodle-dynamic-content a:hover { color: var(--primary); text-decoration: none; }
  .sisi-moodle-dynamic-content .text-muted, .sisi-moodle-dynamic-content .dimmed_text { color: var(--gray) !important; }

  /* Moodle Site Topic Section */
  .sisi-moodle-section {
      background: var(--glass-bg-light); border: 1px solid var(--glass-border);
      border-radius: 24px; padding: 2.5rem; margin-bottom: 3rem;
      backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.2);
  }

  /* Moodle Course Boxes */
  .sisi-moodle-dynamic-content .coursebox {
      background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border);
      border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem;
      backdrop-filter: blur(15px); transition: all 0.3s ease;
  }
  .sisi-moodle-dynamic-content .coursebox:hover {
      background: rgba(255, 255, 255, 0.1); border-color: rgba(243, 112, 33, 0.5);
      transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,0.3);
  }
  .sisi-moodle-dynamic-content .coursebox .courseimage img { border-radius: 12px; margin-bottom: 1rem; }
  .sisi-moodle-dynamic-content .coursebox .info { margin-bottom: 1rem; }
  .sisi-moodle-dynamic-content .coursebox .coursename { font-size: 1.3rem; }

  /* Moodle Category Combo List */
  .sisi-moodle-dynamic-content .frontpage-category-names { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 2rem;}
  .sisi-moodle-dynamic-content .frontpage-category-names .category,
  .sisi-moodle-dynamic-content .categorybox {
      background: linear-gradient(90deg, rgba(243, 112, 33, 0.2), rgba(212, 93, 26, 0.05));
      border: 1px solid rgba(243, 112, 33, 0.3); border-radius: 30px;
      padding: 0.5rem 1.5rem; transition: all 0.3s ease; display: inline-block;
  }
  .sisi-moodle-dynamic-content .frontpage-category-names .category:hover { background: rgba(243, 112, 33, 0.4); }

  /* Moodle Search Bar Form */
  .sisi-moodle-dynamic-content #coursesearch { text-align: left; margin-bottom: 3rem; }
  .sisi-moodle-dynamic-content input[type="text"] {
      background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.2);
      color: white; padding: 0.8rem 1.5rem; border-radius: 30px; width: 100%; max-width: 400px;
  }
  .sisi-moodle-dynamic-content input[type="text"]:focus { outline: none; border-color: var(--primary); }
  
  .sisi-moodle-dynamic-content input[type="submit"],
  .sisi-moodle-dynamic-content .btn,
  .sisi-moodle-dynamic-content button,
  .sisi-moodle-edit-btn .btn {
      background: linear-gradient(90deg, var(--primary), var(--secondary)) !important;
      color: white !important; border: none !important; padding: 0.8rem 2rem !important;
      border-radius: 30px !important; font-weight: 600 !important; cursor: pointer;
      font-family: 'Poppins', sans-serif; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(243, 112, 33, 0.3);
  }
  .sisi-moodle-dynamic-content input[type="submit"]:hover,
  .sisi-moodle-dynamic-content .btn:hover,
  .sisi-moodle-edit-btn .btn:hover {
      transform: translateY(-2px); box-shadow: 0 10px 20px rgba(243, 112, 33, 0.5);
  }

  .sisi-moodle-edit-btn { margin-top: 4rem; text-align: center; }

  @media (max-width: 768px) {
    #sisi-modern-wrapper { padding: 3rem 1.5rem; }
    .sisi-hero { flex-direction: column-reverse; gap: 3rem;}
    .card-stack-container { width: 100%; max-width: 320px; }
    #sisi-modern-wrapper h2 { font-size: 2.2rem; }
  }
</style>

<div id="sisi-modern-wrapper">
    <!-- SVG Wave Background -->
    <svg class="sisi-wave-bg" viewBox="0 0 1440 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#2a105c" />
                <stop offset="100%" stop-color="#18204D" />
            </linearGradient>
            <linearGradient id="g2" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#F37021" />
                <stop offset="100%" stop-color="#D45D1A" />
            </linearGradient>
            <linearGradient id="g3" x1="0%" y1="100%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#00CFFD" />
                <stop offset="100%" stop-color="#4316DB" stop-opacity="0.3"/>
            </linearGradient>
        </defs>
        <rect width="100%" height="100%" fill="url(#g1)" />
        <path d="M0,250 C300,450 500,50 900,200 C1200,300 1440,100 1440,100 L1440,0 L0,0 Z" fill="url(#g2)" opacity="0.85"/>
        <path d="M0,500 C400,650 800,250 1440,550 L1440,900 L0,900 Z" fill="url(#g3)" opacity="0.6"/>
        <path d="M0,700 C300,550 600,850 1000,600 C1300,450 1440,750 1440,750 L1440,900 L0,900 Z" fill="rgba(255,255,255,0.04)"/>
    </svg>

    <div class="sisi-content-z">
        <!-- Hero Section -->
        <div class="sisi-hero">
            <div class="sisi-hero-text">
                <div class="sisi-badge">SETA Accredited Provider</div>
                <h2>Empowering Futures via <br><span>Digital Learning</span></h2>
                <p>Sisizathu Training provides industry-leading, accredited courses designed to accelerate your career. Experience a modern, flexible, and powerful learning management system tailored for African professionals.</p>
                
                <?php if (isloggedin() && !isguestuser()): ?>
                    <a href="<?php echo $CFG->wwwroot; ?>/my/" class="sisi-btn">
                        Go to My Dashboard
                        <svg style="width:16px;height:16px;fill:white;" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0-105.4 105.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $CFG->wwwroot; ?>/login/index.php" class="sisi-btn">
                        Login to Portal
                        <svg style="width:16px;height:16px;fill:white;" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0-105.4 105.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Card Stack Graphic -->
            <div class="card-stack-container">
                <div class="stacked-card stack-3">
                    <h4><svg viewBox="0 0 512 512"><path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7.1-27.7 .2-40.1l216-368C228.7 39.5 241.8 32 256 32z"/></svg> Interactive Quizzes</h4>
                    <div class="faux-line w-80"></div>
                    <div class="faux-line w-60"></div>
                </div>
                <div class="stacked-card stack-2">
                    <h4><svg viewBox="0 0 512 512"><path d="M256 0c-17.7 0-32 14.3-32 32V64H176c-44.2 0-80 35.8-80 80v16H48c-26.5 0-48 21.5-48 48V352c0 26.5 21.5 48 48 48H96v48c0 35.3 28.7 64 64 64H352c35.3 0 64-28.7 64-64V400h48c26.5 0 48-21.5 48-48V208c0-26.5-21.5-48-48-48H416V144c0-44.2-35.8-80-80-80H288V32c-17.7-32-32-32z"/></svg> Live Workshops</h4>
                    <div class="faux-line w-60"></div>
                    <div class="faux-line w-40"></div>
                </div>
                <div class="stacked-card stack-1">
                    <h3><svg viewBox="0 0 640 512"><path d="M48 0C21.5 0 0 21.5 0 48V368c0 26.5 21.5 48 48 48H64c0 53 43 96 96 96s96-43 96-96H384c0 53 43 96 96 96s96-43 96-96h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V288 256 237.3c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7H416V48c-26.5-48-48-48H48z"/></svg> Accredited Courses</h3>
                    <div class="faux-line w-80"></div>
                    <h4><svg viewBox="0 0 512 512"><path d="M256 0c-17.7 0-32 14.3-32 32V64H176c-44.2 0-80 35.8-80 80v16H48c-26.5 0-48 21.5-48 48V352c0 26.5 21.5 48 48 48H96v48c0 35.3 28.7 64 64 64H352c35.3 0 64-28.7 64-64V400h48c26.5 0 48-21.5 48-48V208c0-26.5-21.5-48-48-48H416V144c-44.2-80-80-80H288V32c-17.7-32-32-32z"/></svg> Live Workshops</h4>
                    <div class="faux-line w-60"></div>
                    <h5><svg viewBox="0 0 512 512"><path d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7.1-27.7 .2-40.1l216-368C228.7 39.5 241.8 32 256 32z"/></svg> Interactive Quizzes</h5>
                    <div class="faux-line w-40"></div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="sisi-grid">
            <div class="glass-card">
                <svg class="glass-icon" viewBox="0 0 512 512"><path d="M256 0c-17.7 0-32 14.3-32 32V64H176c-44.2 0-80 35.8-80 80v16H48c-26.5 0-48 21.5-48 48V352c0 26.5 21.5 48 48 48H96v48c0 35.3 28.7 64 64 64H352c35.3 0 64-28.7 64-64V400h48c26.5 0 48-21.5 48-48V208c0-26.5-21.5-48-48-48H416V144c0-44.2-35.8-80-80-80H288V32c0-17.7-14.3-32-32-32z"/></svg>
                <h3>Learn Anywhere</h3>
                <p>Our platform is 100% responsive. Access your course materials, assignments, and grades from your smartphone, tablet, or desktop computer seamlessly.</p>
            </div>
            <div class="glass-card">
                <svg class="glass-icon" viewBox="0 0 576 512"><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156.5 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 355.5 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.9 78.1-95.4 92.9-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.8-35.7-46.1-87.2-92.9-131.1C433.5 68.8 368.8 32 288 32zM128 256a160 160 0 1 1 320 0 160 160 0 1 1 -320 0z"/></svg>
                <h3>Track Progress</h3>
                <p>Visualize your success. Our advanced dashboards show exactly how far you've come and what you need to complete your certification.</p>
            </div>
            <div class="glass-card">
                <svg class="glass-icon" viewBox="0 0 640 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                <h3>Expert Support</h3>
                <p>Connect with accredited facilitators through forums, direct messaging, and live sessions built right into your course environment.</p>
            </div>
        </div>

        <!-- ========================================================== -->
        <!-- NATIVE MOODLE INTEGRATION (STYLED VIA GLASSMORPHISM CSS)   -->
        <!-- ========================================================== -->
        <?php
        
        // 1. Site Topic / Custom Announcement Inclusion
        if (!empty($CFG->customfrontpageinclude)) {
            $modnames = get_module_types_names();
            $modnamesplural = get_module_types_names(true);
            $mods = $modinfo->get_cms();
            include($CFG->customfrontpageinclude);
        } else if ($siteformatoptions['numsections'] > 0) {
            echo '<div class="sisi-moodle-section">';
            echo $courserenderer->frontpage_section1();
            echo '</div>';
        }
        
        // 2. Moodle Lists (Courses, Categories, Search Box)
        echo '<div class="sisi-moodle-dynamic-content">';
        echo $courserenderer->frontpage();
        echo '</div>';
        
        // 3. Edit Mode / Add a new course button
        if ($editing && has_capability('moodle/course:create', context_system::instance())) {
            echo '<div class="sisi-moodle-edit-btn">';
            echo $courserenderer->add_new_course_button();
            echo '</div>';
        }
        ?>

    </div> <!-- End .sisi-content-z -->
</div> <!-- End #sisi-modern-wrapper -->

<?php
// =====================================================================
// --- END OF SISIZATHU CUSTOM MODERN LMS HTML ---
// =====================================================================

// Output Moodle's footer (Closes out page structure and loads JS scripts)
echo $OUTPUT->footer();