<?php
// public_html/bulk_load_basic_computer.php
require_once('config.php');
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// COURSE ID 18 - Basic Computer Skills
$course_id = 18;

$data = [
    // MAP 1: Hardware Foundations (Category 1)
    [
        "id" => 101,
        "course_id" => $course_id,
        "category_id" => 1,
        "title" => "Hardware Essentials",
        "desc" => "Master the physical components that make a computer work.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "Which of these is used to type text into a computer?", "options" => ["Mouse", "Monitor", "Keyboard", "Printer"], "ans" => 2, "xp" => 100],
                ["q" => "What part of the computer acts as the 'brain'?", "options" => ["CPU", "Hard Drive", "RAM", "Monitor"], "ans" => 0, "xp" => 100],
                ["q" => "Which device is used to point and click on items?", "options" => ["Keyboard", "Mouse", "Scanner", "Speaker"], "ans" => 1, "xp" => 100],
                ["q" => "Where do you see the visual output of the computer?", "options" => ["Keyboard", "System Unit", "Monitor", "Printer"], "ans" => 2, "xp" => 100],
                ["q" => "Which button is used to turn on the computer?", "options" => ["Shift", "Enter", "Space", "Power"], "ans" => 3, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which device prints digital documents onto paper?", "options" => ["Scanner", "Printer", "Monitor", "Webcam"], "ans" => 1, "xp" => 120],
                ["q" => "What do you use to hear sound privately from a computer?", "options" => ["Speakers", "Microphone", "Headphones", "Screen"], "ans" => 2, "xp" => 120],
                ["q" => "Which device captures video for a video call?", "options" => ["Webcam", "Scanner", "Printer", "Speaker"], "ans" => 0, "xp" => 120],
                ["q" => "Which device 'reads' a paper document into the computer?", "options" => ["Printer", "Webcam", "Scanner", "Monitor"], "ans" => 2, "xp" => 120],
                ["q" => "What do we call devices like mice and keyboards?", "options" => ["Input Devices", "Output Devices", "Software", "Storage"], "ans" => 0, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the main storage area inside the computer?", "options" => ["RAM", "Hard Drive", "CPU", "Motherboard"], "ans" => 1, "xp" => 150],
                ["q" => "Which of these is a portable storage device?", "options" => ["Monitor", "USB Flash Drive", "CPU", "Motherboard"], "ans" => 1, "xp" => 150],
                ["q" => "What happens to data in RAM when the computer turns off?", "options" => ["It is saved", "It is deleted", "It stays there", "It prints out"], "ans" => 1, "xp" => 150],
                ["q" => "Which part provides power to all computer components?", "options" => ["CPU", "Power Supply", "Keyboard", "Mouse"], "ans" => 1, "xp" => 150],
                ["q" => "The physical parts of the computer are called...", "options" => ["Software", "Firmware", "Hardware", "Malware"], "ans" => 2, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which port is most common for connecting modern devices?", "options" => ["VGA", "Serial", "USB", "Parallel"], "ans" => 2, "xp" => 200],
                ["q" => "What is a laptop's built-in mouse called?", "options" => ["Trackpad", "Keypad", "Screenpad", "Joypad"], "ans" => 0, "xp" => 200],
                ["q" => "What prevents a CPU from overheating?", "options" => ["The Case", "A Fan/Heatsink", "The RAM", "The Hard Drive"], "ans" => 1, "xp" => 200],
                ["q" => "Which device provides battery backup during a power cut?", "options" => ["CPU", "USB", "UPS", "GPU"], "ans" => 2, "xp" => 200],
                ["q" => "What kind of hardware is a Monitor?", "options" => ["Input", "Storage", "Processing", "Output"], "ans" => 3, "xp" => 200]
            ]]
        ]
    ],
    // MAP 2: Operating Systems (Category 2)
    [
        "id" => 102,
        "course_id" => $course_id,
        "category_id" => 2,
        "title" => "Navigating the OS",
        "desc" => "Learn to manage files, folders, and settings in Windows.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the main screen you see after logging in?", "options" => ["Dashboard", "The Desktop", "Browser", "The Kitchen"], "ans" => 1, "xp" => 100],
                ["q" => "Where can you find the Start button in Windows?", "options" => ["Top Right", "Bottom Left", "Middle", "On the Monitor"], "ans" => 1, "xp" => 100],
                ["q" => "Which bar at the bottom shows open applications?", "options" => ["Space Bar", "Title Bar", "Taskbar", "Scroll Bar"], "ans" => 2, "xp" => 100],
                ["q" => "What do we call the small pictures on the desktop?", "options" => ["Paintings", "Icons", "Emojis", "Widgets"], "ans" => 1, "xp" => 100],
                ["q" => "Which button closes a window?", "options" => ["The Dash (-)", "The Square", "The X", "The Arrow"], "ans" => 2, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Where do deleted files go before being gone forever?", "options" => ["Hard Drive", "The Cloud", "Recycle Bin", "Desktop"], "ans" => 2, "xp" => 120],
                ["q" => "What is the shortcut for Copying text?", "options" => ["Ctrl + V", "Ctrl + X", "Ctrl + C", "Ctrl + Z"], "ans" => 2, "xp" => 120],
                ["q" => "What is the shortcut for Pasting text?", "options" => ["Ctrl + C", "Ctrl + P", "Ctrl + V", "Ctrl + S"], "ans" => 2, "xp" => 120],
                ["q" => "Which shortcut Undoes your last action?", "options" => ["Ctrl + U", "Ctrl + Z", "Ctrl + A", "Ctrl + Delete"], "ans" => 1, "xp" => 120],
                ["q" => "How do you select all text in a document?", "options" => ["Ctrl + S", "Ctrl + A", "Ctrl + T", "Alt + A"], "ans" => 1, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Which program is used to browse files and folders?", "options" => ["Chrome", "File Explorer", "Word", "Calculator"], "ans" => 1, "xp" => 150],
                ["q" => "A container used to organize files is called a...", "options" => ["Cabinet", "Bucket", "Folder", "Drive"], "ans" => 2, "xp" => 150],
                ["q" => "Which file extension is usually a document?", "options" => [".mp3", ".jpg", ".docx", ".exe"], "ans" => 2, "xp" => 150],
                ["q" => "Which file extension is usually a picture?", "options" => [".jpg", ".mp4", ".txt", ".zip"], "ans" => 0, "xp" => 150],
                ["q" => "Which file extension is usually an installer?", "options" => [".png", ".exe", ".pdf", ".mp3"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What should you do if an application freezes?", "options" => ["Kick the PC", "Unplug the power", "Use Task Manager", "Wait forever"], "ans" => 2, "xp" => 200],
                ["q" => "Which key opens the Start Menu?", "options" => ["Esc", "Windows Key", "Alt", "Tab"], "ans" => 1, "xp" => 200],
                ["q" => "What is the process of restarting a computer called?", "options" => ["Re-running", "Refreshing", "Rebooting", "Resetting"], "ans" => 2, "xp" => 200],
                ["q" => "Which program manages all hardware and software?", "options" => ["Google", "The BIOS", "Operating System", "Microsoft Word"], "ans" => 2, "xp" => 200],
                ["q" => "How do you find a missing file quickly?", "options" => ["Look manually", "Use Search", "Ask a friend", "Re-type it"], "ans" => 1, "xp" => 200]
            ]]
        ]
    ],
    // MAP 3: Internet & Safety (Category 3)
    [
        "id" => 103,
        "course_id" => $course_id,
        "category_id" => 3,
        "title" => "Safe Surfing",
        "desc" => "Stay safe while exploring the World Wide Web.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "Which of these is a web browser?", "options" => ["Windows", "Google Chrome", "Excel", "Photoshop"], "ans" => 1, "xp" => 100],
                ["q" => "What does 'WWW' stand for?", "options" => ["World Wide Web", "World War West", "Wide Web World", "Web World Wide"], "ans" => 0, "xp" => 100],
                ["q" => "What do you type in the address bar to go to a site?", "options" => ["A Phone Number", "A URL", "Your Name", "A Password"], "ans" => 1, "xp" => 100],
                ["q" => "Which button takes you to the previous page?", "options" => ["Refresh", "Home", "Back", "Forward"], "ans" => 2, "xp" => 100],
                ["q" => "What icon shows a website is secure?", "options" => ["A Star", "A Lock", "A Smile", "An Eye"], "ans" => 1, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which of these is a strong password?", "options" => ["123456", "password", "P@ssw0rd2026!", "admin"], "ans" => 2, "xp" => 120],
                ["q" => "What is a 'Phishing' email?", "options" => ["Email about fish", "A fake email to steal info", "A fast email", "An encrypted email"], "ans" => 1, "xp" => 120],
                ["q" => "What should you do if you receive a suspicious link?", "options" => ["Click it", "Forward it", "Delete it", "Reply to it"], "ans" => 2, "xp" => 120],
                ["q" => "What software protects your computer from viruses?", "options" => ["Adware", "Antivirus", "Hardware", "Spyware"], "ans" => 1, "xp" => 120],
                ["q" => "Public Wi-Fi is usually...", "options" => ["Faster", "More Secure", "Less Secure", "Private"], "ans" => 2, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'The Cloud'?", "options" => ["Storage on the internet", "A physical cloud", "Weather software", "Rainy day backups"], "ans" => 0, "xp" => 150],
                ["q" => "Which of these is a search engine?", "options" => ["Windows", "Outlook", "Google", "Facebook"], "ans" => 2, "xp" => 150],
                ["q" => "What does it mean to 'Download'?", "options" => ["Send to internet", "Receive from internet", "Turn off computer", "Delete a file"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'Upload' mean?", "options" => ["Send to internet", "Receive from internet", "Increase volume", "Charge battery"], "ans" => 0, "xp" => 150],
                ["q" => "Email stands for...", "options" => ["Easy mail", "Electronic mail", "Efficient mail", "Electric mail"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which protocol makes a website encrypted?", "options" => ["HTTP", "HTTPS", "FTP", "HTML"], "ans" => 1, "xp" => 200],
                ["q" => "A program that hides in your PC to steal info is...", "options" => ["Adware", "Spyware", "Freeware", "Shareware"], "ans" => 1, "xp" => 200],
                ["q" => "What should you use to sign out of accounts safely?", "options" => ["Close the browser", "Shut down the PC", "Logout Button", "Turn off monitor"], "ans" => 2, "xp" => 200],
                ["q" => "Incognito mode means...", "options" => ["You are invisible", "History isn't saved locally", "No viruses", "Free internet"], "ans" => 1, "xp" => 200],
                ["q" => "What is the best way to keep your data safe from loss?", "options" => ["Keep it on Desktop", "Regular Backups", "Hide the PC", "Print everything"], "ans" => 1, "xp" => 200]
            ]]
        ]
    ]
];

// Execute update
set_config('journey_data', json_encode($data), 'local_sisizathu');

echo "<h2>✅ Database Overwritten Successfully!</h2>";
echo "<p>Loaded 3 Maps, 12 Levels, and 60 Questions into Course ID: $course_id</p>";
echo "<p><a href='gamified_journey.php'>Go to Gamified Journey to see it!</a></p>";