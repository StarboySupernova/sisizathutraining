<?php
// public_html/kids_kingdom.php
require_once('config.php');

// Handle silent AJAX save progress
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_qidx'])) {
    require_login();
    set_user_preference('kids_kingdom_qidx', (int)$_POST['save_qidx']);
    die('saved');
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/kids_kingdom.php');
$PAGE->set_title('Kids Kingdom');
$PAGE->set_heading('Kids Kingdom');
$PAGE->set_pagelayout('standard'); 

$saved_qidx = (isloggedin() && !isguestuser()) ? get_user_preferences('kids_kingdom_qidx', 0) : 0;
$is_logged_in = (isloggedin() && !isguestuser()) ? 'true' : 'false';

// Fetch dynamic quiz questions from DB
$kids_quiz_json = get_config('local_sisizathu', 'kids_kingdom_quiz');
if (!$kids_quiz_json) {
    $kids_quiz_json = json_encode([
        ["q" => "Who built the ark to save the animals from the great flood?", "opts" => ["Moses", "Noah", "Abraham", "David"], "ans" => "Noah"],
        ["q" => "What giant did David fight with a slingshot?", "opts" => ["Goliath", "Saul", "Pharaoh", "Hercules"], "ans" => "Goliath"],
        ["q" => "Who was swallowed by a giant fish?", "opts" => ["Peter", "Paul", "Jonah", "John"], "ans" => "Jonah"],
        ["q" => "Where was baby Jesus born?", "opts" => ["A castle", "A hospital", "A stable", "A house"], "ans" => "A stable"],
        ["q" => "What did God use to create the first woman, Eve?", "opts" => ["A flower", "Adam's rib", "Clay", "A star"], "ans" => "Adam's rib"]
    ]);
}

echo $OUTPUT->header();
?>

