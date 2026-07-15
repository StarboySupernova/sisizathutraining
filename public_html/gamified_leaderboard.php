<?php
// public_html/gamified_leaderboard.php
require_once('config.php');
require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/gamified_leaderboard.php');
$PAGE->set_title('Global Leaderboard');
$PAGE->set_heading('Hall of Fame');
$PAGE->set_pagelayout('standard'); 

global $DB, $USER;

// Fetch all users' stored map progress from the Moodle Database Ledger
$sql = "SELECT u.id, u.firstname, u.lastname, up.value as progress_json
        FROM {user} u
        JOIN {user_preferences} up ON up.userid = u.id
        WHERE up.name = 'sisi_journey_progress' AND u.deleted = 0 AND u.suspended = 0";
$records = $DB->get_records_sql($sql);

$leaderboard = [];
$found_me = false;

foreach ($records as $rec) {
    $data = json_decode($rec->progress_json, true);
    $xp = isset($data['_globalXP']) ? (int)$data['_globalXP'] : 0;
    
    if ($xp > 0 || $rec->id == $USER->id) { // Only include people with XP, but always ensure current user is logged
        $initials = mb_substr($rec->firstname, 0, 1) . mb_substr($rec->lastname, 0, 1);
        $is_me = ($rec->id == $USER->id);
        if ($is_me) $found_me = true;

        $leaderboard[] = [
            'id' => $rec->id,
            'name' => format_string($rec->firstname . ' ' . $rec->lastname),
            'initials' => strtoupper($initials),
            'xp' => $xp,
            'is_me' => $is_me
        ];
    }
}

// If the logged-in user somehow has no ledger record yet, append them at the bottom with 0 XP.
if (!$found_me) {
    $initials = mb_substr($USER->firstname, 0, 1) . mb_substr($USER->lastname, 0, 1);
    $leaderboard[] = [
        'id' => $USER->id,
        'name' => format_string($USER->firstname . ' ' . $USER->lastname),
        'initials' => strtoupper($initials),
        'xp' => 0,
        'is_me' => true
    ];
}

// Sort the array descending by XP
usort($leaderboard, function($a, $b) {
    return $b['xp'] <=> $a['xp'];
});

echo $OUTPUT->header();
?>

