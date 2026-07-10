<?php
// public_html/gamified_journey.php
require_once('config.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/gamified_journey.php');
$PAGE->set_title('Gamified Learning Hub');
$PAGE->set_heading('Interactive Certification Path');
$PAGE->set_pagelayout('standard'); 

$journey_data_json = get_config('local_sisizathu', 'journey_data') ?: '[]';
$journey_data = json_decode($journey_data_json, true);

$categories_data = json_decode(get_config('local_sisizathu', 'journey_categories') ?: '{}', true);

$course_level_counts = [];
if (is_array($journey_data)) {
    foreach ($journey_data as $level) {
        $cid = $level['course_id'];
        if (!isset($course_level_counts[$cid])) $course_level_counts[$cid] = 0;
        $course_level_counts[$cid]++;
    }
}

global $DB;
$courses = $DB->get_records_select('course', 'id != ?', array(SITEID), 'fullname ASC', 'id, fullname');
$course_list = [];
foreach ($courses as $c) {
    $count = isset($course_level_counts[$c->id]) ? $course_level_counts[$c->id] : 0;
    $cat_id = isset($categories_data[$c->id]) ? $categories_data[$c->id] : 1; // Default mapped to category 1
    $course_list[] = [
        'id' => $c->id, 
        'name' => format_string($c->fullname),
        'level_count' => $count,
        'category_id' => $cat_id
    ];
}

echo $OUTPUT->header();
?>

