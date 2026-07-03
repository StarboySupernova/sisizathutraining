<?php
// public_html/gamified_journey.php
require_once('config.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/gamified_journey.php');
$PAGE->set_title('Gamified Learning Hub');
$PAGE->set_heading('Interactive Certification Path');
$PAGE->set_pagelayout('standard'); 

// Fetch journey data to determine level counts and newest additions
$journey_data_json = get_config('local_sisizathu', 'journey_data') ?: '[]';
$journey_data = json_decode($journey_data_json, true);

$course_level_counts = [];
$latest_course_id = null;
if (is_array($journey_data)) {
    foreach ($journey_data as $level) {
        $cid = $level['course_id'];
        if (!isset($course_level_counts[$cid])) {
            $course_level_counts[$cid] = 0;
        }
        $course_level_counts[$cid]++;
        $latest_course_id = $cid; // The last one in the JSON array is the most recently added
    }
}

// Fetch ALL Moodle courses (removed visible=1 restriction)
global $DB;
$courses = $DB->get_records_select('course', 'id != ?', array(SITEID), 'fullname ASC', 'id, fullname');
$course_list = [];
foreach ($courses as $c) {
    $count = isset($course_level_counts[$c->id]) ? $course_level_counts[$c->id] : 0;
    $course_list[] = [
        'id' => $c->id, 
        'name' => format_string($c->fullname),
        'level_count' => $count,
        'is_new' => ($c->id === $latest_course_id)
    ];
}

// Sort: 1. Courses with levels at the top. 2. Alphabetical fallback.
usort($course_list, function($a, $b) {
    if ($a['level_count'] > 0 && $b['level_count'] == 0) return -1;
    if ($a['level_count'] == 0 && $b['level_count'] > 0) return 1;
    return strcmp($a['name'], $b['name']);
});

echo $OUTPUT->header();
?>

