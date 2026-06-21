<?php

// This file is part of Moodle - http://moodle.org/

require_once("../config.php");
require_once($CFG->dirroot. '/course/lib.php');

$categoryid = optional_param('categoryid', 0, PARAM_INT); // Category id
$site = get_site();

if ($CFG->forcelogin) {
    require_login();
}

$heading = $site->fullname;
if ($categoryid) {
    $category = core_course_category::get($categoryid); // This will validate access.
    $PAGE->set_category_by_id($categoryid);
    $PAGE->set_url(new moodle_url('/course/index.php', array('categoryid' => $categoryid)));
    $PAGE->set_pagetype('course-index-category');
    $heading = $category->get_formatted_name();
} else if ($category = core_course_category::user_top()) {
    // Check if there is only one top-level category, if so use that.
    $categoryid = $category->id;
    $PAGE->set_url('/course/index.php');
    if ($category->is_uservisible() && $categoryid) {
        $PAGE->set_category_by_id($categoryid);
        $PAGE->set_context($category->get_context());
        if (!core_course_category::is_simple_site()) {
            $PAGE->set_url(new moodle_url('/course/index.php', array('categoryid' => $categoryid)));
            $heading = $category->get_formatted_name();
        }
    } else {
        $PAGE->set_context(context_system::instance());
    }
    $PAGE->set_pagetype('course-index-category');
} else {
    throw new moodle_exception('cannotviewcategory');
}

$PAGE->set_pagelayout('coursecategory');
$PAGE->set_primary_active_tab('home');
$PAGE->set_heading($heading);
$PAGE->set_secondary_active_tab('categorymain');

echo $OUTPUT->header();

// =====================================================================
// --- START OF SISIZATHU CUSTOM MODERN LMS CATALOG ---
// =====================================================================
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700;800;900&display=swap" rel="stylesheet">

<style>
  /* 1. OVERRIDE NATIVE MOODLE BG */
  html, body, #page.drawers, #page-wrapper, #page-content, #region-main-box, #region-main, .bg-white, .card, #page-footer {
      background-color: #18204D !important;
      background-image: none !important;
      border: none !important;
      box-shadow: none !important;
      margin: 0 !important;
      max-width: 100% !important; 
  }
  #page.drawers .main-inner { padding: 0 !important; margin: 0 !important; background: #18204D !important; }
  #page-header { display: none !important; }
  #page-footer { border-top: 1px solid rgba(255,255,255,0.05) !important; padding: 3rem 0 !important; color: #CBD5E1 !important;}
  #page-footer a { color: #F37021 !important; }

  /* 2. CUSTOM LMS DESIGN VARIABLES */
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
    left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; 
    background: linear-gradient(180deg, #18204D 0%, #11173b 50%, #18204D 100%);
    overflow: hidden;
    padding-bottom: 5rem;
  }

  .sisi-wave-bot { position: absolute; left: 0; width: 100%; z-index: 0; object-fit: cover; pointer-events: none; bottom: 0; height: 1200px; opacity: 0.6; }
  .sisi-container { position: relative; z-index: 10; max-width: 1400px; margin: 0 auto; padding: 0 5vw; }

  /* 3. CTA HEADER / BANNER */
  .cta-banner {
      background: linear-gradient(135deg, var(--primary), #9c3c08);
      border-radius: 40px; padding: 4rem 3rem; text-align: center; margin: 3rem 0 4rem 0;
      position: relative; overflow: hidden; box-shadow: 0 30px 60px rgba(243, 112, 33, 0.4);
  }
  .cta-banner::after {
      content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.15)" stroke-width="2" fill="none"/></svg>') repeat;
      background-size: 150px; opacity: 0.5; pointer-events: none;
  }
  .cta-banner h2 { font-family: 'Poppins', sans-serif; font-size: clamp(2rem, 4vw, 3rem); color: white; font-weight: 800; margin-bottom: 1rem; position: relative; z-index: 2;}
  .cta-banner p { font-size: clamp(1rem, 2vw, 1.15rem); color: rgba(255,255,255,0.9); position: relative; z-index: 2;}

  /* 4. BUTTONS & CATEGORY BADGES */
  .sisi-btn-outline {
      display: inline-flex; padding: 0.8rem 1.8rem; border-radius: 50px; font-family: 'Poppins', sans-serif;
      font-size: 0.95rem; font-weight: 600; text-decoration: none; transition: all 0.3s ease;
      background: rgba(255,255,255,0.05) !important; color: white !important;
      border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);
  }
  .sisi-btn-outline:hover, .sisi-btn-outline.active { background: var(--primary) !important; border-color: var(--primary); color: white !important; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(243, 112, 33, 0.3); }

  .subcategories-container { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; margin-bottom: 3rem; }

  /* 5. COURSES GRID */
  .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2.5rem; margin-bottom: 4rem; }
  .glass-card {
    background: var(--glass-bg-light); backdrop-filter: blur(25px); border: 1px solid var(--glass-border);
    border-radius: 30px; transition: all 0.4s ease; display: flex; flex-direction: column; overflow: hidden;
  }
  .glass-card:hover { transform: translateY(-10px); background: rgba(255, 255, 255, 0.08); border-color: rgba(243, 112, 33, 0.4); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4); }
  
  .course-img-wrapper { width: 100%; height: 220px; overflow: hidden; position: relative; }
  .course-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
  .glass-card:hover .course-img-wrapper img { transform: scale(1.1); }
  
  .course-tag { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); color: white; padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; z-index: 2; border: 1px solid rgba(255,255,255,0.2); }
  .course-content { padding: 2rem; display: flex; flex-direction: column; flex-grow: 1; }
  .course-meta { display: flex; gap: 15px; color: var(--gray); font-size: 0.85rem; margin-bottom: 1rem; font-weight: 500; }
  .course-content h3 { color: white; font-family: 'Poppins', sans-serif; font-size: 1.4rem; margin-bottom: 1rem; font-weight: 700; }
  .course-content p { font-size: 0.95rem; color: var(--gray); line-height: 1.6; margin-bottom: 2rem; flex-grow: 1; }
  
  .course-btn { display: block; text-align: center; width: 100%; padding: 1rem; border-radius: 30px; background: rgba(255,255,255,0.05); color: white; text-decoration: none; font-weight: 700; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.1); font-family: 'Poppins', sans-serif; margin-top: auto;}
  .course-btn:hover { background: var(--primary); border-color: var(--primary); color: white; box-shadow: 0 10px 20px rgba(243, 112, 33, 0.4); }
  
  .empty-state { text-align: center; padding: 4rem; background: rgba(255,255,255,0.02); border-radius: 20px; border: 1px dashed rgba(255,255,255,0.1); color: var(--gray); width: 100%; }