<!-- SwiftUI Filter logic -->
<svg style="width:0; height:0; position:absolute;" aria-hidden="true" focusable="false">
  <defs>
    <filter id="swiftui-goo">
      <feGaussianBlur in="SourceGraphic" stdDeviation="12" result="blur" />
      <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 20 -8" result="goo" />
      <feBlend in="SourceGraphic" in2="goo" />
    </filter>
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
    /* MAIN CATEGORY MENU (Blobs) */
    #kk-app {
        width: 100%; max-width: 400px; height: 800px; margin: 2rem auto;
        position: relative; overflow: hidden; border-radius: 40px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.6); font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
        background-color: #111; user-select: none;
    }
    .kk-bg-wrapper { position: absolute; inset: 0; z-index: 1; }
    .kk-orb { position: absolute; border-radius: 50%; filter: blur(40px); opacity: 0.6; mix-blend-mode: screen; animation: orbFloat 8s infinite alternate ease-in-out; }
    .orb-1 { width: 300px; height: 300px; background: #FF9500; bottom: -50px; left: -100px; }
    .orb-2 { width: 250px; height: 250px; background: #FF2D55; top: 30%; right: -50px; animation-delay: -3s; }
    .orb-3 { width: 200px; height: 200px; background: #34C759; bottom: 20%; left: 40%; animation-delay: -5s; }
    @keyframes orbFloat { 0% { transform: translate(0,0); } 100% { transform: translate(30px, -40px); } }

    .kk-masked-content { position: absolute; inset: 0; z-index: 10; mask: url(#swiftui-canvas-mask); -webkit-mask: url(#swiftui-canvas-mask); }
    .kk-grad-base { position: absolute; inset: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #007AFF, #AF52DE, #FF2D55); transition: 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .kk-grad-overlay { position: absolute; bottom: 0; left: 0; width: 100%; height: 0%; background: linear-gradient(180deg, #FFD60A, #34C759, #00CFFD); transition: height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    #kk-app.show-activities .kk-grad-overlay { height: 60%; }

    .kk-content-layer { position: absolute; inset: 0; z-index: 20; pointer-events: none; }
    .kk-info-text { position: absolute; top: 70px; width: 100%; text-align: center; color: white; padding: 0 30px; box-sizing: border-box; transition: all 0.4s ease; pointer-events: auto; }
    #kk-app.show-activities .kk-info-text { opacity: 0; transform: translateY(-30px); pointer-events: none; }
    .kk-icon-ring { width: 40px; height: 40px; margin: 0 auto 15px auto; border-radius: 50%; border: 2px solid white; position: relative; }
    .kk-icon-ring::after { content: ''; position: absolute; inset: 6px; border-radius: 50%; border: 1px solid white; }
    .kk-info-text h2 { font-size: 2rem; font-weight: 800; line-height: 1.1; margin-bottom: 15px; color: #fff;}
    .kk-info-text p { font-size: 1rem; line-height: 1.4; opacity: 0.9; }

    .kk-sel-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s; pointer-events: none; z-index: 20; }
    #kk-app.show-activities .kk-sel-container { transform: scale(0); opacity: 0; }
    
    .sel-btn { position: absolute; width: 44px; height: 44px; border-radius: 50%; cursor: pointer; pointer-events: auto; display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); border: 3px solid transparent; }
    .sel-btn.c1 { background: #FF3B30; left: calc(85px - 22px); top: calc(275px - 22px); }
    .sel-btn.c2 { background: #AF52DE; left: calc(200px - 22px); top: calc(275px - 22px); }
    .sel-btn.c3 { background: #34C759; left: calc(315px - 22px); top: calc(275px - 22px); }
    .sel-btn:not(.active) { opacity: 0.7; transform: scale(0.85) translateY(12px); }
    .sel-btn:active { transform: scale(0.9); }

    .kk-activities-list { position: absolute; top: 220px; left: 16px; width: 368px; padding: 20px; box-sizing: border-box; transform: translateY(40px); opacity: 0; pointer-events: none; transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
    #kk-app.show-activities .kk-activities-list { transform: translateY(0); opacity: 1; pointer-events: auto; transition-delay: 0.1s;}
    .kk-activities-list h1 { color: #000; font-weight: 900; font-size: 2.2rem; margin-bottom: 20px; text-align: center;}

    .kk-close-x { position: absolute; top: 585px; left: 50%; transform: translateX(-50%); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: bold; cursor: pointer; pointer-events: none; opacity: 0; transition: opacity 0.3s; }
    #kk-app.show-activities .kk-close-x { pointer-events: auto; opacity: 1; transition-delay: 0.4s;}

    .cat-scrollview { max-height: 280px; overflow-y: auto; padding-right: 10px; }
    .cat-scrollview::-webkit-scrollbar { width: 6px; }
    .cat-scrollview::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); border-radius: 10px; }
    .cat-scrollview::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.6); border-radius: 10px; backdrop-filter: blur(5px); }

    .course-row { background: rgba(15, 15, 25, 0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px; padding: 15px; display: flex; align-items: center; gap: 15px; margin-bottom: 15px; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 25px rgba(0,0,0,0.4); }
    .course-row:active { transform: scale(0.95); }
    .course-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); }
    .course-text h4 { margin: 0; font-weight: 800; color: #fff; font-size: 1.15rem; }
    .course-text p { margin: 0; color: #CBD5E1; font-size: 0.9rem; font-weight: 600; }

    /* GAME MAP & QUIZ LAYER */
    #sisi-game-container {
        width: 100%; max-width: 850px; margin: 2rem auto; height: calc(100vh - 140px); min-height: 600px;
        display: none; flex-direction: column; background: rgba(15, 15, 25, 0.75); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); overflow: hidden;
        color: #F8FAFC !important; font-family: 'Poppins', sans-serif;
    }
    .game-header { padding: 18px 25px; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); font-size: 1.3rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; color: #fff; z-index: 20; }
    .header-btn { background: rgba(255,255,255,0.1); border: none; color: white; padding: 8px 16px; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.3s; }
    .header-btn:hover { background: #F37021; transform: scale(1.05); }
    
    .stats-pill { display: flex; gap: 15px; font-size: 0.95rem; font-weight: 700; }
    .stat-badge { background: rgba(255,255,255,0.08); padding: 5px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 6px; }
    .stat-badge.fire { color: #ffb347; border-color: rgba(255, 179, 71, 0.3); }
    .stat-badge.xp { color: #00CFFD; border-color: rgba(0, 207, 253, 0.3); }

    /* Map Overlay */
    #sisi-map-view { flex-grow: 1; position: relative; padding: 40px 0; display: flex; flex-direction: column; justify-content: space-evenly; align-items: center; overflow-y: auto; }
    #path-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; }
    .game-path-line { fill: none; stroke: rgba(255,255,255,0.15); stroke-width: 6; stroke-linecap: round; stroke-dasharray: 2 18; }
    .game-path-line.active { stroke: #25d366; }
    .level-wrapper { position: relative; display: flex; justify-content: center; align-items: center; z-index: 10; width: 100%; }
    .level-node { width: 75px; height: 75px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; cursor: pointer; transition: 0.3s; position: relative; box-shadow: inset 0 0 10px rgba(0,0,0,0.5); }
    .level-node.locked { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.3); border: 3px solid rgba(255,255,255,0.1); }
    .level-node.current { background: #25d366; color: white; box-shadow: 0 0 30px rgba(37, 211, 102, 0.5); transform: scale(1.15); border: 4px solid #fff; }
    .level-node.completed { background: #F37021; color: white; border: 4px solid #fff; }

    /* Quiz Overlay */
    #sisi-quiz-view { padding: 30px; display: none; flex-direction: column; flex-grow: 1; justify-content: space-between; animation: fadeIn 0.3s ease; }
    .question-box { font-size: 1.5rem; text-align: center; margin: 20px 0; font-weight: 600; color: #fff; }
    .options-grid { display: flex; flex-direction: column; gap: 14px; }
    .option-btn { background: rgba(255,255,255,0.06); border: 2px solid rgba(255,255,255,0.12); padding: 18px; border-radius: 14px; color: #fff; font-size: 1.15rem; cursor: pointer; transition: 0.2s; text-align: center; font-weight: 500; }
    .option-btn:hover:not(.disabled) { background: rgba(243, 112, 33, 0.25); border-color: #F37021; transform: scale(1.02); }
    .option-btn.correct { background: #25d366 !important; border-color: #25d366 !important; box-shadow: 0 0 20px rgba(37, 211, 102, 0.5); }
    .option-btn.wrong { background: #ff4444 !important; border-color: #ff4444 !important; animation: shake 0.4s; }
    .option-btn.disabled { pointer-events: none; opacity: 0.6; }

    .quiz-footer { display: flex; justify-content: space-between; padding-top: 20px; }
    .qz-quit-btn { background: rgba(255, 59, 48, 0.2); border: 1px solid #FF3B30; color: #fff; padding: 12px 24px; border-radius: 12px; cursor: pointer; font-weight: bold; transition: 0.2s; }
    .qz-quit-btn:hover { background: #FF3B30; }
    .skip-btn { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px 24px; border-radius: 12px; cursor: pointer; font-weight: 700; transition: 0.3s; }
    .skip-btn:hover { background: #00CFFD; color: #000; }

    .floating-xp { position: absolute; font-weight: 800; font-size: 1.5rem; color: #25d366; pointer-events: none; animation: floatUp 1s ease forwards; z-index: 100; }
    @keyframes floatUp { 0% { opacity: 1; transform: translateY(0) scale(1); } 100% { opacity: 0; transform: translateY(-60px) scale(1.3); } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<!-- 1. BLOBS UI (Entry Screen) -->
<div id="kk-app">
    <div class="kk-bg-wrapper">
        <div class="kk-orb orb-1"></div>
        <div class="kk-orb orb-2"></div>
        <div class="kk-orb orb-3"></div>
    </div>
    <div class="kk-masked-content"><div class="kk-grad-base"></div><div class="kk-grad-overlay"></div></div>
    
    <div class="kk-content-layer">
        <div class="kk-info-text" id="info-text">
            <div class="kk-icon-ring"></div>
            <h2 id="info-title">Learning Paths</h2>
            <p id="info-desc">Select a category below to explore gamified maps.</p>
        </div>
        <div class="kk-sel-container">
            <div class="sel-btn c1 active" id="sel-1" onclick="handleCatSelection(1)"></div>
            <div class="sel-btn c2" id="sel-2" onclick="handleCatSelection(2)"></div>
            <div class="sel-btn c3" id="sel-3" onclick="handleCatSelection(3)"></div>
        </div>
        <div class="kk-activities-list">
            <h1 id="cat-title">Maps</h1>
            <div class="cat-scrollview" id="course-list-container"></div>
        </div>
        <div class="kk-close-x" onclick="toggleActivities(false)">✕</div>
    </div>
</div>

<!-- 2. GAME VIEW (Map + Quiz) -->
<div id="sisi-game-container">
    <div class="game-header">
        <button id="game-back-btn" class="header-btn" onclick="handleBack()">❮ Map</button>
        <span id="game-title">Select a Course</span>
        <div class="stats-pill">
            <div class="stat-badge fire">🔥 <span id="streak-count">0</span></div>
            <div class="stat-badge xp">⚡ <span id="xp-count">0</span> XP</div>
        </div>
    </div>
    <div id="sisi-map-view"><svg id="path-overlay"></svg></div>
    <div id="sisi-quiz-view">
        <div class="question-box" id="quiz-question">Loading...</div>
        <div class="options-grid" id="quiz-options"></div>
        <div class="quiz-footer">
            <button class="qz-quit-btn" onclick="quitQuiz()">Quit ✖️</button>
            <button class="skip-btn" onclick="skipQuestion()">Skip (-50 XP) ⏭️</button>
        </div>
    </div>
</div>

<script>
    const allLevelsData = <?php echo json_encode($journey_data); ?>;
    const availableCourses = <?php echo json_encode($course_list); ?>;
    const appContainer = document.getElementById('kk-app');
    const maskRect = document.getElementById('mask-main-rect');
    const closeBlob = document.getElementById('mask-close-blob');
    const tails = [document.getElementById('mask-tail-1'), document.getElementById('mask-tail-2'), document.getElementById('mask-tail-3')];

    const infoData = {
        1: { title: "Foundational Modules", desc: "Start your journey here with introductory maps." },
        2: { title: "Intermediate Explorations", desc: "Dive deeper into the core concepts." },
        3: { title: "Capstone Assessments", desc: "Test your mastery with final challenges." }
    };

    let activeAdminCourseId = null;
    let activeCourseLevels = [];
    let selectedLevelIdx = 0;
    let questionIndex = 0;
    let isProcessing = false;
    let streak = 0; let xp = 0;

    let courseProgress = JSON.parse(localStorage.getItem('sisi_course_progress')) || {};
    function loadCourseProgress(cid) { if (!courseProgress[cid]) courseProgress[cid] = { level: 0, q: 0 }; return courseProgress[cid]; }
    function saveCourseProgress(cid, lvl, q) { courseProgress[cid] = { level: lvl, q: q }; localStorage.setItem('sisi_course_progress', JSON.stringify(courseProgress)); }

    function handleCatSelection(id) {
        document.querySelectorAll('.sel-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('sel-' + id).classList.add('active');
        tails.forEach((t, i) => { t.setAttribute('cy', (i+1 === id) ? '275' : '285'); });

        const infoText = document.getElementById('info-text');
        infoText.style.opacity = 0;
        setTimeout(() => {
            document.getElementById('info-title').innerText = infoData[id].title;
            document.getElementById('info-desc').innerText = infoData[id].desc;
            infoText.style.opacity = 1;
        }, 300);
        setTimeout(() => { toggleActivities(true); loadCoursesForCategory(id); }, 800);
    }

    function toggleActivities(show) {
        if (show) {
            appContainer.classList.add('show-activities'); maskRect.setAttribute('height', '580'); closeBlob.setAttribute('cy', '610'); 
        } else {
            appContainer.classList.remove('show-activities'); maskRect.setAttribute('height', '275'); closeBlob.setAttribute('cy', '275'); 
        }
    }

    function loadCoursesForCategory(catId) {
        const container = document.getElementById('course-list-container');
        container.innerHTML = '';
        const filtered = availableCourses.filter(c => c.category_id == catId);
        
        if (filtered.length === 0 || !filtered.some(c => c.level_count > 0)) {
            container.innerHTML = `
                <div id="sisi-empty-state" style="display:flex; flex-direction:column; align-items:center; text-align:center; padding: 20px;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">🏗️</div>
                    <h3 style="color:#000; font-size: 1.5rem; margin:0; font-weight: 900;">No Quizzes Available</h3>
                    <p style="color:#333; font-size: 0.95rem; font-weight: 600; max-width: 280px; margin: 10px 0 20px 0;">This category doesn't have an interactive gamified path set up right now.</p>
                    <a href="manage_journey.php" class="header-btn" style="background:#F37021; padding: 12px 25px; border-radius:12px; text-decoration:none; color:white; font-weight:bold; box-shadow:0 10px 20px rgba(0,0,0,0.3); display:inline-block;">⚙️ Create Quizzes</a>
                </div>`;
        } else {
            filtered.forEach(c => {
                if(c.level_count > 0) {
                    container.innerHTML += `
                        <div class="course-row" onclick="selectCourse(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                            <div class="course-icon" style="color: #007AFF;">🗺️</div>
                            <div class="course-text"><h4>${c.name}</h4><p>🎮 ${c.level_count} Levels</p></div>
                            <div style="margin-left:auto; opacity:0.5; color:#000;">❯</div>
                        </div>`;
                }
            });
        }
    }

    function selectCourse(courseId, courseName) {
        appContainer.style.display = 'none';
        document.getElementById('sisi-game-container').style.display = 'flex';
        
        activeAdminCourseId = courseId;
        activeCourseLevels = allLevelsData.filter(l => l.course_id == courseId);
        
        // Strict state isolation per course
        let prog = loadCourseProgress(courseId);
        selectedLevelIdx = prog.level;
        questionIndex = prog.q;

        document.getElementById('game-title').innerText = courseName;
        document.getElementById('game-back-btn').innerText = '❮ Courses';
        document.getElementById('game-back-btn').onclick = () => {
            document.getElementById('sisi-game-container').style.display = 'none';
            appContainer.style.display = 'block';
        };

        const mapView = document.getElementById('sisi-map-view');
        document.getElementById('sisi-quiz-view').style.display = 'none';
        mapView.style.display = 'flex';
        mapView.innerHTML = '<svg id="path-overlay"></svg>';

        activeCourseLevels.forEach((level, idx) => {
            const isLocked = idx > selectedLevelIdx;
            const isCompleted = idx < selectedLevelIdx;
            const statusClass = isLocked ? 'locked' : (isCompleted ? 'completed' : 'current');
            const icon = isLocked ? '🔒' : (isCompleted ? '✓' : '⭐');

            mapView.innerHTML += `
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
        if (!svg || activeCourseLevels.length < 2) return;
        let html = '';
        const containerRect = document.getElementById('sisi-map-view').getBoundingClientRect();
        for (let i = 0; i < activeCourseLevels.length - 1; i++) {
            const startNode = document.querySelector(`#node-${i} .level-node`);
            const endNode = document.querySelector(`#node-${i+1} .level-node`);
            if (!startNode || !endNode) continue;

            const startX = (startNode.getBoundingClientRect().left + 37) - containerRect.left;
            const startY = (startNode.getBoundingClientRect().top + 37) - containerRect.top;
            const endX = (endNode.getBoundingClientRect().left + 37) - containerRect.left;
            const endY = (endNode.getBoundingClientRect().top + 37) - containerRect.top;

            const strokeClass = (i < selectedLevelIdx) ? 'game-path-line active' : 'game-path-line';
            const cpY = startY + (endY - startY) / 2;
            html += `<path class="${strokeClass}" d="M ${startX} ${startY} C ${startX} ${cpY}, ${endX} ${cpY}, ${endX} ${endY}" />`;
        }
        svg.innerHTML = html;
    }

    function openLevel(idx) {
        if (idx > selectedLevelIdx) return; 
        document.getElementById('sisi-map-view').style.display = 'none'; // Isolation
        document.getElementById('sisi-quiz-view').style.display = 'flex';
        document.getElementById('game-back-btn').innerText = '❮ Map';
        document.getElementById('game-back-btn').onclick = () => closeQuiz();
        renderQuestion();
    }

    function closeQuiz() {
        document.getElementById('sisi-quiz-view').style.display = 'none';
        document.getElementById('sisi-map-view').style.display = 'flex'; // Isolation restored
        document.getElementById('quiz-options').innerHTML = ''; // Prevent phantom questions
        selectCourse(activeAdminCourseId, document.getElementById('game-title').innerText);
    }

    function quitQuiz() {
        if (confirm("Quitting now will deduct 20 XP. Your exact question progress will be saved. Are you sure?")) {
            xp = Math.max(0, xp - 20); updateStats();
            saveCourseProgress(activeAdminCourseId, selectedLevelIdx, questionIndex);
            closeQuiz();
        }
    }

    function skipQuestion() {
        if (confirm("Skipping this question will deduct 50 XP. Are you sure?")) {
            xp = Math.max(0, xp - 50); streak = 0; updateStats();
            nextQuestion(true);
        }
    }

    function renderQuestion() {
        const qData = activeCourseLevels[selectedLevelIdx].questions[questionIndex];
        document.getElementById('game-title').innerText = `Question ${questionIndex + 1} / ${activeCourseLevels[selectedLevelIdx].questions.length}`;
        document.getElementById('quiz-question').innerText = qData.q;
        
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

    function checkAnswer(selectedIndex, btnElement, event) {
        if (isProcessing) return; isProcessing = true;
        const correctIndex = activeCourseLevels[selectedLevelIdx].questions[questionIndex].ans;
        const allBtns = document.querySelectorAll('.option-btn');
        allBtns.forEach(b => b.classList.add('disabled'));

        if (selectedIndex === correctIndex) {
            btnElement.classList.add('correct');
            streak++; xp += 100 + (streak * 10); updateStats();
            const floatEl = document.createElement('div');
            floatEl.className = 'floating-xp'; floatEl.innerText = `+${100 + (streak * 10)} XP 🔥`;
            floatEl.style.left = `${event.clientX - 30}px`; floatEl.style.top = `${event.clientY - 30}px`;
            document.body.appendChild(floatEl);
            setTimeout(() => floatEl.remove(), 1000);
            setTimeout(() => { isProcessing = false; nextQuestion(); }, 1000);
        } else {
            btnElement.classList.add('wrong');
            allBtns[correctIndex].classList.add('correct');
            streak = 0; updateStats();
            setTimeout(() => { isProcessing = false; nextQuestion(); }, 1500);
        }
    }

    function nextQuestion(skipped = false) {
        questionIndex++;
        if (questionIndex >= activeCourseLevels[selectedLevelIdx].questions.length) {
            selectedLevelIdx++; 
            questionIndex = 0;
            saveCourseProgress(activeAdminCourseId, selectedLevelIdx, 0);
            closeQuiz();
        } else {
            saveCourseProgress(activeAdminCourseId, selectedLevelIdx, questionIndex);
            renderQuestion();
        }
        isProcessing = false;
    }

    function updateStats() { document.getElementById('streak-count').innerText = streak; document.getElementById('xp-count').innerText = xp; }
    window.addEventListener('resize', () => { if(document.getElementById('sisi-map-view').style.display === 'flex') drawDynamicPaths(); });
    document.addEventListener("DOMContentLoaded", () => handleCatSelection(1));
</script>

<?php echo $OUTPUT->footer(); ?>