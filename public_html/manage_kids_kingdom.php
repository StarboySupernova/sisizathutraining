<?php
// public_html/manage_kids_kingdom.php
require_once('config.php');

// Security: Only Site Administrators can access this page
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url('/manage_kids_kingdom.php');
$PAGE->set_title('Manage Kids Kingdom Quiz');
$PAGE->set_heading('Kids Kingdom: Quiz Manager');
$PAGE->set_pagelayout('standard');

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['questions_json'])) {
    $json_data = trim($_POST['questions_json']);
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    
    if ($course_id > 0) {
        set_config('kids_kingdom_quiz_' . $course_id, $json_data, 'local_sisizathu');
    } else {
        set_config('kids_kingdom_quiz', $json_data, 'local_sisizathu'); // Default fallback
    }
    $success_msg = "Quiz questions successfully updated!";
}

// Fetch all active Moodle courses for the dropdown
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
        max-width: 900px; margin: 2rem auto; padding: 30px;
        background: rgba(15, 15, 25, 0.8); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px;
        color: #F8FAFC; font-family: 'Poppins', sans-serif;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .admin-manager-container h2 { color: #fff; margin-bottom: 5px; font-weight: 800; }
    .admin-manager-container p { color: #CBD5E1; margin-bottom: 25px; font-size: 1rem; }

    .question-card {
        background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px; padding: 20px; margin-bottom: 20px; position: relative;
        transition: 0.3s;
    }
    .question-card:hover { border-color: rgba(255, 149, 0, 0.4); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }

    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-size: 0.85rem; color: #CBD5E1; margin-bottom: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .admin-manager-container input[type="text"], .admin-manager-container select {
        width: 100%; padding: 12px 15px; background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff; border-radius: 8px; font-family: 'Inter', sans-serif; transition: 0.3s; font-size: 1rem;
    }
    .admin-manager-container input:focus, .admin-manager-container select:focus { border-color: #FF9500; outline: none; box-shadow: 0 0 10px rgba(255, 149, 0, 0.3); }

    .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .option-input-wrapper { display: flex; align-items: center; gap: 12px; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); }
    .option-input-wrapper input[type="radio"] { width: 22px; height: 22px; accent-color: #34C759; cursor: pointer; flex-shrink: 0; }

    .sisi-btn-primary { background: #34C759; color: white; padding: 15px 24px; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; }
    .sisi-btn-primary:hover { background: #28a745; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(52, 199, 89, 0.4); }
    
    .sisi-btn-add { background: rgba(255, 149, 0, 0.1); color: #FF9500; padding: 12px 20px; border: 1px dashed #FF9500; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 1rem; display: block; text-align: center; }
    .sisi-btn-add:hover { background: rgba(255, 149, 0, 0.2); }
    
    .sisi-btn-delete { background: rgba(255, 59, 48, 0.1); color: #FF3B30; padding: 6px 12px; border: 1px solid rgba(255, 59, 48, 0.3); border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 0.8rem; }
    .sisi-btn-delete:hover { background: #FF3B30; color: #fff; box-shadow: 0 0 10px rgba(255, 59, 48, 0.4); }

    .alert-success { background: rgba(52, 199, 89, 0.15); border: 1px solid #34C759; color: #34C759; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
</style>

<div class="admin-manager-container">
    <h2>🧸 Kids Kingdom Quiz Manager</h2>
    <p>Add, edit, or remove questions for the Bible Quiz activity. The radio button selects the correct answer.</p>

    <?php if (!empty($success_msg)): ?>
        <div class="alert-success">✓ <?php echo $success_msg; ?></div>
    <?php endif; ?>

    <div class="form-group" style="margin-bottom: 30px; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 12px; border: 1px solid #FF9500;">
        <label style="color: #FF9500; font-size: 1.1rem; margin-bottom: 10px;">🎯 Select Course for Quiz</label>
        <select id="course-selector" onchange="loadCourseData(this.value)">
            <option value="0">Global Default Quiz</option>
            <?php foreach($course_options as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="visual-builder"></div>

    <button type="button" class="sisi-btn-add" onclick="addQuestion()" style="width: 100%; margin-bottom: 30px;">
        + Add New Question
    </button>

    <form method="POST" id="quiz-form" onsubmit="prepareSubmit()">
        <input type="hidden" name="course_id" id="hidden-course-id" value="0">
        <textarea name="questions_json" id="hidden-json-output" style="display: none;"></textarea>
        <button type="submit" class="sisi-btn-primary" style="width: 100%;">💾 Save Quiz to Database</button>
    </form>
</div>

<script>
    const dbData = <?php 
        $all_data = ['0' => get_config('local_sisizathu', 'kids_kingdom_quiz')];
        foreach($course_options as $c) {
            $all_data[$c['id']] = get_config('local_sisizathu', 'kids_kingdom_quiz_' . $c['id']);
        }
        echo json_encode($all_data); 
    ?>;

    let quizState = [];

    function loadCourseData(courseId) {
        document.getElementById('hidden-course-id').value = courseId;
        const dataStr = dbData[courseId] || '[]';
        quizState = dataStr ? JSON.parse(dataStr) : [];
        
        if (quizState.length === 0 && courseId === "0") {
             quizState = [
                {"q": "Who built the ark to save the animals from the great flood?", "opts": ["Moses", "Noah", "Abraham", "David"], "ans": "Noah"},
                {"q": "What giant did David fight with a slingshot?", "opts": ["Goliath", "Saul", "Pharaoh", "Hercules"], "ans": "Goliath"},
                {"q": "Who was swallowed by a giant fish?", "opts": ["Peter", "Paul", "Jonah", "John"], "ans": "Jonah"}
            ];
        }
        
        quizState.forEach(q => { q.ansIdx = Math.max(0, q.opts.indexOf(q.ans)); });
        render();
    }

    const builderDiv = document.getElementById('visual-builder');

    function render() {
        builderDiv.innerHTML = '';
        
        quizState.forEach((q, qIdx) => {
            const safeQ = q.q.replace(/"/g, '&quot;');
            let optionsHtml = '';
            
            q.opts.forEach((opt, optIdx) => {
                const safeOpt = opt.replace(/"/g, '&quot;');
                optionsHtml += `
                    <div class="option-input-wrapper">
                        <input type="radio" name="ans_${qIdx}" ${q.ansIdx === optIdx ? 'checked' : ''} onchange="updateCorrect(${qIdx}, ${optIdx})">
                        <input type="text" value="${safeOpt}" placeholder="Option ${optIdx + 1}" oninput="updateOption(${qIdx}, ${optIdx}, this.value)">
                    </div>
                `;
            });

            builderDiv.innerHTML += `
                <div class="question-card">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <label style="color:#fff; font-size:1.1rem; margin:0;">Question ${qIdx + 1}</label>
                        <button type="button" class="sisi-btn-delete" onclick="deleteQuestion(${qIdx})">Delete</button>
                    </div>
                    <div class="form-group">
                        <input type="text" value="${safeQ}" placeholder="e.g. Who built the ark?" oninput="updateQuestion(${qIdx}, this.value)">
                    </div>
                    <label style="margin-top: 15px;">Options & Correct Answer</label>
                    <div class="options-grid">
                        ${optionsHtml}
                    </div>
                </div>
            `;
        });

        if (quizState.length === 0) {
            builderDiv.innerHTML = '<div style="text-align:center; padding: 30px; color:#CBD5E1;">No questions added yet. Click "+ Add New Question" below.</div>';
        }
    }

    function updateQuestion(qIdx, val) { quizState[qIdx].q = val; }
    function updateOption(qIdx, optIdx, val) { quizState[qIdx].opts[optIdx] = val; }
    function updateCorrect(qIdx, optIdx) { quizState[qIdx].ansIdx = optIdx; }
    function deleteQuestion(qIdx) { 
        if(confirm("Delete this question?")) {
            quizState.splice(qIdx, 1); 
            render(); 
        }
    }
    function addQuestion() {
        quizState.push({ q: "", opts: ["", "", "", ""], ansIdx: 0 });
        render();
    }
    
    function prepareSubmit() {
        const exportData = quizState.map(q => ({
            q: q.q,
            opts: q.opts,
            ans: q.opts[q.ansIdx] || q.opts[0]
        }));
        document.getElementById('hidden-json-output').value = JSON.stringify(exportData);
    }

    document.addEventListener("DOMContentLoaded", () => loadCourseData("0"));
</script>

<?php echo $OUTPUT->footer(); ?>