<style>
    #sisi-game-container {
        width: 100%; max-width: 850px; margin: 2rem auto; 
        height: calc(100vh - 140px); min-height: 600px;
        display: flex; flex-direction: column;
        background: rgba(15, 15, 25, 0.75); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.6); overflow: hidden;
        color: #F8FAFC !important; font-family: 'Poppins', sans-serif;
    }

    .game-header {
        padding: 18px 25px; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); 
        font-size: 1.3rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; color: #fff; z-index: 20;
    }
    .header-btn { background: rgba(255,255,255,0.1); border: none; color: white; padding: 8px 16px; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.3s; display: none; }
    .header-btn:hover { background: #F37021; transform: scale(1.05); }
    
    .stats-pill { display: flex; gap: 15px; font-size: 0.95rem; font-weight: 700; }
    .stat-badge { background: rgba(255,255,255,0.08); padding: 5px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 6px; }
    .stat-badge.fire { color: #ffb347; border-color: rgba(255, 179, 71, 0.3); }
    .stat-badge.xp { color: #00CFFD; border-color: rgba(0, 207, 253, 0.3); }

    /* SCREEN 1: COURSE LIST */
    #sisi-course-view { padding: 30px; overflow-y: auto; flex-grow: 1; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; align-content: start; }
    .course-hub-card {
        background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 25px;
        cursor: pointer; transition: all 0.3s ease; display: flex; flex-direction: column; justify-content: space-between; min-height: 180px;
    }
    .course-hub-card:hover { transform: translateY(-6px); background: rgba(243, 112, 33, 0.15); border-color: #F37021; box-shadow: 0 10px 25px rgba(243, 112, 33, 0.3); }
    .course-hub-card h3 { font-size: 1.3rem; margin: 0 0 10px 0; color: #fff; line-height: 1.4; }
    .course-hub-card .level-count { font-size: 0.85rem; color: #CBD5E1; background: rgba(0,0,0,0.4); padding: 4px 10px; border-radius: 12px; width: fit-content; }

    /* SCREEN 2: MAP & PLACEHOLDER */
    #sisi-map-view { flex-grow: 1; position: relative; padding: 40px 0; display: none; flex-direction: column; justify-content: space-evenly; align-items: center; overflow-y: auto; }
    #path-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; }
    .game-path-line { fill: none; stroke: rgba(255,255,255,0.15); stroke-width: 6; stroke-linecap: round; stroke-dasharray: 2 18; }
    .game-path-line.active { stroke: #25d366; }
    .level-wrapper { position: relative; display: flex; justify-content: center; align-items: center; z-index: 10; width: 100%; }
    .level-node { width: 75px; height: 75px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; cursor: pointer; transition: 0.3s; position: relative; box-shadow: inset 0 0 10px rgba(0,0,0,0.5); }
    .level-node.locked { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.3); border: 3px solid rgba(255,255,255,0.1); }
    .level-node.current { background: #25d366; color: white; box-shadow: 0 0 30px rgba(37, 211, 102, 0.5); transform: scale(1.15); border: 4px solid #fff; }
    .level-node.completed { background: #F37021; color: white; border: 4px solid #fff; }

    /* Placeholder */
    #sisi-empty-state { display: none; flex-direction: column; align-items: center; justify-content: center; flex-grow: 1; text-align: center; padding: 40px; }
    #sisi-empty-state h3 { font-size: 1.8rem; color: #fff; margin-bottom: 10px; }
    #sisi-empty-state p { color: #CBD5E1; max-width: 400px; margin-bottom: 25px; }

    /* SCREEN 3: QUIZ VIEW */
    #sisi-quiz-view { padding: 30px; display: none; flex-direction: column; flex-grow: 1; justify-content: space-between; animation: fadeIn 0.3s ease; }
    .question-box { font-size: 1.5rem; text-align: center; margin: 20px 0; font-weight: 600; color: #fff; }
    .options-grid { display: flex; flex-direction: column; gap: 14px; }
    .option-btn { background: rgba(255,255,255,0.06); border: 2px solid rgba(255,255,255,0.12); padding: 18px; border-radius: 14px; color: #fff; font-size: 1.15rem; cursor: pointer; transition: 0.2s; text-align: center; font-weight: 500; }
    .option-btn:hover:not(.disabled) { background: rgba(243, 112, 33, 0.25); border-color: #F37021; transform: scale(1.02); }
    .option-btn.correct { background: #25d366 !important; border-color: #25d366 !important; box-shadow: 0 0 20px rgba(37, 211, 102, 0.5); }
    .option-btn.wrong { background: #ff4444 !important; border-color: #ff4444 !important; animation: shake 0.4s; }
    .option-btn.disabled { pointer-events: none; opacity: 0.6; }

    .quiz-footer { display: flex; justify-content: flex-end; padding-top: 20px; }
    .skip-btn { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px 24px; border-radius: 12px; cursor: pointer; font-weight: 700; transition: 0.3s; }
    .skip-btn:hover { background: #00CFFD; color: #000; }

    /* Floating XP Animation */
    .floating-xp { position: absolute; font-weight: 800; font-size: 1.5rem; color: #25d366; pointer-events: none; animation: floatUp 1s ease forwards; z-index: 100; }
    @keyframes floatUp { 0% { opacity: 1; transform: translateY(0) scale(1); } 100% { opacity: 0; transform: translateY(-60px) scale(1.3); } }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div id="sisi-game-container">
    <div class="game-header">
        <button id="game-back-btn" class="header-btn" onclick="handleBack()">❮ Courses</button>
        <span id="game-title">Select a Course</span>
        <div class="stats-pill">
            <div class="stat-badge fire">🔥 <span id="streak-count">0</span></div>
            <div class="stat-badge xp">⚡ <span id="xp-count">0</span> XP</div>
        </div>
    </div>

    <!-- VIEW 1: COURSE HUB -->
    <div id="sisi-course-view"></div>

    <!-- VIEW 2: MAP -->
    <div id="sisi-map-view">
        <svg id="path-overlay"></svg>
    </div>

    <!-- VIEW 2.5: EMPTY STATE -->
    <div id="sisi-empty-state">
        <div style="font-size: 4rem; margin-bottom: 15px;">🏗️</div>
        <h3>No Quizzes Available Yet</h3>
        <p>This course doesn't have an interactive gamified path set up right now.</p>
        <a href="manage_journey.php" id="create-quizzes-btn" class="header-btn" style="display:inline-block; background:#F37021; padding: 12px 25px; text-decoration:none;">⚙️ Create Quizzes for this Course</a>
    </div>

    <!-- VIEW 3: QUIZ -->
    <div id="sisi-quiz-view">
        <div class="question-box" id="quiz-question">Loading...</div>
        <div class="options-grid" id="quiz-options"></div>
        <div class="quiz-footer">
            <button class="skip-btn" onclick="nextQuestion()">Skip Question ⏭️</button>
        </div>
    </div>
</div>

<script>
    const allLevelsData = <?php echo get_config('local_sisizathu', 'journey_data') ?: '[]'; ?>;
    const availableCourses = <?php echo json_encode($course_list); ?>;

    let currentScreen = 'courses';
    let activeCourseLevels = [];
    let selectedLevelIdx = 0;
    let questionIndex = 0;
    let isProcessing = false;
    let streak = 0;
    let xp = 0;

    // 1. RENDER COURSE HUB
    function renderCourseHub() {
        currentScreen = 'courses';
        document.getElementById('game-title').innerText = 'Select a Course';
        document.getElementById('game-back-btn').style.display = 'none';
        document.getElementById('sisi-map-view').style.display = 'none';
        document.getElementById('sisi-quiz-view').style.display = 'none';
        document.getElementById('sisi-empty-state').style.display = 'none';
        
        const courseView = document.getElementById('sisi-course-view');
        courseView.style.display = 'grid';
        courseView.innerHTML = '';

        availableCourses.forEach(c => {
            // Check if this course has the newest level added
            const newBadge = c.is_new ? `<span style="background:#ff4444; color:#fff; padding:3px 10px; border-radius:12px; font-size:0.7rem; font-weight:800; margin-left:10px; vertical-align:middle; box-shadow: 0 0 10px rgba(255,68,68,0.5);">NEW</span>` : '';
            
            courseView.innerHTML += `
                <div class="course-hub-card" onclick="selectCourse(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                    <div>
                        <h3 style="display:flex; align-items:center; flex-wrap:wrap;">${c.name} ${newBadge}</h3>
                        <div class="level-count">🎮 ${c.level_count} Gamified Levels</div>
                    </div>
                    <div style="margin-top:20px; color:#F37021; font-weight:700; font-size:0.9rem;">Play Journey ➔</div>
                </div>
            `;
        });
    }

    // 2. SELECT COURSE & RENDER MAP
    function selectCourse(courseId, courseName) {
        currentScreen = 'map';
        activeCourseLevels = allLevelsData.filter(l => l.course_id == courseId);
        
        document.getElementById('game-title').innerText = courseName;
        document.getElementById('game-back-btn').style.display = 'block';
        document.getElementById('game-back-btn').innerText = '❮ Courses';
        document.getElementById('sisi-course-view').style.display = 'none';

        if (activeCourseLevels.length === 0) {
            document.getElementById('sisi-empty-state').style.display = 'flex';
            document.getElementById('create-quizzes-btn').href = `manage_journey.php?course_id=${courseId}`;
            return;
        }

        const mapView = document.getElementById('sisi-map-view');
        mapView.style.display = 'flex';
        mapView.innerHTML = '<svg id="path-overlay"></svg>';

        activeCourseLevels.forEach((level, idx) => {
            // Logic: Level 0 is unlocked. Subsequent levels unlock as previous ones finish.
            const isLocked = idx > selectedLevelIdx;
            const isCurrent = idx === selectedLevelIdx;
            const isCompleted = idx < selectedLevelIdx;

            let icon = isLocked ? '🔒' : (isCompleted ? '✓' : '⭐');
            let statusClass = isLocked ? 'locked' : (isCompleted ? 'completed' : 'current');

            mapView.innerHTML += `
                <div class="level-wrapper" id="node-${idx}">
                    <div style="position:relative; transform: translateX(${level.offset || 0}px)">
                        <div class="level-node ${statusClass}" onclick="openLevel(${idx})">${icon}</div>
                    </div>
                </div>
            `;
        });

        setTimeout(drawDynamicPaths, 50);
    }

    // Draw S-Curves
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

    // 3. OPEN QUIZ
    function openLevel(idx) {
        if (idx > selectedLevelIdx) return; // Locked
        currentScreen = 'quiz';
        questionIndex = 0;
        
        document.getElementById('sisi-map-view').style.display = 'none';
        document.getElementById('sisi-quiz-view').style.display = 'flex';
        document.getElementById('game-back-btn').innerText = '❮ Map';
        renderQuestion();
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
        if (isProcessing) return;
        isProcessing = true;

        const correctIndex = activeCourseLevels[selectedLevelIdx].questions[questionIndex].ans;
        const allBtns = document.querySelectorAll('.option-btn');
        allBtns.forEach(b => b.classList.add('disabled'));

        if (selectedIndex === correctIndex) {
            btnElement.classList.add('correct');
            streak++;
            xp += 100 + (streak * 10);
            updateStats();

            // Spawn floating XP text at mouse click
            const floatEl = document.createElement('div');
            floatEl.className = 'floating-xp';
            floatEl.innerText = `+${100 + (streak * 10)} XP 🔥`;
            floatEl.style.left = `${event.clientX - 30}px`;
            floatEl.style.top = `${event.clientY - 30}px`;
            document.body.appendChild(floatEl);
            setTimeout(() => floatEl.remove(), 1000);

            setTimeout(() => { isProcessing = false; nextQuestion(); }, 1000);
        } else {
            btnElement.classList.add('wrong');
            allBtns[correctIndex].classList.add('correct');
            streak = 0;
            updateStats();
            setTimeout(() => { isProcessing = false; nextQuestion(); }, 1500);
        }
    }

    function nextQuestion() {
        questionIndex++;
        if (questionIndex >= activeCourseLevels[selectedLevelIdx].questions.length) {
            selectedLevelIdx++; // Unlock next map node
            selectCourse(activeCourseLevels[0].course_id, document.getElementById('game-title').innerText);
        } else {
            renderQuestion();
        }
    }

    function updateStats() {
        document.getElementById('streak-count').innerText = streak;
        document.getElementById('xp-count').innerText = xp;
    }

    function handleBack() {
        if (currentScreen === 'quiz') {
            selectCourse(activeCourseLevels[0].course_id, "Progress Map");
        } else if (currentScreen === 'map') {
            renderCourseHub();
        }
    }

    window.addEventListener('resize', drawDynamicPaths);
    document.addEventListener("DOMContentLoaded", renderCourseHub);
</script>

<?php echo $OUTPUT->footer(); ?>