<style>
    /* --- 1. CORE APP CONTAINER --- */
    /* Locked to 400x800 to perfectly map SwiftUI's coordinate system */
    #kk-app {
        width: 100%; max-width: 400px; height: 800px; margin: 2rem auto;
        position: relative; overflow: hidden; border-radius: 40px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.6);
        font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
        background-color: #111;
        user-select: none;
    }

    /* --- 2. BACKGROUND ORBS (SwiftUI ZStack Background) --- */
    .kk-bg-wrapper { position: absolute; inset: 0; z-index: 1; }
    .kk-orb { position: absolute; border-radius: 50%; filter: blur(40px); opacity: 0.6; mix-blend-mode: screen; animation: orbFloat 8s infinite alternate ease-in-out; }
    .orb-1 { width: 300px; height: 300px; background: #FF9500; bottom: -50px; left: -100px; }
    .orb-2 { width: 250px; height: 250px; background: #FF2D55; top: 30%; right: -50px; animation-delay: -3s; }
    .orb-3 { width: 200px; height: 200px; background: #34C759; bottom: 20%; left: 40%; animation-delay: -5s; }
    @keyframes orbFloat { 0% { transform: translate(0,0); } 100% { transform: translate(30px, -40px); } }

    /* --- 3. THE UNIFIED GOOEY MASK LAYER --- */
    /* This perfectly mimics SwiftUI's .mask(canvas) as one continuous shape */
    .kk-masked-content {
        position: absolute; inset: 0; z-index: 10;
        mask: url(#swiftui-canvas-mask);
        -webkit-mask: url(#swiftui-canvas-mask);
    }
    
    /* The Gradients inside the mask */
    .kk-grad-base {
        position: absolute; inset: 0; width: 100%; height: 100%;
        background: linear-gradient(135deg, #007AFF, #AF52DE, #FF2D55); /* .blue, .purple, .pink */
        transition: 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .kk-grad-overlay {
        position: absolute; bottom: 0; left: 0; width: 100%; height: 0%;
        background: linear-gradient(180deg, #FFD60A, #34C759, #00CFFD); /* .yellow, .green, .teal */
        transition: height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    #kk-app.show-activities .kk-grad-overlay { height: 60%; }

    /* --- 4. INTERACTIVE CONTENT (Z-Index above Mask) --- */
    .kk-content-layer { position: absolute; inset: 0; z-index: 20; pointer-events: none; }

    /* Info Text (Welcome / Bible Adventures) */
    .kk-info-text {
        position: absolute; top: 70px; width: 100%; text-align: center; color: white;
        padding: 0 30px; box-sizing: border-box; transition: all 0.4s ease; pointer-events: auto;
    }
    #kk-app.show-activities .kk-info-text { opacity: 0; transform: translateY(-30px); pointer-events: none; }
    .kk-icon-ring { width: 40px; height: 40px; margin: 0 auto 15px auto; border-radius: 50%; border: 2px solid white; position: relative; }
    .kk-icon-ring::after { content: ''; position: absolute; inset: 6px; border-radius: 50%; border: 1px solid white; }
    .kk-info-text h2 { font-size: 2.2rem; font-weight: 800; line-height: 1.1; margin-bottom: 15px; color: #fff;}
    .kk-info-text p { font-size: 1.1rem; line-height: 1.4; opacity: 0.9; }

   /* Selection Circles */
    .kk-sel-container {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s; 
        pointer-events: none; z-index: 20;
    }
    #kk-app.show-activities .kk-sel-container { transform: scale(0); opacity: 0; }
    
    .sel-btn {
        position: absolute; width: 44px; height: 44px; border-radius: 50%; cursor: pointer; 
        pointer-events: auto; display: flex; align-items: center; justify-content: center;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); border: 3px solid transparent;
    }
    
    /* Align exactly with SVG mask cx/cy (cx-22, cy-22 to mathematically center the 44px button) */
    .sel-btn.c1 { background: #FF3B30; left: calc(85px - 22px); top: calc(275px - 22px); }
    .sel-btn.c2 { background: #AF52DE; left: calc(200px - 22px); top: calc(275px - 22px); }
    .sel-btn.c3 { background: #34C759; left: calc(315px - 22px); top: calc(275px - 22px); }
    
    /* Mimic SwiftUI Tag opacity, scaling, and drop to perfectly match the 285px inactive tail */
    .sel-btn:not(.active) { opacity: 0.7; transform: scale(0.85) translateY(12px); }
    .sel-btn:active { transform: scale(0.9); }

    /* Activities List (Fades in over the expanded mask) */
    .kk-activities-list {
        position: absolute; top: 220px; left: 16px; width: 368px;
        padding: 20px; box-sizing: border-box;
        transform: translateY(40px); opacity: 0; pointer-events: none;
        transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    #kk-app.show-activities .kk-activities-list { transform: translateY(0); opacity: 1; pointer-events: auto; transition-delay: 0.1s;}
    .kk-activities-list h1 { color: #000; font-weight: 900; font-size: 2.2rem; margin-bottom: 20px; text-align: center;}
    
    .kk-activity-row {
        background: rgba(15, 15, 25, 0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px; padding: 15px; 
        display: flex; align-items: center; gap: 15px; margin-bottom: 15px; cursor: pointer; 
        transition: 0.3s; box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    }
    .kk-activity-row:active { transform: scale(0.95); }
    .kk-act-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); box-shadow: inset 0 0 10px rgba(0,0,0,0.5); }
    .kk-act-text h4 { margin: 0; font-weight: 800; color: #fff; font-size: 1.15rem;}
    .kk-act-text p { margin: 0; color: #CBD5E1; font-size: 0.9rem; font-weight: 600;}

    /* Close X Button (Sits exactly over the canvas mask blob) */
    .kk-close-x {
        position: absolute; top: 585px; left: 50%; transform: translateX(-50%);
        width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1.5rem; font-weight: bold; cursor: pointer; pointer-events: none; opacity: 0;
        transition: opacity 0.3s;
    }
    #kk-app.show-activities .kk-close-x { pointer-events: auto; opacity: 1; transition-delay: 0.4s;}

    /* --- 5. FAB MENU (Perfect Emoji Alignment) --- */
    .kk-fab-container { position: absolute; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 50; }
    
    .fab-goo-layer { position: absolute; right: -50px; bottom: -50px; width: 250px; height: 250px; filter: url(#fab-goo); pointer-events: none; }
    
    /* Blobs and Icons locked to identical dimensions and base positions */
    .fab-blob {
        position: absolute; bottom: 50px; right: 50px; width: 60px; height: 60px; border-radius: 50%;
        background: rgba(0,0,0,0.85); z-index: 51; transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .fab-icon {
        position: absolute; bottom: 0; right: 0; width: 60px; height: 60px; border-radius: 50%;
        color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        z-index: 52; cursor: pointer; pointer-events: none; opacity: 0; transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    /* Unified exact transforms */
    .kk-fab-container.open .fab-blob-1, .kk-fab-container.open .fab-icon.i1 { transform: translate(-10px, -100px); opacity: 1; pointer-events: auto; } 
    .kk-fab-container.open .fab-blob-2, .kk-fab-container.open .fab-icon.i2 { transform: translate(-100px, -10px); opacity: 1; pointer-events: auto; } 
    .kk-fab-container.open .fab-blob-3, .kk-fab-container.open .fab-icon.i3 { transform: translate(-80px, -80px); opacity: 1; pointer-events: auto; } 

    .fab-main {
        position: absolute; bottom: 0; right: 0; width: 60px; height: 60px; border-radius: 50%;
        color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 300;
        cursor: pointer; transition: 0.4s; z-index: 55; background: #000; box-shadow: 0 10px 20px rgba(0,0,0,0.5); pointer-events: auto;
    }
    .kk-fab-container.open .fab-main { transform: rotate(45deg); background: #333; }

    /* Hide UI when Quiz is open */
    #kk-app.hide-ui .kk-masked-content, #kk-app.hide-ui .kk-content-layer, #kk-app.hide-ui .kk-fab-container { opacity: 0; pointer-events: none; }

    /* --- 6. QUIZ & RESULTS (Dark Glassmorphism) --- */
    #quiz-view, #results-view { position: absolute; inset: 0; z-index: 100; display: none; flex-direction: column; padding: 40px 30px; box-sizing: border-box; }
    #quiz-view { background: linear-gradient(180deg, #FF9500, #FFCC00); }
    #results-view { background: linear-gradient(180deg, #007AFF, #AF52DE); align-items: center; justify-content: center; text-align: center; }
    
    .qz-header { display: flex; justify-content: space-between; align-items: center; color: white; font-weight: 600; font-size: 1.1rem; margin-bottom: 10px; }
    .qz-quit-btn { background: rgba(255, 59, 48, 0.2); border: 1px solid #FF3B30; color: #fff; padding: 6px 14px; border-radius: 12px; cursor: pointer; font-weight: bold; transition: 0.2s; backdrop-filter: blur(10px); }
    .qz-quit-btn:hover { background: #FF3B30; }

    .qz-progress-bg { width: 100%; height: 8px; background: rgba(255,255,255,0.3); border-radius: 4px; margin-bottom: 40px; overflow: hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.2); }
    .qz-progress-fill { height: 100%; background: white; width: 0%; transition: width 0.3s ease; }
    
    .qz-question-card { background: rgba(15, 15, 25, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.15); border-radius: 24px; padding: 30px 20px; margin-bottom: auto; box-shadow: 0 15px 30px rgba(0,0,0,0.3); }
    .qz-question { font-size: 1.8rem; font-weight: 800; color: white; text-align: center; line-height: 1.3;}
    
    .qz-options { display: flex; flex-direction: column; gap: 15px; margin-bottom: auto; }
    .qz-btn { background: rgba(15, 15, 25, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); padding: 18px; border-radius: 20px; color: white; font-size: 1.2rem; font-weight: 600; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
    .qz-btn.correct { background: #34C759 !important; border-color: white; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
    .qz-btn.wrong { background: #FF3B30 !important; border-color: white; }
    .qz-btn.disabled { opacity: 0.5; pointer-events: none; }
    
    .qz-next { background: white; color: #FF9500; padding: 18px; border-radius: 20px; border: none; font-size: 1.3rem; font-weight: 800; cursor: pointer; box-shadow: 0 10px 20px rgba(0,0,0,0.2); display: none; transition: 0.2s; width: 100%; margin-top: 20px;}
    .qz-next:active { transform: scale(0.95); }

    .res-star { font-size: 6rem; color: #FFCC00; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4)); margin-bottom: 20px; }
    .res-score { font-size: 4.5rem; font-weight: 900; color: white; margin: 10px 0 40px 0; }
    
    .qz-next { background: white; color: #FF9500; padding: 18px; border-radius: 20px; border: none; font-size: 1.3rem; font-weight: 800; cursor: pointer; box-shadow: 0 10px 20px rgba(0,0,0,0.2); display: none; transition: 0.2s; width: 100%; margin-top: 20px;}
    .qz-next:active { transform: scale(0.95); }

    .res-star { font-size: 6rem; color: #FFCC00; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4)); margin-bottom: 20px; }
    .res-score { font-size: 4.5rem; font-weight: 900; color: white; margin: 10px 0 40px 0; }
</style>

<!-- ========================================== -->
<!-- THE UNIFIED SVG MASKS (SwiftUI Canvas logic)-->
<!-- ========================================== -->
<svg style="width:0; height:0; position:absolute;" aria-hidden="true" focusable="false">
  <defs>
    <!-- Main Canvas Goo -->
    <filter id="swiftui-goo">
      <feGaussianBlur in="SourceGraphic" stdDeviation="12" result="blur" />
      <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 20 -8" result="goo" />
      <feBlend in="SourceGraphic" in2="goo" />
    </filter>

    <!-- FAB Canvas Goo -->
    <filter id="fab-goo">
      <feGaussianBlur in="SourceGraphic" stdDeviation="8" result="blur" />
      <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo" />
      <feBlend in="SourceGraphic" in2="goo" />
    </filter>

    <!-- Single Continuous Mask -->
    <mask id="swiftui-canvas-mask">
      <g filter="url(#swiftui-goo)">
        <!-- 1. The Main Expanding Rectangle -->
        <rect id="mask-main-rect" x="16" y="8" width="368" height="275" rx="40" fill="white" />
        
        <!-- 2. The 3 Selection Tails (Swallowed when rect expands) -->
        <circle id="mask-tail-1" cx="85" cy="275" r="22" fill="white" />
        <circle id="mask-tail-2" cx="200" cy="275" r="22" fill="white" />
        <circle id="mask-tail-3" cx="315" cy="275" r="22" fill="white" />
        
        <!-- 3. The Close "X" Tail (Pulls down when expanded) -->
        <circle id="mask-close-blob" cx="200" cy="275" r="30" fill="white" />
      </g>
    </mask>
  </defs>
</svg>

<div id="kk-app">
    <!-- Background Layer -->
    <div class="kk-bg-wrapper">
        <div class="kk-orb orb-1"></div>
        <div class="kk-orb orb-2"></div>
        <div class="kk-orb orb-3"></div>
    </div>

    <!-- MASKED LAYER: This is the single continuous visual shape -->
    <div class="kk-masked-content">
        <div class="kk-grad-base"></div>
        <div class="kk-grad-overlay"></div>
    </div>

    <!-- CONTENT LAYER: Text, Buttons, Activities -->
    <div class="kk-content-layer">
        
        <div class="kk-info-text" id="info-text">
            <div class="kk-icon-ring"></div>
            <h2 id="info-title">Welcome to Kids Kingdom!</h2>
            <p id="info-desc">A fun and safe place to learn about God's amazing love and His exciting stories.</p>
        </div>

        <div class="kk-sel-container">
            <div class="sel-btn c1 active" id="sel-1" onclick="handleSelection(1)"></div>
            <div class="sel-btn c2" id="sel-2" onclick="handleSelection(2)"></div>
            <div class="sel-btn c3" id="sel-3" onclick="handleSelection(3)"></div>
        </div>

        <div class="kk-activities-list">
            <h1>Fun Activities!</h1>
            <div class="kk-activity-row" onclick="alert('Coloring Fun Tapped!')">
                <div class="kk-act-icon" style="color: #007AFF;">🎨</div>
                <div class="kk-act-text"><h4>Coloring Fun</h4><p>Color scenes from Bible stories!</p></div>
                <div style="margin-left:auto; opacity:0.5; color:#000;">❯</div>
            </div>
            <div class="kk-activity-row" onclick="startQuiz()">
                <div class="kk-act-icon" style="color: #FF9500;">❓</div>
                <div class="kk-act-text"><h4>Bible Quiz</h4><p>Test your knowledge with fun questions!</p></div>
                <div style="margin-left:auto; opacity:0.5; color:#000;">❯</div>
            </div>
            <div class="kk-activity-row" onclick="alert('Worship Tapped!')">
                <div class="kk-act-icon" style="color: #FF2D55;">🎤</div>
                <div class="kk-act-text"><h4>Worship Sing-Along</h4><p>Sing and dance to your favorite songs!</p></div>
                <div style="margin-left:auto; opacity:0.5; color:#000;">❯</div>
            </div>
        </div>

        <!-- The "X" exactly overlaps mask-close-blob -->
        <div class="kk-close-x" onclick="toggleActivities(false)">✕</div>
    </div>

    <!-- FAB LAYER (Using identical SwiftUI offsets) -->
    <div class="kk-fab-container" id="fab-menu">
        <div class="fab-goo-layer">
            <div class="fab-blob fab-base"></div>
            <div class="fab-blob fab-blob-1"></div>
            <div class="fab-blob fab-blob-2"></div>
            <div class="fab-blob fab-blob-3"></div>
        </div>
        <div class="fab-icon i1" onclick="alert('Verse of the Day!\nJesus said: Let the little children come to me. - Matthew 19:14')">❤️</div>
        <div class="fab-icon i2" onclick="alert('Story Time Tapped')">📖</div>
        <div class="fab-icon i3" onclick="alert('Worship Song Tapped')">🎵</div>
        <div class="fab-main" onclick="document.getElementById('fab-menu').classList.toggle('open')">+</div>
    </div>

    <!-- QUIZ LAYER -->
    <div id="quiz-view">
        <div class="qz-header">
            <span id="qz-prog-text">Question 1/20</span>
            <span id="qz-score-text">Score: 0</span>
            <button class="qz-quit-btn" onclick="quitQuiz()">Quit</button>
        </div>
        <div class="qz-progress-bg"><div class="qz-progress-fill" id="qz-bar"></div></div>
        <div class="qz-question-card">
            <div class="qz-question" id="qz-q-text">Loading...</div>
        </div>
        <div class="qz-options" id="qz-opts"></div>
        <button class="qz-next" id="qz-next-btn" onclick="nextQuizQuestion()">Next Question</button>
    </div>

    <!-- RESULTS LAYER -->
    <div id="results-view">
        <div class="res-star">⭐</div>
        <h2 style="font-size:2.5rem; color:white; margin:0;">Great Job!</h2>
        <p style="font-size:1.2rem; color:rgba(255,255,255,0.8); margin-top:10px;">You scored</p>
        <div class="res-score" id="res-score-text">0 out of 20</div>
        <button class="qz-next" style="display:block; color:#007AFF;" onclick="restartQuiz()">Play Again</button>
        <button class="qz-next" style="display:block; background:transparent; color:white; border:2px solid white; margin-top:15px;" onclick="closeQuiz()">Back to Menu</button>
    </div>

</div>

<script>
    // --- 1. SELECTION & MASK ANIMATION LOGIC ---
    const appContainer = document.getElementById('kk-app');
    const maskRect = document.getElementById('mask-main-rect');
    const closeBlob = document.getElementById('mask-close-blob');
    const tails = [
        document.getElementById('mask-tail-1'),
        document.getElementById('mask-tail-2'),
        document.getElementById('mask-tail-3')
    ];

    const infoData = {
        1: { title: "Welcome to Kids Kingdom!", desc: "A fun and safe place to learn about God's amazing love and His exciting stories." },
        2: { title: "Fun Activities", desc: "Tap the blue circle to see all the cool things you can do. Let's explore together and have fun!" },
        3: { title: "Bible Adventures", desc: "Listen to awesome stories about heroes like David, Esther, and Noah. Tap the green circle to begin!" }
    };

    function handleSelection(id) {
        // UI Updates
        document.querySelectorAll('.sel-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('sel-' + id).classList.add('active');

        // Animate Tails down slightly to show which is active (SwiftUI offset logic)
        tails.forEach((t, i) => {
            // When active, the tail merges higher into the rect. When inactive, it drops slightly.
            t.setAttribute('cy', (i+1 === id) ? '275' : '285');
        });

        // Text Updates
        const infoText = document.getElementById('info-text');
        infoText.style.opacity = 0;
        setTimeout(() => {
            document.getElementById('info-title').innerText = infoData[id].title;
            document.getElementById('info-desc').innerText = infoData[id].desc;
            infoText.style.opacity = 1;
        }, 300);

        // Expand Sheet if Center button (2) is clicked
        if (id === 2) {
            setTimeout(() => toggleActivities(true), 800);
        } else {
            toggleActivities(false);
        }
    }

    function toggleActivities(show) {
        if (show) {
            appContainer.classList.add('show-activities');
            maskRect.setAttribute('height', '580'); 
            // Pull the close blob out of the bottom of the expanded rect
            closeBlob.setAttribute('cy', '610'); 
        } else {
            appContainer.classList.remove('show-activities');
            maskRect.setAttribute('height', '275'); 
            // Retract the close blob back into the collapsed rect
            closeBlob.setAttribute('cy', '275'); 
        }
    }


   // --- 2. QUIZ LOGIC & PROGRESS SAVING ---
    const isLoggedIn = <?php echo $is_logged_in; ?>;
    const savedQIdx = <?php echo $saved_qidx; ?>;

    const quizData = <?php echo $kids_quiz_json; ?>;

    let currentQIdx = savedQIdx < quizData.length ? savedQIdx : 0;
    let score = 0; // Standard to reset score per session even if resuming questions
    let answerChosen = false;

    function saveProgress(idx) {
        if (!isLoggedIn) return;
        const formData = new FormData();
        formData.append('save_qidx', idx);
        fetch('kids_kingdom.php', { method: 'POST', body: formData });
    }

    function startQuiz() {
        toggleActivities(false); // FIXED: Calling the correct toggle function
        appContainer.classList.add('hide-ui');
        document.getElementById('quiz-view').style.display = 'flex';
        if (currentQIdx >= quizData.length) currentQIdx = 0; // Reset if they finished before
        score = 0; 
        renderQuizQuestion();
    }

    function closeQuiz() {
        appContainer.classList.remove('hide-ui');
        document.getElementById('quiz-view').style.display = 'none';
        document.getElementById('results-view').style.display = 'none';
        handleSelection(1);
    }
    
    function quitQuiz() {
        closeQuiz();
    }

    function renderQuizQuestion() {
        answerChosen = false;
        const qObj = quizData[currentQIdx];
        
        document.getElementById('qz-prog-text').innerText = `Question ${currentQIdx + 1}/${quizData.length}`;
        document.getElementById('qz-score-text').innerText = `Score: ${score}`;
       document.getElementById('qz-bar').style.width = `${((currentQIdx + 1) / quizData.length) * 100}%`;
        
        document.getElementById('qz-q-text').innerText = qObj.q;
        document.getElementById('qz-next-btn').style.display = 'none';

        const optsContainer = document.getElementById('qz-opts');
        optsContainer.innerHTML = '';

        qObj.opts.forEach(opt => {
            const btn = document.createElement('button');
            btn.className = 'qz-btn';
            btn.innerText = opt;
            btn.onclick = () => submitAnswer(opt, btn, qObj.ans);
            optsContainer.appendChild(btn);
        });
    }

    function submitAnswer(selectedOpt, btnEl, correctOpt) {
        if (answerChosen) return; 
        answerChosen = true;

        const allBtns = document.querySelectorAll('.qz-btn');
        allBtns.forEach(b => {
            b.classList.add('disabled');
            if(b.innerText === correctOpt) b.style.borderColor = "#fff"; 
        });

        if (selectedOpt === correctOpt) {
            btnEl.classList.add('correct');
            score++;
            document.getElementById('qz-score-text').innerText = `Score: ${score}`;
        } else {
            btnEl.classList.add('wrong');
            allBtns.forEach(b => { if(b.innerText === correctOpt) b.classList.add('correct'); });
        }

        const nextBtn = document.getElementById('qz-next-btn');
        nextBtn.innerText = (currentQIdx < quizData.length - 1) ? "Next Question" : "Finish Quiz";
        nextBtn.style.display = 'block';
    }

    function nextQuizQuestion() {
        if (currentQIdx < quizData.length - 1) {
            currentQIdx++;
            saveProgress(currentQIdx);
            renderQuizQuestion();
        } else {
            saveProgress(0); // Reset progress on completion
            currentQIdx = 0; // FIX: Reset local index so it starts at Question 1 next time!
            document.getElementById('quiz-view').style.display = 'none';
            document.getElementById('results-view').style.display = 'flex';
            document.getElementById('res-score-text').innerText = `${score} out of ${quizData.length}`;
        }
    }

    function restartQuiz() {
        document.getElementById('results-view').style.display = 'none';
        currentQIdx = 0;
        score = 0;
        saveProgress(0);
        startQuiz();
    }
    
    // Initialize State
    document.addEventListener("DOMContentLoaded", () => handleSelection(1));
</script>

<?php echo $OUTPUT->footer(); ?>
