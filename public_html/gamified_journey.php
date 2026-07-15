<?php
// public_html/gamified_journey.php
require_once('config.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/gamified_journey.php');
$PAGE->set_title('Gamified Learning Hub');
$PAGE->set_heading('Interactive Certification Path');
$PAGE->set_pagelayout('standard'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_progress'])) {
    require_login();
    set_user_preference('sisi_journey_progress', trim($_POST['save_progress']));
    die(json_encode(['status' => 'success']));
}
$journey_data_json = get_config('local_sisizathu', 'journey_data') ?: '[]';
$journey_data = json_decode($journey_data_json, true);
$strict_progression = get_config('local_sisizathu', 'journey_strict_progression') ?: 0;
$user_progress_json = isloggedin() && !isguestuser() ? get_user_preferences('sisi_journey_progress', '{}') : '{}';

// Calculate Maps per course to rank them
$map_counts = [];
$latest_maps_per_course = [];
if (is_array($journey_data)) {
    foreach ($journey_data as $map) {
        $cid = $map['course_id'];
        if (!isset($map_counts[$cid])) $map_counts[$cid] = 0;
        $map_counts[$cid]++;
        $latest_maps_per_course[$cid] = $map['id']; // Tracks the highest/latest ID for this course
    }
}

global $DB;
$courses = $DB->get_records_select('course', 'id != ?', array(SITEID), 'fullname ASC', 'id, fullname');
$course_list = [];
foreach ($courses as $c) {
    $course_list[] = [
        'id' => $c->id, 
        'name' => format_string($c->fullname),
        'map_count' => isset($map_counts[$c->id]) ? $map_counts[$c->id] : 0
    ];
}

// Custom sort: Courses with maps first, ordered by latest map added, then alpha
usort($course_list, function($a, $b) use ($map_counts, $latest_maps_per_course) {
    $c1 = isset($map_counts[$a['id']]) ? $map_counts[$a['id']] : 0;
    $c2 = isset($map_counts[$b['id']]) ? $map_counts[$b['id']] : 0;
    if ($c1 > 0 && $c2 == 0) return -1;
    if ($c1 == 0 && $c2 > 0) return 1;
    if ($c1 > 0 && $c2 > 0) {
        return $latest_maps_per_course[$b['id']] <=> $latest_maps_per_course[$a['id']];
    }
    return strcmp($a['name'], $b['name']);
});

echo $OUTPUT->header();
?>

<!-- SwiftUI Filter logic for Blobs -->
<svg style="width:0; height:0; position:absolute;" aria-hidden="true" focusable="false">
  <defs>
    <filter id="swiftui-goo"><feGaussianBlur in="SourceGraphic" stdDeviation="12" result="blur" /><feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 20 -8" result="goo" /><feBlend in="SourceGraphic" in2="goo" /></filter>
    <filter id="fab-goo"><feGaussianBlur in="SourceGraphic" stdDeviation="8" result="blur" /><feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo" /><feBlend in="SourceGraphic" in2="goo" /></filter>
    <mask id="swiftui-canvas-mask">
      <g filter="url(#swiftui-goo)">
        <rect id="mask-main-rect" x="16" y="8" width="368" height="275" rx="40" fill="white" />
        <circle id="mask-tail-1" cx="85" cy="275" r="22" fill="white" />
        <circle id="mask-tail-2" cx="200" cy="275" r="22" fill="white" />
        <circle id="mask-tail-3" cx="315" cy="275" r="22" fill="white" />
        <circle id="mask-close-blob" cx="200" cy="275" r="30" fill="white" />
      </g>
    </mask>
  </defs>
</svg>