</style>

<div id="sisi-modern-wrapper">
    <!-- Bottom Wave Background -->
    <svg class="sisi-wave-bot" viewBox="0 0 1440 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="g-cyan" x1="0%" y1="100%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#00CFFD" /><stop offset="100%" stop-color="#4316DB" stop-opacity="0.3"/>
            </linearGradient>
            <linearGradient id="g-orange" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#F37021" /><stop offset="100%" stop-color="#D45D1A" />
            </linearGradient>
        </defs>
        <path d="M0,600 C300,400 600,800 1440,500 L1440,900 L0,900 Z" fill="url(#g-cyan)" opacity="0.2"/>
        <path d="M0,750 C400,900 800,600 1440,800 L1440,900 L0,900 Z" fill="url(#g-orange)" opacity="0.4"/>
    </svg>

    <div class="sisi-container">
        
        <!-- Header Banner -->
        <div class="cta-banner">
            <h2><?php echo $categoryid ? $heading : 'Our Complete Course Catalog'; ?></h2>
            <p>Explore our premium digital training modules designed to elevate your career to the next level.</p>
        </div>

        <?php
        // 1. Subcategory Navigation (Pills)
        $subcategories = $category->get_children();
        if (!empty($subcategories) || $categoryid) {
            echo '<div class="subcategories-container">';
            
            // "All Courses" Button
            $allactive = ($categoryid == 0) ? 'active' : '';
            echo '<a href="' . new moodle_url('/course/index.php') . '" class="sisi-btn-outline ' . $allactive . '">All Courses</a>';

            // List child categories
            foreach ($subcategories as $subcat) {
                if (!$subcat->is_uservisible()) { continue; }
                $caturl = new moodle_url('/course/index.php', array('categoryid' => $subcat->id));
                echo '<a href="'.$caturl.'" class="sisi-btn-outline">'.$subcat->get_formatted_name().'</a>';
            }
            echo '</div>';
        }

        // 2. Fetch and render Courses
        $courses = $category->get_courses(array('recursive' => true));
        
        if (empty($courses)) {
            echo '<div class="empty-state">';
            echo '<h3>Check Back Soon!</h3>';
            echo '<p>New courses are actively being developed for this category.</p>';
            echo '</div>';
        } else {
            echo '<div class="features-grid">';
            
            // Fallback imagery array
            $fallbackimages = [
                'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=800&q=80'
            ];
            $i = 0;

            foreach ($courses as $course) {
                // Determine course image
                $imageurl = $fallbackimages[$i % count($fallbackimages)];
                foreach ($course->get_course_overviewfiles() as $file) {
                    if ($file->is_valid_image()) {
                        $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$file->is_external_file());
                        break;
                    }
                }

                // Clean Summary
                $summary = format_text($course->summary, $course->summaryformat, array('context' => context_course::instance($course->id)));
                $summary = shorten_text(strip_tags($summary), 140);
                if (empty(trim($summary))) {
                    $summary = "Enroll in this course today to gain instant access to premium learning materials and expert facilitation.";
                }

                $courselink = new moodle_url('/course/view.php', array('id' => $course->id));

                echo '<div class="glass-card">';
                echo '  <div class="course-img-wrapper">';
                echo '      <span class="course-tag">' . $category->get_formatted_name() . '</span>';
                echo '      <img src="' . $imageurl . '" alt="Course Image">';
                echo '  </div>';
                echo '  <div class="course-content">';
                echo '      <div class="course-meta">';
                echo '          <span>🎓 Accredited</span>';
                echo '          <span>💻 Online</span>';
                echo '      </div>';
                echo '      <h3>' . format_string($course->fullname) . '</h3>';
                echo '      <p>' . $summary . '</p>';
                echo '      <a href="' . $courselink . '" class="course-btn">Explore Details</a>';
                echo '  </div>';
                echo '</div>';
                
                $i++;
            }
            echo '</div>'; // End Grid
        }
        ?>
    </div>
</div>

<?php
echo $OUTPUT->footer();