<style>
    #lb-master-container {
        width: 100%; max-width: 850px; margin: 2rem auto; height: calc(100vh - 140px); min-height: 600px;
        background: linear-gradient(135deg, rgba(10, 10, 20, 0.95), rgba(25, 20, 45, 0.95));
        backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); overflow: hidden;
        color: #F8FAFC !important; font-family: 'Poppins', sans-serif; position: relative;
    }
    
    .lb-header {
        padding: 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; z-index: 20; position: relative;
    }
    
    .lb-header h1 { margin: 0; font-size: 1.6rem; font-weight: 900; color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
    
    .lb-back-btn {
        background: rgba(255,255,255,0.1); border: none; color: white; padding: 10px 18px;
        border-radius: 12px; cursor: pointer; font-weight: 700; transition: 0.3s; text-decoration: none; display: inline-block;
    }
    .lb-back-btn:hover { background: #00CFFD; color: #000; transform: scale(1.05); }

    /* Parallax Background Layers */
    #lb-parallax-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
    .lb-orb { position: absolute; border-radius: 50%; filter: blur(40px); opacity: 0.25; mix-blend-mode: screen; }
    .lb-orb.o1 { width: 400px; height: 400px; background: #FFD700; top: -100px; left: -100px; }
    .lb-orb.o2 { width: 300px; height: 300px; background: #F37021; top: 40%; right: -50px; }
    .lb-orb.o3 { width: 250px; height: 250px; background: #25d366; bottom: -50px; left: 20%; }
    
    .lb-particle { position: absolute; background: #fff; border-radius: 50%; opacity: 0.5; pointer-events: none; filter: blur(1px); }
    .lb-particle.p1 { width: 8px; height: 8px; top: 20%; left: 15%; box-shadow: 0 0 10px #fff; }
    .lb-particle.p2 { width: 12px; height: 12px; top: 60%; right: 10%; box-shadow: 0 0 12px #fff; }
    .lb-particle.p3 { width: 6px; height: 6px; bottom: 25%; left: 70%; box-shadow: 0 0 8px #fff; }

    /* 3D Scroll Plane */
    #lb-scroll-area {
        height: calc(100% - 75px); overflow-y: auto; overflow-x: hidden;
        perspective: 1200px; perspective-origin: 50% 10%; position: relative; z-index: 10; padding: 40px 20px 80px 20px;
    }
    
    #lb-scroll-area::-webkit-scrollbar { width: 8px; }
    #lb-scroll-area::-webkit-scrollbar-track { background: rgba(0,0,0,0.15); border-radius: 10px; margin: 10px 0; }
    #lb-scroll-area::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); }
    #lb-scroll-area::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }

    #lb-3d-plane {
        transform-style: preserve-3d; transform: rotateX(-15deg) translateY(10px);
        transform-origin: top center; display: flex; flex-direction: column; gap: 15px; max-width: 600px; margin: 0 auto;
    }

    /* Ranks & Rows */
    .lb-row {
        background: rgba(30, 30, 45, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 18px; padding: 15px 25px; display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3); color: white; transition: 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        animation: rowSlideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
    }
    
    .lb-row:hover { transform: translateY(-5px) translateZ(30px); background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.3); }

    /* The Current Logged-In User Highlight */
    .lb-row.is-me {
        background: linear-gradient(135deg, rgba(37, 211, 102, 0.15), rgba(0, 207, 253, 0.15)) !important;
        border: 2px solid #25d366 !important;
        box-shadow: 0 10px 40px rgba(37, 211, 102, 0.3) !important;
        transform: scale(1.05) translateZ(30px);
    }
    
    .lb-row.is-me .lb-name { color: #25d366; font-weight: 900; }
    
    .lb-rank { font-size: 1.5rem; font-weight: 900; width: 50px; text-align: center; color: rgba(255,255,255,0.6); }
    
    .lb-user-info { display: flex; align-items: center; gap: 15px; flex-grow: 1; }
    .lb-avatar {
        width: 45px; height: 45px; border-radius: 50%; background: #475569; display: flex; align-items: center;
        justify-content: center; font-weight: 800; font-size: 1.1rem; border: 2px solid rgba(255,255,255,0.2); box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    
    .lb-name { font-size: 1.1rem; font-weight: 700; margin: 0; }
    
    .lb-xp {
        background: rgba(0, 207, 253, 0.15); border: 1px solid rgba(0, 207, 253, 0.4);
        padding: 6px 14px; border-radius: 20px; font-weight: 800; color: #00CFFD; font-size: 1.05rem;
    }

    @keyframes rowSlideIn {
        0% { opacity: 0; transform: translateY(50px) rotateX(10deg); }
        100% { opacity: 1; transform: translateY(0) rotateX(0); }
    }
    
    @media (max-width: 768px) {
        .lb-header { padding: 15px; }
        .lb-header h1 { font-size: 1.3rem; }
        #lb-scroll-area { padding: 20px 10px 60px 10px; }
        .lb-row { padding: 12px 15px; border-radius: 14px; }
        .lb-rank { width: 35px; font-size: 1.2rem; }
        .lb-avatar { width: 35px; height: 35px; font-size: 0.9rem; }
        .lb-name { font-size: 0.95rem; }
        .lb-xp { font-size: 0.85rem; padding: 4px 10px; }
    }
</style>

<div id="lb-master-container">
    <!-- Header -->
    <div class="lb-header">
        <a href="gamified_journey.php" class="lb-back-btn">❮ Back to Hub</a>
        <h1>🏆 Hall of Fame</h1>
    </div>

    <!-- Parallax Background Elements -->
    <div id="lb-parallax-bg">
        <div class="lb-orb o1"></div>
        <div class="lb-orb o2"></div>
        <div class="lb-orb o3"></div>
        <div class="lb-particle p1"></div>
        <div class="lb-particle p2"></div>
        <div class="lb-particle p3"></div>
    </div>

    <!-- 3D Scrolling Plane -->
    <div id="lb-scroll-area">
        <div id="lb-3d-plane">
            <?php
            $rank = 1;
            foreach ($leaderboard as $idx => $user) {
                // Determine Badge/Rank HTML
                $rankHtml = "#$rank";
                if ($rank == 1) $rankHtml = "🥇";
                if ($rank == 2) $rankHtml = "🥈";
                if ($rank == 3) $rankHtml = "🥉";
                
                $isMeClass = $user['is_me'] ? 'is-me' : '';
                $youLabel = $user['is_me'] ? ' <span style="font-size:0.75rem; color:#fff; background:#25d366; padding:2px 6px; border-radius:8px; margin-left:5px;">YOU</span>' : '';
                
                // Animation delay for cascading loading effect
                $delay = $idx * 0.08;
                
                echo "
                <div class='lb-row {$isMeClass}' style='animation-delay: {$delay}s;'>
                    <div class='lb-rank'>{$rankHtml}</div>
                    <div class='lb-user-info'>
                        <div class='lb-avatar'>{$user['initials']}</div>
                        <div class='lb-name'>{$user['name']} {$youLabel}</div>
                    </div>
                    <div class='lb-xp'>⚡ {$user['xp']} XP</div>
                </div>
                ";
                $rank++;
            }
            ?>
        </div>
    </div>
</div>

<script>
    // Parallax logic attached to the 3D scrollview to create infinite depth tracking
    document.addEventListener("DOMContentLoaded", () => {
        const scrollArea = document.getElementById('lb-scroll-area');
        const bg = document.getElementById('lb-parallax-bg');
        
        if (!scrollArea || !bg) return;
        
        const orbs = bg.querySelectorAll('.lb-orb');
        const particles = bg.querySelectorAll('.lb-particle');
        
        let ticking = false;
        
        scrollArea.addEventListener('scroll', () => {
            if (ticking) return;
            ticking = true;
            
            requestAnimationFrame(() => {
                const y = scrollArea.scrollTop;
                
                // Orbs move down slightly indicating distance
                if (orbs[0]) orbs[0].style.transform = `translateY(${y * 0.15}px)`;
                if (orbs[1]) orbs[1].style.transform = `translateY(${y * 0.3}px)`;
                if (orbs[2]) orbs[2].style.transform = `translateY(${y * 0.45}px)`;
                
                // Particles move up sharply indicating proximity
                if (particles[0]) particles[0].style.transform = `translateY(${y * -0.6}px)`;
                if (particles[1]) particles[1].style.transform = `translateY(${y * -0.4}px)`;
                if (particles[2]) particles[2].style.transform = `translateY(${y * -0.8}px)`;
                
                ticking = false;
            });
        }, { passive: true });
        
        // Auto-scroll the container smoothly to the current user if they are far down the list
        setTimeout(() => {
            const meRow = document.querySelector('.lb-row.is-me');
            if (meRow) {
                const rowTop = meRow.offsetTop;
                if (rowTop > scrollArea.clientHeight - 100) {
                    scrollArea.scrollTo({ top: rowTop - 150, behavior: 'smooth' });
                }
            }
        }, 800);
    });
</script>

<?php echo $OUTPUT->footer(); ?>