<?php
// This file is part of Moodle - http://moodle.org/

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

// Removed Moodle's width constraint
// $PAGE->add_body_class('limitedwidth'); 

$PAGE->set_other_editing_capability('moodle/course:update');
$PAGE->set_other_editing_capability('moodle/course:manageactivities');
$PAGE->set_other_editing_capability('moodle/course:activityvisibility');

$PAGE->set_cacheable(false);
require_course_login($SITE);

$hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance());
if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
    print_maintenance_message();
}

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect($CFG->wwwroot .'/'. $CFG->admin .'/index.php');
}

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

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700;800;900&display=swap" rel="stylesheet">

<style>
  /* ========================================================== */
  /* 1. ULTIMATE MOODLE BACKGROUND OVERRIDE (FIXES WHITE CUTOFF)*/
  /* ========================================================== */
  html, body, #page.drawers, #page-wrapper, #page-content, #region-main-box, #region-main, .bg-white, .card, #page-footer {
      background-color: #18204D !important;
      background-image: none !important;
      border: none !important;
      box-shadow: none !important;
      margin: 0 !important;
      max-width: 100% !important; 
  }
  
  /* NUKE Moodle's sneaky inner paddings that cause white bars */
  #page.drawers .main-inner {
      padding: 0 !important;
      margin: 0 !important;
      background: #18204D !important;
  }
  
  #page-header { display: none !important; } /* Hides white top bar */
  #region-main { padding: 0 !important; margin: 0 !important; }
  
  #page-footer { border-top: 1px solid rgba(255,255,255,0.05) !important; padding: 3rem 0 !important; color: var(--gray) !important;}
  #page-footer a { color: #F37021 !important; }

  /* ========================================================== */
  /* 2. CUSTOM LMS DESIGN VARIABLES & BASE                     */
  /* ========================================================== */
  #sisi-modern-wrapper {
    --primary: #F37021; 
    --secondary: #D45D1A; 
    --svg-dark-blue: #18204D;
    --glass-bg-light: rgba(255, 255, 255, 0.04);
    --glass-border: rgba(255, 255, 255, 0.1);
    --white-1: #F8FAFC;
    --gray: #CBD5E1;
    
    font-family: 'Inter', sans-serif;
    color: var(--white-1);
    width: 100vw;
    position: relative;
    left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; /* Full bleed breakout */
    background: linear-gradient(180deg, #18204D 0%, #11173b 50%, #18204D 100%);
    overflow: hidden;
    padding-bottom: 5rem;
    margin-bottom: 0 !important;
  }

  /* Fluid Absolute Waves */
  .sisi-wave-top, .sisi-wave-mid, .sisi-wave-bot {
    position: absolute; left: 0; width: 100%; z-index: 0; object-fit: cover; pointer-events: none; opacity: 0.8;
  }
  .sisi-wave-top { top: 0; height: 1100px; }
  .sisi-wave-mid { top: 40%; height: 800px; opacity: 0.5; transform: scaleY(-1); }
  .sisi-wave-bot { bottom: 0; height: 1200px; opacity: 0.6; }

  /* Content Container Limits Width but scales down safely */
  .sisi-container { position: relative; z-index: 10; max-width: 1400px; margin: 0 auto; padding: 0 5vw; }

  /* Ambient Glowing Orbs & Animations */
  @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
  @keyframes pulseGlow { 0% { opacity: 0.5; transform: scale(1); } 50% { opacity: 0.8; transform: scale(1.1); } 100% { opacity: 0.5; transform: scale(1); } }
  
  .glow-orb-1, .glow-orb-2, .glow-orb-3 { position: absolute; border-radius: 50%; pointer-events: none; z-index: 1; animation: pulseGlow 8s infinite alternate; }
  .glow-orb-1 { width: 50vw; height: 50vw; max-width: 600px; max-height: 600px; background: rgba(243, 112, 33, 0.15); filter: blur(100px); top: 5%; left: -10%; }
  .glow-orb-2 { width: 40vw; height: 40vw; max-width: 500px; max-height: 500px; background: rgba(0, 207, 253, 0.15); filter: blur(120px); top: 45%; right: -10%; }
  .glow-orb-3 { width: 60vw; height: 60vw; max-width: 700px; max-height: 700px; background: rgba(67, 22, 219, 0.15); filter: blur(150px); bottom: 10%; left: 10%; }

  /* Typography */
  .sisi-title { font-family: 'Poppins', sans-serif; font-size: clamp(2.5rem, 5vw, 4.5rem); font-weight: 800; margin-bottom: 1.5rem; color: #fff; letter-spacing: -1.5px; line-height: 1.15; }
  .sisi-title span { background: linear-gradient(90deg, var(--primary), #ffb347); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0 0 15px rgba(243, 112, 33, 0.3)); }
  .sisi-subtitle { font-size: clamp(1rem, 2vw, 1.15rem); line-height: 1.8; color: var(--gray); margin-bottom: 2.5rem; max-width: 650px; }
  .section-heading { font-family: 'Poppins', sans-serif; font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 700; text-align: center; margin-bottom: 3rem; color: white; }

  /* High Contrast Buttons */
  .sisi-btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 12px;
      padding: 1.2rem 2.5rem; border-radius: 50px; font-family: 'Poppins', sans-serif;
      font-size: 1.1rem; font-weight: 700; text-decoration: none; transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
      background: #ffffff !important; color: var(--svg-dark-blue) !important;
      border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.3); text-align: center;
  }
  .sisi-btn:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(243, 112, 33, 0.5); color: var(--primary) !important; }
  .sisi-btn svg { fill: var(--svg-dark-blue); transition: fill 0.3s ease; }
  .sisi-btn:hover svg { fill: var(--primary); }

  .sisi-btn-outline {
      background: rgba(255,255,255,0.05) !important; color: white !important;
      border: 1px solid rgba(255,255,255,0.2); box-shadow: none; backdrop-filter: blur(10px);
  }
  .sisi-btn-outline:hover { background: rgba(255,255,255,0.15) !important; border-color: white; color: white !important; }

  /* Badge */
  .sisi-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(243, 112, 33, 0.15); color: #ffb347; padding: 0.6rem 1.2rem;
    border-radius: 30px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.5px; border: 1px solid rgba(243, 112, 33, 0.3); margin-bottom: 1.5rem;
  }

  /* ========================================================== */
  /* 3. HERO SECTION (Fully Responsive)                         */
  /* ========================================================== */
  .sisi-hero { display: flex; align-items: center; gap: 3rem; padding: 6rem 0 8rem 0; flex-wrap: wrap; position: relative; z-index: 10; }
  .sisi-hero-text { flex: 1 1 500px; }
  .sisi-btn-group { display: flex; gap: 1rem; flex-wrap: wrap; }
  
  /* 3D Stacked Cards */
  .card-stack-container { flex: 1 1 400px; height: 380px; position: relative; perspective: 1000px; cursor: pointer; animation: float 6s ease-in-out infinite; }
  .stacked-card {
    position: absolute; top: 0; left: 10%; width: 80%; height: 100%;
    border-radius: 24px; padding: 2.5rem; backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
    border: 1px solid var(--glass-border); transition: all 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
    box-shadow: 0 20px 50px rgba(0,0,0,0.4); display: flex; flex-direction: column; justify-content: center;
  }
  .stack-1 { z-index: 5; transform: translate(-30px, 20px) rotate(-4deg); background: linear-gradient(135deg, rgba(67, 22, 219, 0.85), rgba(0, 207, 253, 0.4)); }
  .stack-2 { z-index: 4; transform: translate(20px, -5px) scale(0.95) rotate(2deg); background: linear-gradient(135deg, rgba(243, 112, 33, 0.8), rgba(212, 93, 26, 0.5)); }
  .stack-3 { z-index: 3; transform: translate(80px, -30px) scale(0.9) rotate(6deg); background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.02)); }
  
  .card-stack-container:hover .stack-1 { transform: translate(0, 0) rotate(0); z-index: 3; }
  .card-stack-container:hover .stack-2 { transform: translate(20px, -20px) scale(0.95) rotate(0); opacity: 0.95; z-index: 2; }
  .card-stack-container:hover .stack-3 { transform: translate(40px, -40px) scale(0.9) rotate(0); background: rgba(255, 255, 255, 0.15); z-index: 1; }
  
  .stacked-card h3 { font-family: 'Poppins', sans-serif; font-size: 1.4rem; color: white; display: flex; align-items: center; gap: 15px; margin-bottom: 1rem; }
  .stacked-card h4 { font-family: 'Poppins', sans-serif; font-size: 1.1rem; color: rgba(255,255,255,0.9); display: flex; align-items: center; gap: 10px; margin-bottom: 0.8rem; }
  .icon-3d { width: 45px; height: 45px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5)); }
  .icon-3d-sm { width: 30px; height: 30px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4)); }
  .faux-line { height: 8px; background: rgba(255,255,255,0.25); border-radius: 10px; margin-bottom: 12px; }
  .w-80 { width: 80%; } .w-60 { width: 60%; } .w-40 { width: 40%; }

  /* ========================================================== */
  /* 4. STATS BAR                                               */
  /* ========================================================== */
  .stats-container {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 2rem;
      background: linear-gradient(90deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
      border: 1px solid var(--glass-border); border-radius: 24px; padding: 3rem;
      margin-bottom: 8rem; backdrop-filter: blur(20px); box-shadow: 0 15px 35px rgba(0,0,0,0.2);
  }
  .stat-box { text-align: center; }
  .stat-box h3 { font-family: 'Poppins', sans-serif; font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; color: white; margin: 0; line-height: 1; text-shadow: 0 4px 10px rgba(243, 112, 33, 0.4); }
  .stat-box p { font-size: 0.9rem; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-top: 0.8rem; }

  /* ========================================================== */
  /* 5. MASSIVE FEATURES GRID                                   */
  /* ========================================================== */
  .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 8rem; }
  .glass-card {
    background: var(--glass-bg-light); backdrop-filter: blur(25px); border: 1px solid var(--glass-border);
    border-radius: 30px; padding: 3rem 2.5rem; transition: all 0.4s ease; display: flex; flex-direction: column;
  }
  .glass-card:hover { transform: translateY(-10px); background: rgba(255, 255, 255, 0.08); border-color: rgba(243, 112, 33, 0.4); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4); }
  .glass-icon-3d { width: 80px; height: 80px; margin-bottom: 1.5rem; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.5)); transition: transform 0.4s; }
  .glass-card:hover .glass-icon-3d { transform: scale(1.1) rotate(5deg); }
  .glass-card h3 { color: white; font-family: 'Poppins', sans-serif; font-size: 1.4rem; margin-bottom: 1rem; font-weight: 700; }
  .glass-card p { font-size: 1rem; color: var(--gray); line-height: 1.6; }

  /* ========================================================== */
  /* 6. TESTIMONIALS / SOCIAL PROOF                             */
  /* ========================================================== */
  .testimonials { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 8rem; }
  .review-card {
      background: linear-gradient(180deg, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.01) 100%);
      border: 1px solid var(--glass-border); border-radius: 24px; padding: 2.5rem;
      position: relative; overflow: hidden; backdrop-filter: blur(15px);
  }
  .review-card::before {
      content: '"'; position: absolute; top: -10px; right: 20px; font-family: 'Poppins', sans-serif;
      font-size: 8rem; color: rgba(255,255,255,0.06); line-height: 1; pointer-events: none;
  }
  .stars { color: #ffb347; font-size: 1.2rem; margin-bottom: 1rem; letter-spacing: 2px; }
  .review-text { font-size: 1.05rem; color: white; font-style: italic; margin-bottom: 2rem; line-height: 1.7; }
  .reviewer { display: flex; align-items: center; gap: 15px; }
  .reviewer-img { width: 55px; height: 55px; border-radius: 50%; background: #fff; border: 2px solid var(--primary); }
  .reviewer-info h4 { color: white; font-family: 'Poppins', sans-serif; font-size: 1.1rem; margin: 0; }
  .reviewer-info p { color: var(--primary); font-size: 0.9rem; margin: 0; font-weight: 600;}

  /* ========================================================== */
  /* 7. CTA BOTTOM BANNER                                       */
  /* ========================================================== */
  .cta-banner {
      background: linear-gradient(135deg, var(--primary), #9c3c08);
      border-radius: 40px; padding: 5rem 3rem; text-align: center; margin-bottom: 6rem;
      position: relative; overflow: hidden; box-shadow: 0 30px 60px rgba(243, 112, 33, 0.4);
  }
  .cta-banner::after {
      content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.15)" stroke-width="2" fill="none"/></svg>') repeat;
      background-size: 150px; opacity: 0.5; pointer-events: none;
  }
  .cta-banner h2 { font-family: 'Poppins', sans-serif; font-size: clamp(2rem, 4vw, 3.5rem); color: white; font-weight: 800; margin-bottom: 1rem; position: relative; z-index: 2;}
  .cta-banner p { font-size: clamp(1rem, 2vw, 1.3rem); color: rgba(255,255,255,0.9); margin-bottom: 3rem; position: relative; z-index: 2;}
  
  /* ========================================================== */
  /* 8. NATIVE MOODLE INTEGRATION STYLES                        */
  /* ========================================================== */
  .sisi-moodle-dynamic-content { position: relative; z-index: 10; padding-bottom: 2rem;}
  .sisi-moodle-dynamic-content h2 { font-family: 'Poppins', sans-serif; font-size: clamp(1.8rem, 3vw, 2.5rem); text-align: center; margin-bottom: 2.5rem; color: white; }
  .sisi-moodle-dynamic-content .coursebox {
      background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border);
      border-radius: 20px; padding: 2.5rem; margin-bottom: 1.5rem; transition: all 0.3s ease;
      backdrop-filter: blur(10px);
  }
  .sisi-moodle-dynamic-content .coursebox:hover { background: rgba(255, 255, 255, 0.1); border-color: var(--primary); transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,0.3);}
  .sisi-moodle-dynamic-content .coursename a { color: white; text-decoration: none; font-size: 1.5rem; font-family: 'Poppins', sans-serif; font-weight: 700;}
  .sisi-moodle-dynamic-content .coursename a:hover { color: var(--primary); }
  .sisi-moodle-dynamic-content .frontpage-category-names { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-bottom: 4rem; }
  .sisi-moodle-dynamic-content .categorybox {
      background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.15); border-radius: 30px;
      padding: 1rem 2.5rem; transition: all 0.3s ease; backdrop-filter: blur(5px);
  }
  .sisi-moodle-dynamic-content .categorybox:hover { background: var(--primary); border-color: var(--primary); transform: translateY(-3px);}
  .sisi-moodle-dynamic-content .categorybox a { color: white; font-weight: 700; text-decoration: none; font-size: 1.1rem;}
  .sisi-moodle-dynamic-content input[type="text"] { background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 1.2rem 2rem; border-radius: 50px; width: 100%; max-width: 500px; font-size: 1rem;}
  .sisi-moodle-dynamic-content input[type="submit"], .sisi-moodle-edit-btn .btn { background: #fff !important; color: var(--svg-dark-blue) !important; border-radius: 50px !important; padding: 1.2rem 3rem !important; font-weight: 800 !important; border: none !important; margin-left: 10px; cursor: pointer; transition: all 0.3s ease; font-size: 1rem;}
  .sisi-moodle-dynamic-content input[type="submit"]:hover, .sisi-moodle-edit-btn .btn:hover { background: var(--primary) !important; color: white !important; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(243, 112, 33, 0.4);}
  .sisi-moodle-edit-btn { text-align: center; margin-top: 5rem; }

  /* ========================================================== */
  /* 9. MOBILE RESPONSIVENESS FIXES                             */
  /* ========================================================== */
  @media (max-width: 850px) {
    .sisi-hero { flex-direction: column; text-align: center; padding: 3rem 0 5rem 0; gap: 4rem;}
    .sisi-subtitle { margin-left: auto; margin-right: auto; }
    .sisi-btn-group { justify-content: center; }
    .card-stack-container { width: 100%; height: 300px; }
    .stacked-card { left: 0; width: 100%; padding: 1.5rem; }
    .stack-1 { transform: translate(-10px, 10px) rotate(-3deg); }
    .stack-2 { transform: translate(10px, -5px) scale(0.95) rotate(2deg); }
    .stack-3 { transform: translate(25px, -20px) scale(0.9) rotate(5deg); }
    .stats-container { padding: 2rem 1rem; gap: 1.5rem; }
    .cta-banner { padding: 4rem 1.5rem; border-radius: 25px; }
    .sisi-moodle-dynamic-content input[type="text"] { margin-bottom: 1rem; max-width: 100%; }
    .sisi-moodle-dynamic-content input[type="submit"] { margin-left: 0; width: 100%; }
  }
</style>

<div id="sisi-modern-wrapper">
    
    <!-- FLUID MULTIPLE SVG WAVES -->
    <!-- Top Wave -->
    <svg class="sisi-wave-top" viewBox="0 0 1440 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="g-orange" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#F37021" /><stop offset="100%" stop-color="#D45D1A" />
            </linearGradient>
            <linearGradient id="g-cyan" x1="0%" y1="100%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#00CFFD" /><stop offset="100%" stop-color="#4316DB" stop-opacity="0.3"/>
            </linearGradient>
        </defs>
        <path d="M0,250 C300,450 500,50 900,200 C1200,300 1440,100 1440,100 L1440,0 L0,0 Z" fill="url(#g-orange)" opacity="0.9"/>
        <path d="M0,500 C400,650 800,250 1440,550 L1440,0 L0,0 Z" fill="url(#g-cyan)" opacity="0.4"/>
    </svg>

    <!-- Mid Wave -->
    <svg class="sisi-wave-mid" viewBox="0 0 1440 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,300 C400,100 800,600 1440,300 L1440,900 L0,900 Z" fill="rgba(67, 22, 219, 0.15)"/>
        <path d="M0,500 C500,700 900,200 1440,600 L1440,900 L0,900 Z" fill="rgba(243, 112, 33, 0.08)"/>
    </svg>

    <!-- Bottom Wave -->
    <svg class="sisi-wave-bot" viewBox="0 0 1440 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,600 C300,400 600,800 1440,500 L1440,900 L0,900 Z" fill="url(#g-cyan)" opacity="0.2"/>
        <path d="M0,750 C400,900 800,600 1440,800 L1440,900 L0,900 Z" fill="url(#g-orange)" opacity="0.5"/>
    </svg>

    <!-- Ambient Glowing Orbs -->
    <div class="glow-orb-1"></div>
    <div class="glow-orb-2"></div>
    <div class="glow-orb-3"></div>

    <div class="sisi-container">
        
        <!-- 1. HERO SECTION -->
        <div class="sisi-hero">
            <div class="sisi-hero-text">
                <div class="sisi-badge">✨ Next-Generation Learning</div>
                <h1 class="sisi-title">Elevate Your Career with <br><span>Immersive Digital</span> Training</h1>
                <p class="sisi-subtitle">Sisizathu Training delivers elite, industry-recognized certifications. Experience an ultra-modern, gamified learning portal designed to accelerate African professionals to the pinnacle of their industries.</p>
                
                <div class="sisi-btn-group">
                    <?php if (isloggedin() && !isguestuser()): ?>
                        <a href="<?php echo $CFG->wwwroot; ?>/my/" class="sisi-btn">
                            Access My Dashboard
                            <svg style="width:18px;height:18px;" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0-105.4 105.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $CFG->wwwroot; ?>/login/index.php" class="sisi-btn">
                            Login to Portal
                            <svg style="width:18px;height:18px;" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0-105.4 105.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                        </a>
                        <a href="#coursesearch" class="sisi-btn sisi-btn-outline">Explore Courses</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3D Card Stack -->
            <div class="card-stack-container">
                <div class="stacked-card stack-3">
                    <h4>
                        <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Fire/3D/fire_3d.png" class="icon-3d-sm" alt="Fire"> 
                        Instant Feedback
                    </h4>
                    <div class="faux-line w-80"></div>
                </div>
                <div class="stacked-card stack-2">
                    <h4>
                        <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Trophy/3D/trophy_3d.png" class="icon-3d-sm" alt="Trophy"> 
                        Gamified Leaderboards
                    </h4>
                    <div class="faux-line w-60"></div>
                    <div class="faux-line w-40"></div>
                </div>
                <div class="stacked-card stack-1">
                    <h3>
                        <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Rocket/3D/rocket_3d.png" class="icon-3d" alt="Rocket"> 
                        Fast-Track Certification
                    </h3>
                    <div class="faux-line w-80"></div>
                    <h4>
                        <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Trophy/3D/trophy_3d.png" class="icon-3d-sm" alt="Trophy"> 
                        Gamified Quizzes
                    </h4>
                    <div class="faux-line w-60"></div>
                    <div class="faux-line w-40"></div>
                </div>
            </div>
        </div>

        <!-- 2. STATS BAR -->
        <div class="stats-container">
            <div class="stat-box">
                <h3>1k+</h3>
                <p>Active Learners</p>
            </div>
            <div class="stat-box">
                <h3>20+</h3>
                <p>Accredited Courses</p>
            </div>
            <div class="stat-box">
                <h3>99%</h3>
                <p>Success Rate</p>
            </div>
            <div class="stat-box">
                <h3>50+</h3>
                <p>Expert Mentors</p>
            </div>
        </div>

        <!-- 3. MASSIVE FEATURES GRID -->
        <h2 class="section-heading">Why Choose Sisizathu?</h2>
        <div class="features-grid">
            <div class="glass-card">
                <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Laptop/3D/laptop_3d.png" class="glass-icon-3d" alt="Laptop">
                <h3>Learn Anywhere, Anytime</h3>
                <p>Our progressive web app adapts to any device. Study on the train via smartphone, or at your desk via laptop with zero loss of functionality.</p>
            </div>
            <div class="glass-card">
                <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Chart%20increasing/3D/chart_increasing_3d.png" class="glass-icon-3d" alt="Chart">
                <h3>Granular Progress Tracking</h3>
                <p>Visualize your success journey. Our advanced biometric dashboards show exactly which modules you need to conquer next.</p>
            </div>
            <div class="glass-card">
                <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Light%20bulb/3D/light_bulb_3d.png" class="glass-icon-3d" alt="Lightbulb">
                <h3>1-on-1 Expert Support</h3>
                <p>Never get stuck. Connect instantly with accredited facilitators through integrated forums, direct messaging, and live video sessions.</p>
            </div>
            <div class="glass-card">
                <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Video%20game/3D/video_game_3d.png" class="glass-icon-3d" alt="Gamepad">
                <h3>Gamified Experience</h3>
                <p>Earn badges, unlock achievements, and climb the leaderboard. We make retaining complex information addictive and fun.</p>
            </div>
            <div class="glass-card">
                <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Handshake/3D/handshake_3d.png" class="glass-icon-3d" alt="Handshake">
                <h3>Live Collaboration</h3>
                <p>Join virtual study groups. Share notes, participate in real-time whiteboard sessions, and build a network of fellow professionals.</p>
            </div>
            <div class="glass-card">
                <img src="https://raw.githubusercontent.com/microsoft/fluentui-emoji/main/assets/Graduation%20cap/3D/graduation_cap_3d.png" class="glass-icon-3d" alt="Graduation Cap">
                <h3>Verifiable Certificates</h3>
                <p>Graduate with blockchain-verified digital certificates that you can instantly attach to your LinkedIn profile and resume.</p>
            </div>
        </div>

        <!-- 4. TESTIMONIALS SECTION -->
        <h2 class="section-heading">Student Success Stories</h2>
        <div class="testimonials">
            <div class="review-card">
                <div class="stars">★★★★★</div>
                <p class="review-text">"The gamified modules completely changed how I study. I was able to finish my IT certification 3 weeks ahead of schedule because I couldn't put it down."</p>
                <div class="reviewer">
                    <div class="reviewer-img" style="background: url('https://api.dicebear.com/7.x/avataaars/svg?seed=Felix') center/cover;"></div>
                    <div class="reviewer-info">
                        <h4>Thabo M.</h4>
                        <p>Systems Administrator</p>
                    </div>
                </div>
            </div>
            <div class="review-card">
                <div class="stars">★★★★★</div>
                <p class="review-text">"As a working mother, the ability to download lectures and take quizzes offline on my phone was an absolute lifesaver. Highly recommended!"</p>
                <div class="reviewer">
                    <div class="reviewer-img" style="background: url('https://api.dicebear.com/7.x/avataaars/svg?seed=Nala') center/cover;"></div>
                    <div class="reviewer-info">
                        <h4>Sarah K.</h4>
                        <p>HR Manager</p>
                    </div>
                </div>
            </div>
            <div class="review-card">
                <div class="stars">★★★★★</div>
                <p class="review-text">"The direct access to expert facilitators meant I never stayed stuck on a difficult concept for long. Best corporate training investment we've made."</p>
                <div class="reviewer">
                    <div class="reviewer-img" style="background: url('https://api.dicebear.com/7.x/avataaars/svg?seed=Jack') center/cover;"></div>
                    <div class="reviewer-info">
                        <h4>David L.</h4>
                        <p>Operations Director</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. CALL TO ACTION BANNER -->
        <div class="cta-banner">
            <h2>Ready to Transform Your Future?</h2>
            <p>Join thousands of professionals upgrading their skillsets on Africa's premier learning platform.</p>
            <?php if (!isloggedin() || isguestuser()): ?>
                <a href="<?php echo $CFG->wwwroot; ?>/login/signup.php" class="sisi-btn">Create Free Account</a>
            <?php endif; ?>
        </div>

          <!-- ========================================================== -->
          <!-- 6. NATIVE MOODLE INTEGRATION (Dynamic Lists & Search)      -->
          <!-- ========================================================== -->
          <!-- ========================================================== -->
          <!-- 6. NATIVE MOODLE INTEGRATION (Dynamic Lists & Search)      -->
          <!-- ========================================================== -->
          <div class="sisi-moodle-dynamic-content" id="coursesearch">
              <h2 class="section-heading">Browse Our Course Catalog</h2>
              <style>
                  /* Force Moodle Drawers into Dark Mode */
                  .drawer, .drawer.bg-light, .drawer.bg-white { background-color: #11173b !important; color: #CBD5E1 !important; border-left: 1px solid rgba(255,255,255,0.1) !important; }
                  .drawerheader, .drawercontent { background-color: transparent !important; }
                  .drawer .list-group-item { background-color: transparent !important; border-color: rgba(255,255,255,0.05) !important; color: #CBD5E1 !important; }
                  .drawer .list-group-item:hover, .drawer .list-group-item.active { background-color: rgba(243, 112, 33, 0.2) !important; color: #ffffff !important; border-left: 3px solid #F37021 !important; }
                  .drawer .list-group-item a { color: #CBD5E1 !important; }
                  .drawer .list-group-item a:hover { color: #ffffff !important; text-decoration: none !important; }
                  .drawer .btn-close, .drawer .close { filter: invert(1) !important; }
                  .drawer .card, .drawer .block { background-color: rgba(255,255,255,0.05) !important; border: none !important; color: #fff !important; }
                  .drawer .card-title, .drawer h1, .drawer h2, .drawer h3, .drawer h4, .drawer h5 { color: #ffffff !important; }

                  /* Course Cards */
                  .course-card { padding: 0 !important; overflow: hidden; }
                  .course-img-wrapper { width: 100%; height: 220px; overflow: hidden; position: relative; }
                  .course-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
                  .course-card:hover .course-img-wrapper img { transform: scale(1.1); }
                  .course-tag { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; z-index: 2; border: 1px solid rgba(255,255,255,0.2); }
                  .course-price { position: absolute; top: 15px; right: 15px; background: var(--primary); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 800; font-size: 0.9rem; z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.3);}
                  .course-content { padding: 2rem; display: flex; flex-direction: column; flex-grow: 1; }
                  .course-meta { display: flex; gap: 15px; color: var(--gray); font-size: 0.85rem; margin-bottom: 1rem; font-weight: 500; }
                  .course-meta span { display: flex; align-items: center; gap: 5px; }
                  .course-btn { display: block; text-align: center; width: 100%; padding: 1rem; border-radius: 30px; background: rgba(255,255,255,0.05); color: white; text-decoration: none; font-weight: 700; transition: all 0.3s ease; margin-top: auto; border: 1px solid rgba(255,255,255,0.1); font-family: 'Poppins', sans-serif;}
                  .course-btn:hover { background: var(--primary); border-color: var(--primary); color: white; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(243, 112, 33, 0.4); }
              </style>

              <div class="features-grid">
                  <?php
                  global $DB, $CFG;
                  
                  // Fetch all visible courses except the site home (ID=1)
                  $allcourses = $DB->get_records_select('course', 'id != ? AND visible = 1', array(SITEID));
                  
                  $randomcourses = array();
                  if ($allcourses) {
                      // Pick up to 3 random keys safely
                      $limit = min(3, count($allcourses));
                      $keys = array_rand($allcourses, $limit);
                      
                      // If array_rand returns a single item, it doesn't wrap it in an array
                      if (!is_array($keys)) {
                          $keys = [$keys];
                      }
                      
                      foreach ($keys as $key) {
                          $randomcourses[] = $allcourses[$key];
                      }
                  }
                  
                  // Fallback images in case a course doesn't have an uploaded image
                  $fallbackimages = [
                      'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=800&q=80',
                      'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=800&q=80',
                      'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=800&q=80'
                  ];
                  $i = 0;

                  foreach ($randomcourses as $rcourse) {
                      $coursecontext = context_course::instance($rcourse->id);
                      $cat = $DB->get_record('course_categories', array('id' => $rcourse->category));
                      $categoryname = $cat ? format_string($cat->name) : 'General';
                      
                      // Attempt to fetch the actual uploaded course image
                      $courseobj = new core_course_list_element($rcourse);
                      $imageurl = $fallbackimages[$i % 3]; // default to a fallback
                      foreach ($courseobj->get_course_overviewfiles() as $file) {
                          if ($file->is_valid_image()) {
                              $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$file->is_external_file());
                              break;
                          }
                      }
                      
                      // Clean and truncate the course summary
                      $summary = format_text($rcourse->summary, $rcourse->summaryformat, array('context' => $coursecontext));
                      $summary = shorten_text(strip_tags($summary), 120);
                      if (empty(trim($summary))) {
                          $summary = "Enroll in this course to learn more and upgrade your skills today.";
                      }
                      
                      $courselink = new moodle_url('/course/view.php', array('id' => $rcourse->id));
                      
                      echo '<div class="glass-card course-card">';
                      echo '  <div class="course-img-wrapper">';
                      echo '      <span class="course-tag">' . $categoryname . '</span>';
                      echo '      <img src="' . $imageurl . '" alt="Course Image">';
                      echo '  </div>';
                      echo '  <div class="course-content">';
                      echo '      <div class="course-meta">';
                      echo '          <span>🎓 Accredited</span>';
                      echo '          <span>💻 Online</span>';
                      echo '      </div>';
                      echo '      <h3>' . format_string($rcourse->fullname) . '</h3>';
                      echo '      <p>' . $summary . '</p>';
                      echo '      <a href="' . $courselink . '" class="course-btn">View Course</a>';
                      echo '  </div>';
                      echo '</div>';
                      
                      $i++;
                  }
                  ?>
              </div>
              <div style="text-align: center; margin-bottom: 4rem;">
                  <a href="<?php echo $CFG->wwwroot; ?>/course/index.php" class="sisi-btn sisi-btn-outline">View All Courses -></a>
              </div>
              <?php
              // Edit Mode / Add a new course button for Admins
              if ($editing && has_capability('moodle/course:create', context_system::instance())) {
                  echo '<div class="sisi-moodle-edit-btn">';
                  echo $courserenderer->add_new_course_button();
                  echo '</div>';
              }
              ?>
          </div>

    </div> <!-- End .sisi-container -->
</div> <!-- End #sisi-modern-wrapper -->

<?php
// Output Moodle's footer
echo $OUTPUT->footer();