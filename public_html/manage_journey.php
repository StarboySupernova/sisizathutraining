```php
<?php
// public_html/manage_journey.php
require_once('config.php');

// Security: Only Site Administrators can access this page
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url('/manage_journey.php');
$PAGE->set_title('Manage Gamified Journey');
$PAGE->set_heading('Gamified Questions Manager');
$PAGE->set_pagelayout('standard');

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['questions_json'])) {
    $json_data = trim($_POST['questions_json']);
    set_config('journey_data', $json_data, 'local_sisizathu');
    $success_msg = "Questions successfully updated!";
}

// Fetch existing data or load defaults
$current_data = get_config('local_sisizathu', 'journey_data');
if (!$current_data) {
    // Default starter template
    $current_data = '[{"id":1,"course_id":2,"offset":0,"questions":[{"q":"What is the capital of South Africa?","options":["London","Berlin","Pretoria","Madrid"],"ans":2}]}]';
}

// Fetch all active Moodle courses for the dropdown
global $DB;
$courses = $DB->get_records('course', null, 'sortorder', 'id, fullname');
$course_options = [];
foreach($courses as $c) {
    if ($c->id == SITEID) continue; // Skip site home
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

    /* Builder UI */
    .level-card {
        background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px; padding: 20px; margin-bottom: 20px; transition: 0.3s;
    }
    .level-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding-bottom: 15px; margin-bottom: 15px; }
    .level-header h4 { margin: 0; color: #F37021; font-weight: 700; }
    
    .question-card {
        background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 8px; padding: 15px; margin-bottom: 15px; position: relative;
    }

    /* Inputs */
    .form-group { margin-bottom: 15px; }
    .form-group label { display: flex; align-items: center; font-size: 0.85rem; color: #CBD5E1; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .admin-manager-container input[type="text"], .admin-manager-container input[type="number"] {
        width: 100%; padding: 14px 15px; background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff; border-radius: 8px; font-family: 'Inter', sans-serif; transition: 0.3s; font-size: 1rem;
    }
    .admin-manager-container input:focus { border-color: #F37021; outline: none; box-shadow: 0 0 10px rgba(243, 112, 33, 0.3); }

    /* Custom Glassmorphism Dropdown */
    .sisi-custom-select { position: relative; width: 100%; cursor: pointer; user-select: none; }
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
    
    /* Scrollbar for Dropdown */
    .sisi-select-menu::-webkit-scrollbar { width: 8px; }
    .sisi-select-menu::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); border-radius: 12px;}
    .sisi-select-menu::-webkit-scrollbar-thumb { background: #F37021; border-radius: 12px; }

    /* Tooltip / Info Box */
    .info-icon {
        display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px;
        border-radius: 50%; background: rgba(255,255,255,0.15); color: #fff; font-size: 0.75rem; font-weight: bold;
        cursor: pointer; margin-left: 8px; transition: 0.3s;
    }
    .info-icon:hover { background: #00CFFD; color: #000; box-shadow: 0 0 10px rgba(0,207,253,0.5); }
    .info-box {
        background: rgba(0, 207, 253, 0.1); border-left: 4px solid #00CFFD; padding: 15px; margin-top: 10px;
        border-radius: 0 8px 8px 0; font-size: 0.9rem; color: #E2E8F0; line-height: 1.5; display: none; text-transform: none; font-weight: 400;
    }
    .info-box.visible { display: block; animation: fadeIn 0.3s ease forwards; }

    /* Grid for Options */
    .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px; }
    .option-input-wrapper { display: flex; align-items: center; gap: 10px; }
    .option-input-wrapper input[type="radio"] { width: 20px; height: 20px; accent-color: #25d366; cursor: pointer; }

    /* Buttons */
    .sisi-btn-primary { background: #25d366; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 1.1rem; }
    .sisi-btn-primary:hover { background: #1da851; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4); }
    .sisi-btn-add { background: rgba(255, 255, 255, 0.1); color: white; padding: 8px 16px; border: 1px dashed rgba(255, 255, 255, 0.3); border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 0.9rem; }
    .sisi-btn-add:hover { background: rgba(255, 255, 255, 0.2); border-color: #fff; }
    .sisi-btn-delete { background: rgba(255, 68, 68, 0.1); color: #ff4444; padding: 6px 12px; border: 1px solid rgba(255, 68, 68, 0.3); border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 0.8rem; }
    .sisi-btn-delete:hover { background: #ff4444; color: #fff; box-shadow: 0 0 10px rgba(255, 68, 68, 0.4); }

    .alert-success { background: rgba(37, 211, 102, 0.15); border: 1px solid #25d366; color: #25d366; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
    
    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="admin-manager-container">
    <h2>🎮 Gamified Journey Builder</h2>
    <p>Build your learning path visually. Changes are automatically converted and saved to the SisizathuTraining database.</p>

    <?php if (!empty($success_msg)): ?>
        <div class="alert-success">✓ <?php echo $success_msg; ?></div>
    <?php endif; ?>

    <!-- Master Course Selector -->
    <div class="form-group" style="margin-bottom: 30px; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 12px; border: 1px solid #F37021; box-shadow: inset 0 0 20px rgba(0,0,0,0.5);">
        <label style="color: #F37021; font-size: 1.1rem; margin-bottom: 10px;">🎯 Select Course to Edit</label>
        <div class="sisi-custom-select" id="master-course-select">
            <div class="sisi-select-trigger" onclick="toggleMasterDropdown(event)">
                <span id="master-selected-course">Select a Course...</span> ▼
            </div>
            <div class="sisi-select-menu" id="master-dropdown-menu">
                <!-- populated by JS -->
            </div>
        </div>
    </div>

    <!-- Visual Builder Container -->
    <div id="visual-builder"></div>

    <button type="button" id="add-level-btn" class="sisi-btn-add" onclick="addLevel()" style="width: 100%; padding: 15px; font-size: 1.1rem; margin-bottom: 30px; display: none;">
        + Add New Level
    </button>

    <!-- Hidden Form for actual submission -->
    <form method="POST" id="journey-form" onsubmit="prepareSubmit()">
        <textarea name="questions_json" id="hidden-json-output" style="display: none;"></textarea>
        <button type="submit" class="sisi-btn-primary" style="width: 100%;">💾 Save Configuration to Database</button>
    </form>
</div>

<script>
    let journeyState = JSON.parse(<?php echo json_encode($current_data); ?>);
    const availableCourses = <?php echo json_encode($course_options); ?>;
    const builderDiv = document.getElementById('visual-builder');
    
    // Check if we came from gamified_journey.php with a specific course
    const preselectedCourseId = <?php echo isset($_GET['course_id']) ? intval($_GET['course_id']) : 'null'; ?>;
    let activeAdminCourseId = preselectedCourseId;

    function render() {
        // 1. Render Master Dropdown
        const masterCourseText = availableCourses.find(c => c.id == activeAdminCourseId)?.name || "Select a Course...";
        document.getElementById('master-selected-course').innerText = masterCourseText;

        let masterOptionsHtml = '';
        availableCourses.forEach(c => {
            const safeName = c.name.replace(/'/g, "\\'");
            masterOptionsHtml += `<div class="sisi-select-item" onclick="selectMasterCourse(${c.id}, '${safeName}')">${c.name}</div>`;
        });
        document.getElementById('master-dropdown-menu').innerHTML = masterOptionsHtml;

        builderDiv.innerHTML = '';

        // 2. Hide builder if no course is selected
        if (!activeAdminCourseId) {
            builderDiv.innerHTML = '<div style="text-align:center; padding: 50px; color:#CBD5E1; background: rgba(255,255,255,0.05); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.2);">Select a course from the dropdown above to manage its gamified path.</div>';
            document.getElementById('add-level-btn').style.display = 'none';
            return;
        }

        document.getElementById('add-level-btn').style.display = 'block';
        let courseLevelCount = 0;

        // 3. Render only levels for the ACTIVE course
        journeyState.forEach((level, globalIdx) => {
            if (level.course_id != activeAdminCourseId) return; // Skip levels from other courses
            
            courseLevelCount++;

            // Build Questions
            let questionsHtml = '';
            level.questions.forEach((q, qIdx) => {
                // Escape quotes so they don't break the HTML inputs
                const safeQ = q.q.replace(/"/g, '&quot;');
                
                questionsHtml += `
                    <div class="question-card">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <label style="color:#fff; font-size:1rem;">Question ${qIdx + 1}</label>
                            <button type="button" class="sisi-btn-delete" onclick="deleteQuestion(${globalIdx}, ${qIdx})">Delete Question</button>
                        </div>
                        <div class="form-group">
                            <input type="text" value="${safeQ}" placeholder="e.g. What is the capital of South Africa?" oninput="updateQuestion(${globalIdx}, ${qIdx}, 'q', this.value)">
                        </div>
                        <label style="margin-top: 15px;">Options & Correct Answer (Select Radio Button)</label>
                        <div class="options-grid">
                            ${q.options.map((opt, optIdx) => {
                                const safeOpt = opt.replace(/"/g, '&quot;');
                                return `
                                <div class="option-input-wrapper">
                                    <input type="radio" name="ans_${globalIdx}_${qIdx}" ${q.ans === optIdx ? 'checked' : ''} onchange="updateQuestion(${globalIdx}, ${qIdx}, 'ans', ${optIdx})">
                                    <input type="text" value="${safeOpt}" placeholder="Option ${optIdx + 1}" oninput="updateOption(${globalIdx}, ${qIdx}, ${optIdx}, this.value)">
                                </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            });

            // Build Level Card
            const levelHtml = `
                <div class="level-card">
                    <div class="level-header">
                        <h4>Level ${courseLevelCount}</h4>
                        <button type="button" class="sisi-btn-delete" onclick="deleteLevel(${globalIdx})">Delete Level</button>
                    </div>
                    
                    <div class="form-group">
                        <label>Map Curve Offset (Visual) <span class="info-icon" onclick="toggleInfo(${globalIdx})">?</span></label>
                        <input type="number" value="${level.offset}" placeholder="e.g. -60 or 40" oninput="updateLevel(${globalIdx}, 'offset', this.value)">
                        <div class="info-box" id="info-box-${globalIdx}">
                            <strong>What does this do?</strong><br>
                            This value dictates how the progress map curves to reach this level node.<br>
                            • <strong>0</strong> = Center (Straight Line)<br>
                            • <strong>-60</strong> = Curves Left<br>
                            • <strong>60</strong> = Curves Right
                        </div>
                    </div>

                    <div class="questions-container">
                        ${questionsHtml}
                    </div>

                    <button type="button" class="sisi-btn-add" onclick="addQuestion(${globalIdx})">+ Add Question to Level ${courseLevelCount}</button>
                </div>
            `;
            
            builderDiv.innerHTML += levelHtml;
        });

        // Show empty state if course has zero levels
        if (courseLevelCount === 0) {
            builderDiv.innerHTML = '<div style="text-align:center; padding: 30px; color:#CBD5E1;">No levels added to this course yet. Click "+ Add New Level" below.</div>';
        }
    }

    // --- Custom Master Dropdown Handlers ---
    function toggleMasterDropdown(event) {
        if(event) event.stopPropagation();
        document.getElementById('master-dropdown-menu').classList.toggle('open');
    }

    function selectMasterCourse(courseId, courseName) {
        activeAdminCourseId = courseId;
        
        // Auto-spawn a level if they click a course that is completely empty
        if (!journeyState.some(l => l.course_id == activeAdminCourseId)) {
            addLevel(); // Adds level using activeAdminCourseId automatically
        } else {
            render();
        }
    }

    // Close dropdowns if clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#master-course-select')) {
            const menu = document.getElementById('master-dropdown-menu');
            if (menu) menu.classList.remove('open');
        }
    });

    // Info Box Handler
    function toggleInfo(lIdx) {
        document.getElementById(`info-box-${lIdx}`).classList.toggle('visible');
    }

    // --- State Modifiers ---
    function updateLevel(lIdx, field, val) {
        journeyState[lIdx][field] = (field === 'offset') ? Number(val) : val;
    }
    
    function updateQuestion(lIdx, qIdx, field, val) {
        journeyState[lIdx].questions[qIdx][field] = val;
    }

    function updateOption(lIdx, qIdx, oIdx, val) {
        journeyState[lIdx].questions[qIdx].options[oIdx] = val;
    }

    function addLevel() {
        // Find highest existing ID to avoid collisions
        const newId = journeyState.length > 0 ? Math.max(...journeyState.map(l => l.id)) + 1 : 1;
        journeyState.push({
            id: newId, course_id: activeAdminCourseId, offset: 0,
            questions: [{ q: "", options: ["", "", "", ""], ans: 0 }]
        });
        render();
    }

    function deleteLevel(lIdx) {
        if(confirm("Are you sure you want to delete this entire level?")) {
            journeyState.splice(lIdx, 1);
            render();
        }
    }

    function addQuestion(lIdx) {
        journeyState[lIdx].questions.push({ q: "", options: ["", "", "", ""], ans: 0 });
        render();
    }

    function deleteQuestion(lIdx, qIdx) {
        journeyState[lIdx].questions.splice(qIdx, 1);
        render();
    }

    // --- Form Submission ---
    function prepareSubmit() {
        // Saves the ENTIRE state (including other courses) back to the DB
        document.getElementById('hidden-json-output').value = JSON.stringify(journeyState);
    }

    // --- Initialization ---
    document.addEventListener("DOMContentLoaded", () => {
        // If we arrived from a specific course, and it has no levels yet, auto-add one!
        if (activeAdminCourseId && !journeyState.some(l => l.course_id == activeAdminCourseId)) {
            addLevel(); 
        } else {
            render();
        }
    });
</script>

<?php echo $OUTPUT->footer(); ?>