<style>
    /* MASTER LAYOUTS */
    .master-view { display: none; width: 100%; animation: fadeIn 0.4s ease; }
    .master-view.active { display: flex; flex-direction: column; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

   /* VIEW 1: COURSE HUB */
    #sisi-course-hub { padding: 20px; max-width: 1200px; margin: 0 auto; display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; align-content: start; box-sizing: border-box; width: 100%; }
    .course-hub-card { background: rgba(15,15,25,0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 25px; cursor: pointer; transition: all 0.3s ease; display: flex; flex-direction: column; justify-content: space-between; min-height: 180px; box-shadow: 0 15px 30px rgba(0,0,0,0.4); }
    .course-hub-card:hover { transform: translateY(-6px); background: rgba(243, 112, 33, 0.15); border-color: #F37021; box-shadow: 0 10px 25px rgba(243, 112, 33, 0.3); }
    .course-hub-card h3 { font-size: 1.3rem; margin: 0 0 10px 0; color: #fff; line-height: 1.4; }
    .course-hub-card .map-count { font-size: 0.85rem; color: #CBD5E1; background: rgba(0,0,0,0.4); padding: 6px 12px; border-radius: 12px; width: fit-content; font-weight: 600; }
    /* Task 6: Skeleton Loader */
    .skeleton-card { background: linear-gradient(90deg, rgba(255,255,255,0.05) 25%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.05) 75%); background-size: 400% 100%; animation: shimmer 1.5s infinite; border: 1px solid rgba(255,255,255,0.05); border-radius: 18px; min-height: 180px; }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    /* VIEW 2: CATEGORY BLOBS */
    #kk-app { width: 400px; height: 800px; max-width: 100%; margin: 2rem auto; position: relative; overflow: hidden; border-radius: 40px; box-shadow: 0 30px 60px rgba(0,0,0,0.6); font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif; background-color: #111; user-select: none; }
    #blob-ui-view.active { display: flex; justify-content: center; }
    #kk-app { margin: 0; } /* frame now handles spacing/centering */
    
   /* Custom Modal Styles */
    .sisi-modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(10px);
        z-index: 2147483647; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;
    }
    .sisi-modal-overlay.show { display: flex; opacity: 1; }
    .sisi-modal-content {
        background: rgba(20, 20, 30, 0.95); border: 1px solid rgba(255,255,255,0.15);
        border-radius: 24px; padding: 30px; max-width: 400px; width: 90%; text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5); transform: translateY(20px); transition: transform 0.3s;
    }
    .sisi-modal-overlay.show .sisi-modal-content { transform: translateY(0); }
    .sisi-modal-content h3 { margin: 0 0 15px 0; color: #fff; font-size: 1.5rem; }
    .sisi-modal-content p { color: #CBD5E1; margin: 0 0 25px 0; font-size: 1.05rem; line-height: 1.4; }
    .sisi-modal-actions { display: flex; gap: 15px; justify-content: center; }
    .sisi-modal-actions button { flex: 1; padding: 12px; border-radius: 12px; font-weight: bold; border: none; cursor: pointer; transition: 0.2s; color: white; }
    .sisi-modal-actions button:hover { filter: brightness(1.1); transform: translateY(-2px); }

    /* Custom Toast for Idle Timer */
    #sisi-toast-container { position: fixed; top: 80px; right: 20px; z-index: 2147483647; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
    .sisi-toast { background: rgba(0, 207, 253, 0.95); backdrop-filter: blur(10px); color: #000; padding: 16px 24px; border-radius: 12px; font-weight: 600; font-size: 0.95rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); transform: translateX(120%); opacity: 0; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); display: flex; align-items: center; gap: 12px; pointer-events: auto; max-width: 350px; line-height: 1.4; }
    .sisi-toast.show { transform: translateX(0); opacity: 1; }

.kk-device-frame, #kk-device-frame {
    margin: 2rem auto;
    padding: 14px;
    background: linear-gradient(145deg, #3a3a3e, #0c0c0e);
    border-radius: 54px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.7), inset 0 1px 1px rgba(255,255,255,0.12);
    position: relative;
}
.kk-device-frame.wide-mode, #kk-device-frame.wide-mode { border-radius: 34px; padding: 18px; }
.kk-device-frame.wide-mode::after, #kk-device-frame.wide-mode::after {
    content: '';
    position: absolute;
    top: 9px; left: 50%; transform: translateX(-50%);
    width: 6px; height: 6px; border-radius: 50%;
    background: #333;
    z-index: 6;
}
.kk-device-frame.quiz-frame { border-radius: 40px; padding: 16px; }
#kk-app.wide-mode { border-radius: 24px; }

/* Physical side buttons */
.kk-frame-btn {
    position: absolute;
    background: linear-gradient(90deg, #1a1a1c, #2e2e32);
    border-radius: 3px;
    box-shadow: inset 0 1px 1px rgba(255,255,255,0.15), 0 1px 2px rgba(0,0,0,0.5);
    z-index: 1;
}
.kk-btn-power { right: -3px; top: 130px; width: 4px; height: 70px; }
.kk-btn-vol-up { left: -3px; top: 110px; width: 4px; height: 45px; }
.kk-btn-vol-down { left: -3px; top: 165px; width: 4px; height: 45px; }
.kk-device-frame.wide-mode .kk-frame-btn { opacity: 0; pointer-events: none; }

/* Dynamic Island — Task 1: Spring Physics & Task 7: Morphing */
    .kk-dynamic-island {
        position: absolute; top: 14px; left: 50%; transform: translateX(-50%) scale(1);
        min-width: 100px; max-width: 78%; height: 30px; background: #000; border-radius: 18px;
        display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0 16px;
        z-index: 6; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
        transition: background 0.4s ease, box-shadow 0.4s ease, width 0.4s ease; overflow: hidden; white-space: nowrap;
    }
    .kk-dynamic-island.spring { animation: islandSpring 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
    @keyframes islandSpring { 
        0% {transform: translateX(-50%) scale(1);} 
        30% {transform: translateX(-50%) scaleX(1.1) scaleY(0.85);} 
        60% {transform: translateX(-50%) scaleX(0.95) scaleY(1.05);} 
        100% {transform: translateX(-50%) scale(1);} 
    }
    .kk-dynamic-island .di-dot { width: 6px; height: 6px; border-radius: 50%; background: #555; flex-shrink: 0; transition: background 0.3s; }
    .kk-dynamic-island .di-text { color: #ccc; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.3px; display:flex; align-items:center; gap:5px; }
    
    .kk-dynamic-island.state-correct { background: #0d2818; box-shadow: 0 0 0 1px #25d366, 0 0 12px rgba(37,211,102,0.5); }
    .kk-dynamic-island.state-correct .di-dot { background: #25d366; }
    .kk-dynamic-island.state-correct .di-text { color: #6ee7a8; }
    .kk-dynamic-island.state-wrong { background: #2c0d0d; box-shadow: 0 0 0 1px #ff4444, 0 0 12px rgba(255,68,68,0.5); }
    .kk-dynamic-island.state-wrong .di-dot { background: #ff4444; }
    .kk-dynamic-island.state-wrong .di-text { color: #ff8080; }
    .kk-dynamic-island.state-streak { background: #2c1a05; box-shadow: 0 0 0 1px #ffb347, 0 0 15px rgba(255,179,71,0.6); }
    .kk-dynamic-island.state-streak .di-dot { background: #ffb347; animation: diPulse 0.5s infinite; }
    .kk-dynamic-island.state-streak .di-text { color: #ffcb80; text-shadow: 0 0 5px rgba(255,203,128,0.5); }
    .kk-dynamic-island.state-warning { background: #2c1a05; box-shadow: 0 0 0 1px #FF9500; }
    .kk-dynamic-island.state-warning .di-dot { background: #FF9500; }
    .kk-dynamic-island.state-warning .di-text { color: #FF9500; }
    .kk-dynamic-island.state-info { background: #001a2c; box-shadow: 0 0 0 1px #00CFFD; }
    .kk-dynamic-island.state-info .di-dot { background: #00CFFD; }
    .kk-dynamic-island.state-info .di-text { color: #7fe2ff; }
    @keyframes diPulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.3; transform: scale(1.5); } }
    #kk-canvas { position: absolute; top: 0; left: 0; width: 400px; height: 800px; transform-origin: top left; }
    .kk-bg-wrapper { position: absolute; inset: 0; z-index: 1; }
    .kk-orb { position: absolute; border-radius: 50%; filter: blur(40px); opacity: 0.6; mix-blend-mode: screen; animation: orbFloat 8s infinite alternate ease-in-out; }
    .orb-1 { width: 300px; height: 300px; background: #673d02; bottom: -50px; left: -100px; }
    .orb-2 { width: 250px; height: 250px; background: #030746; top: 30%; right: -50px; animation-delay: -3s; }
    .orb-3 { width: 200px; height: 200px; background: #05344b; bottom: 20%; left: 40%; animation-delay: -5s; }
    @keyframes orbFloat { 0% { transform: translate(0,0); } 100% { transform: translate(30px, -40px); } }
    .kk-masked-content { position: absolute; inset: 0; z-index: 10; mask: url(#swiftui-canvas-mask); -webkit-mask: url(#swiftui-canvas-mask); }
    .kk-grad-base { position: absolute; inset: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #007AFF, #AF52DE, #FF2D55); transition: 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .kk-grad-overlay { position: absolute; bottom: 0; left: 0; width: 100%; height: 0%; background: linear-gradient(180deg, #FFD60A, #34C759, #00CFFD); transition: height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .kk-grad-overlay.cat-1 { background: linear-gradient(180deg, #7C2D12, #431407, #1C0A02); }
    .kk-grad-overlay.cat-2 { background: linear-gradient(180deg, #4C1D95, #2E1065, #130625); }
    .kk-grad-overlay.cat-3 { background: linear-gradient(180deg, #14532D, #052E16, #01150A); }
    .kk-grad-base.cat-1 { background: linear-gradient(135deg, #431407, #7C2D12, #9A3412); }
.kk-grad-base.cat-2 { background: linear-gradient(135deg, #2E1065, #4C1D95, #6D28D9); }
.kk-grad-base.cat-3 { background: linear-gradient(135deg, #052E16, #14532D, #15803D); }
    #kk-app.show-activities .kk-grad-overlay { height: 60%; }
    .kk-content-layer { position: absolute; inset: 0; z-index: 20; pointer-events: none; }
    
    .kk-back-btn { position:absolute; top:25px; left:25px; pointer-events:auto; color:white; background:rgba(0,0,0,0.3); padding:8px 14px; border-radius:12px; font-weight:bold; cursor:pointer; backdrop-filter:blur(10px); z-index:30;}
    .kk-info-text { position: absolute; top: 70px; width: 100%; text-align: center; color: white; padding: 0 30px; box-sizing: border-box; transition: all 0.4s ease; pointer-events: auto; }
    #kk-app.show-activities .kk-info-text { opacity: 0; transform: translateY(-30px); pointer-events: none; }
    .kk-icon-ring { width: 40px; height: 40px; margin: 0 auto 15px auto; border-radius: 50%; border: 2px solid white; position: relative; }
    .kk-icon-ring::after { content: ''; position: absolute; inset: 6px; border-radius: 50%; border: 1px solid white; }
    .kk-info-text h2 { font-size: 1.8rem; font-weight: 800; line-height: 1.1; margin-bottom: 15px; color: #fff;}
    .kk-info-text p { font-size: 0.9rem; line-height: 1.4; opacity: 0.9; }

    .kk-sel-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s; pointer-events: none; z-index: 20; }
    #kk-app.show-activities .kk-sel-container { transform: scale(0); opacity: 0; }
    .sel-btn { position: absolute; width: 44px; height: 44px; border-radius: 50%; cursor: pointer; pointer-events: auto; display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); border: 3px solid transparent; }
    .sel-btn.c1 { background: #460703; left: calc(85px - 22px); top: calc(275px - 22px); }
    .sel-btn.c2 { background: #02082e; left: calc(200px - 22px); top: calc(275px - 22px); }
    .sel-btn.c3 { background: #032e0e; left: calc(315px - 22px); top: calc(275px - 22px); }
    .sel-btn:not(.active) { opacity: 0.7; transform: scale(0.85) translateY(12px); }
    .sel-btn:active { transform: scale(0.9); }

    .kk-activities-list { position: absolute; top: 200px; left: 16px; width: 368px; padding: 20px; box-sizing: border-box; transform: translateY(40px); opacity: 0; pointer-events: none; transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    #kk-app.show-activities .kk-activities-list { top: 10px; transform: translateY(0); opacity: 1; pointer-events: auto; transition-delay: 0.1s;}
    .kk-activities-list h1 { color: #fff; font-weight: 900; font-size: 2rem; margin: 0 0 20px 0; padding-top: 12px; text-align: center; text-shadow: 0 2px 10px rgba(0,0,0,0.3);}
    .kk-close-x { position: absolute; top: 585px; left: 50%; transform: translateX(-50%); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: bold; cursor: pointer; pointer-events: none; opacity: 0; transition: opacity 0.3s; }
    #kk-app.show-activities .kk-close-x { pointer-events: auto; opacity: 1; transition-delay: 0.4s;}

    .cat-scrollview { max-height: 250px; overflow-y: auto; padding-right: 10px; margin-bottom: 20px; transition: max-height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    #kk-app.show-activities .cat-scrollview { max-height: 440px; }
    .cat-scrollview::-webkit-scrollbar { width: 6px; }
    .cat-scrollview::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); border-radius: 10px; }
    .cat-scrollview::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); }
    .cat-scrollview::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
    .map-row { background: rgba(15, 15, 25, 0.6); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px; padding: 15px; display: flex; align-items: center; gap: 15px; margin-bottom: 15px; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 25px rgba(0,0,0,0.4); }
    .map-row:active { transform: scale(0.95); }
    .map-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); }
    .map-text h4 { margin: 0; font-weight: 800; color: #fff; font-size: 1.1rem; }
    .map-text p { margin: 0; color: #CBD5E1; font-size: 0.85rem; font-weight: 600; }
    .kk-app.wide-mode .cat-scrollview, #kk-app.wide-mode .cat-scrollview { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; max-height: 160px; padding-bottom: 10px; }
    #kk-app.wide-mode.show-activities .cat-scrollview { max-height: 310px; }

    /* VIEW 3: GAME MAP & QUIZ LAYER */
    #sisi-game-container {
        width: 100%; max-width: 850px; margin: 2rem auto; height: calc(100vh - 140px); min-height: 600px;
        background: rgba(15, 15, 25, 0.75); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); overflow: hidden;
        color: #F8FAFC !important; font-family: 'Poppins', sans-serif; position: relative; transition: background 0.5s;
    }
    #sisi-game-container.cat-bg-1 { background: linear-gradient(135deg, rgba(67, 20, 7, 0.9), rgba(15, 15, 25, 0.95)); }
    #sisi-game-container.cat-bg-2 { background: linear-gradient(135deg, rgba(46, 16, 101, 0.9), rgba(15, 15, 25, 0.95)); }
    #sisi-game-container.cat-bg-3 { background: linear-gradient(135deg, rgba(5, 46, 22, 0.9), rgba(15, 15, 25, 0.95)); }
    .game-header { padding: 18px 25px; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); font-size: 1.3rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; color: #fff; z-index: 20; }
    .header-btn { background: rgba(255,255,255,0.1); border: none; color: white; padding: 8px 16px; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.3s; }
    .header-btn:hover { background: #F37021; transform: scale(1.05); }
    .stats-pill { display: flex; gap: 15px; font-size: 0.95rem; font-weight: 700; }
    .stat-badge { background: rgba(255,255,255,0.08); padding: 5px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 6px; }
    .stat-badge.fire { color: #ffb347; border-color: rgba(255, 179, 71, 0.3); }
    .stat-badge.xp { color: #00CFFD; border-color: rgba(0, 207, 253, 0.3); }

    /* Map Overlay */
    #sisi-map-view { flex-grow: 1; position: relative; padding: 20px 0 80px 0; display: flex; flex-direction: column; align-items: center; overflow-x: hidden; overflow-y: auto; height: 100%; perspective: 1200px; perspective-origin: 50% 10%; }
    #sisi-map-3d-plane { width: 100%; position: relative; display: flex; flex-direction: column; gap: 30px; transform-style: preserve-3d; transform: rotateX(-20deg) translateY(20px); transform-origin: top center; padding-bottom: 80px; }
    #sisi-map-view::-webkit-scrollbar { width: 8px; }
    #sisi-map-view::-webkit-scrollbar-track { background: rgba(0,0,0,0.15); border-radius: 10px; margin: 10px 0; }
    #sisi-map-view::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); }
    #sisi-map-view::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
    #path-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; }
    /* Task 3: Animated Path Flow */
    .game-path-line { fill: none; stroke: rgba(255,255,255,0.15); stroke-width: 6; stroke-linecap: round; stroke-dasharray: 2 18; }
    .game-path-line.active { stroke: #25d366; animation: pathFlow 0.8s linear infinite; filter: drop-shadow(0 0 5px #25d366); }
    @keyframes pathFlow { to { stroke-dashoffset: -20; } }
    .level-wrapper { position: relative; display: flex; justify-content: center; align-items: center; z-index: 10; width: 100%; flex-shrink: 0; }
    .level-node { width: 75px; height: 75px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; cursor: pointer; transition: 0.3s; position: relative; box-shadow: inset 0 0 10px rgba(0,0,0,0.5); }
    .level-node.locked { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.3); border: 3px solid rgba(255,255,255,0.1); }
    .level-node.current { background: #25d366; color: white; box-shadow: 0 0 30px rgba(37, 211, 102, 0.5); transform: scale(1.15); border: 4px solid #fff; }
    .level-node.completed { background: #F37021; color: white; border: 4px solid #fff; }

    /* FAB MENU (Kids Kingdom imported) */
    .kk-fab-container { position: absolute; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 150; }
    .fab-goo-layer { position: absolute; right: -50px; bottom: -50px; width: 250px; height: 250px; filter: url(#fab-goo); pointer-events: none; }
    .fab-blob { position: absolute; bottom: 50px; right: 50px; width: 60px; height: 60px; border-radius: 50%; background: rgba(0,0,0,0.85); z-index: 151; transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); border: 1px solid rgba(255,255,255,0.1); }
    .fab-icon { position: absolute; bottom: 0; right: 0; width: 60px; height: 60px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; z-index: 152; cursor: pointer; pointer-events: none; opacity: 0; transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .kk-fab-container.open .fab-blob-1, .kk-fab-container.open .fab-icon.i1 { transform: translate(-10px, -100px); opacity: 1; pointer-events: auto; } 
    .kk-fab-container.open .fab-blob-2, .kk-fab-container.open .fab-icon.i2 { transform: translate(-100px, -10px); opacity: 1; pointer-events: auto; } 
    .kk-fab-container.open .fab-blob-3, .kk-fab-container.open .fab-icon.i3 { transform: translate(-80px, -80px); opacity: 1; pointer-events: auto; } 
    .fab-main { position: absolute; bottom: 0; right: 0; width: 60px; height: 60px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 300; cursor: pointer; transition: 0.4s; z-index: 155; background: #000; box-shadow: 0 10px 20px rgba(0,0,0,0.5); pointer-events: auto; border: 1px solid rgba(255,255,255,0.2); }
    .kk-fab-container.open .fab-main { transform: rotate(45deg); background: #333; }
    #kk-app.show-activities .kk-fab-container { opacity: 0; pointer-events: none; }

   /* Quiz Overlay & Results View */
    #sisi-quiz-view { padding: 30px 20px; display: none; flex-direction: column; flex-grow: 1; height: 100%; overflow-y: auto; }
    #sisi-quiz-view::-webkit-scrollbar { width: 6px; }
    #sisi-quiz-view::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); border-radius: 10px; margin: 10px 0; }
    #sisi-quiz-view::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); }
    #sisi-quiz-view::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
    .question-box { font-size: 1.3rem; text-align: center; margin: 0 0 20px 0; font-weight: 600; color: #fff; line-height:1.4; flex-shrink: 0; }
    .options-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 40px; } /* flex-grow removed, arbitrary padding added */
    .option-btn { background: rgba(255,255,255,0.06); border: 2px solid rgba(255,255,255,0.12); padding: 16px; border-radius: 14px; color: #fff; font-size: 1.05rem; cursor: pointer; transition: 0.2s; text-align: center; font-weight: 500; }
    .option-btn:hover:not(.disabled) { background: rgba(243, 112, 33, 0.25); border-color: #F37021; transform: scale(1.02); }
    .option-btn.correct { background: #25d366 !important; border-color: #25d366 !important; box-shadow: 0 0 20px rgba(37, 211, 102, 0.5); }
    .option-btn.wrong { background: #ff4444 !important; border-color: #ff4444 !important; animation: shake 0.4s; }
    .option-btn.disabled { pointer-events: none; opacity: 0.6; }

    .quiz-footer { display: flex; justify-content: space-between; padding-top: 20px; padding-bottom: 30px; border-top: 1px solid rgba(255,255,255,0.1); }
    .qz-quit-btn { background: rgba(255, 59, 48, 0.2); border: 1px solid #FF3B30; color: #fff; padding: 12px 20px; border-radius: 12px; cursor: pointer; font-weight: bold; transition: 0.2s; }
    .qz-quit-btn:hover { background: #FF3B30; }
    .skip-btn { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px 20px; border-radius: 12px; cursor: pointer; font-weight: 700; transition: 0.3s; }
    .skip-btn:hover { background: #00CFFD; color: #000; }
    
    #sisi-results-view { display: none; flex-direction: column; justify-content: center; align-items: center; height: 100%; text-align: center; padding: 30px; animation: fadeIn 0.4s ease; z-index: 100; position: absolute; inset: 0; border-radius: inherit; }
    .res-icon { font-size: 5rem; margin-bottom: 15px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5)); }
    .res-title { font-size: 2rem; font-weight: 900; color: white; margin-bottom: 10px; }
    .res-desc { font-size: 1.1rem; color: #CBD5E1; margin-bottom: 30px; line-height: 1.4; }
    .res-btn { background: #F37021; color: white; padding: 14px 30px; border-radius: 14px; font-size: 1.1rem; font-weight: bold; cursor: pointer; border: none; box-shadow: 0 10px 20px rgba(243, 112, 33, 0.4); transition: 0.3s; }
    .res-btn:hover { transform: translateY(-3px); }

    .floating-xp { position: absolute; font-weight: 800; font-size: 1.5rem; color: #25d366; pointer-events: none; animation: floatUp 1s ease forwards; z-index: 200; }
    @keyframes floatUp { 0% { opacity: 1; transform: translateY(0) scale(1); } 100% { opacity: 0; transform: translateY(-60px) scale(1.3); } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } }

    /* Map Complete & Regal Gold Styles */
    .game-path-line.gold { stroke: #FFD700; filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.8)); stroke-dasharray: 1000; stroke-dashoffset: 1000; }
    @keyframes traceGold { to { stroke-dashoffset: 0; } }
    .level-node.gold-node { background: linear-gradient(135deg, #FFD700, #F39C12) !important; color: #000 !important; border: 4px solid #FFF !important; box-shadow: 0 0 20px rgba(255, 215, 0, 0.8) !important; transform: scale(1.15) !important; }
    
    #sisi-map-complete-view { display: none; position: absolute; inset: 0; background: rgba(15,15,25,0.95); z-index: 300; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 30px; animation: fadeIn 0.5s ease; border-radius: inherit; backdrop-filter: blur(10px); }
    .map-comp-icon { font-size: 6rem; animation: pulse 2s infinite; margin-bottom: 20px; filter: drop-shadow(0 0 20px rgba(255,215,0,0.5)); }
    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }

    /* Parallax Floating Particles */
    #map-parallax-bg .kk-particle { position: absolute; background: #fff; border-radius: 50%; opacity: 0.6; pointer-events: none; filter: blur(1px); }
    .p-1 { width: 10px; height: 10px; top: 15%; left: 10%; box-shadow: 0 0 10px #fff; }
    .p-2 { width: 14px; height: 14px; top: 45%; right: 12%; box-shadow: 0 0 10px #fff; }
    .p-3 { width: 8px; height: 8px; bottom: 30%; left: 60%; box-shadow: 0 0 10px #fff; }
    /* (rebuilt): parallax background — a fixed non-scrolling layer, positioned
       purely via JS from scrollTop, instead of scrolling natively with the content */
    #map-parallax-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
    #map-parallax-bg .kk-orb { position: absolute; mix-blend-mode: normal; opacity: 0.35; animation: none; }
    #map-parallax-bg .orb-1 { width: 400px; height: 400px; background: #F37021; top: -100px; left: -100px; }
    #map-parallax-bg .orb-2 { width: 250px; height: 250px; background: #00CFFD; top: 50%; right: -50px; }
    #map-parallax-bg .orb-3 { width: 300px; height: 300px; background: #25d366; bottom: -100px; left: 20%; }
    #sisi-map-view { z-index: 1; }
    /* ============ MOBILE RESPONSIVENESS ============ */
@media (max-width: 768px) {
    #sisi-course-hub { padding: 12px; gap: 14px; grid-template-columns: 1fr; }
    .course-hub-card { padding: 18px; min-height: 140px; }
    .course-hub-card h3 { font-size: 1.1rem; }

    #sisi-game-container { max-width: 100%; height: calc(100vh - 100px); min-height: 480px; border-radius: 16px; margin: 0.5rem auto; }
    .game-header { flex-wrap: wrap; gap: 10px; padding: 14px 16px; font-size: 1.05rem; }
    #game-title { font-size: 0.95rem; order: 3; width: 100%; text-align: center; }
    .stats-pill { gap: 8px; }
    .stat-badge { font-size: 0.85rem; padding: 4px 10px; }

    .level-node { width: 60px; height: 60px; font-size: 1.4rem; }
    .question-box { font-size: 1.15rem; margin: 12px 0; }
    .option-btn { padding: 14px; font-size: 1rem; }
    #sisi-quiz-view { padding: 18px; }

    .kk-fab-container { bottom: 16px; right: 16px; }
}

@media (max-width: 430px) {
    .course-hub-card { padding: 16px; }
    .stat-badge.xp, .stat-badge.fire { font-size: 0.8rem; }
}
</style>

<!-- VIEW 1: COURSE HUB -->
<div id="course-hub-view" class="master-view active">
    <div style="text-align:center; padding:20px; color:#fff;">
        <h2 style="font-size:2.2rem; font-weight:900;">Learning Path Hub</h2>
        <p style="color:#CBD5E1;">Select a course to view its available gamified maps.</p>
        <div style="display:flex; justify-content:center; flex-wrap:wrap; gap:15px; margin-top:15px;">
            <button onclick="window.location.href='gamified_leaderboard.php'" style="background:rgba(255,215,0,0.15); border:1px solid #FFD700; color:#FFD700; padding:10px 20px; border-radius:12px; cursor:pointer; font-weight:bold; transition:0.3s; box-shadow:0 5px 15px rgba(255,215,0,0.15);" onmouseover="this.style.background='#FFD700'; this.style.color='#000';" onmouseout="this.style.background='rgba(255,215,0,0.15)'; this.style.color='#FFD700';">🏆 View Global Leaderboard</button>
            <button onclick="resetAllProgress()" style="background:rgba(255,59,48,0.15); border:1px solid #FF3B30; color:#fff; padding:10px 20px; border-radius:12px; cursor:pointer; font-weight:bold; transition:0.3s;" onmouseover="this.style.background='#FF3B30'" onmouseout="this.style.background='rgba(255,59,48,0.15)'">↻ Reset Progress</button>
        </div>
    </div>
    <div id="sisi-course-hub"></div>
</div>

<!-- VIEW 2: BLOBS MENU -->
<div id="blob-ui-view" class="master-view">
    <div id="kk-device-frame">
        <div class="kk-dynamic-island" id="dyn-island-blob"><span class="di-dot"></span><span class="di-text">Kids Kingdom</span></div>
        <div class="kk-frame-btn kk-btn-power"></div>
        <div class="kk-frame-btn kk-btn-vol-up"></div>
        <div class="kk-frame-btn kk-btn-vol-down"></div>
        <div id="kk-app">
            <div id="kk-canvas">
                <div class="kk-bg-wrapper"><div class="kk-orb orb-1"></div><div class="kk-orb orb-2"></div><div class="kk-orb orb-3"></div></div>
                <div class="kk-masked-content"><div class="kk-grad-base"></div><div class="kk-grad-overlay"></div></div>
                <div class="kk-content-layer">
                    <div class="kk-back-btn" onclick="showCourseHub()">❮ Courses</div>
                    <div class="kk-info-text" id="info-text">
                        <div class="kk-icon-ring"></div>
                        <h2 id="info-title">Categories</h2>
                        <p id="info-desc">Select a category below to explore gamified maps.</p>
                    </div>
                    <div class="kk-sel-container">
                        <div class="sel-btn c1 active" id="sel-1" onclick="handleCatSelection(1)"></div>
                        <div class="sel-btn c2" id="sel-2" onclick="handleCatSelection(2)"></div>
                        <div class="sel-btn c3" id="sel-3" onclick="handleCatSelection(3)"></div>
                    </div>
                    <div class="kk-activities-list">
                        <h1 id="cat-title">Maps</h1>
                        <div class="cat-scrollview" id="map-list-container"></div>
                    </div>
                    <div class="kk-close-x" onclick="toggleActivities(false)">✕</div>

                    <!-- FAB Menu imported from Kids Kingdom -->
                    <div class="kk-fab-container" id="fab-menu">
                        <div class="fab-goo-layer"><div class="fab-blob fab-base"></div><div class="fab-blob fab-blob-1"></div><div class="fab-blob fab-blob-2"></div><div class="fab-blob fab-blob-3"></div></div>
                        <div class="fab-icon i1" onclick="alert('Verse of the Day!\nLet the little children come to me. - Matt 19:14')">❤️</div>
                        <div class="fab-icon i2" onclick="alert('Story Time Tapped')">📖</div>
                        <div class="fab-icon i3" onclick="alert('Worship Song Tapped')">🎵</div>
                        <div class="fab-main" onclick="document.getElementById('fab-menu').classList.toggle('open')">+</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- VIEW 3: GAME MAP & QUIZ -->
<div id="game-map-view" class="master-view">
    <div class="kk-device-frame quiz-frame">
        <div class="kk-dynamic-island" id="dyn-island-quiz"><span class="di-dot"></span><span class="di-text">Ready</span></div>
        <div class="kk-frame-btn kk-btn-power"></div>
        <div class="kk-frame-btn kk-btn-vol-up"></div>
        <div class="kk-frame-btn kk-btn-vol-down"></div>
        <div id="sisi-game-container">
            <div class="game-header">
            <button id="game-header-back-btn" class="header-btn" onclick="handleGameBack()">❮ Maps</button>
            <div style="display:flex; align-items:center; gap:10px;">
                <span id="game-title">Select a Map</span>
                <button id="reset-map-btn" onclick="resetCurrentMap()" style="display:none; background:rgba(255,59,48,0.2); border:1px solid #FF3B30; color:#fff; border-radius:8px; cursor:pointer; padding:4px 8px; font-size:0.75rem; font-weight:bold; transition:0.3s;" onmouseover="this.style.background='#FF3B30'" onmouseout="this.style.background='rgba(255,59,48,0.2)'" title="Reset this map's progress">↻ Reset</button>
            </div>
            <div class="stats-pill">
                <div class="stat-badge fire">🔥 <span id="streak-count">0</span></div>
                <div class="stat-badge xp">⚡ <span id="xp-count">0</span> XP</div>
            </div>
        </div>

        <!-- Persistent parallax background layer — lives outside the scrolling map so it never scrolls out of view -->
            <div id="map-parallax-bg">
                <div class="kk-orb orb-1"></div>
                <div class="kk-orb orb-2"></div>
                <div class="kk-orb orb-3"></div>
                <div class="kk-particle p-1"></div>
                <div class="kk-particle p-2"></div>
                <div class="kk-particle p-3"></div>
            </div>

        <div id="sisi-map-view"><svg id="path-overlay"></svg></div>
        
       <div id="sisi-quiz-view">
            <div class="question-box" id="quiz-question">Loading...</div>
            <div class="options-grid" id="quiz-options"></div>
            <div class="quiz-footer">
                <button class="qz-quit-btn" onclick="quitQuiz()">Quit ✖️</button>
                <button class="skip-btn" onclick="skipQuestion()">Skip ⏭️</button>
            </div>
        </div> 
        <div id="sisi-results-view">
            <div class="res-icon" id="res-icon">🏆</div>
            <div class="res-title" id="res-title">Level Complete!</div>
            <div class="res-desc" id="res-desc">Great job.</div>
            <button class="res-btn" onclick="closeResults()">Continue</button>
        </div>
        <div id="sisi-map-complete-view">
            <div class="map-comp-icon">👑</div>
            <h2 style="color:#FFD700; font-size:2.2rem; font-weight:900; margin:0 0 15px 0;">Map Mastered!</h2>
            <p style="color:#CBD5E1; font-size:1.1rem; line-height:1.5; margin-bottom:30px; max-width:80%;">You've completed all extant levels in this map! Return later to see if new challenges have been added.</p>
            <button class="res-btn" style="background:#FFD700; color:#000; box-shadow:0 10px 20px rgba(255,215,0,0.4);" onclick="closeMapComplete()">View Golden Map</button>
        </div>
    </div> 
</div> 

<!-- Custom UI Overlays & Task 4 Particle Canvas -->
<canvas id="sisi-particle-canvas" style="position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:2147483647;"></canvas>
<div id="sisi-toast-container"></div>
<div id="sisi-custom-modal" class="sisi-modal-overlay" style="z-index: 2147483647;">
    <div class="sisi-modal-content">
        <h3 id="sisi-modal-title">Confirm</h3>
        <p id="sisi-modal-text">Are you sure?</p>
        <div class="sisi-modal-actions">
            <button id="sisi-modal-cancel" style="background:rgba(255,255,255,0.1);">Cancel</button>
            <button id="sisi-modal-confirm" style="background:#FF3B30;">Confirm</button>
        </div>
    </div>
</div>

<script>
    const allMapsData = <?php echo json_encode($journey_data); ?>;
    const availableCourses = <?php echo json_encode($course_list); ?>;
    const serverProgress = <?php echo $user_progress_json; ?>; // Task 5: Server State
    const STRICT_PROGRESSION = <?php echo $strict_progression; ?> == 1; 
    
    const catData = {
        1: { title: "Foundational Modules", desc: "Start your journey here with introductory maps." },
        2: { title: "Core Competencies", desc: "Dive deeper into the core concepts." },
        3: { title: "Valedictory Capstone", desc: "Test your mastery with final challenges." }
    };

    function setDynamicIsland(islandId, text, state) {
        const island = document.getElementById(islandId);
        if (!island) return;
        island.className = 'kk-dynamic-island' + (state && state !== 'neutral' ? ' state-' + state : '');
        
        // Task 1: Trigger Spring Physics
        island.classList.remove('spring');
        void island.offsetWidth; // Force reflow
        island.classList.add('spring');

        const label = island.querySelector('.di-text');
        if (label) label.innerHTML = text; // innerHTML allows emojis for the idle eye
    }

    const KK_MODES = {
        phone: { w: 400, h: 800, mainRect: { x: 16, width: 368, closedHeight: 275, openHeight: 580 }, tailCx: [85, 200, 315], tailCy: 275, closeCx: 200, closedCy: 275, openCy: 610, closeXTop: 585 },
        wide: { w: 700, h: 550, mainRect: { x: 16, width: 668, closedHeight: 275, openHeight: 460 }, tailCx: [175, 350, 525], tailCy: 275, closeCx: 350, closedCy: 275, openCy: 490, closeXTop: 465 }
    };
    let currentMode = 'phone';

    function applyKkMode(mode) {
        currentMode = mode;
        const cfg = KK_MODES[mode];
        const app = document.getElementById('kk-app');
        const canvas = document.getElementById('kk-canvas');

        app.classList.toggle('wide-mode', mode === 'wide');
        document.getElementById('kk-device-frame').classList.toggle('wide-mode', mode === 'wide');
        canvas.style.width = cfg.w + 'px'; canvas.style.height = cfg.h + 'px';

        document.getElementById('mask-main-rect').setAttribute('width', cfg.mainRect.width);
        const tails = [document.getElementById('mask-tail-1'), document.getElementById('mask-tail-2'), document.getElementById('mask-tail-3')];
        tails.forEach((t, i) => t.setAttribute('cx', cfg.tailCx[i]));
        document.getElementById('mask-close-blob').setAttribute('cx', cfg.closeCx);

        document.querySelectorAll('.sel-btn').forEach((btn, i) => {
            btn.style.left = (cfg.tailCx[i] - 22) + 'px'; btn.style.top = (cfg.tailCy - 22) + 'px';
        });
        document.querySelector('.kk-activities-list').style.width = cfg.mainRect.width + 'px';
        document.querySelector('.kk-close-x').style.top = cfg.closeXTop + 'px';

        toggleActivities(app.classList.contains('show-activities')); 
    }

    let activeAdminCourseId = null;
    let activeCourseName = "";
    let activeMapData = null;
    let activeLevels = [];
    
    let selectedLevelIdx = 0;
    let playingLevelIdx = 0;   // the level actually being rendered/played
    let questionIndex = 0;
    let correctAnswersCount = 0;
    let isProcessing = false;
    let quizTimerInterval = null;
    let quizStartTime = null;
    let islandOverrideUntil = 0;

    let userProgress = serverProgress || {};
    let xp = userProgress._globalXP || 0;
    let streak = 0; 
    
    function loadMapProgress(mapId) { 
        if (!userProgress[mapId]) userProgress[mapId] = { level: 0, q: 0, xp: 0 }; 
        return userProgress[mapId]; 
    }
    
    // Ledger helper: Modifies Global XP and Map-Specific XP in tandem
    function updateXP(amount) {
        xp = Math.max(0, xp + amount);
        if (activeMapData && activeMapData.id) {
            let p = loadMapProgress(activeMapData.id);
            p.xp = Math.max(0, (p.xp || 0) + amount);
        }
        updateStats();
    }

    function saveMapProgress(mapId, lvl, q) { 
        let currentXp = userProgress[mapId] ? (userProgress[mapId].xp || 0) : 0;
        userProgress[mapId] = { level: lvl, q: q, xp: currentXp }; 
        userProgress._globalXP = xp; // Save global XP natively into JSON
        localStorage.setItem('sisi_map_progress', JSON.stringify(userProgress)); 
        // Background AJAX Sync heartbeat (Our Cloud Database Ledger)
        const fd = new FormData(); fd.append('save_progress', JSON.stringify(userProgress));
        fetch('gamified_journey.php', { method: 'POST', body: fd });
    }
    
    function resetAllProgress() {
        showCustomConfirm("Reset Progress?", "Are you sure you want to delete all your progress and reset your XP? This cannot be undone.", "Reset", "#FF3B30", () => {
            userProgress = {}; xp = 0;
            const fd = new FormData(); fd.append('save_progress', JSON.stringify(userProgress));
            fetch('gamified_journey.php', { method: 'POST', body: fd }).then(() => location.reload());
        });
    }

    function resetCurrentMap() {
        showCustomConfirm("Reset Map Progress?", `Are you sure you want to reset your progress for "${activeMapData.title}"? This will also remove the XP you earned on this specific map.`, "Reset Map", "#FF3B30", () => {
            let mapXp = userProgress[activeMapData.id] ? (userProgress[activeMapData.id].xp || 0) : 0;
            xp = Math.max(0, xp - mapXp); // Refund/Remove this map's specific XP from Global XP
            userProgress[activeMapData.id] = { level: 0, q: 0, xp: 0 }; // Wipe map ledger
            saveMapProgress(activeMapData.id, 0, 0); 
            updateStats();
            startMap(activeMapData.id); // Instantly reload map cleanly
        });
    }

    function switchView(viewId) {
        document.querySelectorAll('.master-view').forEach(v => v.classList.remove('active'));
        document.getElementById(viewId).classList.add('active');
    }

    function showCourseHub() {
        switchView('course-hub-view');
        const container = document.getElementById('sisi-course-hub');
        container.innerHTML = '';
        
        // Task 6: Shimmering Skeletons
        for(let i=0; i<6; i++) container.innerHTML += '<div class="skeleton-card"></div>';

        setTimeout(() => {
            container.innerHTML = '';
            availableCourses.forEach(c => {
                const extra = c.map_count === 0 ? '<span style="color:#FF3B30; font-size:0.8rem; display:block; margin-top:5px;">(No Maps Yet)</span>' : '';
                container.innerHTML += `
                    <div class="course-hub-card" onclick="showBlobUI(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                        <div><h3>${c.name}</h3><div class="map-count">🗺️ ${c.map_count} Maps Total</div>${extra}</div>
                        <div style="margin-top:20px; color:#F37021; font-weight:700; font-size:0.9rem;">View Categories ➔</div>
                    </div>`;
            });
        }, 400); // 400ms Skeleton shimmer effect
    }

    function showBlobUI(courseId, courseName, catId = 1) {
        activeAdminCourseId = courseId;
        activeCourseName = courseName;
        switchView('blob-ui-view');
        setDynamicIsland('dyn-island-blob', courseName, 'neutral');
        scaleKkApp(); 
        toggleActivities(false);
        setTimeout(() => handleCatSelection(catId), 500); 
    }

    function isCategoryLocked(catId) {
        if (!STRICT_PROGRESSION || catId === 1) return false;
        // To unlock Cat 2, ALL maps in Cat 1 must be 100% complete
        const prevCatMaps = allMapsData.filter(m => m.course_id == activeAdminCourseId && (m.category_id == catId - 1 || (!m.category_id && catId - 1 == 1)));
        if (prevCatMaps.length === 0) return false; 
        
        for (let map of prevCatMaps) {
            let prog = loadMapProgress(map.id);
            if (prog.level < map.levels.length) return true; 
        }
        return false;
    }

    function handleCatSelection(catId) {
        if (isCategoryLocked(catId)) {
            showIdleToast("🔒 You must complete all Maps in the previous category to unlock this one!");
            return;
        }

        document.querySelectorAll('.sel-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('sel-' + catId).classList.add('active');

        const overlay = document.querySelector('.kk-grad-overlay');
        overlay.classList.remove('cat-1', 'cat-2', 'cat-3'); overlay.classList.add('cat-' + catId);
        const base = document.querySelector('.kk-grad-base');
        base.classList.remove('cat-1', 'cat-2', 'cat-3'); base.classList.add('cat-' + catId);

        setDynamicIsland('dyn-island-blob', catData[catId].title, 'info');
        
        const tails = [document.getElementById('mask-tail-1'), document.getElementById('mask-tail-2'), document.getElementById('mask-tail-3')];
        const baseCy = KK_MODES[currentMode].tailCy;
        tails.forEach((t, i) => { t.setAttribute('cy', (i+1 === catId) ? baseCy : baseCy + 10); });

        const infoText = document.getElementById('info-text');
        infoText.style.opacity = 0;
        setTimeout(() => {
            document.getElementById('info-title').innerText = catData[catId].title;
            document.getElementById('info-desc').innerHTML = `${catData[catId].desc}<br><strong style="color:#FF9500; display:block; margin-top:5px;">Course: ${activeCourseName}</strong>`;
            infoText.style.opacity = 1;
        }, 300);
        setTimeout(() => { toggleActivities(true); loadMapsForCategory(catId); }, 800);
    }

    function toggleActivities(show) {
        const app = document.getElementById('kk-app');
        const cfg = KK_MODES[currentMode];
        const maskRect = document.getElementById('mask-main-rect');
        const closeBlob = document.getElementById('mask-close-blob');
        const infoText = document.getElementById('info-text');
        if (show) {
            app.classList.add('show-activities');
            maskRect.setAttribute('height', cfg.mainRect.openHeight);
            closeBlob.setAttribute('cy', cfg.openCy);
            infoText.style.opacity = '';
        } else {
            app.classList.remove('show-activities');
            maskRect.setAttribute('height', cfg.mainRect.closedHeight);
            closeBlob.setAttribute('cy', cfg.closedCy);
            document.getElementById('game-map-view').classList.remove('active');
        }
    }

    function loadMapsForCategory(catId) {
        const container = document.getElementById('map-list-container');
        container.innerHTML = '';
        
        let courseMaps = allMapsData.filter(m => m.course_id == activeAdminCourseId && (m.category_id == catId || (!m.category_id && catId == 1)));
        courseMaps.sort((a,b) => a.id - b.id);

        if (courseMaps.length === 0) {
            setDynamicIsland('dyn-island-blob', 'No Maps Yet', 'warning');
            container.innerHTML = `
                <div style="display:flex; flex-direction:column; align-items:center; text-align:center; padding: 20px;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">🏗️</div>
                    <h3 style="color:#000; font-size: 1.5rem; margin:0; font-weight: 900;">No Maps Available</h3>
                    <p style="color:#333; font-size: 0.95rem; font-weight: 600; max-width: 280px; margin: 10px 0 20px 0;">This category doesn't have an interactive gamified path set up right now.</p>
                </div>`;
        } else {
            setDynamicIsland('dyn-island-blob', `${courseMaps.length} Map${courseMaps.length > 1 ? 's' : ''} Available`, 'info');
            
            let prevMapComplete = true;
            courseMaps.forEach((m, idx) => {
                let prog = loadMapProgress(m.id);
                let isCompleted = prog.level >= m.levels.length;
                let isLocked = STRICT_PROGRESSION && !prevMapComplete && idx > 0;
                
                let icon = isLocked ? '🔒' : (isCompleted ? '⭐' : '🗺️');
                let opacity = isLocked ? '0.5' : '1';
                let onClick = isLocked ? `showIdleToast('🔒 Complete the previous Map first!')` : `startMap(${m.id})`;

                container.innerHTML += `
                    <div class="map-row" onclick="${onClick}" style="opacity: ${opacity}">
                        <div class="map-icon" style="color: ${isLocked ? '#888' : '#007AFF'};">${icon}</div>
                        <div class="map-text"><h4>${m.title}</h4><p>🎮 ${m.levels.length} Levels</p></div>
                        <div style="margin-left:auto; opacity:0.5; color:#000;">❯</div>
                    </div>`;
                
                if (!isCompleted) prevMapComplete = false;
            });
        }
    }

    let parallaxAttached = false;
    function attachParallaxScroll() {
        if (parallaxAttached) return;      // only bind the listener once, ever
        parallaxAttached = true;
        const mapView = document.getElementById('sisi-map-view');
        const bg = document.getElementById('map-parallax-bg');
        if (!mapView || !bg) return;
        const orbs = bg.querySelectorAll('.kk-orb');
        const particles = bg.querySelectorAll('.kk-particle');
        let ticking = false;
        mapView.addEventListener('scroll', () => {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(() => {
                const y = mapView.scrollTop;
                // Deep background elements move down slightly
                if (orbs[0]) orbs[0].style.transform = `translateY(${y * 0.2}px)`;
                if (orbs[1]) orbs[1].style.transform = `translateY(${y * 0.4}px)`;
                if (orbs[2]) orbs[2].style.transform = `translateY(${y * 0.6}px)`;
                // Foreground elements move up sharply against scroll (strong depth)
                if (particles[0]) particles[0].style.transform = `translateY(${y * -0.5}px)`;
                if (particles[1]) particles[1].style.transform = `translateY(${y * -0.3}px)`;
                if (particles[2]) particles[2].style.transform = `translateY(${y * -0.8}px)`;
                ticking = false;
            });
        }, { passive: true });
    }

    function startMap(mapId) {
        activeMapData = allMapsData.find(m => m.id == mapId);
        activeLevels = activeMapData.levels;
        
        let prog = loadMapProgress(mapId);
        selectedLevelIdx = prog.level;
        questionIndex = prog.q;
        updateStats(); // Render natively loaded XP immediately

        const isMapComplete = selectedLevelIdx >= activeLevels.length;

        document.getElementById('game-title').innerText = activeMapData.title;
        document.getElementById('reset-map-btn').style.display = 'inline-block';
        document.getElementById('game-header-back-btn').innerText = '❮ Maps';
        switchView('game-map-view');
                
        const gameContainer = document.getElementById('sisi-game-container');
        gameContainer.className = 'cat-bg-' + (activeMapData.category_id || 1);
        
        document.getElementById('sisi-quiz-view').style.display = 'none';
        document.getElementById('sisi-results-view').style.display = 'none';
        gameContainer.classList.remove('hide-fab');
        
        const mapView = document.getElementById('sisi-map-view');
        mapView.style.display = 'flex';
        mapView.innerHTML = `<div id="sisi-map-3d-plane"><svg id="path-overlay"></svg></div>`;
        const plane = document.getElementById('sisi-map-3d-plane');

        attachParallaxScroll();   // binds once, safe to call every startMap

        activeLevels.forEach((level, idx) => {
            const isLocked = idx > selectedLevelIdx;
            const isCompleted = idx < selectedLevelIdx;
            
            let statusClass = isLocked ? 'locked' : (isCompleted ? 'completed' : 'current');
            if (isMapComplete) statusClass += ' gold-node'; // Render Regal Gold Nodes
            
            const icon = isLocked ? '🔒' : (isCompleted ? '✓' : '⭐');

            plane.innerHTML += `
                <div class="level-wrapper" id="node-${idx}">
                    <div style="position:relative; transform: translateX(${level.offset || 0}px)">
                        <div class="level-node ${statusClass}" onclick="openLevel(${idx})">${icon}</div>
                    </div>
                </div>`;
        });
       setTimeout(drawDynamicPaths, 50);
    }

    function drawDynamicPaths() {
        const svg = document.getElementById('path-overlay');
        const plane = document.getElementById('sisi-map-3d-plane');
        if (!svg || !plane || activeLevels.length < 2) return;
        
        const isMapComplete = selectedLevelIdx >= activeLevels.length;

        // 1. Temporarily remove 3D transform to get exact 2D metrics for drawing lines
        const originalTransform = plane.style.transform;
        plane.style.transform = 'none';

        // 2. Ensure SVG covers the full scrollable height of the map so lines don't cut off
        svg.style.height = plane.scrollHeight + 'px';

        let html = '';
        // 3. Get the bounding box of the SVG itself to use as the absolute coordinate baseline
        const svgRect = svg.getBoundingClientRect(); 

        for (let i = 0; i < activeLevels.length - 1; i++) {
            const startNode = document.querySelector(`#node-${i} .level-node`);
            const endNode = document.querySelector(`#node-${i+1} .level-node`);
            if (!startNode || !endNode) continue;

            const startRect = startNode.getBoundingClientRect();
            const endRect = endNode.getBoundingClientRect();

            // 3. Robust calculation: Compare Node position directly against SVG position. 
            // This ignores scrolling offsets, paddings, and CSS transforms automatically!
            const startX = (startRect.left - svgRect.left) + (startRect.width / 2);
            const startY = (startRect.top - svgRect.top) + (startRect.height / 2);
            const endX = (endRect.left - svgRect.left) + (endRect.width / 2);
            const endY = (endRect.top - svgRect.top) + (endRect.height / 2);

            let strokeClass = (i < selectedLevelIdx) ? 'game-path-line active' : 'game-path-line';
            if (isMapComplete) strokeClass = 'game-path-line gold'; // Render Regal Gold Lines
            const cpY = startY + (endY - startY) / 2;
            
            // Draw S-Curve connecting the exact centers
            html += `<path class="${strokeClass}" d="M ${startX} ${startY} C ${startX} ${cpY}, ${endX} ${cpY}, ${endX} ${endY}" />`;
        }
        svg.innerHTML = html;
        
        // Restore 3D transform now that paths are calculated flawlessly
        plane.style.transform = originalTransform;

        // Tracing path animation for Gold paths
        if (isMapComplete) {
            setTimeout(() => {
                svg.querySelectorAll('.game-path-line.gold').forEach((p, index) => {
                    const len = p.getTotalLength();
                    p.style.strokeDasharray = len;
                    p.style.strokeDashoffset = len;
                    p.style.animation = `traceGold 1.5s ease-in-out ${index * 0.4}s forwards`;
                });
            }, 50);
        }
    }

    function showCustomConfirm(title, message, confirmText, confirmColor, onConfirm, onCancel) {
        const overlay = document.getElementById('sisi-custom-modal');
        document.getElementById('sisi-modal-title').innerText = title;
        document.getElementById('sisi-modal-text').innerText = message;
        
        const confirmBtn = document.getElementById('sisi-modal-confirm');
        confirmBtn.innerText = confirmText;
        confirmBtn.style.background = confirmColor;
        
        confirmBtn.onclick = () => {
            overlay.classList.remove('show');
            setTimeout(() => { overlay.style.display = 'none'; onConfirm(); }, 300);
        };
        
        document.getElementById('sisi-modal-cancel').onclick = () => {
            overlay.classList.remove('show');
            setTimeout(() => { 
                overlay.style.display = 'none'; 
                if (onCancel) onCancel(); // Trigger cancel callback if provided
            }, 300);
        };
        
        overlay.style.display = 'flex';
        requestAnimationFrame(() => overlay.classList.add('show'));
    }

    function showIdleToast(message) {
        let container = document.getElementById('sisi-toast-container');
        const toast = document.createElement('div');
        toast.className = 'sisi-toast';
        toast.innerHTML = `<span style="font-size:1.5rem;">💡</span> <div>${message}</div>`;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 6000);
    }

    let idleTimer = null;
    let idleWarningTimer = null;
    function resetIdleTimer() {
        clearTimeout(idleTimer);
        clearTimeout(idleWarningTimer);
        
        // Task 7: Contextual Island "Eye" Morph
        idleWarningTimer = setTimeout(() => {
            setDynamicIsland('dyn-island-quiz', '<span style="font-size:1.2rem;">👀</span> Looking for the answer?', 'warning');
        }, 15000);

        idleTimer = setTimeout(() => {
            showIdleToast("Need a hint? Take your time, or click Skip if you are stuck!");
            resetIdleTimer(); // Re-arm the timer
        }, 30000); 
    }

    function handleGameBack() {
        if (document.getElementById('sisi-quiz-view').style.display === 'flex') {
            quitQuiz(); // If in quiz, trigger quit confirmation
        } else {
            showBlobUI(activeAdminCourseId, activeCourseName, activeMapData.category_id || 1); // Returns precisely to Category being browsed
        }
    }

    function openLevel(idx) {
        if (idx > selectedLevelIdx) {
            if (STRICT_PROGRESSION) showIdleToast("🔒 You must complete previous levels first!");
            return; 
        }
        playingLevelIdx = idx;   
        correctAnswersCount = 0; // Reset score tracker
        document.getElementById('sisi-map-view').style.display = 'none'; 
        document.getElementById('sisi-quiz-view').style.display = 'flex';
        document.getElementById('game-header-back-btn').innerText = '❮ Map'; // Update back text
        
        // Remove background overlay so it applies to the quiz too
        document.getElementById('sisi-game-container').classList.add('hide-fab'); 
        
        renderQuestion();
        startQuizTimer();
    }

    function closeQuiz() {
        stopQuizTimer();
        clearTimeout(idleTimer);
        document.getElementById('sisi-quiz-view').style.display = 'none';
        document.getElementById('sisi-map-view').style.display = 'flex';
        document.getElementById('sisi-game-container').classList.remove('hide-fab');
        document.getElementById('quiz-options').innerHTML = ''; 
        document.getElementById('game-header-back-btn').innerText = '❮ Maps'; // Revert back text
        startMap(activeMapData.id); 
    }

function quitQuiz() {
    clearTimeout(idleTimer);
    const isReplay = playingLevelIdx !== selectedLevelIdx;   // NEW
    showCustomConfirm(
        "Quit Quiz?", 
        isReplay
            ? "This is a replay, so quitting won't cost XP or change your saved progress."
            : "Quitting now will deduct 20 XP. Your progress on this question will be saved.", 
        isReplay ? "Quit" : "Quit (-20 XP)", "#FF3B30", 
        () => {
            if (!isReplay) {                                  // NEW guard
                updateXP(-20);
                saveMapProgress(activeMapData.id, selectedLevelIdx, questionIndex);
            }
            closeQuiz();
        },
        () => resetIdleTimer()
    );
}

function skipQuestion() {
    clearTimeout(idleTimer);
    const qData = activeLevels[playingLevelIdx].questions[questionIndex];
    const penalty = Math.floor((qData.xp || 100) / 2);
    const isReplay = playingLevelIdx !== selectedLevelIdx;   // NEW
    
    showCustomConfirm(
        "Skip Question?", 
        isReplay
            ? "This is a replay, so skipping won't cost you any XP."
            : `Skipping this question will deduct ${penalty} XP from your score. Are you sure?`,
        isReplay ? "Skip" : `Skip (-${penalty} XP)`, "#FF9500", 
        () => {
            if (!isReplay) { updateXP(-penalty); }   // NEW guard
            streak = 0; updateStats();
            flashQuizIsland(isReplay ? '⏭ Skipped' : `⏭ Skipped · -${penalty} XP`, 'warning', 1200);
            nextQuestion(true);
        },
        () => resetIdleTimer()
    );
}

    function renderQuestion() {
        resetIdleTimer(); 
        const qData = activeLevels[playingLevelIdx].questions[questionIndex];
        document.getElementById('game-title').innerText = `Level ${playingLevelIdx + 1} | Q: ${questionIndex + 1} / ${activeLevels[playingLevelIdx].questions.length}`;
        document.getElementById('quiz-question').innerText = qData.q;
        
        const skipBtn = document.querySelector('.skip-btn');
        const penalty = Math.floor((qData.xp || 100) / 2);
        skipBtn.innerHTML = `Skip (-${penalty} XP) ⏭️`;

        const optionsGrid = document.getElementById('quiz-options');
        optionsGrid.innerHTML = '';
        qData.options.forEach((opt, idx) => {
            const btn = document.createElement('div');
            btn.className = 'option-btn';
            btn.innerText = opt;
            btn.onclick = (e) => checkAnswer(idx, btn, e);
            optionsGrid.appendChild(btn);
        });
    }

    function spawnXPParticles(startX, startY, endX, endY) {
        const cvs = document.getElementById('sisi-particle-canvas');
        if (!cvs) return;
        const ctx = cvs.getContext('2d');
        cvs.width = window.innerWidth; cvs.height = window.innerHeight;
        
        let particles = [];
        for(let i=0; i<30; i++) {
            particles.push({
                x: startX, y: startY,
                vx: (Math.random() - 0.5) * 20, vy: (Math.random() - 0.5) * 20 - 5,
                life: 1, size: Math.random() * 6 + 3,
                color: ['#25d366','#FFD60A','#00CFFD', '#FFF'][Math.floor(Math.random()*4)]
            });
        }
        function animate() {
            ctx.clearRect(0,0,cvs.width,cvs.height);
            let alive = false;
            particles.forEach(p => {
                if (p.life > 0) {
                    alive = true;
                    p.vx += (endX - p.x) * 0.05; // Steer to target
                    p.vy += (endY - p.y) * 0.05; 
                    p.x += p.vx; p.y += p.vy;
                    p.life -= 0.02;
                    ctx.beginPath(); ctx.arc(p.x, p.y, p.size * p.life, 0, Math.PI*2);
                    ctx.fillStyle = p.color; ctx.shadowBlur = 10; ctx.shadowColor = p.color; ctx.fill();
                }
            });
            if(alive) requestAnimationFrame(animate);
            else ctx.clearRect(0,0,cvs.width,cvs.height);
        }
        animate();
    }

function checkAnswer(selectedIndex, btnElement, event) {
    if (isProcessing) return; isProcessing = true;
    clearTimeout(idleTimer); 
    clearTimeout(idleWarningTimer);

    const qData = activeLevels[playingLevelIdx].questions[questionIndex];
    const correctIndex = qData.ans;
    const qXp = parseInt(qData.xp) || 100;
    const isReplay = playingLevelIdx !== selectedLevelIdx;   // NEW

    const allBtns = document.querySelectorAll('.option-btn');
    allBtns.forEach(b => b.classList.add('disabled'));

    if (selectedIndex === correctIndex) {
        btnElement.classList.add('correct');
        streak++; correctAnswersCount++;
        const earned = qXp + (streak * 10);

        if (!isReplay) {                                     // — only real progress earns XP
            updateXP(earned);
            const btnRect = btnElement.getBoundingClientRect();
            const xpRect = document.querySelector('.stat-badge.xp').getBoundingClientRect();
            spawnXPParticles(btnRect.left + btnRect.width/2, btnRect.top, xpRect.left + xpRect.width/2, xpRect.top + xpRect.height/2);
        }

        const sLabel = streakLabel(streak);
        const label = isReplay
            ? (sLabel ? `${sLabel} (Replay)` : '✓ Correct (Replay)')
            : (sLabel ? `${sLabel} +${earned} XP` : `✓ +${earned} XP`);
        flashQuizIsland(label, sLabel ? 'streak' : 'correct', 950);
        
        setTimeout(() => { isProcessing = false; nextQuestion(); }, 1200);
   } else {
        btnElement.classList.add('wrong');
        allBtns[correctIndex].classList.add('correct');
        streak = 0; updateStats();
        flashQuizIsland('✗ Not quite — streak reset', 'wrong', 1400);
        setTimeout(() => { isProcessing = false; nextQuestion(); }, 1500);
    }
}

    function showResultsView(correct, total) {
        stopQuizTimer();
        document.getElementById('sisi-quiz-view').style.display = 'none';
        const resView = document.getElementById('sisi-results-view');
        
        // Dynamically style based on the category background
        resView.className = document.getElementById('sisi-game-container').className; 
        
        const pct = correct / total;
        const icon = document.getElementById('res-icon');
        const title = document.getElementById('res-title');
        const desc = document.getElementById('res-desc');
        
        if (pct >= 0.8) {
            icon.innerText = '🏆'; title.innerText = 'Outstanding!';
            desc.innerText = `You dominated this level with ${correct}/${total} correct! Massive XP gains!`;
        } else if (pct >= 0.5) {
            icon.innerText = '⭐'; title.innerText = 'Good Job!';
            desc.innerText = `You passed with ${correct}/${total} correct. Keep going!`;
        } else {
            icon.innerText = '📚'; title.innerText = 'Keep Practicing!';
            desc.innerText = `You got ${correct}/${total} right. Review the material and you'll get it next time!`;
        }
        
        resView.style.display = 'flex';
        flashQuizIsland(`Level Complete!`, 'streak', 2000);
    }

    function closeResults() {
        document.getElementById('sisi-results-view').style.display = 'none';
        // Immediately trigger Golden Completed banner ONLY when playing & beating the ultimate final level
        if (selectedLevelIdx >= activeLevels.length && playingLevelIdx === selectedLevelIdx - 1) {
            document.getElementById('sisi-map-complete-view').style.display = 'flex';
            document.getElementById('sisi-game-container').classList.remove('hide-fab');
        } else {
            closeQuiz();
        }
    }

    function closeMapComplete() {
        document.getElementById('sisi-map-complete-view').style.display = 'none';
        closeQuiz();
    }

function nextQuestion(skipped = false) {
    questionIndex++;
    const totalQ = activeLevels[playingLevelIdx].questions.length;
    if (questionIndex >= totalQ) {
        questionIndex = 0;
        if (playingLevelIdx === selectedLevelIdx) {   // only advance if this WAS the frontier level
            selectedLevelIdx++;
            saveMapProgress(activeMapData.id, selectedLevelIdx, 0);
        }
        showResultsView(correctAnswersCount, totalQ);
    } else {
        if (playingLevelIdx === selectedLevelIdx) {   // don't clobber saved progress on replay
            saveMapProgress(activeMapData.id, selectedLevelIdx, questionIndex);
        }
        renderQuestion();
    }
    isProcessing = false;
}

    function updateStats() { document.getElementById('streak-count').innerText = streak; document.getElementById('xp-count').innerText = xp; }

    function startQuizTimer() {
        quizStartTime = Date.now();
        islandOverrideUntil = 0;
        updateQuizIslandDefault();
        clearInterval(quizTimerInterval);
        quizTimerInterval = setInterval(updateQuizIslandDefault, 1000);
    }
    function stopQuizTimer() {
        clearInterval(quizTimerInterval);
        quizTimerInterval = null;
        quizStartTime = null;
    }
    function updateQuizIslandDefault() {
        if (!quizStartTime || Date.now() < islandOverrideUntil) return;
        const elapsed = Math.floor((Date.now() - quizStartTime) / 1000);
        const mm = String(Math.floor(elapsed / 60)).padStart(2, '0');
        const ss = String(elapsed % 60).padStart(2, '0');
        const lvl = activeLevels[playingLevelIdx];
        const qTotal = lvl ? lvl.questions.length : '?';
        setDynamicIsland('dyn-island-quiz', `⏱ ${mm}:${ss} · Q${questionIndex + 1}/${qTotal}`, 'neutral');
    }
    function flashQuizIsland(text, state, holdMs) {
        islandOverrideUntil = Date.now() + holdMs;
        setDynamicIsland('dyn-island-quiz', text, state);
    }
    function streakLabel(s) {
        if (s >= 10) return '🚀 Unstoppable!';
        if (s >= 5) return '🔥 On Fire!';
        if (s >= 3) return '⚡ Streak!';
        return null;
    }
    window.addEventListener('resize', () => {
        if(document.getElementById('sisi-map-view').style.display === 'flex') drawDynamicPaths();
        scaleKkApp();
    });

    function scaleKkApp() {
        const app = document.getElementById('kk-app');
        const canvas = document.getElementById('kk-canvas');
        const wrapper = document.getElementById('blob-ui-view');
        if (!app || !canvas || !wrapper) return;
        if (getComputedStyle(wrapper).display === 'none') return;

        const vw = window.innerWidth;
        const vh = window.innerHeight;
        const desiredMode = vw >= 700 ? 'wide' : 'phone';
        if (desiredMode !== currentMode) applyKkMode(desiredMode);

        const cfg = KK_MODES[currentMode];
        const availableWidth = wrapper.clientWidth - 64;
        let targetWidth, targetHeight;

        if (currentMode === 'wide') {
            targetHeight = vh * 0.8;
            targetWidth = targetHeight * (cfg.w / cfg.h);
            if (targetWidth > availableWidth) {
                targetWidth = availableWidth;
                targetHeight = targetWidth * (cfg.h / cfg.w);
            }
        } else {
            targetWidth = Math.min(cfg.w, availableWidth);
            targetHeight = cfg.h * (targetWidth / cfg.w);
        }

        const scale = targetWidth / cfg.w;
        app.style.width = targetWidth + 'px';
        app.style.height = targetHeight + 'px';
        canvas.style.transform = `scale(${scale})`;
        wrapper.style.minHeight = targetHeight + 32 + 'px';
    }

    // Boot sequence
    document.addEventListener("DOMContentLoaded", showCourseHub);
</script>

<?php echo $OUTPUT->footer(); ?>