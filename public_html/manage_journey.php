<?php
// public_html/manage_journey.php
require_once('config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url('/manage_journey.php');
$PAGE->set_title('Manage Gamified Journey');
$PAGE->set_heading('Gamified Questions Manager');
$PAGE->set_pagelayout('standard');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['questions_json'])) {
        set_config('journey_data', trim($_POST['questions_json']), 'local_sisizathu');
    }
    if (isset($_POST['strict_progression'])) {
        set_config('journey_strict_progression', (int)$_POST['strict_progression'], 'local_sisizathu');
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'success']);
        die();
    }
    $success_msg = "Path successfully updated!";
}

$strict_mode = get_config('local_sisizathu', 'journey_strict_progression') ?: 0;

$current_data = get_config('local_sisizathu', 'journey_data');
if (!$current_data) $current_data = '[]';

global $DB;
$courses = $DB->get_records('course', null, 'sortorder', 'id, fullname');
$course_options = [];
foreach($courses as $c) {
    if ($c->id == SITEID) continue; 
    $course_options[] = ['id' => $c->id, 'name' => format_string($c->fullname)];
}

echo $OUTPUT->header();
?>

<style>
    .admin-manager-container {
        max-width: 1000px; margin: 2rem auto; padding: 30px;
        background: rgba(15, 15, 25, 0.8); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px;
        color: #F8FAFC; font-family: 'Poppins', sans-serif;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .admin-manager-container h2 { color: #fff; margin-bottom: 5px; font-weight: 800; }
    .admin-manager-container p { color: #CBD5E1; margin-bottom: 25px; font-size: 1rem; }

    .map-card { background: rgba(255, 255, 255, 0.02); border: 2px solid #FF3B30; border-radius: 16px; padding: 25px; margin-bottom: 30px; }
    .map-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 20px; }
    
    .level-card {
        background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px; padding: 20px; margin-bottom: 20px; transition: 0.3s;
    }
    .level-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    
    .question-card {
        background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 8px; padding: 15px; margin-bottom: 15px; position: relative;
    }

    .form-group { margin-bottom: 15px; }
    .form-group label { display: flex; align-items: center; font-size: 0.85rem; color: #CBD5E1; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin-manager-container input[type="text"], .admin-manager-container input[type="number"], .admin-manager-container select {
        width: 100%; padding: 14px 15px; background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff; border-radius: 8px; font-family: 'Inter', sans-serif; transition: 0.3s; font-size: 1rem; box-sizing: border-box;
    }
    .admin-manager-container input:focus, .admin-manager-container select:focus { border-color: #F37021; outline: none; box-shadow: 0 0 10px rgba(243, 112, 33, 0.3); }

    .sisi-custom-select { position: relative; width: 100%; cursor: pointer; user-select: none; margin-bottom: 15px; }
    .sisi-select-trigger {
        display: flex; justify-content: space-between; align-items: center; padding: 14px 15px; background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; color: #fff; transition: 0.3s; font-family: 'Inter', sans-serif; font-size: 1rem;
    }
    .sisi-select-trigger.active { border-color: #F37021; box-shadow: 0 0 15px rgba(243, 112, 33, 0.3); }
    .sisi-select-menu {
        position: absolute; top: calc(100% + 8px); left: 0; width: 100%; max-height: 250px; overflow-y: auto;
        background: rgba(20, 20, 30, 0.95); backdrop-filter: blur(20px); border: 1px solid #F37021; border-radius: 12px;
        z-index: 100; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.8); display: none;
    }
    .sisi-select-menu.open { display: block; animation: fadeUp 0.3s ease forwards; }
    .sisi-select-item { padding: 12px 20px; color: #CBD5E1; border-bottom: 1px solid rgba(255,255,255,0.05); transition: 0.2s; font-size: 0.95rem; }
    .sisi-select-item:hover { background: #F37021; color: #fff; }

    .info-icon { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: rgba(255,255,255,0.15); color: #fff; font-size: 0.75rem; font-weight: bold; cursor: pointer; margin-left: 8px; transition: 0.3s; }
    .info-icon:hover { background: #00CFFD; color: #000; box-shadow: 0 0 10px rgba(0,207,253,0.5); }
    .info-box { background: rgba(0, 207, 253, 0.1); border-left: 4px solid #00CFFD; padding: 15px; margin-top: 10px; border-radius: 0 8px 8px 0; font-size: 0.9rem; color: #E2E8F0; line-height: 1.5; display: none; font-weight: 400; text-transform:none; }
    .info-box.visible { display: block; animation: fadeIn 0.3s ease forwards; }

    .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px; }
    .option-input-wrapper { display: flex; align-items: center; gap: 10px; }
    .option-input-wrapper input[type="radio"] { width: 20px; height: 20px; accent-color: #25d366; cursor: pointer; }

    .sisi-btn-primary { background: #25d366; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 1.1rem; }
    .sisi-btn-primary:hover { background: #1da851; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4); }
    .sisi-btn-add { background: rgba(255, 255, 255, 0.1); color: white; padding: 8px 16px; border: 1px dashed rgba(255, 255, 255, 0.3); border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 0.9rem; margin-top: 10px; }
    .sisi-btn-add:hover { background: rgba(255, 255, 255, 0.2); border-color: #fff; }
    .sisi-btn-delete { background: rgba(255, 68, 68, 0.1); color: #ff4444; padding: 6px 12px; border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 0.8rem; }
    .sisi-btn-delete:hover { background: #ff4444; color: #fff; box-shadow: 0 0 10px rgba(255, 68, 68, 0.4); }

    .xp-badge { background: #FF9500; color: #000; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 800; margin-left: auto; margin-right: 15px; box-shadow: 0 2px 10px rgba(255, 149, 0, 0.4); }
    .course-xp-panel { background: rgba(37, 211, 102, 0.15); border: 1px solid #25d366; color: #fff; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; display: flex; justify-content: space-between; font-size: 1.1rem; }
    .alert-success { background: rgba(37, 211, 102, 0.15); border: 1px solid #25d366; color: #25d366; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    @media (max-width: 768px) {
        .admin-manager-container { margin: 1rem auto; padding: 20px; border-radius: 14px; width: 95%; box-sizing: border-box; }
        .map-card, .level-card, .question-card { padding: 15px; }
        .map-header, .level-header { flex-direction: column; align-items: flex-start; gap: 12px; }
        .map-header h3, .level-header h4 { font-size: 1.15rem; width: 100%; }
        .xp-badge { margin: 0 0 5px 0; align-self: flex-start; }
        .options-grid { grid-template-columns: 1fr; gap: 10px; }
        .course-xp-panel { flex-direction: column; gap: 8px; text-align: center; }
        .sisi-btn-delete { width: 100%; justify-content: center; }
    }

    @media (max-width: 480px) {
        .admin-manager-container { padding: 15px; margin: 0.5rem auto; width: 98%; }
        .admin-manager-container input[type="text"],
        .admin-manager-container input[type="number"] { padding: 12px; font-size: 0.95rem; }
        .sisi-btn-primary, .sisi-btn-add { font-size: 1rem; padding: 12px; }
        .option-input-wrapper { align-items: center; }
        .sisi-select-trigger { font-size: 0.9rem; padding: 12px; }
    }

    /* Custom Toast Notifications */
    #sisi-toast-container {
        position: fixed; top: 80px; right: 20px; z-index: 9999;
        display: flex; flex-direction: column; gap: 10px; pointer-events: none;
    }
    .sisi-toast {
        background: rgba(255, 59, 48, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        color: white; padding: 16px 24px; border-radius: 12px; font-weight: 600; font-size: 0.95rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2);
        transform: translateX(120%); opacity: 0; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex; align-items: center; gap: 12px; pointer-events: auto; max-width: 350px; line-height: 1.4;
    }
    .sisi-toast.show { transform: translateX(0); opacity: 1; }
    .sisi-toast.success { background: rgba(52, 199, 89, 0.95); }
    .sisi-toast-icon { font-size: 1.5rem; }
</style>

<div class="admin-manager-container">
    <h2>🎮 Gamified Journey Builder</h2>
    <p>Build your learning path visually. Organize Maps into Foundational Modules, Core Competencies, or Capstones.</p>

    <?php if (!empty($success_msg)): ?>
        <div class="alert-success">✓ <?php echo $success_msg; ?></div>
    <?php endif; ?>

    <div class="form-group" style="margin-bottom: 30px; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 12px; border: 1px solid #F37021; box-shadow: inset 0 0 20px rgba(0,0,0,0.5);">
        <label style="color: #F37021; font-size: 1.1rem; margin-bottom: 10px;">🎯 Select Course to Edit</label>
        
        <div class="sisi-custom-select" id="master-course-select">
            <div class="sisi-select-trigger" onclick="toggleDropdown(event, 'master-dropdown-menu')">
                <span id="master-selected-course">Select a Course...</span> ▼
            </div>
            <div class="sisi-select-menu" id="master-dropdown-menu"></div>
        </div>

        <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
        
        <label style="color: #00CFFD; font-size: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <span>🔒 Enforce Strict Progression<br><small style="color:#CBD5E1; font-weight:normal; font-size:0.8rem;">Users must finish previous maps/categories to unlock the next ones.</small></span>
            <input type="checkbox" id="strict-mode-toggle" <?php echo $strict_mode ? 'checked' : ''; ?> style="width:24px; height:24px; accent-color:#00CFFD; cursor:pointer;">
        </label>
    </div>

    <div id="course-xp-container"></div>
    <div id="visual-builder"></div>

    <button type="button" id="add-map-btn" class="sisi-btn-add" onclick="addMap()" style="width: 100%; padding: 15px; font-size: 1.1rem; margin-bottom: 30px; display: none; background: rgba(243, 112, 33, 0.15); border-color: #F37021; color: #F37021;">
        + Add New Map
    </button>

    <button type="button" id="save-db-btn" class="sisi-btn-primary" style="width: 100%; display: none;" onclick="saveToDB(false)">💾 Save Configuration to Database</button>
</div>

<script>
    let rawData = JSON.parse(<?php echo json_encode($current_data); ?>);
    let journeyState = [];
    
    // Migration from old simple levels to structured Maps
    if (rawData.length > 0 && typeof rawData[0].levels === 'undefined') {
        let mapId = 1;
        let courseLevels = {};
        rawData.forEach(lvl => {
            if(!courseLevels[lvl.course_id]) courseLevels[lvl.course_id] = [];
            lvl.questions.forEach(q => { if(!q.xp) q.xp = 100; });
            courseLevels[lvl.course_id].push(lvl);
        });
        for (let cid in courseLevels) {
            journeyState.push({
                id: mapId++, course_id: parseInt(cid), category_id: 1, 
                title: "Imported Map", desc: "Auto-migrated map",
                levels: courseLevels[cid].map(l => ({ offset: l.offset, questions: l.questions }))
            });
        }
    } else {
        journeyState = rawData;
    }

    const availableCourses = <?php echo json_encode($course_options); ?>;
    const catNames = { 1: "Foundational Modules (Phase 1)", 2: "Core Competencies (Phase 2)", 3: "Valedictory and Course Capstone (Phase 3)" };
    
    const builderDiv = document.getElementById('visual-builder');
    let activeAdminCourseId = <?php echo isset($_GET['course_id']) ? intval($_GET['course_id']) : 'null'; ?>;

    function render() {
        const masterCourseText = availableCourses.find(c => c.id == activeAdminCourseId)?.name || "Select a Course...";
        document.getElementById('master-selected-course').innerText = masterCourseText;

        let masterOptionsHtml = '';
        availableCourses.forEach(c => {
            masterOptionsHtml += `<div class="sisi-select-item" onclick="selectMasterCourse(${c.id}, '${c.name.replace(/'/g, "\\'")}')">${c.name}</div>`;
        });
        document.getElementById('master-dropdown-menu').innerHTML = masterOptionsHtml;

        builderDiv.innerHTML = '';
        document.getElementById('course-xp-container').innerHTML = '';

        if (!activeAdminCourseId) {
            builderDiv.innerHTML = '<div style="text-align:center; padding: 50px; color:#CBD5E1; background: rgba(255,255,255,0.05); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.2);">Select a course from the dropdown above to manage its gamified maps.</div>';
            document.getElementById('add-map-btn').style.display = 'none';
            document.getElementById('save-db-btn').style.display = 'none';
            return;
        }

        document.getElementById('add-map-btn').style.display = 'block';
        document.getElementById('save-db-btn').style.display = 'block';
        
        let courseTotalXP = 0;
        let mapsHtml = '';

        journeyState.forEach((map, mIdx) => {
            if (map.course_id != activeAdminCourseId) return; 

            let mapTotalXP = 0;
            let levelsHtml = '';

            map.levels.forEach((lvl, lIdx) => {
                let levelTotalXP = 0;
                let questionsHtml = '';
                
                lvl.questions.forEach((q, qIdx) => {
                    levelTotalXP += parseInt(q.xp) || 0;
                    const safeQ = q.q.replace(/"/g, '&quot;');
                    questionsHtml += `
                        <div class="question-card">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <label style="color:#fff; font-size:1rem;">Question ${qIdx + 1}</label>
                                <button type="button" class="sisi-btn-delete" onclick="deleteQuestion(${mIdx}, ${lIdx}, ${qIdx})">Delete Question</button>
                            </div>
                            <div class="form-group">
                                <input type="text" value="${safeQ}" placeholder="e.g. What is the capital of South Africa?" oninput="updateQuestion(${mIdx}, ${lIdx}, ${qIdx}, 'q', this.value)">
                            </div>
                            <div class="form-group">
                                <label style="color:#FF9500;">⚡ XP Value for this Question</label>
                                <input type="number" value="${q.xp || 100}" min="1" placeholder="e.g. 100" oninput="updateQuestion(${mIdx}, ${lIdx}, ${qIdx}, 'xp', this.value)">
                            </div>
                            <label style="margin-top: 15px;">Options & Correct Answer (Select Radio Button)</label>
                            <div class="options-grid">
                                ${q.options.map((opt, optIdx) => {
                                    const safeOpt = opt.replace(/"/g, '&quot;');
                                    return `
                                    <div class="option-input-wrapper">
                                        <input type="radio" name="ans_${mIdx}_${lIdx}_${qIdx}" ${q.ans === optIdx ? 'checked' : ''} onchange="updateQuestion(${mIdx}, ${lIdx}, ${qIdx}, 'ans', ${optIdx})">
                                        <input type="text" value="${safeOpt}" placeholder="Option ${optIdx + 1}" oninput="updateOption(${mIdx}, ${lIdx}, ${qIdx}, ${optIdx}, this.value)">
                                    </div>`;
                                }).join('')}
                            </div>
                        </div>
                    `;
                });

                mapTotalXP += levelTotalXP;
                levelsHtml += `
                    <div class="level-card">
                        <div class="level-header">
                            <h4 style="color:#fff; margin:0;">Level ${lIdx + 1}</h4>
                            <span class="xp-badge">XP: ${levelTotalXP}</span>
                            <button type="button" class="sisi-btn-delete" onclick="deleteLevel(${mIdx}, ${lIdx})">Delete Level</button>
                        </div>
                        <div class="form-group">
                            <label>Map Curve Offset (Visual) <span class="info-icon" onclick="toggleInfo('${mIdx}_${lIdx}')">?</span></label>
                            <input type="number" value="${lvl.offset}" oninput="updateLevel(${mIdx}, ${lIdx}, 'offset', this.value)">
                            <div class="info-box" id="info-box-${mIdx}_${lIdx}">This value dictates how the map curves (e.g. 0, -60, 60).</div>
                        </div>
                        <div class="questions-container">${questionsHtml}</div>
                        <button type="button" class="sisi-btn-add" onclick="addQuestion(${mIdx}, ${lIdx})">+ Add Question to Level ${lIdx + 1}</button>
                    </div>
                `;
            });

            courseTotalXP += mapTotalXP;
            const safeTitle = map.title.replace(/"/g, '&quot;');
            const safeDesc = map.desc.replace(/"/g, '&quot;');

            mapsHtml += `
                <div class="map-card">
                    <div class="map-header">
                        <h3 style="color:#fff; margin:0; display:flex; align-items:center;">🗺️ Map Configuration</h3>
                        <span class="xp-badge" style="background:#34C759; color:#fff;">Total Map XP: ${mapTotalXP}</span>
                        <button type="button" class="sisi-btn-delete" onclick="deleteMap(${mIdx})">Delete Map</button>
                    </div>
                    <div class="form-group">
                        <label>Map Title</label>
                        <input type="text" value="${safeTitle}" placeholder="e.g. Intro to Hardware" oninput="updateMap(${mIdx}, 'title', this.value)">
                    </div>
                    <div class="form-group">
                        <label>Map Description</label>
                        <input type="text" value="${safeDesc}" placeholder="A short description..." oninput="updateMap(${mIdx}, 'desc', this.value)">
                    </div>
                    <div class="form-group">
                        <label style="color:#00CFFD;">Map Phase Category</label>
                        <div class="sisi-custom-select">
                            <div class="sisi-select-trigger" onclick="toggleDropdown(event, 'cat-dropdown-menu-${mIdx}')" style="border-color: rgba(0, 207, 253, 0.3);">
                                <span>${catNames[map.category_id || 1]}</span> ▼
                            </div>
                            <div class="sisi-select-menu" id="cat-dropdown-menu-${mIdx}" style="border-color: #00CFFD;">
                                <div class="sisi-select-item" onclick="selectMapCategory(${mIdx}, 1)">${catNames[1]}</div>
                                <div class="sisi-select-item" onclick="selectMapCategory(${mIdx}, 2)">${catNames[2]}</div>
                                <div class="sisi-select-item" onclick="selectMapCategory(${mIdx}, 3)">${catNames[3]}</div>
                            </div>
                        </div>
                    </div>
                    <h4 style="color:#CBD5E1; margin-top:30px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:10px;">Levels in this Map</h4>
                    ${levelsHtml}
                    <button type="button" class="sisi-btn-add" onclick="addLevel(${mIdx})" style="width:100%; border-style:solid; background:rgba(255,255,255,0.05); padding:12px;">+ Add Level to Map</button>
                </div>
            `;
        });

        if (mapsHtml === '') {
            mapsHtml = '<div style="text-align:center; padding: 30px; color:#CBD5E1;">No maps added to this course yet. Click "+ Add New Map" below.</div>';
        } else {
            document.getElementById('course-xp-container').innerHTML = `
                <div class="course-xp-panel">
                    <span>Overall Course XP Potential:</span>
                    <span style="font-weight:900; color:#fff; text-shadow: 0 0 10px rgba(255,255,255,0.5);">⚡ ${courseTotalXP} XP</span>
                </div>
            `;
        }

        builderDiv.innerHTML = mapsHtml;
    }

    function toggleDropdown(event, menuId) {
        if(event) event.stopPropagation();
        // Close all other open dropdowns
        document.querySelectorAll('.sisi-select-menu').forEach(m => {
            if(m.id !== menuId) m.classList.remove('open');
        });
        document.getElementById(menuId).classList.toggle('open');
    }

    function selectMasterCourse(courseId, courseName) {
        activeAdminCourseId = courseId;
        render(); 
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sisi-custom-select')) {
            document.querySelectorAll('.sisi-select-menu').forEach(m => m.classList.remove('open'));
        }
    });

    function toggleInfo(id) { document.getElementById(`info-box-${id}`).classList.toggle('visible'); }
    
    function updateMap(mIdx, field, val) { journeyState[mIdx][field] = val; if(field==='category_id') render(); }
    
    function selectMapCategory(mIdx, catId) {
        updateMap(mIdx, 'category_id', catId);
    }

    function updateLevel(mIdx, lIdx, field, val) { journeyState[mIdx].levels[lIdx][field] = (field === 'offset') ? Number(val) : val; }
    function updateQuestion(mIdx, lIdx, qIdx, field, val) { 
        journeyState[mIdx].levels[lIdx].questions[qIdx][field] = (field === 'xp') ? Number(val) : val; 
        if(field === 'xp') render(); // Live update XP badge
    }
    function updateOption(mIdx, lIdx, qIdx, oIdx, val) { journeyState[mIdx].levels[lIdx].questions[qIdx].options[oIdx] = val; }

    function addMap() {
        const newId = journeyState.length > 0 ? Math.max(...journeyState.map(m => m.id)) + 1 : 1;
        journeyState.push({
            id: newId, course_id: activeAdminCourseId, category_id: 1, title: "New Map", desc: "A fun gamified path",
            levels: [{ offset: 60, questions: [{ q: "", options: ["", "", "", ""], ans: 0, xp: 100 }] }]
        });
        render();
    }
    function deleteMap(mIdx) { 
        if(confirm("Delete entire map?")) { 
            journeyState.splice(mIdx, 1); 
            render(); 
            saveToDB(true); 
        } 
    }

    function addLevel(mIdx) {
        const lvlCount = journeyState[mIdx].levels.length;
        const newOffset = (lvlCount % 2 === 0) ? 60 : -60; // Auto alternating!
        journeyState[mIdx].levels.push({
            offset: newOffset, questions: [{ q: "", options: ["", "", "", ""], ans: 0, xp: 100 }]
        });
        render();
    }
    
    function deleteLevel(mIdx, lIdx) { 
        if(journeyState[mIdx].levels.length <= 1) {
            showToast("A map must have at least 1 level. Delete the map instead.", false);
            return;
        }
        if(confirm("Delete level?")) { 
            journeyState[mIdx].levels.splice(lIdx, 1); 
            render(); 
            saveToDB(true); 
        } 
    }
    
    function addQuestion(mIdx, lIdx) { 
        journeyState[mIdx].levels[lIdx].questions.push({ q: "", options: ["", "", "", ""], ans: 0, xp: 100 }); 
        render(); 
    }
    
    function deleteQuestion(mIdx, lIdx, qIdx) { 
        if(journeyState[mIdx].levels[lIdx].questions.length <= 1) {
            showToast("A level must have at least 1 question. Delete the level instead.", false);
            return;
        }
        if(confirm("Delete Question?")) {
            journeyState[mIdx].levels[lIdx].questions.splice(qIdx, 1); 
            render(); 
            saveToDB(true); 
        } 
    }

    function showToast(message, isSuccess = false) {
        let container = document.getElementById('sisi-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'sisi-toast-container';
            document.body.appendChild(container);
        }
        
        const toast = document.createElement('div');
        toast.className = 'sisi-toast' + (isSuccess ? ' success' : '');
        toast.innerHTML = `<span class="sisi-toast-icon">${isSuccess ? '✅' : '⚠️'}</span> <div>${message}</div>`;
        
        container.appendChild(toast);
        
        // Trigger animation
        requestAnimationFrame(() => { toast.classList.add('show'); });
        
        // Remove after 4.5 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 4500);
    }

    async function saveToDB(isSilent = false) {
        let valid = true;
        let errorMsg = "";
        
        // INTERCEPTOR: Prevent manual saving if the course has 0 maps
        const courseMaps = journeyState.filter(m => m.course_id == activeAdminCourseId);
        if (!isSilent && courseMaps.length === 0) {
            showToast("Cannot save an empty course. Please add at least one Map.", false);
            return false;
        }
        
        outerLoop: for (let map of journeyState) {
            if(!map.title.trim()) { valid=false; errorMsg="Every map must have a title."; break; }
            if(!map.levels || map.levels.length===0) { valid=false; errorMsg=`The map "<b>${map.title}</b>" needs at least 1 level added to it.`; break; }
            for (let i = 0; i < map.levels.length; i++) {
                let lvl = map.levels[i];
                if (!lvl.questions || lvl.questions.length === 0) { valid = false; errorMsg = `Level ${i+1} in "<b>${map.title}</b>" is empty. It must have at least one question.`; break outerLoop; }
                for (let j = 0; j < lvl.questions.length; j++) {
                    let q = lvl.questions[j];
                    if (!q.q.trim()) { valid = false; errorMsg = `Question ${j+1} in Level ${i+1} cannot be empty. Please type a question.`; break outerLoop; }
                    if (!q.xp || isNaN(q.xp) || q.xp <= 0) { valid = false; errorMsg = `Question ${j+1} in Level ${i+1} must have a valid XP value greater than 0.`; break outerLoop; }
                    const filledOptions = q.options.filter(o => o.trim() !== '');
                    if (filledOptions.length < 4) { valid = false; errorMsg = `Question ${j+1} in Level ${i+1} is incomplete. Every question must have exactly 4 options typed in.`; break outerLoop; }
                }
            }
        }

        if (!valid) {
            showToast(errorMsg, false); // Always show errors, even on silent saves
            return false;
        }

        const formData = new FormData();
        formData.append('questions_json', JSON.stringify(journeyState));
        formData.append('strict_progression', document.getElementById('strict-mode-toggle').checked ? 1 : 0);

        try {
            const response = await fetch('manage_journey.php', { 
                method: 'POST', 
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) {
                if(!isSilent) showToast("Path successfully updated and saved to the database!", true);
                return true;
            }
        } catch(e) {
            if(!isSilent) showToast("Error communicating with database. Please check your connection.", false);
            return false;
        }
    }

    document.addEventListener("DOMContentLoaded", render);
</script>

<?php echo $OUTPUT->footer(); ?>