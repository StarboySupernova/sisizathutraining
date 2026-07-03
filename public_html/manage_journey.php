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
    // Save to Moodle's config table under a custom namespace
    set_config('journey_data', $json_data, 'local_sisizathu');
    $success_msg = "Questions successfully updated!";
}

// Fetch existing data or load defaults
$current_data = get_config('local_sisizathu', 'journey_data');
if (!$current_data) {
    // Default fallback data if empty
    $current_data = '[{"id":1,"course_id":2,"offset":-80,"questions":[{"q":"What is the capital of France?","options":["London","Berlin","Paris","Madrid"],"ans":2}]}]';
}

echo $OUTPUT->header();
?>

<style>
    .admin-manager-container {
        max-width: 900px; margin: 2rem auto; padding: 30px;
        background: rgba(15, 15, 25, 0.8); backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px;
        color: #fff; font-family: 'Poppins', sans-serif;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .admin-manager-container h2 { color: #F37021; margin-bottom: 20px; }
    .admin-manager-container textarea {
        width: 100%; height: 400px; background: rgba(0,0,0,0.5); color: #00CFFD;
        border: 1px solid rgba(255,255,255,0.2); border-radius: 8px;
        padding: 15px; font-family: monospace; font-size: 14px;
    }
    .sisi-save-btn {
        background: #25d366; color: white; padding: 12px 24px; border: none;
        border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px;
        font-size: 1.1rem; transition: 0.3s;
    }
    .sisi-save-btn:hover { background: #1da851; transform: translateY(-2px); }
    .alert-success { background: rgba(37, 211, 102, 0.2); border-left: 4px solid #25d366; color: white; padding: 15px; margin-bottom: 20px; }
</style>

<div class="admin-manager-container">
    <h2>⚙️ Gamified Journey Manager</h2>
    <p style="color:#CBD5E1; margin-bottom: 20px;">
        Edit your levels, questions, and assign them to specific Course IDs. 
        Ensure your JSON syntax is valid before saving.
    </p>

    <?php if (!empty($success_msg)): ?>
        <div class="alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <form method="POST">
        <textarea name="questions_json" spellcheck="false"><?php echo htmlspecialchars($current_data); ?></textarea>
        <button type="submit" class="sisi-save-btn">💾 Save Configuration</button>
    </form>
</div>

<?php echo $OUTPUT->footer(); ?>