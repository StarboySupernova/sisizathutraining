<?php
// public_html/gamified_journey.php
require_once('config.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/gamified_journey.php');
$PAGE->set_title('Gamified Learning Journey');
$PAGE->set_heading('Interactive Certification Path');
$PAGE->set_pagelayout('standard'); 

echo $OUTPUT->header();
?>

<style>
    /* Full Screen Dynamic Container */
    #sisi-game-container {
        width: 100%; max-width: 800px; margin: 2rem auto; 
        height: calc(100vh - 150px); min-height: 600px;
        display: flex; flex-direction: column;
        background: rgba(15, 15, 25, 0.6); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5); overflow: hidden;
        color: #F8FAFC !important; font-family: 'Poppins', sans-serif;
    }

    .game-header {
        text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); 
        font-size: 1.5rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; color: #fff; z-index: 20; position: relative;
    }
    #game-back-btn { background: rgba(255,255,255,0.1); border: none; color: white; padding: 8px 15px; border-radius: 8px; cursor: pointer; display: none; font-weight: 600; transition: 0.3s; }
    #game-back-btn:hover { background: #25d366; }
    #game-progress-text { font-size: 1rem; background: #25d366; padding: 4px 12px; border-radius: 20px; display: none; color: white; }

    /* Map View - Dynamic Spacing */
    #sisi-map-view {
        flex-grow: 1; position: relative; padding: 40px 0;
        display: flex; flex-direction: column; justify-content: space-evenly; align-items: center;
    }
    
    /* Overlay for dynamic SVG path lines */
    #path-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none; }
    .game-path-line { fill: none; stroke: rgba(255,255,255,0.15); stroke-width: 6; stroke-linecap: round; stroke-dasharray: 2 18; }
    .game-path-line.active { stroke: #25d366; } /* Active iOS Green */

    .level-wrapper { position: relative; display: flex; justify-content: center; align-items: center; z-index: 10; width: 100%; }
    
    /* Nodes matching SwiftUI screenshot perfectly */
    .level-node {
        width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; 
        font-size: 1.6rem; cursor: pointer; transition: all 0.3s ease; position: relative; box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
    }
    .level-node.locked { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); border: 3px solid rgba(255,255,255,0.1); }
    .level-node.current { background: #25d366; color: white; box-shadow: 0 0 30px rgba(37, 211, 102, 0.4); transform: scale(1.1); border: 5px solid rgba(0,0,0,0.4); }
    .level-node.completed { background: #25d366; color: white; border: 5px solid rgba(0,0,0,0.4); }

    /* Ring around current */
    .progress-ring { position: absolute; top: -15px; left: -15px; width: 100px; height: 100px; pointer-events: none; }
    .progress-ring circle { fill: transparent; stroke-width: 5; stroke-linecap: round; transform: rotate(-90deg); transform-origin: 50% 50%; transition: stroke-dashoffset 0.5s ease; }
    .ring-bg { stroke: rgba(0, 0, 0, 0.4); }
    .ring-fill { stroke: #4316DB; stroke-dasharray: 283; stroke-dashoffset: 283; }

    /* Quiz View */
    #sisi-quiz-view { padding: 40px 20px; display: none; animation: fadeIn 0.4s ease; flex-grow: 1; }
    .question-text { font-size: 1.6rem; text-align: center; margin-bottom: 40px; min-height: 100px; display: flex; align-items: center; justify-content: center; color: #fff; }
    .options-grid { display: flex; flex-direction: column; gap: 15px; }
    .option-btn { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); padding: 18px; border-radius: 12px; color: #CBD5E1; font-size: 1.2rem; cursor: pointer; transition: all 0.3s ease; text-align: center; }
    .option-btn:hover:not(.disabled) { background: rgba(37, 211, 102, 0.2); border-color: #25d366; color: white; }
    .option-btn.correct { background: #25d366 !important; border-color: #25d366 !important; color: white !important; box-shadow: 0 0 15px rgba(37, 211, 102, 0.4); }
    .option-btn.wrong { background: #ff4444 !important; border-color: #ff4444 !important; color: white !important; box-shadow: 0 0 15px rgba(255, 68, 68, 0.4); }
    .option-btn.disabled { cursor: not-allowed; opacity: 0.7; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div id="sisi-game-container">
    <div class="game-header">
        <button id="game-back-btn" onclick="showMap()">❮ Back</button>
        <span id="game-title">Progress Map</span>
        <span id="game-progress-text"></span>
    </div>

    <div id="sisi-map-view">
        <svg id="path-overlay"></svg>
        <!-- Nodes injected here via JS -->
    </div>

    <div id="sisi-quiz-view">
        <div class="question-text" id="quiz-question">Loading...</div>
        <div class="options-grid" id="quiz-options"></div>
    </div>
</div>

<script>
    const levelsData = <?php echo get_config('local_sisizathu', 'journey_data') ?: '[]'; ?>;

    let selectedLevel = 1;
    let questionIndex = 0;
    let isProcessing = false;

    function renderMap() {
        const mapView = document.getElementById('sisi-map-view');
        mapView.innerHTML = '<svg id="path-overlay"></svg>';

        levelsData.forEach((level, index) => {
            const isLocked = selectedLevel < level.id;
            const isCompleted = selectedLevel > level.id;
            const isCurrent = selectedLevel === level.id;

            let icon = isLocked ? '🔒' : (isCompleted ? '✓' : '⭐');
            let statusClass = isLocked ? 'locked' : (isCompleted ? 'completed' : 'current');
            
            let progress = 0;
            if (isCompleted) progress = 1;
            else if (isCurrent) progress = questionIndex / level.questions.length;
            const dashOffset = 283 - (283 * progress); 

            const ringHtml = isCurrent || isCompleted ? `
                <svg class="progress-ring">
                    <circle class="ring-bg" cx="50" cy="50" r="45"></circle>
                    <circle class="ring-fill" cx="50" cy="50" r="45" style="stroke-dashoffset: ${dashOffset};"></circle>
                </svg>
            ` : '';

            const nodeHtml = `
                <div class="level-wrapper" id="node-${index}">
                    <div style="position:relative; transform: translateX(${level.offset}px)">
                        ${ringHtml}
                        <div class="level-node ${statusClass}" onclick="openLevel(${level.id})">${icon}</div>
                    </div>
                </div>
            `;
            mapView.innerHTML += nodeHtml;
        });

        setTimeout(drawDynamicPaths, 50); 
    }

    // Absolutely positions dynamic SVG curves exactly between the nodes
    function drawDynamicPaths() {
        const svg = document.getElementById('path-overlay');
        if (!svg) return;
        
        let html = '';
        const containerRect = document.getElementById('sisi-map-view').getBoundingClientRect();

        for(let i=0; i<levelsData.length-1; i++) {
            const startNode = document.querySelector(`#node-${i} .level-node`);
            const endNode = document.querySelector(`#node-${i+1} .level-node`);
            
            if(!startNode || !endNode) continue;

            const startRect = startNode.getBoundingClientRect();
            const endRect = endNode.getBoundingClientRect();

            const startX = (startRect.left + (startRect.width / 2)) - containerRect.left;
            const startY = (startRect.top + (startRect.height / 2)) - containerRect.top;
            const endX = (endRect.left + (endRect.width / 2)) - containerRect.left;
            const endY = (endRect.top + (endRect.height / 2)) - containerRect.top;
            
            const isActive = (selectedLevel > levelsData[i].id);
            const strokeClass = isActive ? 'game-path-line active' : 'game-path-line';

            // Beautiful smooth S-Curve Bezier Path
            const cpY1 = startY + (endY - startY) / 2;
            html += `<path class="${strokeClass}" d="M ${startX} ${startY} C ${startX} ${cpY1}, ${endX} ${cpY1}, ${endX} ${endY}" />`;
        }
        svg.innerHTML = html;
    }

    window.addEventListener('resize', drawDynamicPaths);

    function openLevel(id) {
        if (id > selectedLevel) return; 
        document.getElementById('sisi-map-view').style.display = 'none';
        document.getElementById('sisi-quiz-view').style.display = 'flex';
        document.getElementById('sisi-quiz-view').style.flexDirection = 'column';
        document.getElementById('game-back-btn').style.display = 'block';
        document.getElementById('game-progress-text').style.display = 'block';
        document.getElementById('game-title').innerText = `Level ${id}`;
        renderQuestion();
    }

    function showMap() {
        document.getElementById('sisi-quiz-view').style.display = 'none';
        document.getElementById('sisi-map-view').style.display = 'flex';
        document.getElementById('game-back-btn').style.display = 'none';
        document.getElementById('game-progress-text').style.display = 'none';
        document.getElementById('game-title').innerText = 'Progress Map';
        renderMap();
    }

    function renderQuestion() {
        const levelData = levelsData[selectedLevel - 1];
        const qData = levelData.questions[questionIndex];
        
        document.getElementById('game-progress-text').innerText = `${questionIndex + 1} / ${levelData.questions.length}`;
        document.getElementById('quiz-question').innerText = qData.q;
        
        const optionsGrid = document.getElementById('quiz-options');
        optionsGrid.innerHTML = '';

        qData.options.forEach((opt, idx) => {
            const btn = document.createElement('div');
            btn.className = 'option-btn';
            btn.innerText = opt;
            btn.onclick = () => checkAnswer(idx, btn);
            optionsGrid.appendChild(btn);
        });
    }

    function checkAnswer(selectedIndex, btnElement) {
        if (isProcessing) return;
        isProcessing = true;

        const levelData = levelsData[selectedLevel - 1];
        const correctIndex = levelData.questions[questionIndex].ans;
        const allBtns = document.querySelectorAll('.option-btn');

        allBtns.forEach(b => b.classList.add('disabled'));

        if (selectedIndex === correctIndex) {
            btnElement.classList.add('correct');
            setTimeout(() => {
                questionIndex++;
                if (questionIndex >= levelData.questions.length) {
                    selectedLevel++; questionIndex = 0; showMap();
                } else { renderQuestion(); }
                isProcessing = false;
            }, 1000);
        } else {
            btnElement.classList.add('wrong');
            allBtns[correctIndex].classList.add('correct');
            setTimeout(() => { 
                // Advance to next question even if wrong
                questionIndex++;
                if (questionIndex >= levelData.questions.length) {
                    selectedLevel++; questionIndex = 0; showMap();
                } else { renderQuestion(); }
                isProcessing = false; 
            }, 1500);
        }
    }

    document.addEventListener("DOMContentLoaded", renderMap);
</script>

<?php echo $OUTPUT->footer(); ?>