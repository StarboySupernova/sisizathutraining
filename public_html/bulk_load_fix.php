<?php
// public_html/bulk_load_fix.php
require_once('config.php');
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

global $DB;

// Automatically find the correct course IDs from your Moodle database
$basic_course = $DB->get_record_select('course', "fullname LIKE '%Basic Computer Skills%'", null, 'id');
$inter_course = $DB->get_record_select('course', "fullname LIKE '%Intermediate Computer Skills%'", null, 'id');
$advanced_course = $DB->get_record_select('course', "fullname LIKE '%Advanced Computer Skills%'", null, 'id');
$biz_course = $DB->get_record_select('course', "fullname LIKE '%Business Administration%'", null, 'id');
$comm_course = $DB->get_record_select('course', "fullname LIKE '%Communication Studies and Language%'", null, 'id');
$cs_course = $DB->get_record_select('course', "fullname LIKE '%Customer Service%'", null, 'id');
$entrep_course = $DB->get_record_select('course', "fullname LIKE '%Entrepreneurial Skills%'", null, 'id');
$fin_course = $DB->get_record_select('course', "fullname LIKE '%Financial Administration%'", null, 'id'); 
$fire_course = $DB->get_record_select('course', "fullname LIKE '%Fire Fighting%'", null, 'id'); 
$gm_course = $DB->get_record_select('course', "fullname LIKE '%Generic Management%'", null, 'id');

$target_course_id = $basic_course ? $basic_course->id : 15; 
$wrong_course_id = $inter_course ? $inter_course->id : 18;
$advanced_course_id = $advanced_course ? $advanced_course->id : 21;
$biz_course_id = $biz_course ? $biz_course->id : 24;
$comm_course_id = $comm_course ? $comm_course->id : 27; 
$cs_course_id = $cs_course ? $cs_course->id : 30; 
$entrep_course_id = $entrep_course ? $entrep_course->id : 33; 
$fin_course_id = $fin_course ? $fin_course->id : 36; 
$fire_course_id = $fire_course ? $fire_course->id : 39; 
$gm_course_id = $gm_course ? $gm_course->id : 42; // Fallback

// 1. Fetch current data and REMOVE maps from the wrong course
$current_data_json = get_config('local_sisizathu', 'journey_data') ?: '[]';
$current_data = json_decode($current_data_json, true);
$cleaned_data = [];
$highest_id = 0;

if (is_array($current_data)) {
    foreach ($current_data as $map) {
        // Safeguard to scrub out the corrupt null entry currently in your database
        if (!is_array($map) || !isset($map['course_id'])) {
            continue; 
        }

        // Keep only maps that DO NOT belong to the courses we are updating
        if ($map['course_id'] != $wrong_course_id && $map['course_id'] != $target_course_id && $map['course_id'] != $advanced_course_id && $map['course_id'] != $biz_course_id && $map['course_id'] != $comm_course_id && $map['course_id'] != $cs_course_id && $map['course_id'] != $entrep_course_id && $map['course_id'] != $fin_course_id && $map['course_id'] != $fire_course_id && $map['course_id'] != $gm_course_id) {
                $cleaned_data[] = $map; 
        }
        
        if ($map['id'] > $highest_id) {
            $highest_id = $map['id'];
        }
    }
}

// 2. Build the 12 requested Maps for Basic Computer Skills
$new_maps = [
    // ==========================================================
    // CATEGORY 1: FOUNDATIONAL MODULES (Maps 1-4)
    // ==========================================================
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 1,
        "title" => "Map 1: Hardware & System Architecture", "desc" => "Master the physical components, peripherals, and foundational anatomy of a computer system.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "Which component is considered the 'brain' of the computer?", "options" => ["Central Processing Unit (CPU)", "Hard Drive", "Random Access Memory (RAM)", "Motherboard"], "ans" => 0, "xp" => 100],
                ["q" => "What is the primary function of a computer monitor?", "options" => ["To process data", "To display visual output", "To store files permanently", "To provide physical power"], "ans" => 1, "xp" => 100],
                ["q" => "Which of the following is strictly an input device?", "options" => ["Printer", "Speaker", "Keyboard", "Projector"], "ans" => 2, "xp" => 100],
                ["q" => "What is the main circuit board that connects all internal components together?", "options" => ["Power Supply", "Motherboard", "Graphics Card", "Network Interface"], "ans" => 1, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which device is designed to digitize physical paper documents?", "options" => ["Printer", "Webcam", "Scanner", "Monitor"], "ans" => 2, "xp" => 120],
                ["q" => "Which of these devices serves as BOTH an input and output device?", "options" => ["Touchscreen Monitor", "Standard Keyboard", "Mouse", "Microphone"], "ans" => 0, "xp" => 120],
                ["q" => "What type of connector is currently the most universal for attaching peripherals?", "options" => ["VGA", "Parallel Port", "USB (Universal Serial Bus)", "PS/2"], "ans" => 2, "xp" => 120],
                ["q" => "Which component provides the electrical energy required for the computer to operate?", "options" => ["CPU", "Power Supply Unit (PSU)", "RAM", "Hard Disk"], "ans" => 1, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What happens to data in RAM when the computer loses power?", "options" => ["It is saved permanently", "It is cleared/lost completely", "It is sent to the printer", "It is backed up to the cloud"], "ans" => 1, "xp" => 150],
                ["q" => "Which storage drive has no moving parts and operates much faster than traditional drives?", "options" => ["Hard Disk Drive (HDD)", "Solid State Drive (SSD)", "Optical CD-ROM", "Floppy Disk"], "ans" => 1, "xp" => 150],
                ["q" => "What is the primary difference between a desktop and a laptop computer?", "options" => ["Desktops are always faster", "Laptops are portable and have built-in batteries", "Desktops don't require an OS", "Laptops cannot connect to the internet"], "ans" => 1, "xp" => 150],
                ["q" => "What component prevents the CPU from overheating?", "options" => ["The Computer Case", "A Heatsink and Fan", "The Motherboard", "The Hard Drive"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which port is exclusively used for connecting a device to a wired computer network?", "options" => ["HDMI", "USB-C", "Ethernet (RJ-45)", "Audio Jack"], "ans" => 2, "xp" => 200],
                ["q" => "What does HDMI primarily transmit?", "options" => ["Power only", "High-definition video and audio", "Internet data only", "Print commands"], "ans" => 1, "xp" => 200],
                ["q" => "Which built-in laptop feature replaces the need for a traditional desktop mouse?", "options" => ["Trackpad/Touchpad", "Number Pad", "Webcam", "Function Keys"], "ans" => 0, "xp" => 200],
                ["q" => "What is a UPS (Uninterruptible Power Supply) used for?", "options" => ["Speeding up processing", "Providing battery backup during power outages", "Cooling the system", "Connecting wireless devices"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Which of the following is considered best practice for computer ergonomics?", "options" => ["Staring at the screen continuously", "Positioning the monitor at eye level", "Using a chair with no back support", "Keeping the keyboard on your lap"], "ans" => 1, "xp" => 250],
                ["q" => "How should you safely clean a flat-screen monitor?", "options" => ["Spray window cleaner directly on it", "Use a dry or slightly damp microfiber cloth", "Wipe it with a wet paper towel", "Use compressed air only"], "ans" => 1, "xp" => 250],
                ["q" => "What is the primary purpose of a surge protector?", "options" => ["To act as a battery backup", "To shield devices from sudden voltage spikes", "To increase internet speed", "To add more USB ports"], "ans" => 1, "xp" => 250],
                ["q" => "If a wired USB mouse stops working suddenly, what is the best first troubleshooting step?", "options" => ["Buy a new mouse", "Reboot the router", "Unplug it and try a different USB port", "Open the mouse casing"], "ans" => 2, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does the term 'plug and play' refer to?", "options" => ["A gaming console", "Hardware that the OS recognizes and configures automatically", "A type of power cable", "Software that requires no installation"], "ans" => 1, "xp" => 300],
                ["q" => "Which internal component dictates the maximum graphical performance of a computer?", "options" => ["Power Supply", "GPU (Graphics Processing Unit)", "Network Card", "Sound Card"], "ans" => 1, "xp" => 300],
                ["q" => "What is the primary function of BIOS/UEFI on a motherboard?", "options" => ["To run Microsoft Word", "To initialize hardware during the boot process", "To store personal photos", "To connect to Wi-Fi"], "ans" => 1, "xp" => 300],
                ["q" => "Which metric is commonly used to measure the clock speed of a CPU?", "options" => ["Gigabytes (GB)", "Gigahertz (GHz)", "Megapixels (MP)", "Terabytes (TB)"], "ans" => 1, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 1,
        "title" => "Map 2: Operating System Navigation", "desc" => "Learn to seamlessly interact with your desktop environment, manage windows, and customize settings.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary screen you interact with after logging into a computer?", "options" => ["The Browser", "The Desktop", "The Control Panel", "The Terminal"], "ans" => 1, "xp" => 100],
                ["q" => "Where is the Start Menu traditionally located in a Windows operating system?", "options" => ["Top Right", "Bottom Left/Center", "Middle of the screen", "Top Left"], "ans" => 1, "xp" => 100],
                ["q" => "What is the strip usually at the bottom of the screen that shows open applications?", "options" => ["Menu Bar", "Scroll Bar", "Taskbar", "Title Bar"], "ans" => 2, "xp" => 100],
                ["q" => "What are the small graphical images on the desktop that represent programs or files?", "options" => ["Widgets", "Icons", "Emojis", "Cursors"], "ans" => 1, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which button at the top right of a window allows you to hide it without closing the application?", "options" => ["The X button", "The Minimize (-) button", "The Maximize (Square) button", "The Help (?) button"], "ans" => 1, "xp" => 120],
                ["q" => "What does the Maximize button do to a window?", "options" => ["Closes the application completely", "Makes the window fill the entire screen", "Splits the screen in half", "Makes the text larger"], "ans" => 1, "xp" => 120],
                ["q" => "How can you resize a window that is not maximized?", "options" => ["Hover over the edge until the cursor changes into a double-arrow, then click and drag", "Right-click the center of the window", "Press the spacebar repeatedly", "Scroll the mouse wheel"], "ans" => 0, "xp" => 120],
                ["q" => "Which keyboard shortcut allows you to quickly switch between open windows?", "options" => ["Ctrl + P", "Alt + Tab", "Shift + Enter", "Windows Key + L"], "ans" => 1, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Where do you go to change the computer's background wallpaper or screen resolution?", "options" => ["Task Manager", "Settings or Control Panel", "File Explorer", "Web Browser"], "ans" => 1, "xp" => 150],
                ["q" => "What area of the taskbar (usually bottom right) displays the time, volume, and network status?", "options" => ["The Start Menu", "The System Tray / Notification Area", "The Search Bar", "The Recycle Bin"], "ans" => 1, "xp" => 150],
                ["q" => "How can you temporarily mute the computer's audio?", "options" => ["Turn off the monitor", "Click the speaker icon in the system tray and select the mute symbol", "Unplug the keyboard", "Close all open windows"], "ans" => 1, "xp" => 150],
                ["q" => "Which tool helps you connect to a wireless internet network?", "options" => ["The Wi-Fi icon in the system tray", "The Power Options", "The Device Manager", "The Sound Settings"], "ans" => 0, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which keyboard shortcut quickly takes you straight to the Desktop, hiding all windows?", "options" => ["Windows Key + D", "Ctrl + Alt + Delete", "Alt + F4", "Ctrl + Shift + Esc"], "ans" => 0, "xp" => 200],
                ["q" => "What is the quickest way to lock your computer when you step away from your desk?", "options" => ["Turn off the monitor", "Press Windows Key + L", "Unplug the power", "Close the laptop lid quickly"], "ans" => 1, "xp" => 200],
                ["q" => "Which action safely exits your user profile but leaves the computer running for others?", "options" => ["Shut Down", "Sleep", "Log Off / Sign Out", "Restart"], "ans" => 2, "xp" => 200],
                ["q" => "What does putting the computer to 'Sleep' do?", "options" => ["Erases your hard drive", "Puts it in a low-power state, allowing quick resumption of work", "Turns off all internal components permanently", "Closes all applications without saving"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "If an application completely freezes and won't close, what built-in tool can force it to shut down?", "options" => ["Control Panel", "Task Manager", "File Explorer", "Command Prompt"], "ans" => 1, "xp" => 250],
                ["q" => "Which shortcut instantly opens the Task Manager in Windows?", "options" => ["Ctrl + Shift + Esc", "Alt + Tab", "Windows Key + R", "F12"], "ans" => 0, "xp" => 250],
                ["q" => "What does 'Rebooting' a system mean?", "options" => ["Installing a new OS", "Safely shutting down and automatically starting back up", "Deleting all temporary files", "Kicking the computer case"], "ans" => 1, "xp" => 250],
                ["q" => "Where can you see a list of programs that automatically start when you turn on your PC?", "options" => ["Recycle Bin", "Task Manager (Startup tab)", "Desktop", "Network Settings"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the function of the 'Print Screen' (PrtScn) key?", "options" => ["Sends the document to the printer", "Captures an image of your current screen to the clipboard", "Turns on the webcam", "Locks the computer"], "ans" => 1, "xp" => 300],
                ["q" => "Which feature allows you to snap two windows side-by-side perfectly on the screen?", "options" => ["Task View", "Aero Snap (dragging windows to the screen edges)", "Magnifier", "Cortana"], "ans" => 1, "xp" => 300],
                ["q" => "How do you access the context menu for a specific file or desktop area?", "options" => ["Double-click the left mouse button", "Right-click the mouse", "Press the Enter key", "Scroll the mouse wheel"], "ans" => 1, "xp" => 300],
                ["q" => "What does 'Safe Mode' do when troubleshooting Windows?", "options" => ["Loads the OS with a minimal set of drivers and programs", "Deletes all viruses automatically", "Connects to a secure VPN", "Backs up files to the cloud"], "ans" => 0, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 1,
        "title" => "Map 3: File Management & Organization", "desc" => "Gain mastery over files, folders, shortcuts, and digital organization strategies.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary purpose of a 'Folder' (or Directory)?", "options" => ["To run software applications", "To organize and store groups of files", "To connect to the internet", "To increase processing speed"], "ans" => 1, "xp" => 100],
                ["q" => "Which built-in application is used to browse files and folders in Windows?", "options" => ["Internet Explorer", "Microsoft Word", "File Explorer", "Task Manager"], "ans" => 2, "xp" => 100],
                ["q" => "What is a 'File' in computing terms?", "options" => ["A physical paper", "A self-contained piece of data or information stored on a computer", "The hardware that reads discs", "A type of computer virus"], "ans" => 1, "xp" => 100],
                ["q" => "By default, where do files downloaded from the web get saved?", "options" => ["The Desktop", "The Recycle Bin", "The Downloads folder", "The Documents folder"], "ans" => 2, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "How do you create a brand new folder on your Desktop?", "options" => ["Left-click and select 'New'", "Right-click empty space, select 'New' > 'Folder'", "Press Ctrl + N", "Drag a file onto another file"], "ans" => 1, "xp" => 120],
                ["q" => "What does the action of 'Renaming' a file do?", "options" => ["Changes its content", "Changes the file's title without altering its contents", "Moves the file to another folder", "Deletes the file"], "ans" => 1, "xp" => 120],
                ["q" => "What happens when you 'Delete' a file in Windows by simply pressing the Del key?", "options" => ["It is permanently erased immediately", "It is moved to the Recycle Bin", "It is uploaded to the cloud", "It is printed out"], "ans" => 1, "xp" => 120],
                ["q" => "How can you recover a file you accidentally deleted yesterday?", "options" => ["Open the Recycle Bin, right-click the file, and select 'Restore'", "Restart the computer", "Buy a new hard drive", "Press Ctrl + Z on the Desktop"], "ans" => 0, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the difference between 'Copying' and 'Cutting' a file?", "options" => ["Copy duplicates it; Cut moves it to a new location", "Copy moves it; Cut deletes it", "There is no difference", "Cut duplicates it; Copy deletes it"], "ans" => 0, "xp" => 150],
                ["q" => "What is the universal keyboard shortcut for 'Copy'?", "options" => ["Ctrl + X", "Ctrl + V", "Ctrl + C", "Ctrl + P"], "ans" => 2, "xp" => 150],
                ["q" => "What is the universal keyboard shortcut for 'Paste'?", "options" => ["Ctrl + X", "Ctrl + C", "Ctrl + V", "Ctrl + Z"], "ans" => 2, "xp" => 150],
                ["q" => "What action allows you to physically grab a file with your mouse and move it into a folder?", "options" => ["Click and Paste", "Drag and Drop", "Point and Shoot", "Hover and Drop"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'File Extension'?", "options" => ["A very long file name", "The 3-4 letters after the dot indicating the file type (e.g., .pdf)", "The size of the file", "A folder inside a folder"], "ans" => 1, "xp" => 200],
                ["q" => "Which file extension indicates a Microsoft Word document?", "options" => [".mp4", ".jpg", ".docx", ".exe"], "ans" => 2, "xp" => 200],
                ["q" => "Which file extension represents an image/photo file?", "options" => [".jpg / .png", ".mp3 / .wav", ".xlsx", ".txt"], "ans" => 0, "xp" => 200],
                ["q" => "Why shouldn't you arbitrarily change a file's extension (e.g., changing .jpg to .docx)?", "options" => ["It makes the file larger", "It may render the file unreadable by its associated program", "It turns the file into a virus", "It deletes the file immediately"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "How do you select multiple files at once that are physically next to each other in a list?", "options" => ["Click the first file, hold Shift, and click the last file", "Click them one by one", "Hold Alt and click", "Right-click each one"], "ans" => 0, "xp" => 250],
                ["q" => "How do you select multiple files that are NOT next to each other?", "options" => ["Hold Shift and click them", "Hold Ctrl (or Cmd on Mac) and click each specific file", "Press Ctrl + A", "It cannot be done"], "ans" => 1, "xp" => 250],
                ["q" => "What does the keyboard shortcut Ctrl + A do?", "options" => ["Aligns text", "Archives the folder", "Selects all items in the current window", "Deletes all items"], "ans" => 2, "xp" => 250],
                ["q" => "How can you quickly find a file if you forgot which folder you put it in?", "options" => ["Open every folder manually", "Use the Search bar at the top right of File Explorer", "Reboot the PC", "Empty the Recycle Bin"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does compressing files into a ZIP folder accomplish?", "options" => ["Increases file quality", "Combines multiple files into one smaller package for easy sharing", "Permanently deletes the original files", "Encrypts the files so nobody can read them"], "ans" => 1, "xp" => 300],
                ["q" => "How do you open the files contained inside a .zip archive?", "options" => ["Change the extension to .docx", "Right-click the file and select 'Extract All...'", "Double-click and drag them into the Recycle Bin", "Print the file"], "ans" => 1, "xp" => 300],
                ["q" => "How can you permanently delete a file instantly without sending it to the Recycle Bin?", "options" => ["Press Shift + Delete", "Press Ctrl + Delete", "Press Alt + Delete", "Drag it off the screen"], "ans" => 0, "xp" => 300],
                ["q" => "What does sorting files by 'Date Modified' do?", "options" => ["Alphabetizes them", "Arranges them by size", "Shows the most recently edited files at the top (or bottom)", "Hides older files"], "ans" => 2, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 1,
        "title" => "Map 4: Software Applications & Management", "desc" => "Understand how to install, update, and manage the applications that make your computer useful.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary difference between Hardware and Software?", "options" => ["Hardware is cheap; Software is expensive", "Hardware is physical components; Software is digital instructions/programs", "Hardware breaks easily; Software doesn't", "There is no difference"], "ans" => 1, "xp" => 100],
                ["q" => "Which of the following is considered 'Application Software'?", "options" => ["Windows 11", "macOS", "Microsoft Excel", "Motherboard"], "ans" => 2, "xp" => 100],
                ["q" => "What is an Operating System (OS)?", "options" => ["A program that browses the internet", "The core system software that manages hardware and allows apps to run", "A physical piece of equipment", "A virus scanner"], "ans" => 1, "xp" => 100],
                ["q" => "Which software is specifically designed to let you browse the World Wide Web?", "options" => ["Spreadsheet", "Web Browser", "Word Processor", "Media Player"], "ans" => 1, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does it mean to 'Install' software?", "options" => ["To delete it", "To set up the application on your computer so it can run", "To copy it to a USB drive", "To print its source code"], "ans" => 1, "xp" => 120],
                ["q" => "Which file type is a standard installer package on Windows?", "options" => [".jpg", ".exe (Executable)", ".txt", ".mp3"], "ans" => 1, "xp" => 120],
                ["q" => "What is a 'Software Update' or 'Patch'?", "options" => ["A piece of cloth to clean the screen", "A downloaded fix that improves software security, stability, or features", "A virus that locks the app", "A bill for using the software"], "ans" => 1, "xp" => 120],
                ["q" => "Why is it important to keep your Operating System updated?", "options" => ["To use more electricity", "To protect against newly discovered security threats and bugs", "To make the computer heavier", "To increase the monitor's brightness"], "ans" => 1, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Freeware'?", "options" => ["Hardware that is free", "Software you can use indefinitely without paying", "Software that breaks your computer", "A free trial that expires"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'Open Source' software mean?", "options" => ["The software has no security", "The original source code is made freely available and may be modified", "It can only be used outdoors", "It costs money after 30 days"], "ans" => 1, "xp" => 150],
                ["q" => "What is an EULA (End User License Agreement)?", "options" => ["A certification of completion", "A legal contract between the software author and the user regarding usage rules", "A hardware warranty", "A type of internet connection"], "ans" => 1, "xp" => 150],
                ["q" => "Software accessed over the internet via a browser (like Google Docs) is often called...", "options" => ["Vaporware", "SaaS (Software as a Service) / Cloud Software", "Firmware", "Shareware"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the correct way to remove unwanted software in Windows?", "options" => ["Delete its desktop shortcut", "Drag its folder to the Recycle Bin", "Use 'Add or Remove Programs' in Settings", "Smash the hard drive"], "ans" => 2, "xp" => 200],
                ["q" => "What happens if you only delete a program's Desktop Shortcut?", "options" => ["The program is completely uninstalled", "The program remains on the computer, but the quick link is gone", "The computer crashes", "Your files are deleted"], "ans" => 1, "xp" => 200],
                ["q" => "What does it mean if an application is running 'in the background'?", "options" => ["It is displayed on the desktop", "It is operating silently without a visible window (e.g., antivirus)", "It is changing your wallpaper", "It has crashed"], "ans" => 1, "xp" => 200],
                ["q" => "Which utility manages which programs open specific file types (e.g., making Chrome open all web links)?", "options" => ["Task Manager", "Default Apps settings", "Device Manager", "Network Center"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Firmware'?", "options" => ["Software written on paper", "Permanent software programmed into a hardware device's read-only memory", "A type of flexible screen", "An accounting program"], "ans" => 1, "xp" => 250],
                ["q" => "If an app requires Administrator Privileges to install, what does this mean?", "options" => ["It is guaranteed to be a virus", "It needs high-level system access to make core changes to the computer", "It costs money", "Only the CEO of a company can use it"], "ans" => 1, "xp" => 250],
                ["q" => "What is the purpose of an App Store (like Microsoft Store or Mac App Store)?", "options" => ["To buy physical computers", "To provide a centralized, secure platform to discover and install software", "To store physical backups", "To repair broken hardware"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Malware'?", "options" => ["Software that runs very fast", "Malicious software designed to harm or exploit systems", "A program used in malls", "Software for organizing hardware"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What should you do before installing a major operating system upgrade?", "options" => ["Buy a new mouse", "Turn off the monitor", "Back up all your important personal files", "Unplug the internet"], "ans" => 2, "xp" => 300],
                ["q" => "If an application crashes frequently, what is the best first troubleshooting step?", "options" => ["Reinstall the OS completely", "Check for application updates or restart the computer", "Open the computer case", "Delete all your files"], "ans" => 1, "xp" => 300],
                ["q" => "What does a 'clean install' of software mean?", "options" => ["Wiping the computer with a cloth before installing", "Completely removing the old version before installing the new one to prevent conflicts", "Installing without internet", "Using a brand new keyboard"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Software Driver'?", "options" => ["A program that drives a car", "A specialized program that allows the OS to communicate with a specific piece of hardware (like a printer)", "The person using the software", "A high-speed storage disk"], "ans" => 1, "xp" => 300]
            ]]
        ]
    ],

    // ==========================================================
    // CATEGORY 2: CORE COMPETENCIES (Maps 5-8)
    // ==========================================================
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 2,
        "title" => "Map 5: Word Processing Mastery", "desc" => "Create, format, and polish professional documents using word processing software.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the blinking vertical line that indicates where text will appear when you type?", "options" => ["The Mouse Pointer", "The Insertion Point / Cursor", "The Scroll Bar", "The Ribbon"], "ans" => 1, "xp" => 100],
                ["q" => "Which key removes the character to the LEFT of the cursor?", "options" => ["Delete key", "Spacebar", "Backspace key", "Enter key"], "ans" => 2, "xp" => 100],
                ["q" => "Which key removes the character to the RIGHT of the cursor?", "options" => ["Backspace key", "Delete key", "Shift key", "Ctrl key"], "ans" => 1, "xp" => 100],
                ["q" => "What is the 'Ribbon' in applications like Microsoft Word?", "options" => ["The cable connecting the printer", "The tabbed toolbar at the top containing commands and options", "The physical paper border", "The scroll wheel"], "ans" => 1, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which tool allows you to change the style/design of the text letters?", "options" => ["Font Face selection", "Page Margins", "Line Spacing", "Spell Check"], "ans" => 0, "xp" => 120],
                ["q" => "What does the keyboard shortcut Ctrl + B do to highlighted text?", "options" => ["Makes it Bigger", "Applies Bold formatting", "Deletes it", "Adds a Bullet point"], "ans" => 1, "xp" => 120],
                ["q" => "What does the keyboard shortcut Ctrl + I do to highlighted text?", "options" => ["Indents the text", "Italicizes the text", "Inserts an image", "Ignores the text"], "ans" => 1, "xp" => 120],
                ["q" => "What does the keyboard shortcut Ctrl + U do?", "options" => ["Undoes an action", "Underlines the text", "Uploads the file", "Updates the document"], "ans" => 1, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "How do you ensure text aligns evenly on both the left and right margins of the page?", "options" => ["Left Align", "Center Align", "Justify", "Right Align"], "ans" => 2, "xp" => 150],
                ["q" => "What is 'Line Spacing'?", "options" => ["The blank space around the edges of the paper", "The amount of vertical space between lines of text in a paragraph", "The space between two words", "The length of the document"], "ans" => 1, "xp" => 150],
                ["q" => "When creating a list where the order of items matters (like a recipe), which formatting should you use?", "options" => ["Bulleted List", "Numbered List", "Centered Text", "Underlined Text"], "ans" => 1, "xp" => 150],
                ["q" => "What does the 'Format Painter' tool do?", "options" => ["Changes the color of images", "Copies the formatting from one piece of text and applies it to another", "Paints the background page color", "Draws shapes"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What are the blank spaces around the top, bottom, and sides of a printed page called?", "options" => ["Gutters", "Margins", "Borders", "Headers"], "ans" => 1, "xp" => 200],
                ["q" => "What is the difference between 'Portrait' and 'Landscape' page orientation?", "options" => ["Portrait is vertical; Landscape is horizontal", "Portrait has pictures; Landscape has text", "Portrait is color; Landscape is black & white", "There is no difference"], "ans" => 0, "xp" => 200],
                ["q" => "How do you insert a grid of rows and columns to organize data in a document?", "options" => ["Insert > Shape", "Insert > Table", "Draw it manually with underscores", "Use the spacebar repeatedly"], "ans" => 1, "xp" => 200],
                ["q" => "What feature forces the text that follows to begin on a brand new page?", "options" => ["Pressing Enter 50 times", "Inserting a Page Break", "Changing margins", "Using a large font"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What feature automatically corrects common typos (e.g., 'teh' to 'the') as you type?", "options" => ["AutoCorrect", "Spell Check", "Thesaurus", "Grammar Check"], "ans" => 0, "xp" => 250],
                ["q" => "If a word has a red squiggly line under it, what does the software suggest?", "options" => ["The grammar is incorrect", "The word is spelled incorrectly or is not in the dictionary", "The word is too long", "The text is hyperlinked"], "ans" => 1, "xp" => 250],
                ["q" => "How can you quickly change all instances of the word 'Company' to 'Corporation' in a 50-page document?", "options" => ["Read and retype each one", "Use the Find and Replace tool", "Use the Spell Checker", "Delete the document and start over"], "ans" => 1, "xp" => 250],
                ["q" => "What is the purpose of 'Headers and Footers'?", "options" => ["To hold the title and author on the cover page", "To place repeating information (like page numbers or dates) at the top or bottom of every page", "To make text larger", "To insert images"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does 'Save As' do compared to 'Save'?", "options" => ["It deletes the old file", "It lets you save a copy of the current document with a new name, location, or format", "It saves the file to the cloud instantly", "It prints the document"], "ans" => 1, "xp" => 300],
                ["q" => "Why is exporting a final document as a PDF often recommended before sending it to a client?", "options" => ["PDFs lock the formatting so it looks exactly the same on any device", "PDFs are always editable by anyone", "PDFs have built-in spellcheck", "PDFs use less electricity to open"], "ans" => 0, "xp" => 300],
                ["q" => "Which shortcut opens the Print menu?", "options" => ["Ctrl + S", "Ctrl + P", "Alt + P", "Ctrl + Shift + P"], "ans" => 1, "xp" => 300],
                ["q" => "What does 'Text Wrapping' allow you to do when inserting an image?", "options" => ["It puts the image in a gift box", "It determines how the text flows around or over the image", "It crops the image automatically", "It deletes the text behind the image"], "ans" => 1, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 2,
        "title" => "Map 6: Spreadsheet Fundamentals", "desc" => "Navigate grids, manage data entries, and calculate numbers using basic spreadsheet formulas.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the intersection of a column and a row called in a spreadsheet?", "options" => ["A Box", "A Cell", "A Block", "A Grid"], "ans" => 1, "xp" => 100],
                ["q" => "How are columns typically identified in Excel or Google Sheets?", "options" => ["By Numbers (1, 2, 3...)", "By Letters (A, B, C...)", "By Colors", "By Symbols"], "ans" => 1, "xp" => 100],
                ["q" => "How are rows typically identified?", "options" => ["By Letters", "By Numbers (1, 2, 3...)", "By Roman Numerals", "By Names"], "ans" => 1, "xp" => 100],
                ["q" => "If a cell is in column B and row 5, what is its Cell Reference?", "options" => ["5B", "Column 2 Row 5", "B5", "Cell 5"], "ans" => 2, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "When you type data into a cell and press Enter, what usually happens to the active cell selection?", "options" => ["It moves right", "It stays in the same cell", "It moves down to the next cell", "It deletes the data"], "ans" => 2, "xp" => 120],
                ["q" => "What happens if a cell displays a series of hash marks (#####)?", "options" => ["The formula is broken", "The column is not wide enough to display the numeric value", "The file is corrupted", "The cell is locked"], "ans" => 1, "xp" => 120],
                ["q" => "What is the small square at the bottom-right corner of an active cell used for?", "options" => ["Deleting the cell", "The Fill Handle (to copy data or continue a series)", "Opening the settings", "Changing the cell color"], "ans" => 1, "xp" => 120],
                ["q" => "How can you quickly adjust a column's width to fit the longest text inside it?", "options" => ["Double-click the boundary line between the column headers", "Delete extra words", "Change the font to size 8", "Use the eraser tool"], "ans" => 0, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What character MUST you type first to tell the spreadsheet you are entering a formula?", "options" => ["@", "#", "=", "+"], "ans" => 2, "xp" => 150],
                ["q" => "What is the correct way to add the values of cell A1 and B1?", "options" => ["A1+B1", "=A1+B1", "SUM(A1+B1)", "Add A1 B1"], "ans" => 1, "xp" => 150],
                ["q" => "Which symbol is used for multiplication in a spreadsheet formula?", "options" => ["x", "*", "/", "-"], "ans" => 1, "xp" => 150],
                ["q" => "Which symbol is used for division in a spreadsheet formula?", "options" => ["\\", "%", "÷", "/"], "ans" => 3, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which function automatically adds up a range of numbers?", "options" => ["=TOTAL()", "=ADD()", "=SUM()", "=PLUS()"], "ans" => 2, "xp" => 200],
                ["q" => "What does the formula =AVERAGE(A1:A10) do?", "options" => ["Finds the middle number", "Calculates the mathematical mean of values from A1 through A10", "Adds A1 and A10 and divides by 2", "Counts how many cells have data"], "ans" => 1, "xp" => 200],
                ["q" => "Which function finds the highest value in a selected range?", "options" => ["=TOP()", "=HIGH()", "=MAX()", "=PEAK()"], "ans" => 2, "xp" => 200],
                ["q" => "What does the colon (:) signify in a range like B2:B10?", "options" => ["Divide B2 by B10", "Select only B2 and B10", "Select the continuous range of cells from B2 through B10", "It is a typo"], "ans" => 2, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "If you want a cell to display currency (e.g., $5.00 instead of 5), what should you do?", "options" => ["Type the dollar sign manually every time", "Change the cell's Number Format to Currency", "It cannot be done", "Use a special keyboard"], "ans" => 1, "xp" => 250],
                ["q" => "What does 'Merge & Center' do?", "options" => ["Combines multiple selected cells into one larger cell and centers the text", "Averages the numbers in the cells", "Sorts the data alphabetically", "Deletes empty cells"], "ans" => 0, "xp" => 250],
                ["q" => "What tool allows you to alphabetize a list of names in a column?", "options" => ["Filter", "Find & Replace", "Sort (A to Z)", "AutoSum"], "ans" => 2, "xp" => 250],
                ["q" => "How can you visually separate data in a spreadsheet for printing?", "options" => ["Apply Cell Borders", "Change the background to black", "Print it on colored paper", "Use larger fonts everywhere"], "ans" => 0, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which feature allows you to lock the top row so it remains visible as you scroll down a long list?", "options" => ["Lock Cells", "Freeze Panes", "Pin Row", "Sticky Header"], "ans" => 1, "xp" => 300],
                ["q" => "What is a fast way to visually compare data using bars, lines, or slices?", "options" => ["Use conditional formatting", "Insert a Chart or Graph", "Color cells manually", "Draw on the screen"], "ans" => 1, "xp" => 300],
                ["q" => "If a spreadsheet is slightly too wide to print on one page, what setting helps fix this?", "options" => ["Delete the last column", "Change scaling to 'Fit all columns on one page'", "Cut the paper in half", "Increase the font size"], "ans" => 1, "xp" => 300],
                ["q" => "What does it mean when a formula shows the error '#DIV/0!'?", "options" => ["The formula is trying to divide a number by zero or an empty cell", "The spelling is incorrect", "The cell is locked", "The printer is disconnected"], "ans" => 0, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 2,
        "title" => "Map 7: Internet Research & Browsing", "desc" => "Navigate the World Wide Web effectively, use search engines, and evaluate online information.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a Web Browser?", "options" => ["The company providing internet access", "A software application used to access and view websites", "A search engine", "The physical cables underground"], "ans" => 1, "xp" => 100],
                ["q" => "Which of the following is an example of a Web Browser?", "options" => ["Google (Search Engine)", "Windows 11", "Mozilla Firefox", "Microsoft Word"], "ans" => 2, "xp" => 100],
                ["q" => "What does URL stand for?", "options" => ["Universal Routing Link", "Uniform Resource Locator (a web address)", "Ultra Reliable Line", "Unified Response Language"], "ans" => 1, "xp" => 100],
                ["q" => "Where do you type a URL to go directly to a specific website?", "options" => ["The Taskbar", "The Search Engine Box", "The Address Bar at the top of the browser", "The Status Bar"], "ans" => 2, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which button reloads the current webpage to show updated content?", "options" => ["Home", "Back", "Refresh / Reload", "Stop"], "ans" => 2, "xp" => 120],
                ["q" => "What is the purpose of 'Browser Tabs'?", "options" => ["To pay for websites", "To open multiple web pages within a single browser window", "To organize bookmarks", "To close the browser completely"], "ans" => 1, "xp" => 120],
                ["q" => "How can you save a website address to visit it easily later?", "options" => ["Create a Bookmark / Favorite", "Download the whole website", "Take a photo of the screen", "Write the HTML code down"], "ans" => 0, "xp" => 120],
                ["q" => "What is a hyperlink (or link)?", "options" => ["A very fast internet connection", "Clickable text or image that connects to another webpage or file", "A virus", "A printed web page"], "ans" => 1, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the difference between a Browser and a Search Engine?", "options" => ["They are the exact same thing", "A browser is the software; a search engine is a website used to find other websites", "A browser searches; a search engine browses", "A search engine requires no internet"], "ans" => 1, "xp" => 150],
                ["q" => "When searching Google, how can you find exact matches for a specific phrase?", "options" => ["Type it in ALL CAPS", "Put the phrase in quotation marks (e.g., \"global warming facts\")", "Add a hashtag", "Type it backwards"], "ans" => 1, "xp" => 150],
                ["q" => "How can you exclude a word from your search results (e.g., searching for apples, but not computers)?", "options" => ["apple NOT computer", "apple -computer (using a minus sign)", "apple [exclude computer]", "apple /computer"], "ans" => 1, "xp" => 150],
                ["q" => "What does the extension '.gov' at the end of a URL indicate?", "options" => ["A commercial business", "An educational institution", "A government entity", "A non-profit organization"], "ans" => 2, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Browser History'?", "options" => ["A textbook about the internet", "A chronological log of all websites you have visited", "The date the browser was created", "Your saved passwords"], "ans" => 1, "xp" => 200],
                ["q" => "What are 'Cookies' in web browsing?", "options" => ["Digital snacks", "Small files saved by websites to remember your preferences and login state", "Viruses that destroy files", "Pop-up advertisements"], "ans" => 1, "xp" => 200],
                ["q" => "What does 'Incognito' or 'Private Browsing' mode do?", "options" => ["Hides you from hackers completely", "Gives you free internet access", "Prevents the browser from saving your local history, cookies, and search data", "Speeds up your internet"], "ans" => 2, "xp" => 200],
                ["q" => "Why should you occasionally clear your browser's Cache?", "options" => ["To resolve loading errors and free up hard drive space", "To delete viruses", "To cancel internet subscriptions", "To log out of Windows"], "ans" => 0, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "How can you securely download an image you see on a webpage?", "options" => ["Click the screen with a pen", "Right-click the image and select 'Save image as...'", "Drag the monitor", "Press Ctrl+P"], "ans" => 1, "xp" => 250],
                ["q" => "What does the 'Padlock' icon next to a URL mean?", "options" => ["The website requires a password", "The connection between your browser and the server is encrypted (HTTPS)", "The website is locked and cannot be viewed", "The website is 100% free of viruses"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Pop-up Blocker'?", "options" => ["A tool that stops secondary advertisement windows from opening automatically", "A physical screen cover", "An antivirus scanner", "A firewall"], "ans" => 0, "xp" => 250],
                ["q" => "What is a 'Browser Extension' or 'Add-on'?", "options" => ["A longer internet cable", "A small software module that adds custom features to your web browser", "A fee charged by your ISP", "A larger monitor"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which strategy is best for evaluating the credibility of a source online?", "options" => ["Trusting the first result always", "Checking the author's credentials, date of publication, and verifying claims on other sites", "Believing it if it looks professional", "Asking a friend"], "ans" => 1, "xp" => 300],
                ["q" => "What does it mean if an article is 'Clickbait'?", "options" => ["It uses an exaggerated or misleading headline to attract clicks and pageviews", "It contains computer viruses", "It is an academic article", "It is written by AI"], "ans" => 0, "xp" => 300],
                ["q" => "What is the safest way to log in to a banking website?", "options" => ["Clicking a link in a random email", "Typing the bank's official URL directly into the address bar", "Searching for the bank and clicking the first ad", "Using a public computer"], "ans" => 1, "xp" => 300],
                ["q" => "If a webpage suddenly asks you to download a 'critical security update' out of nowhere, what should you do?", "options" => ["Download it immediately", "Close the tab or browser, as it is likely a malicious scam", "Provide your credit card", "Call the phone number on the screen"], "ans" => 1, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 2,
        "title" => "Map 8: Digital Communication & Etiquette", "desc" => "Master professional email usage, instant messaging, and video conferencing.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does the 'To' field in an email represent?", "options" => ["The subject of the email", "The primary recipient(s) expected to read and act on the email", "The sender's address", "The attached files"], "ans" => 1, "xp" => 100],
                ["q" => "What does 'CC' stand for in an email?", "options" => ["Carbon Copy (for keeping others informed without expecting action)", "Computer Code", "Creative Commons", "Copy & Cut"], "ans" => 0, "xp" => 100],
                ["q" => "What is the purpose of the 'Subject Line'?", "options" => ["To write the entire message", "To provide a brief, clear summary of the email's content", "To add passwords", "To attach documents"], "ans" => 1, "xp" => 100],
                ["q" => "Which button do you click to respond ONLY to the person who sent you an email?", "options" => ["Reply All", "Forward", "Reply", "Send"], "ans" => 2, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'BCC' (Blind Carbon Copy) used for?", "options" => ["Sending an invisible email", "Copying recipients without letting others on the email chain see their addresses", "Copying the email to a USB drive", "Bolding the text"], "ans" => 1, "xp" => 120],
                ["q" => "What happens when you click 'Reply All'?", "options" => ["Your response goes to the sender AND everyone else in the To and CC fields", "Your response goes to everyone in your contact list", "It deletes the email", "It translates the email into another language"], "ans" => 0, "xp" => 120],
                ["q" => "What is an 'Attachment'?", "options" => ["A feeling of affection for the email", "A file (like a PDF or photo) sent along with the email message", "A signature at the bottom", "A virus scanner"], "ans" => 1, "xp" => 120],
                ["q" => "If you receive an email with an attachment from someone you don't know, what is the safest action?", "options" => ["Open it immediately", "Forward it to all your contacts", "Do not open the attachment; verify or delete the email", "Reply and ask for more attachments"], "ans" => 2, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Which feature automatically adds your name, title, and contact info to the bottom of every outgoing email?", "options" => ["Auto-reply", "Email Signature", "Subject Line", "Attachment"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Out of Office' (or Vacation) Responder?", "options" => ["A program that turns off your computer", "An automated reply letting senders know you are away and when you will return", "A virus that deletes emails", "A tool for deleting spam"], "ans" => 1, "xp" => 150],
                ["q" => "Why shouldn't you type professional emails in ALL CAPS?", "options" => ["It uses too much ink when printed", "In digital etiquette, ALL CAPS is interpreted as SHOUTING or aggression", "It breaks the keyboard", "It causes spelling errors"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Spam' or 'Junk' folder?", "options" => ["A folder for important emails", "A filter destination for unsolicited, bulk, or suspicious emails", "A folder for drafts", "A folder for deleted items"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "When sending a large 50MB video file to a colleague, what is the best method?", "options" => ["Attach it to an email", "Upload it to cloud storage (like OneDrive/Google Drive) and email them the share link", "Break the video into 10 smaller emails", "Print the video"], "ans" => 1, "xp" => 200],
                ["q" => "What does 'Forwarding' an email do?", "options" => ["Returns it to the sender", "Sends the existing email thread and attachments to a new recipient", "Deletes it from the server", "Translates it to Spanish"], "ans" => 1, "xp" => 200],
                ["q" => "How can you keep your inbox organized if you receive hundreds of emails?", "options" => ["Delete them all", "Use Folders, Labels, and Rules/Filters to categorize them", "Print every email", "Reply to all of them with 'Received'"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Phishing' in the context of email?", "options" => ["Emails about fishing trips", "Fraudulent emails designed to trick you into revealing passwords or financial info", "Emails with very large attachments", "Automated marketing emails"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "In professional instant messaging (like Slack or Teams), what does a 'Red Dot' or 'Busy' status mean?", "options" => ["The person is fired", "The person is currently occupied and replies may be delayed", "The person's computer is broken", "The network is down"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Thread' in a messaging app?", "options" => ["A virus", "A specific chain of replies organized under one main message, keeping chats tidy", "A group of administrators", "A deleted message"], "ans" => 1, "xp" => 250],
                ["q" => "Is it appropriate to send complex, multi-paragraph project details via instant message?", "options" => ["Yes, always", "No, long or complex information is better suited for an Email or Document", "Yes, if you use emojis", "Only to your boss"], "ans" => 1, "xp" => 250],
                ["q" => "If you need an immediate, urgent answer from a coworker, which is generally the best tool?", "options" => ["A physical letter", "An Email", "An Instant Message or Phone Call", "A Calendar Invite"], "ans" => 2, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the most crucial etiquette rule when joining a large video conference meeting?", "options" => ["Eat loudly", "Mute your microphone when you are not speaking to eliminate background noise", "Leave your camera off the entire time", "Talk over the presenter"], "ans" => 1, "xp" => 300],
                ["q" => "What does 'Screen Sharing' allow you to do during a video call?", "options" => ["Show the participants exactly what is on your computer screen (like a presentation)", "Share your passwords automatically", "Take a picture of the participants", "Turn off their monitors"], "ans" => 0, "xp" => 300],
                ["q" => "How can you maintain professionalism on camera if your room is messy?", "options" => ["Sit in the dark", "Use the 'Blur Background' or 'Virtual Background' feature", "Wear sunglasses", "Apologize repeatedly"], "ans" => 1, "xp" => 300],
                ["q" => "If participants say you sound 'muffled' or 'echoey', what is a good technical solution?", "options" => ["Yell louder", "Use a headset with a dedicated microphone instead of laptop speakers", "Turn off your camera", "Type everything instead"], "ans" => 1, "xp" => 300]
            ]]
        ]
    ],

    // ==========================================================
    // CATEGORY 3: VALEDICTORY CAPSTONE (Maps 9-12)
    // ==========================================================
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 3,
        "title" => "Map 9: Cybersecurity & Device Protection", "desc" => "Protect your digital identity, secure your data, and defend against online threats.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the most fundamental step to securing an online account?", "options" => ["Using the same password everywhere", "Using a strong, unique password for every account", "Never logging out", "Sharing your password with friends for backup"], "ans" => 1, "xp" => 100],
                ["q" => "Which of these is an example of a STRONG password?", "options" => ["password123", "admin", "12345678", "Tr@ctor$Blue99!Sky"], "ans" => 3, "xp" => 100],
                ["q" => "What tool securely stores all your complex passwords so you don't have to memorize them?", "options" => ["A sticky note on your monitor", "A Password Manager software", "A Word document named 'Passwords'", "An email draft"], "ans" => 1, "xp" => 100],
                ["q" => "What does 'Biometric' authentication mean?", "options" => ["Using math to log in", "Using physical characteristics like a fingerprint or facial recognition to verify identity", "A password that changes every minute", "Using two passwords"], "ans" => 1, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is Two-Factor Authentication (2FA / MFA)?", "options" => ["Requiring two people to log in", "Requiring a password PLUS a second verification step (like a text code or app prompt)", "Typing your password twice", "Using two different web browsers"], "ans" => 1, "xp" => 120],
                ["q" => "Why is 2FA critically important?", "options" => ["It speeds up login times", "Even if a hacker steals your password, they cannot access the account without the second factor (e.g., your phone)", "It prevents your phone from dying", "It gives you free internet"], "ans" => 1, "xp" => 120],
                ["q" => "If you receive a 2FA code via text message but you didn't try to log in, what does it mean?", "options" => ["The system is broken", "Someone has your password and is trying to break into your account; change your password immediately", "You should reply to the text with your password", "Nothing, ignore it safely"], "ans" => 1, "xp" => 120],
                ["q" => "What is an Authenticator App (e.g., Google Authenticator)?", "options" => ["An app that generates temporary 6-digit codes used for 2FA", "An app that guarantees websites are real", "An antivirus scanner", "A password generator"], "ans" => 0, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What general term describes software meant to damage or disable computers?", "options" => ["Firmware", "Malware", "Freeware", "Hardware"], "ans" => 1, "xp" => 150],
                ["q" => "What type of malware locks your files and demands payment to unlock them?", "options" => ["Spyware", "Ransomware", "Adware", "Trojan Horse"], "ans" => 1, "xp" => 150],
                ["q" => "What does Antivirus software do?", "options" => ["Cleans physical dust from the CPU", "Scans for, blocks, and removes malicious software", "Organizes your files", "Increases internet speed"], "ans" => 1, "xp" => 150],
                ["q" => "What is a Firewall?", "options" => ["A physical wall around the server room", "A security system that monitors and controls incoming/outgoing network traffic based on rules", "A program that burns CDs", "A tool for deleting old files"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the primary danger of using open, public Wi-Fi (like at a coffee shop)?", "options" => ["It uses your battery faster", "Data sent over unencrypted public networks can be intercepted by hackers nearby", "It automatically downloads viruses", "It prevents you from using a mouse"], "ans" => 1, "xp" => 200],
                ["q" => "What does a VPN (Virtual Private Network) do?", "options" => ["Creates a secure, encrypted tunnel for your internet traffic, protecting it on public networks", "Speeds up your computer processor", "Acts as an antivirus", "Deletes your browsing history permanently"], "ans" => 0, "xp" => 200],
                ["q" => "Why should you never plug a random USB flash drive you found into your computer?", "options" => ["It might be too small", "It could be programmed to secretly install malware or steal data the moment it's plugged in", "It will erase your hard drive instantly", "It will format itself"], "ans" => 1, "xp" => 200],
                ["q" => "If a website URL begins with HTTP instead of HTTPS, what does this mean?", "options" => ["It is faster", "The connection is not encrypted, making data like passwords vulnerable to interception", "The website is broken", "It is an older version of the internet"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Which tactic involves a hacker tricking a person into voluntarily giving up confidential info?", "options" => ["Brute Force", "Social Engineering", "Port Scanning", "DDoS"], "ans" => 1, "xp" => 250],
                ["q" => "You get a call from 'Microsoft Tech Support' saying your PC has a virus and they need remote access. What do you do?", "options" => ["Give them access to fix it", "Hang up immediately; tech companies do not cold-call users for support", "Pay the fee they ask for", "Ask them for their employee ID"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Trojan' in cybersecurity?", "options" => ["A fast computer", "Malware disguised as legitimate software to trick users into installing it", "A secure firewall", "A type of password"], "ans" => 1, "xp" => 250],
                ["q" => "How can you verify if a link in an email is safe BEFORE clicking it?", "options" => ["Click it quickly and press back", "Hover your mouse over the link without clicking to preview the actual destination URL", "Forward it to a friend to test", "Ask the sender"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "If your computer is acting extremely sluggish and showing pop-ups constantly, what should you do first?", "options" => ["Buy a new computer", "Run a full system scan with updated Antivirus/Anti-malware software", "Unplug the mouse", "Format the C: Drive"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Shoulder Surfing'?", "options" => ["Surfing the web standing up", "Someone physically watching you type your password or view sensitive data from behind", "A type of malware", "Using two monitors"], "ans" => 1, "xp" => 300],
                ["q" => "What should you do before selling or giving away an old computer?", "options" => ["Empty the recycle bin", "Log out of Facebook", "Perform a complete factory reset / secure wipe of the hard drive", "Change your wallpaper"], "ans" => 2, "xp" => 300],
                ["q" => "If you suspect your credit card information was stolen online, what is the best immediate step?", "options" => ["Change your Facebook password", "Contact your bank/credit card provider immediately to freeze the card", "Wait a few days to see if charges appear", "Run an antivirus scan"], "ans" => 1, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 3,
        "title" => "Map 10: Cloud Computing & Data Backup", "desc" => "Understand the cloud, sync files across devices, and ensure data survival through backups.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "In basic terms, what is 'The Cloud'?", "options" => ["A satellite in space", "A network of remote servers accessed over the internet used to store and process data", "A weather application", "A hard drive inside your computer"], "ans" => 1, "xp" => 100],
                ["q" => "Which of the following is a popular Cloud Storage service?", "options" => ["Microsoft Word", "Windows Explorer", "Google Drive", "VLC Media Player"], "ans" => 2, "xp" => 100],
                ["q" => "What is the primary requirement to access your files stored in the cloud?", "options" => ["A specific brand of computer", "An active Internet Connection", "A USB cable", "A premium subscription"], "ans" => 1, "xp" => 100],
                ["q" => "What is the opposite of Cloud Storage?", "options" => ["Local Storage (saving directly to the device's hard drive)", "Rain Storage", "Network Storage", "Flash Storage"], "ans" => 0, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does it mean to 'Sync' (Synchronize) files?", "options" => ["To delete them permanently", "To ensure files on your computer match exactly with the copies in the cloud, updating automatically", "To encrypt them", "To print them"], "ans" => 1, "xp" => 120],
                ["q" => "If you edit a synced Word document on your laptop, what happens when you open it later on your phone?", "options" => ["It shows the old version", "It will reflect the newest changes made on the laptop", "It will be deleted", "It will ask for a password"], "ans" => 1, "xp" => 120],
                ["q" => "What does 'Uploading' mean?", "options" => ["Copying data FROM the cloud TO your local device", "Copying data FROM your local device TO the cloud/internet", "Starting the computer", "Updating the software"], "ans" => 1, "xp" => 120],
                ["q" => "What does 'Downloading' mean?", "options" => ["Copying data FROM the internet/cloud TO your local device", "Sending an email", "Saving a file to a USB", "Closing a web page"], "ans" => 0, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Instead of emailing a large file, how can you use cloud storage to share it?", "options" => ["Mail them a printed copy", "Generate a shareable link and email the link", "Text them the file", "It's impossible"], "ans" => 1, "xp" => 150],
                ["q" => "If you share a cloud link and give the person 'Viewer' permission, what can they do?", "options" => ["Read/view the file, but not make any changes", "Edit the text", "Delete the file from your drive", "Share it with the whole internet automatically"], "ans" => 0, "xp" => 150],
                ["q" => "What does 'Editor' permission allow?", "options" => ["Only reading the file", "Viewing, modifying, and potentially deleting content in the file", "Only printing the file", "Only downloading the file"], "ans" => 1, "xp" => 150],
                ["q" => "What is a major benefit of cloud-based word processors (like Google Docs or Word Online)?", "options" => ["They require no electricity", "Multiple people can edit the exact same document simultaneously in real-time", "They cannot get viruses", "They are always 100% private"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Backup'?", "options" => ["A reverse gear", "A duplicate copy of data stored separately to protect against data loss", "A battery for the computer", "A type of firewall"], "ans" => 1, "xp" => 200],
                ["q" => "Why is keeping a backup on the SAME hard drive as the original file a bad idea?", "options" => ["It takes up too much space", "If the hard drive physically fails, you lose both the original and the backup", "The computer will run slower", "It is illegal"], "ans" => 1, "xp" => 200],
                ["q" => "What does the '3-2-1 Backup Rule' stand for?", "options" => ["3 clicks, 2 folders, 1 file", "3 copies of data, on 2 different media types, with 1 copy kept offsite (or in the cloud)", "3 hours to backup 2 gigabytes in 1 minute", "3 passwords for 2 users on 1 PC"], "ans" => 1, "xp" => 200],
                ["q" => "If your laptop is stolen, are your locally stored files (not synced to the cloud) lost?", "options" => ["No, you can download them from the manufacturer", "Yes, unless you have them backed up elsewhere (like an external drive or cloud)", "No, the police have a copy", "Yes, but they magically reappear on your next laptop"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Version History' in cloud documents?", "options" => ["A list of updates to the software", "A feature that saves previous iterations of a file, allowing you to restore an older version if you make a mistake", "A historical record of the internet", "A list of users"], "ans" => 1, "xp" => 250],
                ["q" => "How can you backup an entire Windows or Mac computer locally?", "options" => ["Use an External Hard Drive and built-in software like File History or Time Machine", "Copy all files to a single CD-ROM", "Print every document", "Take photos of the screen"], "ans" => 0, "xp" => 250],
                ["q" => "If a cloud service goes bankrupt and shuts down tomorrow, what happens to your data?", "options" => ["It is moved to a different cloud", "You could lose it entirely if you don't have a local backup", "It is mailed to you on a USB", "The government saves it"], "ans" => 1, "xp" => 250],
                ["q" => "When a synced file is deleted from your local 'OneDrive' or 'Dropbox' folder, what happens?", "options" => ["Nothing", "It is also deleted from the cloud and any other synced devices", "It creates a backup", "The computer restarts"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "How can you recover a file you accidentally deleted from your Cloud Storage?", "options" => ["Call the internet provider", "Log into the web interface and check the Cloud Trash/Recycle Bin within 30 days", "Buy a new account", "It is impossible"], "ans" => 1, "xp" => 300],
                ["q" => "What happens if your local hard drive is full, but you need access to cloud files?", "options" => ["You must delete Windows", "Use 'Files On-Demand', which keeps a shortcut locally but stores the actual file in the cloud until opened", "You must buy a new laptop", "The cloud shrinks the files permanently"], "ans" => 1, "xp" => 300],
                ["q" => "What is the primary drawback of using Cloud Backup exclusively?", "options" => ["It's too fast", "Restoring a massive amount of data (like a whole hard drive) can take days depending on internet speed", "It is always free", "It requires CDs"], "ans" => 1, "xp" => 300],
                ["q" => "Which is the most robust setup for critical business files?", "options" => ["Saved only to Desktop", "Saved to a USB stick in your pocket", "Synced to cloud storage AND backed up nightly to an external drive", "Emailed to yourself"], "ans" => 2, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 3,
        "title" => "Map 11: Privacy, Ethics & Digital Citizenship", "desc" => "Navigate the internet ethically, protect personal privacy, and build a positive digital footprint.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Digital Footprint'?", "options" => ["The physical space a computer takes up", "The trail of data you create while using the internet (posts, searches, comments)", "A virus tracker", "Your internet speed"], "ans" => 1, "xp" => 100],
                ["q" => "How long can information posted online potentially last?", "options" => ["24 hours", "One year", "Indefinitely; it can be archived or screenshot by others even if deleted", "Until you turn off your PC"], "ans" => 2, "xp" => 100],
                ["q" => "What is 'Netiquette'?", "options" => ["A type of fishing net", "The correct or acceptable way of communicating on the internet", "Network configuration", "A firewall setting"], "ans" => 1, "xp" => 100],
                ["q" => "If you see cyberbullying happening in a forum or chat, what is the best action?", "options" => ["Join in to look cool", "Ignore it completely", "Report the behavior to a moderator or trusted authority and support the victim", "Reply with angry insults"], "ans" => 2, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is Plagiarism in the digital age?", "options" => ["Copying someone else's digital work (text, images, ideas) and presenting it as your own", "Downloading free software", "Typing very fast", "Quoting a source with proper credit"], "ans" => 0, "xp" => 120],
                ["q" => "What does Copyright protect?", "options" => ["Ideas in your head", "Original works of authorship (like articles, photos, music) from being copied without permission", "Computer hardware", "Public domain facts"], "ans" => 1, "xp" => 120],
                ["q" => "If you find an image on Google Images, can you legally use it in a commercial presentation?", "options" => ["Yes, everything on Google is free", "No, unless it is licensed for reuse (e.g., Creative Commons) or you have permission", "Yes, if you change the color", "Yes, if you don't tell anyone"], "ans" => 1, "xp" => 120],
                ["q" => "What is 'Public Domain'?", "options" => ["A website address", "Creative works whose intellectual property rights have expired or been forfeited, making them free to use", "A public Wi-Fi network", "Government secrets"], "ans" => 1, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary purpose of a website's 'Terms of Service' (ToS) or 'Privacy Policy'?", "options" => ["To teach you how to use a mouse", "To explain what data the company collects and the legal rules for using their platform", "To make the website load faster", "To charge you money"], "ans" => 1, "xp" => 150],
                ["q" => "When an app asks to track your location, what should you consider?", "options" => ["Always say yes", "Does this app actually need my location to function (e.g., Maps vs a Calculator)?", "Always say no", "Turn off the phone"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Doxxing'?", "options" => ["Fixing a computer", "Maliciously publishing private/identifying information about a person on the internet", "Sending a document", "Deleting files"], "ans" => 1, "xp" => 150],
                ["q" => "Why should you be cautious about posting vacation photos while you are still away?", "options" => ["It uses too much data", "It broadcasts to the public that your home is currently empty", "The photos will be low quality", "It is against social media rules"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the 'Echo Chamber' effect on social media?", "options" => ["When audio on a video call echoes", "When algorithms only show you information that aligns with your existing beliefs, limiting diverse perspectives", "A group chat with many people", "When you reply to your own posts"], "ans" => 1, "xp" => 200],
                ["q" => "What is a 'Deepfake'?", "options" => ["A very deep folder structure", "AI-manipulated media (video or audio) that convincingly replaces one person's likeness/voice with another", "A secure password", "A hidden webpage"], "ans" => 1, "xp" => 200],
                ["q" => "Before sharing a sensational news article on social media, what is a crucial digital citizenship step?", "options" => ["Add lots of emojis", "Fact-check the claim using reputable, independent sources", "Tag all your friends", "Change the headline"], "ans" => 1, "xp" => 200],
                ["q" => "What does 'Anonymity' on the internet often lead to?", "options" => ["Faster typing", "The 'Online Disinhibition Effect,' where people act ruder or bolder than they would in real life", "Better grammar", "More secure passwords"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is Digital Wellbeing?", "options" => ["Buying a new computer", "Practicing healthy habits regarding screen time and technology use to protect physical and mental health", "Using antivirus software", "Upgrading your internet"], "ans" => 1, "xp" => 250],
                ["q" => "How can blue light from screens affect you?", "options" => ["It causes a sunburn", "It can disrupt melatonin production, making it harder to fall asleep if used before bed", "It permanently damages the retina immediately", "It drains the battery faster"], "ans" => 1, "xp" => 250],
                ["q" => "What is the 20-20-20 rule for eye strain?", "options" => ["Buy a 20-inch monitor for 20 dollars in 20 minutes", "Every 20 minutes, look at something 20 feet away for at least 20 seconds", "Blink 20 times every 20 hours", "Type 20 words in 20 seconds"], "ans" => 1, "xp" => 250],
                ["q" => "What feature on most modern smartphones helps track and limit app usage?", "options" => ["Screen Time / Digital Wellbeing dashboards", "The Camera", "The Calculator", "Bluetooth"], "ans" => 0, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "If an employer searches your name on Google, what are they likely looking for?", "options" => ["Your high score in a video game", "Red flags in your digital footprint (unprofessional behavior, extreme opinions)", "Your internet speed test", "Your computer brand"], "ans" => 1, "xp" => 300],
                ["q" => "When is it ethically acceptable to use someone's secure Wi-Fi network without permission?", "options" => ["When you need to send a quick email", "It is never ethically or legally acceptable (often considered theft of service)", "When they are not home", "When the signal is strong"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Phubbing'?", "options" => ["Fixing a USB hub", "Ignoring a person in a real-life social setting to look at your phone (Phone Snubbing)", "Sending spam emails", "Deleting photos"], "ans" => 1, "xp" => 300],
                ["q" => "As a responsible digital citizen, how should you handle data breaches involving your favorite service?", "options" => ["Ignore it", "Promptly change your passwords and monitor your financial statements", "Sue the internet provider", "Delete your browser"], "ans" => 1, "xp" => 300]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $target_course_id, "category_id" => 3,
        "title" => "Map 12: Capstone Final Assessment", "desc" => "Bring it all together! Prove your mastery of computer skills through advanced, real-world scenarios.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: Your computer won't turn on at all (no lights, no sounds). What is the FIRST thing to check?", "options" => ["Check if the power cable is firmly connected and the wall outlet has power", "Buy a new motherboard", "Reinstall Windows", "Check the Wi-Fi router"], "ans" => 0, "xp" => 100],
                ["q" => "SCENARIO: You want to move a photo from your Desktop into a folder named 'Vacation'. How do you do it?", "options" => ["Delete the photo", "Click, drag, and drop the photo onto the 'Vacation' folder icon", "Use Ctrl+P", "Change the photo's extension to .vacation"], "ans" => 1, "xp" => 100],
                ["q" => "SCENARIO: The text on your screen is too small to read. Where do you go to fix this?", "options" => ["Display Settings to increase the Scale/Resolution", "Task Manager", "The Printer Settings", "The Recycle Bin"], "ans" => 0, "xp" => 100],
                ["q" => "SCENARIO: You typed an entire page in a word processor, but suddenly the power goes out. You hadn't saved. What happens?", "options" => ["The document is fully saved on the monitor", "Unless AutoRecover caught it, the unsaved data in RAM is lost", "It automatically emailed to you", "It printed out"], "ans" => 1, "xp" => 100]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: You are typing a report and accidentally delete a whole paragraph. What is the fastest fix?", "options" => ["Retype it from memory", "Press Ctrl + Z (Undo)", "Restart the computer", "Run a spell check"], "ans" => 1, "xp" => 120],
                ["q" => "SCENARIO: You need to send four separate PDF documents to a client in a single email, but the files are disorganized. What is best?", "options" => ["Send 4 different emails", "Place them in a folder, compress/ZIP the folder, and attach the single ZIP file", "Paste the text of all PDFs into the email body", "Fax them"], "ans" => 1, "xp" => 120],
                ["q" => "SCENARIO: Your web browser says 'No Internet Connection', but your phone (on the same Wi-Fi) works fine. What's the likely issue?", "options" => ["The whole internet is down", "An issue specific to the computer's Wi-Fi adapter or settings", "Your monitor is broken", "The website was deleted"], "ans" => 1, "xp" => 120],
                ["q" => "SCENARIO: A file named 'Financial_Report.xlsx' won't open when you double-click it. Why?", "options" => ["It is a virus", "You don't have a spreadsheet application (like Excel) installed to read it", "The file is too old", "You need a new mouse"], "ans" => 1, "xp" => 120]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: You receive an urgent email from your 'Boss' asking you to buy $500 in gift cards and reply with the codes. What do you do?", "options" => ["Buy the cards immediately", "Verify the sender's actual email address and call your boss directly to confirm; this is a classic scam", "Forward it to your mom", "Reply with fake codes"], "ans" => 1, "xp" => 150],
                ["q" => "SCENARIO: You want to average the sales for the week in cells B2 through B6. What formula do you type?", "options" => ["=AVERAGE(B2:B6)", "=SUM(B2:B6)/2", "AVERAGE(B2+B6)", "=B2+B6/AVG"], "ans" => 0, "xp" => 150],
                ["q" => "SCENARIO: You downloaded an installer for a new web browser. It is called 'setup.exe'. Where is it most likely located?", "options" => ["In the Recycle Bin", "In the 'Downloads' folder", "In the Cloud", "On the Desktop automatically"], "ans" => 1, "xp" => 150],
                ["q" => "SCENARIO: You are in a loud coffee shop on a Zoom call and someone else is speaking. What should you do?", "options" => ["Turn off your camera", "Click the 'Mute' button until you need to speak", "Yell at the barista", "Close the laptop"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: Your laptop trackpad stops working, but you must finish an urgent email. What can you do?", "options" => ["Use the keyboard (Tab, Arrows, Enter) to navigate, or plug in a USB mouse", "You cannot finish the email", "Shake the laptop", "Use a touchscreen pen on a non-touch screen"], "ans" => 0, "xp" => 200],
                ["q" => "SCENARIO: You need to securely send confidential tax documents to your accountant. How should you do it?", "options" => ["Post it on Facebook", "Send via unencrypted email", "Use a secure Cloud Storage link with a password and expiration date", "Put it on a USB and mail it without a tracking number"], "ans" => 2, "xp" => 200],
                ["q" => "SCENARIO: Your computer is frozen on a specific application and the mouse won't move. Ctrl+Alt+Del does nothing. What is the last resort?", "options" => ["Throw it away", "Press and hold the physical Power button for 5-10 seconds to force a hard shutdown", "Unplug the monitor", "Press the Spacebar repeatedly"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You need an exact phrase 'Global Market Trends 2026' in Google. How do you format the search?", "options" => ["Global AND Market AND Trends", "\"Global Market Trends 2026\"", "Global_Market_Trends_2026", "Search(Global Market Trends 2026)"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "CAPSTONE: Which of the following describes the complete, proper way to back up critical data?", "options" => ["Copy files to a folder on the Desktop", "Email the files to yourself once a year", "Keep original files on the PC, a local copy on an external drive, and a synced copy in a secure Cloud service (3-2-1 rule)", "Print out the ones and zeros"], "ans" => 2, "xp" => 250],
                ["q" => "CAPSTONE: A colleague sent you a file ending in .zip, but your email blocked it as a security threat. Why?", "options" => ["ZIP files are always illegal", "Hackers often hide malicious .exe files inside .zip archives to bypass basic email scanners", "The file was too small", "Your email is out of space"], "ans" => 1, "xp" => 250],
                ["q" => "CAPSTONE: You step away from your desk in a busy office. What combination of keys should you press before walking away?", "options" => ["Windows Key + L (Lock)", "Alt + F4", "Ctrl + P", "Ctrl + Shift + Esc"], "ans" => 0, "xp" => 250],
                ["q" => "CAPSTONE: When formatting a formal business letter in a word processor, how should the text be aligned?", "options" => ["Centered", "Right-aligned", "Left-aligned or Justified, with a clear, readable font (e.g., Arial or Times New Roman)", "Zig-zag aligned"], "ans" => 2, "xp" => 250]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "MASTER EXAM: How do Hardware, OS, and Applications work together?", "options" => ["They don't; they are separate", "The OS manages the Hardware, providing a platform for Applications to run and allow the user to perform tasks", "Applications manage the OS", "Hardware runs Applications directly without an OS"], "ans" => 1, "xp" => 300],
                ["q" => "MASTER EXAM: You have to collaborate on a spreadsheet with three remote coworkers simultaneously. What is the BEST tool?", "options" => ["Microsoft Excel offline, emailing versions back and forth (v1, v2, v3)", "Google Sheets or Excel Online (Cloud collaboration)", "A physical whiteboard on a webcam", "Printing the sheet and faxing it"], "ans" => 1, "xp" => 300],
                ["q" => "MASTER EXAM: You click a link and your browser warns you that the connection is 'Not Private' (invalid certificate). What should you do?", "options" => ["Click 'Proceed anyway' to see what it is", "Enter your credit card just in case", "Heed the warning and close the tab, as the site may be compromised or intercepted", "Turn off your antivirus"], "ans" => 2, "xp" => 300],
                ["q" => "MASTER EXAM: To ensure your digital workspace remains secure and efficient long-term, you should...", "options" => ["Never update software, use 'password' for everything, and click all links", "Regularly install OS updates, use a password manager, run antivirus scans, and back up data", "Delete all files daily", "Only use the computer offline"], "ans" => 1, "xp" => 300]
            ]]
        ]
    ]
];

// ==========================================================
// INTERMEDIATE COMPUTER SKILLS CURRICULUM (Maps 13-24)
// ==========================================================
$inter_maps = [
    // CATEGORY 1: ADVANCED PRODUCTIVITY (Maps 13-16)
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 1,
        "title" => "Map 13: Professional Document Styling", "desc" => "Master Styles, Table of Contents, and automated document structures.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary benefit of using 'Styles' (Heading 1, Heading 2) instead of manual bolding?", "options" => ["It changes the font color only", "It allows for the automatic generation of a Table of Contents", "It saves the file faster", "It prevents others from editing"], "ans" => 1, "xp" => 150],
                ["q" => "Where can you find the 'Navigation Pane' to see a structural outline of your document?", "options" => ["Layout Tab", "Review Tab", "View Tab", "Insert Tab"], "ans" => 2, "xp" => 150],
                ["q" => "Which feature allows you to change the margins for only ONE specific page in a document?", "options" => ["Page Break", "Section Break", "Line Break", "Paragraph Spacing"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Kerning' in typography?", "options" => ["The space between paragraphs", "The space between specific pairs of characters", "The thickness of the font", "The height of the page"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "How do you update a Table of Contents after changing your headings?", "options" => ["Delete and re-insert it", "Right-click the TOC and select 'Update Field'", "Restart Word", "It updates automatically every second"], "ans" => 1, "xp" => 180],
                ["q" => "Which tool allows you to see non-printing characters like spaces and paragraph marks?", "options" => ["The Ruler", "The Show/Hide (¶) button", "The Zoom slider", "The Spellchecker"], "ans" => 1, "xp" => 180],
                ["q" => "What is the purpose of a 'Gutter Margin'?", "options" => ["To add space for a document that will be bound/stapled", "To keep text away from the bottom of the page", "To create space for images", "To hide text from the printer"], "ans" => 0, "xp" => 180],
                ["q" => "Which shortcut key opens the 'Find and Replace' dialog?", "options" => ["Ctrl + F", "Ctrl + H", "Ctrl + R", "Ctrl + G"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What feature allows multiple authors to suggest changes that can be accepted or rejected?", "options" => ["Comments Only", "Track Changes", "Mail Merge", "Document Protect"], "ans" => 1, "xp" => 210],
                ["q" => "In 'Track Changes', what does a vertical red line in the left margin signify?", "options" => ["A spelling error", "A change has been made on that line", "The document is locked", "The page is out of bounds"], "ans" => 1, "xp" => 210],
                ["q" => "How can you prevent a specific paragraph from being split across two pages?", "options" => ["Keep with Next / Keep lines together", "Add 20 Enters", "Decrease Font size", "Change to Landscape"], "ans" => 0, "xp" => 210],
                ["q" => "What is a 'Watermark'?", "options" => ["A digital signature", "A faint background image or text used for branding or security", "A type of virus", "A blue font color"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the function of 'Mail Merge'?", "options" => ["To send one email to a group", "To automatically populate documents (like letters or labels) with data from a list", "To combine two Word documents", "To check email inside Word"], "ans" => 1, "xp" => 250],
                ["q" => "Which file type is best for a reusable 'template' that opens a new document every time?", "options" => [".docx", ".dotx", ".pdf", ".txt"], "ans" => 1, "xp" => 250],
                ["q" => "What does the 'Inspect Document' tool do?", "options" => ["Checks for viruses", "Finds hidden metadata, personal info, and hidden text before sharing", "Counts the words", "Translates the document"], "ans" => 1, "xp" => 250],
                ["q" => "Which ribbon tab contains 'Citations & Bibliography'?", "options" => ["Home", "Insert", "References", "Design"], "ans" => 2, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Macro' in Word?", "options" => ["A large font", "A recorded sequence of commands to automate repetitive tasks", "A high-resolution image", "A type of table"], "ans" => 1, "xp" => 300],
                ["q" => "What language are Word Macros typically written in?", "options" => ["Python", "VBA (Visual Basic for Applications)", "HTML", "C++"], "ans" => 1, "xp" => 300],
                ["q" => "How do you insert a Cross-Reference (e.g., 'See page 12') that updates automatically?", "options" => ["Type it manually", "Insert > Cross-reference", "References > Update All", "Review > Link"], "ans" => 1, "xp" => 300],
                ["q" => "Which view is best for focusing on the structure of a document rather than its appearance?", "options" => ["Print Layout", "Web Layout", "Outline View", "Read Mode"], "ans" => 2, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does 'Restrict Editing' allow you to do?", "options" => ["Password protect the entire PC", "Limit formatting or only allow 'Filling in forms' or 'Comments'", "Delete the file if someone opens it", "Send the file to the Recycle Bin"], "ans" => 1, "xp" => 400],
                ["q" => "Which feature creates a different set of headers for 'Odd' and 'Even' pages?", "options" => ["Section Breaks + Different Odd & Even Pages setting", "Page Breaks", "Margin adjustments", "Style sets"], "ans" => 0, "xp" => 400],
                ["q" => "What is an 'Orphan' in document processing?", "options" => ["A file with no name", "The first line of a paragraph printed alone at the bottom of a page", "A deleted document", "A broken hyperlink"], "ans" => 1, "xp" => 400],
                ["q" => "To create a 3-column newsletter layout for only half a page, what must you use?", "options" => ["Continuous Section Breaks", "Page Breaks", "Tab keys", "Table with 3 cells"], "ans" => 0, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 1,
        "title" => "Map 14: Logic & Data Analysis", "desc" => "Master VLOOKUP, IF statements, and complex spreadsheet logic.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "Which function allows you to perform a logical test and return one value if True and another if False?", "options" => ["=SUM()", "=IF()", "=COUNT()", "=CHECK()"], "ans" => 1, "xp" => 150],
                ["q" => "What symbol is used to create an 'Absolute Reference' (locking a cell so it doesn't change when copied)?", "options" => ["%", "#", "$", "&"], "ans" => 2, "xp" => 150],
                ["q" => "In the formula =IF(A1>50, 'Pass', 'Fail'), what happens if A1 is 50?", "options" => ["Pass", "Fail", "Error", "50"], "ans" => 1, "xp" => 150],
                ["q" => "What does the function =TODAY() return?", "options" => ["Your birth date", "The current system date", "The time of day", "The date the file was created"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does VLOOKUP stand for?", "options" => ["Variable Lookup", "Vertical Lookup", "Value Lookup", "Verify Lookup"], "ans" => 1, "xp" => 180],
                ["q" => "In VLOOKUP, what does the 'col_index_num' argument specify?", "options" => ["The column number in the range from which to retrieve the value", "The name of the column", "The row to look at", "The color of the column"], "ans" => 0, "xp" => 180],
                ["q" => "What is the purpose of 'Conditional Formatting'?", "options" => ["To change cell appearance based on specific rules (e.g., turning red if < 0)", "To lock the sheet", "To hide the data", "To calculate the sum"], "ans" => 0, "xp" => 180],
                ["q" => "Which function counts the number of cells in a range that are NOT empty?", "options" => ["=COUNT()", "=COUNTA()", "=COUNTBLANK()", "=SUM()"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Pivot Table' used for?", "options" => ["To draw circles", "To summarize, analyze, and explore large amounts of data", "To change the orientation of the monitor", "To create a Table of Contents"], "ans" => 1, "xp" => 210],
                ["q" => "How do you 'Group' data in a Pivot Table by Month?", "options" => ["Type the months manually", "Right-click a date field in the Pivot Table and select 'Group'", "Sort from A to Z", "Use the IF function"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Slicer' in a spreadsheet?", "options" => ["A tool for cutting paper", "A visual filter for Pivot Tables or data tables", "A type of chart", "A way to delete rows"], "ans" => 1, "xp" => 210],
                ["q" => "Which function calculates the number of days between two dates?", "options" => ["=DAYS()", "=CALC()", "=TIME()", "=SUM()"], "ans" => 0, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does the formula =IFERROR(A1/B1, 0) do?", "options" => ["Displays an error if B1 is zero", "Performs the division, but returns 0 if an error occurs", "Always returns zero", "Deletes A1 and B1"], "ans" => 1, "xp" => 250],
                ["q" => "What function is used to join multiple text strings into one?", "options" => ["=ADD()", "=CONCATENATE() or =TEXTJOIN()", "=COMBINE()", "=JOIN()"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Data Validation' used for?", "options" => ["To prove the data is true", "To restrict what can be entered into a cell (e.g., a dropdown list)", "To encrypt the sheet", "To check spelling"], "ans" => 1, "xp" => 250],
                ["q" => "What does 'Paste Special > Transpose' do?", "options" => ["Pastes data as an image", "Switches rows to columns or vice versa", "Pastes only formulas", "Pastes only formatting"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Goal Seek'?", "options" => ["A game", "A tool to find the input value needed to achieve a specific result", "A search engine", "A way to find hidden files"], "ans" => 1, "xp" => 300],
                ["q" => "What are 'Nested Functions'?", "options" => ["Functions stored in the cloud", "Using a function as an argument inside another function", "Functions that only work on holidays", "Hidden functions"], "ans" => 1, "xp" => 300],
                ["q" => "Which function returns a value based on a column and row intersection (often replacing VLOOKUP)?", "options" => ["=INDEX() and =MATCH()", "=FIND()", "=SEARCH()", "=IF()"], "ans" => 0, "xp" => 300],
                ["q" => "What is the 'Named Range' feature?", "options" => ["Changing the file name", "Assigning a descriptive name to a cell or range of cells", "Naming the author of the sheet", "The title at the top of the column"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the 'XLOOKUP' function (available in newer versions)?", "options" => ["A secure version of VLOOKUP", "A more flexible replacement for VLOOKUP that can search in any direction", "A function for finding X and Y coordinates", "A way to delete lookup errors"], "ans" => 1, "xp" => 400],
                ["q" => "How do you record a Macro in Excel?", "options" => ["Developer Tab > Record Macro", "Home Tab > AutoSum", "Review Tab > Protect", "File > Save As"], "ans" => 0, "xp" => 400],
                ["q" => "What is 'Power Query' used for?", "options" => ["Increasing CPU speed", "Connecting to, combining, and refining data from multiple sources", "Searching for files in Windows", "Formatting a table fast"], "ans" => 1, "xp" => 400],
                ["q" => "In a formula, what does the '!' symbol signify?", "options" => ["An error", "A reference to a different worksheet (e.g., Sheet2!A1)", "A high-priority calculation", "The end of a formula"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 1,
        "title" => "Map 15: Digital Presentations & Design", "desc" => "Create professional, engaging slideshows with advanced animation and transitions.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the 'Slide Master' used for?", "options" => ["The final slide in a deck", "A template that controls the global look (fonts, logos) of all slides", "The person presenting", "A list of all slides"], "ans" => 1, "xp" => 150],
                ["q" => "What is the difference between a 'Transition' and an 'Animation'?", "options" => ["None", "Transitions apply to the whole slide; Animations apply to individual objects", "Animations are for sound only", "Transitions are for text only"], "ans" => 1, "xp" => 150],
                ["q" => "Which view is best for reordering a large number of slides quickly?", "options" => ["Normal View", "Slide Sorter View", "Reading View", "Notes Page"], "ans" => 1, "xp" => 150],
                ["q" => "What is the shortcut to start a presentation from the FIRST slide?", "options" => ["F1", "F5", "Shift + F5", "Alt + P"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does the 'Morph' transition do?", "options" => ["Changes the color of the slide", "Creates smooth movement of objects from one slide to the next", "Deletes redundant slides", "Plays a video"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Presenter View'?", "options" => ["A view that shows your notes and upcoming slides on your screen, but only the current slide to the audience", "A view for recording videos", "A view that turns off the projector", "A high-contrast mode for the blind"], "ans" => 0, "xp" => 180],
                ["q" => "How can you ensure your presentation file includes all the fonts you used, even on a different PC?", "options" => ["Save as PDF", "Embed Fonts in the file settings", "Copy the font files to a USB", "Use only Arial"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'SmartArt'?", "options" => ["AI-generated art", "A tool to quickly convert text into professional-looking diagrams", "A high-resolution photo", "A link to a website"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Which feature allows you to jump to a specific slide or website during a presentation?", "options" => ["Hyperlink / Action Button", "Transition", "Animation Pane", "Design Ideas"], "ans" => 0, "xp" => 210],
                ["q" => "What is the 'Animation Pane' used for?", "options" => ["To draw characters", "To manage the order, timing, and duration of multiple animations on a slide", "To change the background color", "To record a voiceover"], "ans" => 1, "xp" => 210],
                ["q" => "How do you create a 'Custom Slide Show'?", "options" => ["Delete slides you don't need", "Use the 'Custom Slide Show' feature to select specific slides from a larger deck", "Create a new file", "Hide the taskbar"], "ans" => 1, "xp" => 210],
                ["q" => "What is an 'Entrance' animation?", "options" => ["An animation when a slide opens", "An animation that controls how an object appears on the slide", "An animation that makes an object leave", "A sound effect"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the purpose of 'Alt Text' for images in a presentation?", "options" => ["To provide a caption", "To describe the image for people using screen readers (Accessibility)", "To change the image resolution", "To search for the image on Google"], "ans" => 1, "xp" => 250],
                ["q" => "How do you insert a video that plays automatically when the slide opens?", "options" => ["Insert > Video > Playback Tab > Start: Automatically", "Yell at the computer", "Right-click the video and select 'Loop'", "Animations > Entrance > Appear"], "ans" => 0, "xp" => 250],
                ["q" => "What are 'Speaker Notes'?", "options" => ["Text that appears on the slide", "Private notes for the presenter that are not visible to the audience", "A transcript for the audience to read", "The text used for the title"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Design Ideas' (in PowerPoint)?", "options" => ["A manual for designers", "An AI feature that suggests professional layouts for your slide content", "A collection of clip art", "A search for stock photos"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What does 'Compress Pictures' do to a presentation?", "options" => ["Makes them look better", "Reduces file size by lowering image resolution and removing cropped areas", "Zips the PowerPoint file", "Turns images into black and white"], "ans" => 1, "xp" => 300],
                ["q" => "How can you create a self-running 'Kiosk' presentation that loops?", "options" => ["Press F5 repeatedly", "Set Up Slide Show > Browsed at a kiosk (full screen)", "Use the Morph transition", "Hold the Spacebar"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Triggers' in animation?", "options" => ["A virus", "Setting an animation to start only when a specific object is clicked", "The start of a slide show", "The end of a presentation"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Ink to Shape'?", "options" => ["A printing setting", "A feature that converts hand-drawn doodles into perfect geometric shapes", "A type of printer ink", "A way to sign your name"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which format is best for sharing a presentation so the recipient can't edit it, but can view it perfectly?", "options" => [".pptx", ".ppshow or .pdf", ".txt", ".mp3"], "ans" => 1, "xp" => 400],
                ["q" => "How do you 'Rehearse Timings'?", "options" => ["Use a stopwatch", "Use the 'Rehearse Timings' tool to record how long you spend on each slide", "Guess based on word count", "Practice with a friend"], "ans" => 1, "xp" => 400],
                ["q" => "What does 'Section' do in a long presentation?", "options" => ["Deletes slides", "Groups related slides together for easier organization and navigation", "Changes the font for a group", "Adds a password"], "ans" => 1, "xp" => 400],
                ["q" => "What is the best way to maintain visual consistency across 100 slides?", "options" => ["Copy-paste styles manually", "Use and strictly follow the Slide Master and Theme", "Use many different colors", "Don't use any images"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 1,
        "title" => "Map 16: Data Visualization Concepts", "desc" => "Select and create the right charts to tell a story with your data.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "Which chart type is best for showing trends over a period of time?", "options" => ["Pie Chart", "Line Chart", "Bar Chart", "Scatter Plot"], "ans" => 1, "xp" => 150],
                ["q" => "Which chart is best for showing the proportions of a whole (e.g., market share)?", "options" => ["Line Chart", "Pie Chart", "Histogram", "Area Chart"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Legend' on a chart?", "options" => ["A famous presentation", "A key that explains what the colors or patterns represent", "The title of the chart", "The largest data point"], "ans" => 1, "xp" => 150],
                ["q" => "What are the horizontal and vertical lines on a chart background called?", "options" => ["Data Lines", "Gridlines", "Borders", "Axis Labels"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "When comparing discrete categories (e.g., sales by 5 different people), which chart is best?", "options" => ["Scatter Plot", "Bar or Column Chart", "Line Chart", "Radar Chart"], "ans" => 1, "xp" => 180],
                ["q" => "What does the 'X-Axis' usually represent in a chart?", "options" => ["Quantity", "Categories or Time", "The Title", "The Percentage"], "ans" => 1, "xp" => 180],
                ["q" => "What does the 'Y-Axis' usually represent?", "options" => ["Time", "Values or Measurements", "Categories", "The Legend"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Sparkline'?", "options" => ["A tiny chart that fits inside a single cell", "A flash of light on a slide", "A very fast data update", "An animation"], "ans" => 0, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Scatter Plot' used for?", "options" => ["Showing percentages", "Showing the relationship or correlation between two variables", "Showing a simple list", "Showing a single total"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Waterfall Chart' typically used for?", "options" => ["Showing weather data", "Visualizing how an initial value is affected by a series of positive and negative changes", "Showing project timelines", "Comparing two people"], "ans" => 1, "xp" => 210],
                ["q" => "How can you add a 'Trendline' to a chart?", "options" => ["Draw it with a pen", "Right-click a data series and select 'Add Trendline'", "Change the chart to a Line chart", "Add more data"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Combo Chart'?", "options" => ["A chart with many colors", "A chart that combines two different types (e.g., a Bar chart with a Line chart overlay)", "A chart that shows two years", "A chart made in a different program"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Data Junk' in visualization?", "options" => ["Deleted files", "Unnecessary visual elements (like 3D effects or heavy gridlines) that distract from the data", "Incorrect numbers", "Old charts"], "ans" => 1, "xp" => 250],
                ["q" => "What is the danger of using a 3D Pie Chart?", "options" => ["It uses too much RAM", "Perspective can distort the perceived size of the slices, making them misleading", "It can crash the printer", "It is black and white"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Histogram'?", "options" => ["A chart about history", "A chart that shows the frequency distribution of a data set", "A chart for stocks", "A chart with images"], "ans" => 1, "xp" => 250],
                ["q" => "Which chart is best for project management to show a schedule/timeline?", "options" => ["Pie Chart", "Gantt Chart", "Radar Chart", "Area Chart"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Heat Map'?", "options" => ["A map of the sun", "A visualization that uses color to represent data intensity or values", "A chart showing temperatures", "A 3D model of a building"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Sunburst' chart used for?", "options" => ["Looking at the weather", "Visualizing hierarchical data (groups within groups) in a circular layout", "Showing stock market crashes", "Simple comparisons"], "ans" => 1, "xp" => 300],
                ["q" => "How do you switch the Data on the X and Y axis in a chart?", "options" => ["Delete and start over", "Click 'Switch Row/Column' in the Chart Design tab", "Rotate the monitor", "Change the font size"], "ans" => 1, "xp" => 300],
                ["q" => "What does 'Logarithmic Scale' do in a chart?", "options" => ["Calculates logs", "Compresses the scale to show data that spans a huge range of values", "Makes the chart look like a tree", "Deletes outliers"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Dashboard'?", "options" => ["The part of a car", "A collection of interactive charts and visualizations that provide an overview of key data", "A type of keyboard", "A login screen"], "ans" => 1, "xp" => 400],
                ["q" => "Why should you generally start your Value (Y) Axis at Zero?", "options" => ["To save space", "To avoid exaggerating small differences and misleading the viewer", "Because spreadsheets don't allow other numbers", "To make the bars look taller"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Infographic' design?", "options" => ["A spreadsheet full of numbers", "A visual representation of information or data designed to be easily understood at a glance", "A type of computer code", "A high-speed internet line"], "ans" => 1, "xp" => 400],
                ["q" => "Which chart is best for showing the components of a total value AND how that total has changed over time?", "options" => ["Pie Chart", "Stacked Area or Column Chart", "Scatter Plot", "Bubble Chart"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 2: DIGITAL WORKSPACE & SYSTEMS (Maps 17-20)
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 2,
        "title" => "Map 17: System Optimization & Health", "desc" => "Keep your computer running at peak performance with maintenance and diagnostics.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does 'Disk Cleanup' do?", "options" => ["Cleans the outside of the computer", "Deletes temporary and unnecessary system files to free up space", "Formats the hard drive", "Installs updates"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'Defragmenting' a hard drive (HDD) do?", "options" => ["Breaks it into small pieces", "Organizes files so the drive can read them faster", "Erases all data", "Increases internet speed"], "ans" => 1, "xp" => 150],
                ["q" => "Why should you NOT defragment a Solid State Drive (SSD)?", "options" => ["SSDs are too fast", "It doesn't help performance and can shorten the lifespan of the drive", "SSDs don't have files", "It will erase the OS"], "ans" => 1, "xp" => 150],
                ["q" => "Which tool shows you exactly how much RAM and CPU each application is using?", "options" => ["Control Panel", "Task Manager", "File Explorer", "Device Manager"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does 'Resource Monitor' provide compared to Task Manager?", "options" => ["Nothing, they are the same", "Much more detailed real-time data on CPU, Memory, Disk, and Network usage", "A way to play games", "A list of users"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Service' in Windows?", "options" => ["A repair shop", "A program that runs in the background to support the OS or apps", "A type of internet", "A free software"], "ans" => 1, "xp" => 180],
                ["q" => "How can you prevent a slow PC at startup?", "options" => ["Buy a new PC", "Disable unnecessary apps in the Task Manager 'Startup' tab", "Unplug the mouse", "Turn off the monitor"], "ans" => 1, "xp" => 180],
                ["q" => "What does 'ReadyBoost' do (on older PCs)?", "options" => ["Increases CPU speed", "Uses a USB flash drive as additional system memory (RAM) cache", "Boosts Wi-Fi signal", "Makes the fans run faster"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the 'Windows Registry'?", "options" => ["A list of all users", "A database that stores configuration settings for the OS and applications", "A log of all websites visited", "A place to register your PC"], "ans" => 1, "xp" => 210],
                ["q" => "Why is it dangerous to edit the Registry without a backup?", "options" => ["It uses too much power", "Incorrect changes can cause the system to crash or stop booting", "It will delete your photos", "It is illegal"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Virtual Memory' (Paging File)?", "options" => ["Memory in the cloud", "Space on the hard drive used as temporary RAM when physical RAM is full", "A type of VR headset", "Memory that doesn't exist"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'System Restore'?", "options" => ["A way to clean the case", "A feature that lets you roll back the PC's system files to an earlier point in time", "A tool for deleting all files", "A factory reset"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Driver'? (Review/Advanced)", "options" => ["A person using the PC", "Software that allows hardware (like a GPU) to communicate with the OS", "A fast storage disk", "A type of USB cable"], "ans" => 1, "xp" => 250],
                ["q" => "What does 'Overclocking' mean?", "options" => ["Changing the time zone", "Increasing a component's clock speed beyond its factory settings to gain performance", "Running the PC for 24 hours", "Setting a timer"], "ans" => 1, "xp" => 250],
                ["q" => "What is the primary risk of overclocking?", "options" => ["Excessive heat and potential hardware damage", "Running out of disk space", "Lowering internet speed", "Getting a virus"], "ans" => 0, "xp" => 250],
                ["q" => "What is 'Event Viewer' used for?", "options" => ["Watching movies", "Checking system logs to troubleshoot errors and warnings", "Booking tickets", "Seeing who logged in"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'BIOS' or 'UEFI'?", "options" => ["A type of monitor", "The first software that runs when you turn on the computer to initialize hardware", "An operating system", "A game"], "ans" => 1, "xp" => 300],
                ["q" => "How do you usually access the BIOS/UEFI?", "options" => ["Through Windows Settings", "By pressing a specific key (like F2 or Del) during the initial boot-up splash screen", "By right-clicking the Desktop", "By unplugging the PC"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'CMOS'? ", "options" => ["A type of CPU", "A small battery on the motherboard that keeps the BIOS settings and time accurate", "A type of monitor", "A sound card"], "ans" => 1, "xp" => 300],
                ["q" => "What does 'Safe Mode with Networking' allow you to do?", "options" => ["Play games online", "Troubleshoot Windows while still having access to the internet for drivers/research", "Surf the web anonymously", "Delete viruses automatically"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Clean Boot'?", "options" => ["Washing the PC", "Starting Windows with a minimal set of drivers and startup programs to find software conflicts", "A factory reset", "Starting without a monitor"], "ans" => 1, "xp" => 400],
                ["q" => "What does the 'Check Disk' (chkdsk) command do?", "options" => ["Checks if the disk is round", "Scans the hard drive for file system errors and bad sectors", "Checks the disk space", "Deletes all files"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Hardware Acceleration'?", "options" => ["Driving a car with a PC", "Offloading tasks from the CPU to specialized hardware like the GPU to increase speed", "Making the fans spin faster", "Buying more RAM"], "ans" => 1, "xp" => 400],
                ["q" => "If a PC is 'Thermal Throttling', what is happening?", "options" => ["The PC is too cold", "The system is intentionally slowing down the CPU to prevent damage from overheating", "The PC is running out of battery", "The internet is slow"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 2,
        "title" => "Map 18: Networking & Troubleshooting", "desc" => "Understand routers, IP addresses, and how to fix connectivity issues.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does 'IP Address' stand for?", "options" => ["Internal Protocol", "Internet Protocol", "Internet Page", "Internal Page"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Router'?", "options" => ["A device that stores files", "A device that directs data traffic between your local network and the internet", "A type of cable", "A computer monitor"], "ans" => 1, "xp" => 150],
                ["q" => "What is the difference between 2.4GHz and 5GHz Wi-Fi?", "options" => ["No difference", "2.4GHz has better range; 5GHz has faster speeds at close range", "5GHz is always better", "2.4GHz is for phones only"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'SSID' refer to in Wi-Fi settings?", "options" => ["The password", "The name of the Wi-Fi network", "The speed of the network", "The type of encryption"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Modem'?", "options" => ["A type of computer", "A device that converts signal from your ISP into a digital format your router can use", "A Wi-Fi antenna", "A web browser"], "ans" => 1, "xp" => 180],
                ["q" => "What does 'Ping' measure in a network test?", "options" => ["Download speed", "Latency (the time it takes for data to travel to a server and back)", "Upload speed", "The number of users"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Ethernet'?", "options" => ["Wireless internet", "A physical cable used to connect devices to a local network (LAN)", "A type of satellite", "A web browser"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'LAN' (Local Area Network)?", "options" => ["The whole internet", "A network limited to a small area like a home or office", "A type of server", "A website"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'DHCP'?", "options" => ["A type of virus", "A system that automatically assigns IP addresses to devices on a network", "A high-speed cable", "A type of router"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Static IP'?", "options" => ["An IP that changes every day", "A manually assigned IP address that never changes", "A very fast IP", "An invisible IP"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'DNS' (Domain Name System)?", "options" => ["A way to save files", "The service that translates web addresses (like google.com) into IP addresses", "A type of security software", "A download manager"], "ans" => 1, "xp" => 210],
                ["q" => "What does 'Bandwidth' mean?", "options" => ["The weight of the router", "The maximum rate of data transfer across a network", "The number of devices", "The length of a cable"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'MAC Address'?", "options" => ["An address for Apple computers", "A unique physical identifier assigned to every network interface card (NIC)", "A secret code for the internet", "A type of IP address"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Port Forwarding'?", "options" => ["Moving a computer", "Allowing external devices to access services on a private network by opening specific ports", "Connecting two routers", "Speeding up the internet"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Gateway'?", "options" => ["A brand of computer", "The node (usually a router) that serves as an access point to another network", "A type of password", "A firewall"], "ans" => 1, "xp" => 250],
                ["q" => "What does it mean to 'Power Cycle' a router?", "options" => ["Unplugging it, waiting 30 seconds, and plugging it back in to reset it", "Buying a new one", "Upgrading its software", "Changing the password"], "ans" => 0, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Packet Loss'?", "options" => ["Losing your laptop", "When data units fail to reach their destination, causing lag or broken connections", "Losing your password", "When a file is too large"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'WPA3'?", "options" => ["A type of cable", "The latest and most secure encryption standard for Wi-Fi", "A web browser", "A network speed"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Mesh Network'?", "options" => ["A network for catching fish", "A system of multiple interconnected routers to provide seamless Wi-Fi over a large area", "A type of internet cable", "A virus"], "ans" => 1, "xp" => 300],
                ["q" => "What does 'Airplane Mode' do on a device?", "options" => ["Makes the device fly", "Disables all wireless transmissions (Wi-Fi, Bluetooth, Cellular)", "Speeds up the CPU", "Cleans the disk"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Subnet Mask' used for?", "options" => ["To hide your IP", "To define the size and range of a local network", "To change the Wi-Fi password", "To encrypt a file"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'ipconfig' (or 'ifconfig')?", "options" => ["A website", "A command-line tool used to view and manage network settings on a computer", "A type of router", "An internet speed test"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Bridge Mode' on a router?", "options" => ["Connecting to a bridge", "Disabling the routing functions of a modem/router so another router can handle them", "Making the Wi-Fi go across water", "Connecting two PCs with a cable"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Fiber Optic' internet?", "options" => ["Internet through phone lines", "Internet that uses light pulses over glass fibers to transmit data at very high speeds", "Wireless satellite internet", "Internet through electricity lines"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 2,
        "title" => "Map 19: Advanced Cloud & Collaboration", "desc" => "Master shared drives, version control, and permissions for team projects.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Version Control'?", "options" => ["Checking the time", "A system that records changes to files over time so you can recall specific versions later", "Controlling the speed of a download", "Naming a file 'v1'"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Conflict' in cloud syncing?", "options" => ["Two people arguing", "When two people edit the same file simultaneously and the system doesn't know which to keep", "A virus", "A slow internet connection"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'Real-time Co-authoring' mean?", "options" => ["Writing a book together", "Multiple people editing a document at the exact same time and seeing each other's changes", "Sending emails back and forth", "Sharing a password"], "ans" => 1, "xp" => 150],
                ["q" => "What is the benefit of a 'Shared Drive' (in Google Workspace or Teams)?", "options" => ["It's faster", "The files belong to the team/organization, not an individual user", "It's free", "It doesn't need internet"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is an 'Expiration Link'?", "options" => ["A link to a supermarket", "A shared link that automatically stops working after a certain date", "A broken link", "A link to a calendar"], "ans" => 1, "xp" => 180],
                ["q" => "What is the difference between 'Owner' and 'Editor' permissions?", "options" => ["No difference", "Owners can delete the file and manage permissions; Editors can only change the content", "Editors are faster", "Owners get paid more"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Selective Sync'?", "options" => ["Choosing which colors to sync", "Choosing only specific folders to download to your computer to save space", "Syncing only when you are awake", "Syncing to only one device"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Public Link'?", "options" => ["A link for the government", "A link that allows anyone with the URL to view the file without logging in", "A link on a billboard", "A link to a park"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Metadata' in a file?", "options" => ["A type of virus", "Data that provides information about other data (e.g., author, date created, file size)", "The actual text in a document", "A backup file"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Audit Log'?", "options" => ["A record of financial data", "A record that shows who accessed or edited a file and when", "A type of tree", "A log of errors"], "ans" => 1, "xp" => 210],
                ["q" => "What does 'Check Out' a file mean in some systems (like SharePoint)?", "options" => ["To buy it", "To lock a file so only you can edit it, preventing others from making changes until you 'Check In'", "To delete it", "To send it via email"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'IaaS' (Infrastructure as a Service)?", "options" => ["A type of building", "Cloud-based rent-able computing resources like virtual servers and storage", "A type of internet speed", "A word processing app"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'API' (Application Programming Interface)?", "options" => ["A type of monitor", "A set of rules that allow different software applications to communicate with each other", "A fast computer", "A type of browser"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Webhooks'?", "options" => ["Hooks for cables", "Automated messages sent from apps when something happens (e.g., a file is uploaded)", "A way to catch fish", "A type of web browser"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Single Sign-On' (SSO)?", "options" => ["One person using a PC", "A service that allows you to use one set of credentials to log into multiple related applications", "A password that only has one letter", "A fingerprint scanner"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'SLA' (Service Level Agreement)?", "options" => ["A type of cable", "A contract specifying the guaranteed uptime and support from a cloud provider", "A way to share files", "A type of firewall"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Scalability' in cloud computing?", "options" => ["The weight of a server", "The ability to easily increase or decrease computing resources based on demand", "Measuring the size of a file", "The number of users"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Multi-Tenancy'?", "options" => ["Many people living in a house", "When multiple customers share the same physical server resources while keeping data separate", "Using many computers", "A type of Wi-Fi"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Data Residency'?", "options" => ["Where the data lives at home", "The physical or geographic location where data is stored (important for laws/GDPR)", "The age of the data", "The size of the data"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Hybrid Cloud'?", "options" => ["A cloud that produces rain", "A combination of private on-premise servers and public cloud services", "A car that uses a computer", "A cloud with two colors"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Zero Trust' security?", "options" => ["Trusting nobody", "A security model where no user or device is trusted by default, even inside the network", "A firewall that blocks everything", "A computer with no password"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Encryption at Rest'?", "options" => ["Encryption while you sleep", "Data that is encrypted while stored on a disk or in the cloud", "Encryption while sending a file", "Encryption that is turned off"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Egress Fees'?", "options" => ["Fees for entering a site", "Charges for transferring data OUT of a cloud provider's network", "A type of internet tax", "A subscription fee"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cold Storage'?", "options" => ["Storing a PC in a fridge", "Storing data that is rarely accessed on very cheap, slower-access servers", "Storing data on a USB", "A backup on paper"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 2,
        "title" => "Map 20: Multimedia & Content Creation", "desc" => "Understand file formats, resolution, and basic digital media editing.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Resolution' in an image?", "options" => ["A promise for the new year", "The number of pixels that make up an image (measured in Width x Height)", "The file size", "The color of the image"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'DPI' stand for?", "options" => ["Data Per Inch", "Dots Per Inch", "Digital Picture Interface", "Document Processing Info"], "ans" => 1, "xp" => 150],
                ["q" => "Which file format is best for a logo that needs a transparent background?", "options" => [".jpg", ".png", ".bmp", ".txt"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Vector' image?", "options" => ["A photo", "An image based on mathematical paths that can be scaled infinitely without losing quality", "A very large file", "A 3D image"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the difference between 'Lossy' and 'Lossless' compression?", "options" => ["No difference", "Lossy removes data to save space; Lossless keeps all data but has larger files", "Lossy is for audio only", "Lossless is for video only"], "ans" => 1, "xp" => 180],
                ["q" => "Which video format is the most universal for web use?", "options" => [".avi", ".mp4", ".mov", ".wmv"], "ans" => 1, "xp" => 180],
                ["q" => "What does 'Aspect Ratio' mean? (e.g., 16:9)", "options" => ["The size of the file", "The proportional relationship between a screen's width and height", "The brightness of the screen", "The frame rate"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Frame Rate' (FPS)?", "options" => ["The weight of a monitor", "The number of individual images displayed per second in a video", "The speed of the CPU", "The number of pixels"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Cropping' an image?", "options" => ["Changing the color", "Removing the outer parts of an image to improve framing or change aspect ratio", "Making it larger", "Printing it"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Saturation' in photo editing?", "options" => ["The brightness", "The intensity or purity of colors in an image", "The focus", "The file format"], "ans" => 1, "xp" => 210],
                ["q" => "What does 'RGB' stand for in digital color?", "options" => ["Real Good Brightness", "Red, Green, Blue", "Resolution, Graphics, Bits", "Range, Grayscale, Black"], "ans" => 1, "xp" => 210],
                ["q" => "What color mode is used for professional printing (physical ink)?", "options" => ["RGB", "CMYK (Cyan, Magenta, Yellow, Black)", "Grayscale", "Hex"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Codec'?", "options" => ["A type of computer code", "Software or hardware that compresses and decompresses digital media files", "A video player", "A high-speed camera"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Bitrate'?", "options" => ["The price of a computer", "The amount of data processed per unit of time in a video or audio file", "The number of colors", "The length of a video"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Thumbnail'?", "options" => ["A part of your finger", "A small, reduced-size version of a picture or video used for browsing", "A very fast download", "A virus"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Stock Media'?", "options" => ["Media about the stock market", "Professional photos, videos, or music licensed for use in projects", "Media stored in a warehouse", "Media you make yourself"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Fair Use'?", "options" => ["Using something for free always", "A legal doctrine that allows limited use of copyrighted material without permission for purposes like criticism or education", "Sharing passwords", "Buying a used PC"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Creative Commons' (CC)?", "options" => ["A type of government", "A set of public licenses that allow creators to give permission for others to use their work with specific conditions", "A social media site", "A design software"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Watermarking' a video/image?", "options" => ["Cleaning it", "Overlaying a logo or text to prevent unauthorized use and identify the owner", "Adding a blue filter", "Improving resolution"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Metadata' (EXIF) in a photo?", "options" => ["The file name", "Embedded info like camera settings, date, and sometimes GPS location", "The colors used", "A backup copy"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Rendering' in video editing?", "options" => ["Taking a photo", "The process of generating the final video file from the project's edits, effects, and layers", "Recording a voiceover", "Deleting a clip"], "ans" => 1, "xp" => 400],
                ["q" => "What is '4K' resolution?", "options" => ["4,000 files", "A display resolution with approximately 4,000 pixels horizontally", "4 gigabytes", "4 colors"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Layer' in graphic design?", "options" => ["A level in a game", "A separate plane of an image that can be edited independently", "A type of monitor", "A backup"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Opacity'?", "options" => ["The speed of a video", "The degree of transparency of an object or layer", "The file size", "The resolution"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 3: VALEDICTORY CAPSTONE (Maps 21-24)
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 3,
        "title" => "Map 21: Advanced Privacy & Protection", "desc" => "Deep dive into encryption, VPN tunnels, and advanced digital defense.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'End-to-End Encryption' (E2EE)?", "options" => ["Encryption only at the start", "A system where only the communicating users can read the messages (even the service provider cannot)", "Encryption for emails only", "Encryption that is very fast"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'VPN Tunnel'?", "options" => ["A cable underground", "An encrypted connection that hides your data as it travels over the public internet", "A way to speed up the CPU", "A browser for the dark web"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'HTTPS' use to secure a website connection?", "options" => ["A password", "SSL/TLS Certificates", "A firewall", "A fast server"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Disk Encryption' (e.g., BitLocker or FileVault)?", "options" => ["Encrypting one file", "Encrypting the entire hard drive to protect data if the physical device is stolen", "Encrypting a USB only", "Formatting the drive"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Social Engineering'?", "options" => ["Building a social media site", "Manipulating people into giving up confidential information using psychology", "Engineering a faster network", "A type of marketing"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Brute Force Attack'?", "options" => ["Breaking a computer with a hammer", "Attempting every possible combination of characters to crack a password", "Sending a very large email", "A type of hardware failure"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Password Manager'? (Review)", "options" => ["A person who remembers passwords", "Software that stores and generates strong, unique passwords for every site", "A sticky note on the monitor", "A word document"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Salting' a password?", "options" => ["Adding salt to a PC", "Adding random data to a password before hashing it to make it harder to crack", "Sharing a password", "Deleting a password"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Rootkit'?", "options" => ["A tool for gardening", "Malicious software designed to hide its presence and maintain privileged access to a system", "A fast processor", "A type of keyboard"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Keylogging'?", "options" => ["A way to log in", "Software that records every keystroke made on a computer to steal passwords", "A type of typing game", "A backup of files"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Man-in-the-Middle' (MitM) attack?", "options" => ["A game", "When a hacker intercepts communication between two parties without them knowing", "A computer in the middle of a room", "A person fixing a PC"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Zero-Day Vulnerability'?", "options" => ["A bug that is zero years old", "A security flaw that is unknown to the software creator and has no patch yet", "A very small bug", "A bug that happens at midnight"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Tor' (The Onion Router)?", "options" => ["A type of vegetable", "Software that enables anonymous communication by routing traffic through a global network of relays", "A fast web browser", "A firewall"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Whaling' in cybersecurity?", "options" => ["Fishing", "A phishing attack specifically targeted at high-ranking executives", "A very large virus", "A type of backup"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Sandbox'?", "options" => ["A place for kids to play", "A secure environment to run suspicious programs without affecting the rest of the system", "A type of hard drive", "A keyboard cover"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'SIM Swapping'?", "options" => ["Changing your phone", "A scam where hackers take over your phone number to bypass 2FA codes", "Trading SIM cards with a friend", "A way to speed up data"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Digital Signature'?", "options" => ["Your name typed in a font", "A mathematical technique used to validate the authenticity and integrity of a digital message", "A scan of your hand-written signature", "A password"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Public Key Infrastructure' (PKI)?", "options" => ["A type of building", "A system for managing digital certificates and public-key encryption", "A set of public keys", "A way to unlock a PC"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Data Sovereignty'?", "options" => ["Powerful data", "The idea that data is subject to the laws of the country in which it is located", "Free data for all", "High-quality data"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Penetration Testing' (Pen Testing)?", "options" => ["Testing a pen", "An authorized simulated cyberattack on a system to find security weaknesses", "A typing test", "A test for hardware speed"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Honey Pot' in security?", "options" => ["A pot of honey", "A decoy system designed to lure hackers and study their methods", "A very good password", "A type of antivirus"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cold Wallet' (for Crypto/Security)?", "options" => ["A wallet in the snow", "An offline storage method (like a USB device) for holding digital keys/assets to prevent hacking", "A cheap wallet", "A digital bank account"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Biometric Spoofing'?", "options" => ["Using a fingerprint", "Using fake data (like a photo or 3D mask) to trick biometric security systems", "A fast login", "A way to change your face"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Dark Web'?", "options" => ["The internet at night", "A hidden part of the internet that requires special software to access and is often used for anonymous activity", "A black web browser", "A broken website"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 3,
        "title" => "Map 22: PC Hardware & IT Repair", "desc" => "Troubleshoot hardware failures, understand components, and perform basic repairs.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "If you hear 'Beep Codes' when starting a PC, what is happening?", "options" => ["The PC is happy", "The BIOS is reporting a hardware error before the screen can turn on", "The printer is out of paper", "A video is playing"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Thermal Paste' used for?", "options" => ["To glue parts together", "To transfer heat from the CPU to the heatsink effectively", "To clean the monitor", "To lubricate the fans"], "ans" => 1, "xp" => 150],
                ["q" => "Which component is usually responsible for a 'No Display' issue even if the PC turns on?", "options" => ["The Mouse", "The GPU (Graphics Card) or RAM", "The Hard Drive", "The Keyboard"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'ESD' (Electrostatic Discharge)?", "options" => ["A type of power supply", "Static electricity that can instantly destroy sensitive computer components", "A fast internet connection", "A type of software"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'POST' (Power-On Self-Test)?", "options" => ["An online post", "A diagnostic test performed by the BIOS to check hardware on startup", "A letter in the mail", "A typing test"], "ans" => 1, "xp" => 180],
                ["q" => "What does it mean if a hard drive is making a 'clicking' sound?", "options" => ["It's working hard", "It is likely physically failing (mechanical failure)", "It's saving a file", "It needs more power"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'RAM' (Random Access Memory) used for? (Review)", "options" => ["Permanent storage", "Temporary workspace for the PC to store data it is currently using", "Storing photos", "Connecting to Wi-Fi"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'VGA' or 'HDMI' port used for?", "options" => ["Connecting a mouse", "Connecting a monitor or projector", "Connecting to the internet", "Connecting a power cable"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'Expansion Slot' (PCIe)?", "options" => ["A slot for a coin", "A slot on the motherboard to add components like GPUs or sound cards", "A slot for a USB", "A place to put your phone"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Bottleneck' in computer hardware?", "options" => ["A part of a bottle", "When one slow component limits the overall performance of the entire system", "A very fast PC", "A type of cooling"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Form Factor' (e.g., ATX, ITX)?", "options" => ["The shape of a font", "The standardized size and shape of a motherboard or case", "The speed of a CPU", "The number of ports"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'NVMe'?", "options" => ["A type of video", "A high-speed interface for modern SSDs that is much faster than SATA", "A network protocol", "A type of monitor"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'RAID'?", "options" => ["A bug spray", "A way of combining multiple hard drives for data redundancy or speed", "A type of attack", "A backup software"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Integrated Graphics'?", "options" => ["A professional GPU", "Graphics processing built directly into the CPU, rather than on a separate card", "A way to draw images", "A type of monitor"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'SoC' (System on a Chip)?", "options" => ["A computer on a desk", "An integrated circuit that integrates all components of a computer into a single chip (like in phones)", "A type of RAM", "A motherboard"], "ans" => 1, "xp" => 250],
                ["q" => "What does 'modular' mean regarding a Power Supply (PSU)?", "options" => ["It's very small", "You can attach only the cables you need, reducing clutter", "It uses two batteries", "It can be programmed"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Blue Screen of Death' (BSOD)?", "options" => ["A fancy wallpaper", "A critical system error that causes Windows to stop running and restart", "A virus notification", "A hardware update"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Stress Testing'?", "options" => ["Taking a hard exam", "Running a computer at maximum load to test stability and cooling", "Yelling at the PC", "Checking for viruses"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Undervolting'?", "options" => ["Turning off the PC", "Reducing the voltage to a component to lower heat and power consumption", "Increasing the speed", "Buying a cheap battery"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Cable Management'?", "options" => ["Selling cables", "Organizing cables inside a PC for better airflow and appearance", "Connecting to the internet", "A type of network"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Factory Reset'?", "options" => ["Moving a factory", "Restoring a device to its original out-of-the-box settings, erasing all data", "Restarting the PC", "Cleaning the disk"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Bit Depth' in audio/images?", "options" => ["The size of the file", "The number of bits used to represent each sample or pixel (higher = more quality)", "The speed of the data", "The volume of the sound"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Firmware' (Advanced)?", "options" => ["Soft software", "Permanent software programmed into a hardware device to control its basic functions", "A type of screen", "A backup"], "ans" => 1, "xp" => 400],
                ["q" => "What is the best tool for removing dust from inside a PC?", "options" => ["A vacuum cleaner", "Compressed Air", "A wet cloth", "A hair dryer on hot"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 3,
        "title" => "Map 23: Career Tech & Digital Presence", "desc" => "Master LinkedIn, professional portfolios, and the tools of the modern digital worker.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'LinkedIn' primarily used for?", "options" => ["Sharing vacation photos", "Professional networking and job searching", "Playing games", "Buying groceries"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Digital Portfolio'?", "options" => ["A physical folder", "A website or digital collection showcasing your work, projects, and skills", "A backup of your PC", "A list of passwords"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'SEO' (Search Engine Optimization) mean?", "options" => ["Searching for Every Option", "Improving a website to make it rank higher in search results", "A secure operating system", "Speeding up a browser"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Personal Branding'?", "options" => ["Getting a tattoo", "The practice of marketing yourself and your career as a brand", "Buying a specific brand of PC", "Creating a logo"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Remote Work' tech?", "options" => ["A TV remote", "Tools like Zoom, Slack, and VPNs that allow you to work from anywhere", "A robot that works for you", "A fast internet connection"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Freelancing'?", "options" => ["Getting things for free", "Working for yourself and taking on projects for different clients", "A type of social media", "A free software"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Soft Skill' in tech?", "options" => ["Typing fast", "Communication, teamwork, and problem-solving", "Programming", "Fixing hardware"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Continuous Learning' in IT?", "options" => ["Going to school forever", "The habit of constantly updating your skills to keep up with changing technology", "A type of software update", "A long video course"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'ATS' (Applicant Tracking System)?", "options" => ["A GPS for jobs", "Software used by companies to scan resumes for keywords before a human sees them", "A type of monitor", "A test for typing speed"], "ans" => 1, "xp" => 210],
                ["q" => "Why should you use 'Keywords' in your LinkedIn profile?", "options" => ["To make it look busy", "To ensure recruiters can find you when searching for specific skills", "To pass a spell check", "To get more likes"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Networking'?", "options" => ["Connecting computers", "Building relationships with other professionals in your field", "Using social media", "Sending emails"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Cover Letter'?", "options" => ["A letter that covers a box", "A document sent with your resume to provide additional info on your skills and experience", "A signature at the end of an email", "The title of your resume"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'GitHub'?", "options" => ["A place to buy hubs", "A platform for developers to host, share, and collaborate on code", "A social network for gamers", "A type of web browser"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Markdown'?", "options" => ["A sale at a store", "A lightweight markup language with plain text formatting syntax", "A way to grade students", "A type of file backup"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Project Management' software (e.g., Trello, Asana)?", "options" => ["Games", "Tools used to track tasks, deadlines, and team progress on projects", "Software for management only", "A type of spreadsheet"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Coworking'?", "options" => ["Working for two companies", "Shared office spaces where independent workers from different companies work together", "A type of video call", "Working with a cow"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Digital Literacy'?", "options" => ["Reading digital books", "The ability to find, evaluate, and communicate information through various digital platforms", "Knowing how to type", "Having a high-speed internet"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Upskilling'?", "options" => ["Moving to a higher floor", "Learning new and more advanced skills to stay competitive in the workforce", "Upgrading your PC", "Buying a new mouse"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Information Overload'?", "options" => ["A virus", "The difficulty in understanding an issue and making decisions caused by too much information", "A fast download", "A large hard drive"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Thought Leadership'?", "options" => ["Reading minds", "Establishing yourself as an expert in a specific field by sharing valuable content and insights", "A type of management", "A brain-computer interface"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Niche' in a career?", "options" => ["A hole in a wall", "A specialized segment of the market for a particular kind of product or service", "A type of desk", "A software bug"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Gig Economy'?", "options" => ["An economy based on gigabytes", "A labor market characterized by the prevalence of short-term contracts or freelance work", "Economy of big companies", "A music industry term"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'CRM' (Customer Relationship Management)?", "options" => ["A way to fix PCs", "Software used to manage a company’s interactions with current and potential customers", "A type of social media", "A secure network"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Disruptive Technology'?", "options" => ["Technology that breaks often", "An innovation that significantly alters the way consumers, industries, or businesses operate", "A loud computer", "A type of firewall"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $wrong_course_id, "category_id" => 3,
        "title" => "Map 24: Emerging Tech & The Future", "desc" => "Explore AI, Machine Learning, and how technology will change in the next decade.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Artificial Intelligence' (AI)?", "options" => ["A robot with a brain", "The simulation of human intelligence processes by computer systems", "A very fast computer", "A fake computer"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Machine Learning'?", "options" => ["A robot learning to walk", "A type of AI that allows software to become more accurate at predicting outcomes without being explicitly programmed", "A machine that goes to school", "A type of hardware"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Chatbot'?", "options" => ["A talking toy", "An AI-powered program designed to simulate conversation with human users", "A virus that talks to you", "A fast web browser"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'IoT' (Internet of Things)?", "options" => ["The internet for everyone", "The network of physical objects embedded with sensors and software to connect and exchange data with other devices", "A type of internet cable", "A social media site"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Cloud Gaming'?", "options" => ["Playing games in the rain", "Streaming video games from remote servers directly to your device without needing powerful hardware", "Games about clouds", "A type of 3D game"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Blockchain'?", "options" => ["A chain made of blocks", "A decentralized, distributed digital ledger used to record transactions across many computers", "A type of security fence", "A backup system"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Cryptocurrency'?", "options" => ["A secret map", "Digital or virtual currency secured by cryptography", "Money made of plastic", "A type of bank account"], "ans" => 1, "xp" => 180],
                ["q" => "What is an 'NFT' (Non-Fungible Token)?", "options" => ["A type of coin", "A unique digital identifier that cannot be copied or substituted, used to certify ownership of digital assets", "A new file format", "A virus"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Virtual Reality' (VR)?", "options" => ["A 3D movie", "A simulated experience that can be similar to or completely different from the real world, usually using a headset", "A high-quality photo", "A type of video call"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Augmented Reality' (AR)?", "options" => ["A very loud noise", "An interactive experience where digital information is overlaid on the real-world environment (like Pokémon GO)", "A high-resolution screen", "A type of VR"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Big Data'?", "options" => ["A very large computer", "Extremely large data sets that may be analyzed computationally to reveal patterns and trends", "A list of all users", "A backup of the whole internet"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Quantum Computing'?", "options" => ["A very small PC", "A type of computing that uses quantum-mechanical phenomena to perform calculations much faster than classical computers", "A PC made of atoms", "A type of battery"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Automation'?", "options" => ["A type of car", "The use of technology to perform tasks with reduced human assistance", "A fast computer", "A type of software update"], "ans" => 1, "xp" => 250],
                ["q" => "What is '5G'?", "options" => ["5 gigabytes", "The 5th generation mobile network, providing much higher speeds and lower latency", "A type of Wi-Fi", "A fast CPU"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Smart City'?", "options" => ["A city with many schools", "An urban area that uses IoT sensors and data to manage assets and resources efficiently", "A city with free Wi-Fi", "A city in a video game"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Biotechnology' in computing?", "options" => ["Computers made of wood", "The intersection of biology and technology, such as using DNA for data storage", "A type of virus", "A green computer"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Generative AI'?", "options" => ["AI that is very old", "AI capable of generating new content, such as text, images, or music, based on training data", "AI that saves files", "A type of search engine"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Deep Learning'?", "options" => ["A long study session", "A subset of machine learning based on artificial neural networks with multiple layers", "A type of VR", "A backup system"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Algorithm Bias'?", "options" => ["A fast algorithm", "When an AI system produces systematically prejudiced results due to erroneous assumptions or biased training data", "A mathematical error", "A type of virus"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Edge Computing'?", "options" => ["Computing on a table edge", "Processing data closer to where it is generated (the 'edge') rather than in a centralized cloud to save time", "A type of browser", "A very fast internet connection"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Technological Singularity'?", "options" => ["A single computer", "A hypothetical future point where technological growth becomes uncontrollable and irreversible, resulting in unfathomable changes", "A type of battery", "The end of the internet"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Neuralink'?", "options" => ["A link to a network", "A company developing ultra-high bandwidth brain-machine interfaces", "A type of cable", "A new social media"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Autonomous Vehicle'?", "options" => ["A car with a manual engine", "A vehicle capable of sensing its environment and operating without human involvement", "A fast car", "A remote-controlled car"], "ans" => 1, "xp" => 400],
                ["q" => "What is the primary ethical concern of 'AI in the Workplace'?", "options" => ["Computers being too loud", "Potential job displacement and the need for worker retraining", "Using too much power", "The color of the robots"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ]
];

// ==========================================================
// ADVANCED COMPUTER SKILLS CURRICULUM (Maps 25-36)
// ==========================================================
$advanced_maps = [
    // CATEGORY 1: ENTERPRISE INFRASTRUCTURE & DEVOPS
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 1,
        "title" => "Map 25: Virtualization & Containers", "desc" => "Master Hypervisors, Docker, and Kubernetes orchestration.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Type 1' Hypervisor?", "options" => ["Software that runs on top of an OS", "Software that runs directly on the hardware (Bare Metal)", "A type of web browser", "A physical hard drive"], "ans" => 1, "xp" => 200],
                ["q" => "What is the primary difference between a VM and a Container?", "options" => ["VMs are faster", "Containers share the host OS kernel; VMs include a full Guest OS", "Containers are larger files", "VMs don't use RAM"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Docker'?", "options" => ["A hardware brand", "A platform for developing, shipping, and running applications in containers", "A cloud storage provider", "A programming language"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Snapshotting' in virtualization?", "options" => ["Taking a photo of the PC", "Saving the exact state of a Virtual Machine at a point in time", "Deleting the OS", "Printing the screen"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does 'Kubernetes' (K8s) do?", "options" => ["Edits images", "Orchestrates and manages containerized applications at scale", "Checks for viruses", "Hosts websites"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Pod' in Kubernetes?", "options" => ["A group of servers", "The smallest deployable unit that can contain one or more containers", "A storage disk", "A network cable"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'V2V' migration?", "options" => ["Video to Video", "Virtual-to-Virtual migration of a VM between different platforms", "Voice to Voice", "Virus to Virus"], "ans" => 1, "xp" => 240],
                ["q" => "Which of these is a Type 2 Hypervisor?", "options" => ["VMware ESXi", "Oracle VirtualBox", "Microsoft Hyper-V (Server)", "Citrix Hypervisor"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Infrastructure as Code' (IaC)?", "options" => ["Writing code on paper", "Managing and provisioning infrastructure through machine-readable definition files", "Building a PC manually", "Coding a website"], "ans" => 1, "xp" => 280],
                ["q" => "Which tool is commonly used for IaC?", "options" => ["Photoshop", "Terraform", "Excel", "Chrome"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Immutable Infrastructure'?", "options" => ["Infrastructure that is never updated", "Infrastructure that is replaced rather than modified when updates are needed", "Infrastructure made of stone", "Infrastructure that cannot be deleted"], "ans" => 1, "xp" => 280],
                ["q" => "What is a 'Container Registry'?", "options" => ["A list of containers", "A repository for storing and managing container images (e.g., Docker Hub)", "A type of database", "A firewall for containers"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'DevOps'?", "options" => ["A software program", "A set of practices that combines software development (Dev) and IT operations (Ops)", "A type of server", "A programming language"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'CI/CD'?", "options" => ["Computer Interface / Cloud Data", "Continuous Integration / Continuous Deployment", "Coded Input / Cached Data", "Central Intelligence / Coded Delivery"], "ans" => 1, "xp" => 350],
                ["q" => "What is the purpose of a 'Jenkins' or 'GitHub Actions' pipeline?", "options" => ["To send emails", "To automate the building, testing, and deployment of code", "To search the web", "To store photos"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Microservices' architecture?", "options" => ["One giant app", "Developing a single application as a suite of small, independent services", "Using small computers", "A type of cloud storage"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'High Availability' (HA)?", "options" => ["High speed internet", "A system design that ensures a prearranged level of operational performance (uptime)", "A very tall server", "Fast CPU speed"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Load Balancing'?", "options" => ["Distributing network traffic across multiple servers", "Making a server heavier", "Charging for internet usage", "Checking for errors"], "ans" => 0, "xp" => 400],
                ["q" => "What is 'Horizontal Scaling'?", "options" => ["Adding more RAM to one server", "Adding more machines into your pool of resources", "Making the monitor wider", "Buying a larger case"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Vertical Scaling' (Scaling Up)?", "options" => ["Adding more servers", "Adding more power (CPU/RAM) to an existing machine", "Stacking servers on top of each other", "Increasing the font size"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Serverless' computing (FaaS)?", "options" => ["Computing without any servers existing", "A model where the cloud provider manages the server and dynamically allocates resources", "Running a PC without a motherboard", "Offline computing"], "ans" => 1, "xp" => 500],
                ["q" => "What is a 'Cold Start' in serverless functions?", "options" => ["Starting a PC in winter", "The delay when a function is executed for the first time after being idle", "A frozen screen", "A hardware failure"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Blue-Green Deployment'?", "options" => ["Using two different monitors", "A technique that reduces downtime by running two identical production environments", "A type of color grading", "A branding strategy"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Canary Deployment'?", "options" => ["Deploying software to birds", "Releasing an update to a small subgroup of users before rolling it out to everyone", "A type of encryption", "A backup method"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 1,
        "title" => "Map 26: Cloud Architecture (AWS/Azure)", "desc" => "Deep dive into S3, EC2, IAM, and global cloud infrastructure.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'AWS EC2'?", "options" => ["A storage service", "Virtual servers in the cloud", "A database", "A networking cable"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'S3' in AWS?", "options" => ["A type of CPU", "Scalable object storage for data backup and archiving", "A web browser", "A secure network"], "ans" => 1, "xp" => 200],
                ["q" => "What is an 'Availability Zone' (AZ)?", "options" => ["A country", "One or more discrete data centers within a Region", "A desk in an office", "A type of firewall"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'IAM' (Identity and Access Management)?", "options" => ["A storage tool", "A framework for managing digital identities and permissions", "A type of server", "A backup system"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'VPC' (Virtual Private Cloud)?", "options" => ["A private server in your house", "A logically isolated section of the cloud where you can launch resources", "A type of VPN software", "A cloud storage drive"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Content Delivery Network' (CDN)?", "options" => ["A TV station", "A system of distributed servers that deliver web content based on geographic location", "A type of internet provider", "A file sharing app"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'CloudWatch' or 'Azure Monitor'?", "options" => ["A digital watch", "A monitoring and observability service for cloud resources", "A type of security camera", "A cloud-based clock"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Auto Scaling'?", "options" => ["Automatically resizing windows", "Automatically adjusting the number of computational resources based on load", "A type of weight scale", "Buying more storage"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'PaaS' (Platform as a Service)?", "options" => ["Hardware rental", "A category of cloud services that provides a platform for customers to develop apps", "Software like Word", "A physical office"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Shared Responsibility Model' in the cloud?", "options" => ["Sharing a password", "The provider secures the 'Cloud', the customer secures data 'IN' the cloud", "Splitting the bill", "Using two accounts"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Azure Active Directory' (Entra ID)?", "options" => ["A file folder", "A cloud-based identity and access management service", "A type of hard drive", "A messaging app"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Elastic Block Store' (EBS)?", "options" => ["A rubber band", "Persistent block storage volumes for use with EC2 instances", "A type of cloud network", "A CPU feature"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Serverless Database' (e.g., DynamoDB/CosmosDB)?", "options" => ["A database on paper", "A NoSQL database service that scales automatically without managing servers", "A database you host at home", "A slow database"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'VPC Peering'?", "options" => ["Looking at a cloud", "Connecting two VPCs to route traffic between them using private IP addresses", "A type of firewall", "A cloud backup"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Route 53'?", "options" => ["A highway", "A highly available and scalable Cloud Domain Name System (DNS)", "A type of server", "A cloud storage limit"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Lambda' in AWS?", "options" => ["A Greek letter", "A serverless, event-driven compute service", "A type of cloud storage", "A security protocol"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'CloudFormation' or 'ARM Templates'?", "options" => ["Weather patterns", "Infrastructure as Code services for cloud resource provisioning", "A type of cloud storage", "A graphics tool"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Direct Connect' or 'ExpressRoute'?", "options" => ["A fast USB cable", "A dedicated network connection from your premises to the cloud", "A wireless mouse", "A type of internet"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Object Lock' in S3?", "options" => ["Locking a folder", "A feature that prevents an object from being deleted or overwritten for a fixed amount of time", "A password on a file", "A type of encryption"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Multi-Region' architecture?", "options" => ["Using many monitors", "Deploying applications across multiple geographic cloud locations for disaster recovery", "Having many users", "A type of database"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Cost Explorer' used for?", "options" => ["Finding cheap servers", "Visualizing, understanding, and managing your cloud costs and usage over time", "Searching the web", "Calculating RAM"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Glacier' storage?", "options" => ["Cold storage for archival data that is rarely accessed", "A type of ice", "Fast SSD storage", "A cloud database"], "ans" => 0, "xp" => 500],
                ["q" => "What is an 'Edge Location'?", "options" => ["The corner of a desk", "Data centers used by CDNs to cache content closer to users", "A type of firewall", "The start of a network"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'CloudTrail'?", "options" => ["A walk in the sky", "A service that provides a record of actions taken by a user or role in the cloud", "A type of backup", "An internet speed test"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 1,
        "title" => "Map 27: Advanced Databases & SQL", "desc" => "Go beyond basic tables into Joins, Indexing, and ACID compliance.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'INNER JOIN' in SQL?", "options" => ["Combining all rows from both tables", "Returns records that have matching values in both tables", "Deleting matching records", "Creating a new table"], "ans" => 1, "xp" => 200],
                ["q" => "What is a 'Primary Key'?", "options" => ["The main password", "A unique identifier for each record in a database table", "The title of the database", "A type of column"], "ans" => 1, "xp" => 200],
                ["q" => "What is a 'Foreign Key'?", "options" => ["A key from another country", "A column that provides a link between data in two tables", "A backup key", "An encrypted password"], "ans" => 1, "xp" => 200],
                ["q" => "What does 'SQL' stand for?", "options" => ["Secure Query Language", "Structured Query Language", "Simple Query Logic", "System Quantified List"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Normalization' in a database?", "options" => ["Making things normal", "The process of organizing data to reduce redundancy and improve integrity", "Deleting old data", "Calculating averages"], "ans" => 1, "xp" => 240],
                ["q" => "What is an 'Index' used for in SQL?", "options" => ["To list all tables", "To speed up data retrieval operations", "To encrypt data", "To count the rows"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'ACID' compliance?", "options" => ["A chemical test", "A set of properties (Atomicity, Consistency, Isolation, Durability) that guarantee reliable transactions", "A type of database", "A security protocol"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Stored Procedure'?", "options" => ["A medical term", "A prepared SQL code that you can save and reuse over and over", "A type of backup", "A data entry form"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the difference between SQL and NoSQL?", "options" => ["SQL is free; NoSQL is paid", "SQL is relational (structured); NoSQL is non-relational (flexible/document)", "SQL is newer", "NoSQL is for small data only"], "ans" => 1, "xp" => 280],
                ["q" => "What is a 'Transaction' in a database?", "options" => ["A payment", "A sequence of operations performed as a single logical unit of work", "A backup", "A new user"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Deadlock' in database management?", "options" => ["A broken lock", "A situation where two or more transactions are waiting for each other to release locks", "A deleted database", "A high-security mode"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Sharding'?", "options" => ["Breaking a glass", "Horizontal partitioning of data across multiple database servers", "A type of encryption", "Deleting old records"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is an 'Execution Plan'?", "options" => ["A business plan", "A description of how the database engine will retrieve or modify data", "A type of backup", "A script to delete data"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Database Replication'?", "options" => ["Copying a database", "Storing the same data on multiple storage devices to ensure availability", "Deleting data", "Changing data types"], "ans" => 1, "xp" => 350],
                ["q" => "What is a 'View' in SQL?", "options" => ["The monitor", "A virtual table based on the result-set of an SQL statement", "A photo of the database", "The database schema"], "ans" => 1, "xp" => 350],
                ["q" => "What is the purpose of 'Group By'?", "options" => ["To sort alphabetically", "To arrange identical data into groups (often used with aggregate functions like SUM)", "To delete groups", "To name a table"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'ETL'?", "options" => ["Electronic Time Log", "Extract, Transform, and Load (data integration process)", "External Training Level", "Encrypted Text Layer"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Data Warehousing'?", "options" => ["A physical building", "A system used for reporting and data analysis", "Storing data on a USB", "A type of cloud backup"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Trigger'?", "options" => ["A virus", "A stored procedure that automatically runs when an event occurs in the database", "A button on the mouse", "A type of query"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Referential Integrity'?", "options" => ["Trusting a user", "Ensuring the relationships between tables remain consistent", "The accuracy of a single cell", "A database backup"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'OLAP'?", "options" => ["A type of RAM", "Online Analytical Processing (for complex data queries)", "A programming language", "A network protocol"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'JSON' commonly used for in databases?", "options" => ["Storing images", "A lightweight data-interchange format often used in NoSQL databases", "A type of password", "Calculating sums"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'BigQuery' (or Redshift)?", "options" => ["A large monitor", "A cloud-based big data warehouse for analytics", "A fast internet connection", "A type of CPU"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Database Partitioning'?", "options" => ["Splitting a database into distinct parts for better management/performance", "Deleting a database", "Sharing a database", "Encrypting a database"], "ans" => 0, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 1,
        "title" => "Map 28: Enterprise Networking", "desc" => "Subnetting, VPN protocols, and high-level routing architectures.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Subnetting'?", "options" => ["Fishing with a net", "The practice of dividing a network into two or more smaller networks", "Buying more routers", "Increasing internet speed"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'VLAN'?", "options" => ["A fast LAN", "Virtual Local Area Network (grouping devices together logically rather than physically)", "A type of cable", "A wireless network"], "ans" => 1, "xp" => 200],
                ["q" => "What is the 'OSI Model'?", "options" => ["A brand of router", "A conceptual framework used to understand and describe network communications", "A type of internet", "A security standard"], "ans" => 1, "xp" => 200],
                ["q" => "How many layers are in the OSI Model?", "options" => ["5", "7", "10", "4"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which OSI layer is responsible for IP addressing and routing?", "options" => ["Layer 1 (Physical)", "Layer 2 (Data Link)", "Layer 3 (Network)", "Layer 4 (Transport)"], "ans" => 2, "xp" => 240],
                ["q" => "What is 'BGP' (Border Gateway Protocol)?", "options" => ["A backup protocol", "The protocol that makes the internet work by routing traffic between autonomous systems", "A type of Wi-Fi", "A firewall"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Packet Sniffing'?", "options" => ["A physical check of a router", "Intercepting and logging network traffic", "Cleaning a network cable", "Speeding up a connection"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'DHCP' snooping?", "options" => ["Watching a router", "A security feature that acts like a firewall between untrusted hosts and trusted DHCP servers", "Stealing IP addresses", "A network speed test"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'QoS' (Quality of Service)?", "options" => ["High speed internet", "Prioritizing certain types of network traffic (like Voice or Video) to prevent lag", "A customer service metric", "A type of cable"], "ans" => 1, "xp" => 280],
                ["q" => "What is a 'Layer 7' Firewall?", "options" => ["A physical wall", "A firewall that can inspect and filter traffic based on the actual application (e.g., blocking only specific websites)", "A very tall firewall", "A fast firewall"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'IPv6'?", "options" => ["The 6th version of Windows", "The most recent version of IP addressing, created to solve the exhaustion of IPv4 addresses", "A faster type of Wi-Fi", "A type of network cable"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'NAT' (Network Address Translation)?", "options" => ["A language translator", "Mapping private IP addresses to a single public IP address", "Calculating internet speed", "Connecting two PCs"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'MPLS'?", "options" => ["Multiple Private Lines", "Multiprotocol Label Switching (a technique for high-performance telecommunications networks)", "A type of internet browser", "A wireless protocol"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'SD-WAN'?", "options" => ["A secure WAN", "Software-Defined Wide Area Network", "A very fast WAN", "A manual WAN"], "ans" => 1, "xp" => 350],
                ["q" => "What is a 'Trunk Port'?", "options" => ["A port for a elephant", "A switch port that carries traffic for multiple VLANs", "A port for power", "A port for a USB"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'STP' (Spanning Tree Protocol)?", "options" => ["A protocol for trees", "A protocol that prevents loops in a network topology", "A speed test", "A type of security"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'ICMP' used for?", "options" => ["Sending files", "Error reporting and diagnostic functions (like Ping and Traceroute)", "Playing games", "Connecting to Wi-Fi"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Port 443'?", "options" => ["Email", "HTTPS (Secure web traffic)", "FTP", "DNS"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Port 22'?", "options" => ["Web traffic", "SSH (Secure Shell)", "Printing", "Audio"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'MTU' (Maximum Transmission Unit)?", "options" => ["Most Time Used", "The largest packet or frame size that can be sent in a packet- or frame-based network", "Minimum Total Units", "A type of network"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Dark Fiber'?", "options" => ["Fiber optic cables that are broken", "Unused optical fiber infrastructure that is available for use", "A type of malware", "Black colored cables"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Latency' vs 'Jitter'?", "options" => ["Latency is delay; Jitter is the variation in delay", "They are the same", "Latency is speed; Jitter is volume", "Latency is for video; Jitter is for audio"], "ans" => 0, "xp" => 500],
                ["q" => "What is a 'Default Gateway'?", "options" => ["The main door of a building", "The IP address of the router that connects your local network to other networks", "The password for Wi-Fi", "A backup router"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Anycast'?", "options" => ["Casting to any TV", "A routing method where traffic is sent to the nearest member of a group of nodes", "A type of internet", "A security standard"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],

    // CATEGORY 2: CYBERSECURITY & SPECIALIZED SYSTEMS
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 2,
        "title" => "Map 29: Advanced Cryptography", "desc" => "Symmetric vs Asymmetric, Salting, Hashing, and Quantum risks.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the difference between Hashing and Encryption?", "options" => ["Hashing is for files; Encryption is for text", "Hashing is a one-way function (cannot be reversed); Encryption is two-way", "Hashing is faster", "There is no difference"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Asymmetric Encryption'?", "options" => ["Encryption that is not centered", "Using two different keys (Public and Private) for encryption and decryption", "Using one secret key", "Encryption without a password"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Symmetric Encryption'?", "options" => ["Using the same key for both encryption and decryption", "Using a different key for each user", "Encryption with a fingerprint", "A type of firewall"], "ans" => 0, "xp" => 200],
                ["q" => "What is 'AES-256'?", "options" => ["A type of CPU", "A highly secure symmetric encryption standard used globally", "An old password", "A web browser"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'RSA'?", "options" => ["A security company only", "A widely used asymmetric cryptosystem for secure data transmission", "A type of network cable", "A programming language"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Salt' in cryptography?", "options" => ["A seasoning", "Random data added to a password before hashing to defend against rainbow table attacks", "A type of virus", "A password manager"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Rainbow Table'?", "options" => ["A colorful table", "A precomputed table for reversing cryptographic hash functions (cracking passwords)", "A type of database", "A security log"], "ans" => 1, "xp" => 240],
                ["q" => "What does 'Perfect Forward Secrecy' ensure?", "options" => ["Your password never expires", "The compromise of one session key does not compromise past session keys", "A backup is always made", "Fast encryption"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Elliptic Curve Cryptography' (ECC)?", "options" => ["Encryption for circles", "An approach to public-key cryptography based on the algebraic structure of elliptic curves", "A 3D graphics tool", "A type of monitor"], "ans" => 1, "xp" => 280],
                ["q" => "What is a 'Key Exchange' (e.g., Diffie-Hellman)?", "options" => ["Trading passwords", "A method of securely exchanging cryptographic keys over a public channel", "Buying a new PC", "A network update"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Quantum-Resistant' cryptography?", "options" => ["Encryption for atoms", "Cryptographic algorithms thought to be secure against a coordinated attack by a quantum computer", "Very fast encryption", "Encryption that works in space"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Steganography'?", "options" => ["Studying dinosaurs", "The practice of concealing a file, message, or image within another file", "A type of password", "Encrypted email"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Zero-Knowledge Proof'?", "options" => ["A proof with no evidence", "A method by which one party can prove to another that they know a value, without conveying the value itself", "A type of password", "A deleted file"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Homomorphic Encryption'?", "options" => ["Encryption for photos", "A form of encryption that allows computation on ciphertexts, generating an encrypted result which matches the result of operations on plaintexts", "A type of CPU", "Fast encryption"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Blockchain' from a cryptographic perspective?", "options" => ["A digital chain", "A distributed ledger utilizing cryptographic hashing to secure chronologically linked blocks", "A type of cloud storage", "A high-speed network"], "ans" => 1, "xp" => 350],
                ["q" => "What is a 'Digital Certificate'?", "options" => ["A degree in IT", "A digital document used to prove the ownership of a public key", "A password", "An image file"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Collision' in hashing?", "options" => ["Two cars hitting", "When two different inputs produce the same hash output", "A broken hard drive", "A slow network"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'SHA-256'?", "options" => ["A security code", "A cryptographic hash function that generates a 256-bit signature", "A type of monitor", "A backup tool"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Ciphertext'?", "options" => ["A book about code", "The encrypted result of a plaintext message", "A type of font", "A secure network"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Brute Force' vs 'Dictionary Attack'?", "options" => ["Brute force tries everything; Dictionary tries common words", "They are the same", "Brute force uses a PC; Dictionary uses a book", "Brute force is for hardware"], "ans" => 0, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'PGP' (Pretty Good Privacy)?", "options" => ["A basic password", "An encryption program that provides cryptographic privacy and authentication for data communication", "A type of firewall", "A cloud storage app"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Kerberos'?", "options" => ["A mythological creature", "A computer network authentication protocol that works on the basis of tickets", "A type of hard drive", "A web browser"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Side-Channel Attack'?", "options" => ["An attack from the side", "An attack based on information gained from the physical implementation of a system (e.g., power consumption)", "A type of social engineering", "A network virus"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Bit-flipping'? ", "options" => ["Changing the screen orientation", "A type of attack where the attacker changes bits in a ciphertext to produce a specific plaintext", "Flipping a coin", "Rotating a hard drive"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 2,
        "title" => "Map 30: Penetration Testing & Defense", "desc" => "Learn the tools and tactics of ethical hackers.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Ethical Hacking'?", "options" => ["Stealing data for good", "Authorized hacking to find and fix security vulnerabilities", "Hacking for free", "Learning to code"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Nmap' used for?", "options" => ["Drawing maps", "Network discovery and security auditing", "Editing videos", "Browsing the web"], "ans" => 1, "xp" => 200],
                ["q" => "What is a 'Vulnerability Scan'?", "options" => ["Checking if a person is sick", "An automated process to identify security weaknesses in a system", "A manual hardware check", "A speed test"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'SQL Injection' (SQLi)?", "options" => ["Adding more rows to a table", "Inserting malicious SQL code into an input field to manipulate a database", "A type of database backup", "A faster query"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Metasploit'?", "options" => ["A type of cryptocurrency", "A framework for developing and executing exploit code against a remote target machine", "A web browser", "A backup tool"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Cross-Site Scripting' (XSS)?", "options" => ["Writing code on multiple sites", "Injecting malicious scripts into otherwise benign and trusted websites", "A type of monitor", "A network protocol"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Privilege Escalation'?", "options" => ["Getting a promotion", "Exploiting a bug to gain elevated access to resources normally protected from a user", "Using a faster PC", "A type of password"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Reverse Shell'?", "options" => ["A shell on the beach", "A type of shell connection where the target machine initiates a connection back to the attacker", "A type of command prompt", "A backup system"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Social Engineering' in hacking?", "options" => ["Building a network", "Manipulating people into performing actions or divulging confidential information", "A type of software engineering", "A marketing strategy"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Wireshark'?", "options" => ["A shark that lives in the wire", "A network protocol analyzer used for troubleshooting and analysis", "A web browser", "A game"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Payload' in an exploit?", "options" => ["The weight of a computer", "The part of the malware which performs the malicious action", "The price of the software", "A type of storage"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Red Teaming'?", "options" => ["A team that wears red", "An independent group that challenges an organization to improve its effectiveness by assuming an adversarial role", "A type of social media", "A group of administrators"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Blue Teaming'?", "options" => ["A team that wears blue", "The group responsible for defending against cyberattacks", "A group of hackers", "A type of software update"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'SIEM'?", "options" => ["A type of RAM", "Security Information and Event Management (centralized logging and analysis)", "A programming language", "A network protocol"], "ans" => 1, "xp" => 350],
                ["q" => "What is a 'SOC' (Security Operations Center)?", "options" => ["A type of CPU", "A centralized unit that deals with security issues on an organizational and technical level", "A keyboard brand", "A storage device"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Threat Hunting'?", "options" => ["Hunting animals", "The process of proactively searching through networks to detect and isolate advanced threats", "A type of antivirus", "Deleting suspicious files"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'CVE'?", "options" => ["Computer Video Error", "Common Vulnerabilities and Exposures (a list of publicly disclosed cybersecurity vulnerabilities)", "Certified Virus Expert", "Central Verification Engine"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Exploit'?", "options" => ["A story", "A piece of software or data that takes advantage of a vulnerability to cause unintended behavior", "A successful login", "A type of hardware"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Burp Suite' used for?", "options" => ["Cooking", "Testing the security of web applications", "Managing databases", "Recording videos"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Footprinting'?", "options" => ["Walking in the snow", "The process of gathering information about a target system before an attack", "A type of digital signature", "A BIOS setting"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Bug Bounty'?", "options" => ["A prize for finding insects", "A program where individuals can receive recognition and compensation for reporting security vulnerabilities", "A type of software license", "A security log"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'GDPR'?", "options" => ["General Data Protection Regulation (a legal framework for data privacy in the EU)", "A type of encryption", "A network protocol", "A high-speed hard drive"], "ans" => 0, "xp" => 500],
                ["q" => "What is 'Least Privilege' principle?", "options" => ["Giving everyone admin access", "Giving users only the access they need to do their job", "Deleting all accounts", "Sharing passwords"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Air Gapping'?", "options" => ["Leaving a window open", "Ensuring a computer or network is physically isolated from unsecured networks (like the internet)", "A type of cooling", "A wireless protocol"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 2,
        "title" => "Map 31: Advanced OS Administration", "desc" => "Active Directory, Group Policy, and Shell Scripting.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Active Directory'?", "options" => ["A list of websites", "A directory service developed by Microsoft for Windows domain networks", "A type of storage", "A messaging app"], "ans" => 1, "xp" => 200],
                ["q" => "What is a 'Domain Controller'?", "options" => ["A person who owns a website", "A server that responds to security authentication requests within a Windows domain", "A router", "A type of firewall"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Group Policy' (GPO)?", "options" => ["A company policy on insurance", "A feature that controls the working environment of user accounts and computer accounts", "A type of backup", "A software license"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'LDAP'?", "options" => ["A type of monitor", "Lightweight Directory Access Protocol (used for accessing directory services)", "A high-speed network", "A programming language"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'SSH'?", "options" => ["A quiet sound", "Secure Shell (a cryptographic network protocol for operating network services securely)", "A fast hard drive", "A type of monitor"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Cron' in Linux?", "options" => ["A type of food", "A time-based job scheduler", "A network protocol", "A security tool"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Sudo'?", "options" => ["A game", "A program that allows users to run programs with the security privileges of another user (usually root)", "A type of computer", "A backup tool"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Bash'?", "options" => ["A party", "A Unix shell and command language", "A type of hard drive", "A web browser"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'PowerShell'?", "options" => ["A powerful battery", "A task automation and configuration management framework from Microsoft", "A type of screen", "A fast internet"], "ans" => 1, "xp" => 280],
                ["q" => "What is a 'Kernel'?", "options" => ["A part of popcorn", "The core part of an operating system that manages hardware and software interactions", "A type of monitor", "A backup disk"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Registry Editor' (regedit)?", "options" => ["Editing a guest list", "A tool for viewing and changing settings in the Windows system registry", "A type of photo editor", "A network tool"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Systemd'?", "options" => ["A high-speed PC", "An init system and service manager for Linux operating systems", "A type of firewall", "A cloud storage service"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Log Rotation'?", "options" => ["Spinning a log book", "The process of managing log files so they don't consume too much disk space", "Deleting all logs", "Encrypting logs"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Uptime'?", "options" => ["The time you wake up", "The amount of time a computer has been running without a restart", "Internet speed", "CPU clock speed"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Load Average' in Linux?", "options" => ["Average weight of the PC", "A measure of the amount of computational work that a computer system performs", "Average internet speed", "Average temperature"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Package Manager' (e.g., APT, YUM)?", "options" => ["A person who packs boxes", "A tool that automates the process of installing, upgrading, and removing software", "A type of storage", "A web browser"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'RDP' (Remote Desktop Protocol)?", "options" => ["A type of RAM", "A protocol that allows a user to connect to another computer over a network connection", "A fast internet cable", "A security standard"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Chmod' used for?", "options" => ["Changing the time", "Changing the access permissions of file system objects in Unix-like systems", "Changing the monitor color", "Changing the CPU speed"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Chown' used for?", "options" => ["Changing the owner of a file or directory", "Changing the font size", "Buying a new computer", "Checking the network"], "ans" => 0, "xp" => 400],
                ["q" => "What is 'Kernel Panic'?", "options" => ["A scared computer", "A safety measure taken by an OS's kernel upon detecting a fatal internal error", "A type of virus", "A hardware update"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'RAID'?", "options" => ["A type of bug", "Redundant Array of Independent Disks (for data redundancy/performance)", "A network protocol", "A security standard"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'ZFS' or 'Btrfs'?", "options" => ["A type of CPU", "Advanced file systems that include features like snapshots and self-healing", "A web browser", "A programming language"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Disk Quota'?", "options" => ["A discount on a hard drive", "A limit set by a system administrator that restricts aspects of file system usage", "A type of encryption", "A network speed limit"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Runlevel' in Linux?", "options" => ["How fast a person runs", "A mode of operation in the init system of a Unix-like OS", "The level of a game", "A hardware setting"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 2,
        "title" => "Map 32: Enterprise IT Governance", "desc" => "ITIL, Disaster Recovery, and Compliance (SOC2/HIPAA).",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'ITIL'?", "options" => ["A type of computer", "Information Technology Infrastructure Library (a set of practices for IT service management)", "A network protocol", "A security tool"], "ans" => 1, "xp" => 200],
                ["q" => "What is an 'SLA' (Service Level Agreement)?", "options" => ["A type of cable", "A contract between a service provider and a customer specifying service standards", "A fast internet connection", "A programming language"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Disaster Recovery' (DR)?", "options" => ["Cleaning up after a storm", "A set of policies and tools to enable the recovery of IT infrastructure after a disaster", "Buying a new PC", "A type of antivirus"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Business Continuity Planning' (BCP)?", "options" => ["Making a business plan", "The process of creating systems of prevention and recovery to deal with potential threats to a company", "Paying employees", "Buying office furniture"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'SOC2'?", "options" => ["A type of RAM", "A compliance standard for service organizations to manage data securely", "A web browser", "A network protocol"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'HIPAA'?", "options" => ["A large animal", "Health Insurance Portability and Accountability Act (US law for data privacy in healthcare)", "A programming language", "A type of hard drive"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Change Management'?", "options" => ["Counting money", "A process for managing the lifecycle of all changes to IT infrastructure", "Buying new PCs", "Hiring new staff"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Asset Management'?", "options" => ["Managing a bank account", "The process of tracking and managing the physical and digital assets of an organization", "A type of backup", "A software update"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'RTO' (Recovery Time Objective)?", "options" => ["The time it takes to get to work", "The maximum duration of time that a business process must be restored after a disaster", "The price of a backup", "A hardware setting"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'RPO' (Recovery Point Objective)?", "options" => ["The point where you stop working", "The maximum age of files that must be recovered from backup storage for operations to resume", "A network protocol", "A type of monitor"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Governance' in IT?", "options" => ["The government's IT department", "Ensuring IT activities are aligned with business goals and comply with laws", "Buying the best computers", "A type of firewall"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Audit' in IT?", "options" => ["A large room", "An independent examination of IT systems and processes to ensure they meet standards", "A software update", "A backup system"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Risk Management'?", "options" => ["Taking risks", "The process of identifying, assessing, and controlling threats to an organization's capital and earnings", "A type of insurance", "Buying a fast PC"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Access Control'?", "options" => ["Controlling a remote", "The selective restriction of access to a place or other resource", "A type of keyboard", "A network protocol"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Shadow IT'?", "options" => ["IT in the dark", "IT systems or software used within an organization without explicit organizational approval", "A secret IT department", "A type of virus"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Patch Management'?", "options" => ["Fixing a hole in a roof", "The process of managing a network of computers by deploying software updates", "A type of gardening", "Deleting old files"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'MFA'?", "options" => ["Multi-Factor Authentication", "Most Frequent Access", "Managed File Archive", "Main Frame Array"], "ans" => 0, "xp" => 400],
                ["q" => "What is 'BYOD'?", "options" => ["Buy Your Own Device", "Bring Your Own Device (policy of employees using personal devices for work)", "Back Up Your Data", "Build Your Own Database"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Data Loss Prevention' (DLP)?", "options" => ["Buying more storage", "A set of tools and processes to ensure sensitive data is not lost, misused, or accessed by unauthorized users", "A type of firewall", "A hardware update"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Incident Response'?", "options" => ["Answering a question", "An organized approach to addressing and managing the aftermath of a security breach", "A type of software", "A network speed test"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Data Sovereignty'?", "options" => ["Powerful data", "The concept that data is subject to the laws of the country in which it is physically located", "Free data", "High-quality data"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'ISO/IEC 27001'?", "options" => ["A part for a car", "An international standard for information security management systems", "A programming language", "A web browser"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Egress' in data terms?", "options" => ["Entering a site", "Data leaving a network", "Deleting data", "Fast data"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Identity Governance'?", "options" => ["Managing a country", "Managing user identities and their access rights across an organization", "A type of password", "A network protocol"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],

    // CATEGORY 3: CUTTING-EDGE ENGINEERING & STRATEGY
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 3,
        "title" => "Map 33: Artificial Intelligence Engineering", "desc" => "Neural Networks, LLMs, and AI implementation strategies.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'Artificial Neural Network'?", "options" => ["A robot brain", "A computing system inspired by the biological neural networks in animal brains", "A very fast internet", "A type of database"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Deep Learning'?", "options" => ["Studying a lot", "A subset of machine learning based on artificial neural networks with representation learning", "A type of VR", "A backup system"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Natural Language Processing' (NLP)?", "options" => ["Speaking naturally", "The branch of AI that gives computers the ability to understand text and spoken words", "A type of monitor", "A network protocol"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Computer Vision'?", "options" => ["A computer's eyesight", "A field of AI that trains computers to interpret and understand the visual world", "A high-resolution monitor", "A type of graphics card"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Large Language Model' (LLM)?", "options" => ["A big dictionary", "A type of AI trained on vast amounts of text to understand and generate human-like language", "A programming language", "A database of words"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Reinforcement Learning'?", "options" => ["Learning from a teacher", "A type of machine learning where an agent learns to make decisions by performing actions and receiving rewards", "Repeating a task", "A backup method"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Supervised Learning'?", "options" => ["Learning with a boss", "A type of machine learning where the model is trained on labeled data", "Learning on your own", "A network protocol"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Unsupervised Learning'?", "options" => ["Learning without a teacher", "A type of machine learning that looks for previously unknown patterns in a data set without pre-existing labels", "A type of virus", "A hardware setting"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Turing Test'?", "options" => ["A driving test", "A test of a machine's ability to exhibit intelligent behavior equivalent to that of a human", "A speed test", "A security check"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Explainable AI' (XAI)?", "options" => ["AI that can talk", "AI in which the results of the solution can be understood by humans", "A simple AI", "A type of search engine"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'AI Bias'?", "options" => ["An AI that is unfair", "Anomalies in the output of machine learning algorithms due to prejudiced assumptions in the training data", "A mathematical error", "A type of hardware failure"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Edge AI'?", "options" => ["AI on a table edge", "Running AI algorithms locally on a device rather than on a centralized cloud server", "A very fast AI", "A new AI company"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'AIGC'?", "options" => ["Artificial Intelligence Generated Content", "All Internet Game Center", "Advanced Input Global Code", "Automated Image Graphic Cell"], "ans" => 0, "xp" => 350],
                ["q" => "What is a 'GPU' role in AI?", "options" => ["Displaying images only", "Performing the massive amount of parallel calculations required for training AI models", "Calculating internet speed", "Storing data"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'TensorFlow' or 'PyTorch'?", "options" => ["A weather app", "Popular open-source frameworks for machine learning and deep learning", "A programming language", "A type of cloud storage"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Model Training'?", "options" => ["Teaching a model", "The process of providing an ML algorithm with data to learn from", "Building a hardware model", "A software update"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Overfitting' in ML?", "options" => ["Fitting too many clothes", "When a model learns the training data too well, including the noise, and fails to generalize to new data", "A small database", "A fast computer"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Underfitting'?", "options" => ["A small model", "When a model cannot capture the underlying trend of the data", "A type of virus", "A hardware update"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Data Scientist'?", "options" => ["A person who studies atoms", "A professional who uses scientific methods and algorithms to extract insights from data", "A computer programmer only", "A network administrator"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Feature Engineering'?", "options" => ["Building a car", "The process of using domain knowledge to select and transform variables when creating a predictive model", "A type of design", "A network tool"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'AGI' (Artificial General Intelligence)?", "options" => ["Basic AI", "Hypothetical AI that can understand or learn any intellectual task that a human can", "AI that is very fast", "AI used in games"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Singularity' in AI?", "options" => ["A single AI", "The point at which AI surpasses human intelligence and becomes self-improving", "A type of battery", "The end of the internet"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Robotic Process Automation' (RPA)?", "options" => ["Building a robot", "Using software to automate repetitive human tasks", "A type of factory", "A self-driving car"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'AI Ethics'?", "options" => ["AI manners", "The field of study that addresses the moral issues related to AI development and use", "A type of programming", "A legal framework"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 3,
        "title" => "Map 34: Blockchain & Web 3.0", "desc" => "Smart Contracts, Decentralization, and the future of trust.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Smart Contract'?", "options" => ["A contract that is very clever", "Self-executing contracts with the terms of the agreement directly written into lines of code", "A digital signature", "An encrypted email"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Decentralization'?", "options" => ["Moving to a new city", "Transferring control and decision-making from a centralized entity to a distributed network", "Deleting all servers", "Sharing a password"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Ethereum'?", "options" => ["A type of gas", "A decentralized, open-source blockchain with smart contract functionality", "A web browser", "A programming language"], "ans" => 1, "xp" => 200],
                ["q" => "What is a 'Crypto Wallet'?", "options" => ["A physical wallet", "A tool that allows you to interact with the blockchain and manage your digital assets", "A secure folder", "A type of bank account"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Proof of Work' (PoW)?", "options" => ["Working hard at a job", "A consensus mechanism that requires miners to solve complex mathematical puzzles", "A type of security", "A network protocol"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Proof of Stake' (PoS)?", "options" => ["Owning a piece of wood", "A consensus mechanism where validators are chosen based on the number of coins they hold", "A type of backup", "A speed test"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Mining' in crypto?", "options" => ["Digging in a hole", "The process of verifying transactions and adding them to the public ledger (blockchain)", "Buying coins", "A type of virus"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Private Key' vs 'Public Key' in crypto?", "options" => ["Private is for everyone; Public is for you", "Private is your signature/access; Public is your address for receiving", "They are the same", "Private is for storage; Public is for speed"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Web 3.0'?", "options" => ["The 3rd version of Google", "The idea for a new iteration of the World Wide Web based on blockchain and decentralization", "Very fast internet", "A type of web browser"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'DeFi'?", "options" => ["Decentralized Finance (financial services on a blockchain)", "Digital Finance", "Deep Finance", "Definitive File Interface"], "ans" => 0, "xp" => 280],
                ["q" => "What is a 'Stablecoin'?", "options" => ["A coin that doesn't move", "A cryptocurrency designed to have a stable value, often pegged to a fiat currency", "A very old coin", "A type of physical money"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'IPFS'?", "options" => ["InterPlanetary File System (a peer-to-peer hypermedia protocol)", "Internet Private File Store", "Internal Project File System", "Infinite Protocol File Sync"], "ans" => 0, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'DAO'?", "options" => ["Digital Access Only", "Decentralized Autonomous Organization (governed by smart contracts)", "Data Archive Office", "Direct Access Output"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Metaverse'?", "options" => ["A new search engine", "A collective virtual shared space, created by the convergence of VR, AR, and the internet", "A planet", "A type of database"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Solidity'?", "options" => ["The hardness of an object", "A programming language for writing smart contracts on Ethereum", "A type of security", "A hardware setting"], "ans" => 1, "xp" => 350],
                ["q" => "What is a 'Gas Fee'?", "options" => ["Paying for fuel for your car", "A fee paid to miners/validators to process and validate transactions on a blockchain", "The cost of internet", "A type of tax"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Layer 2' in blockchain?", "options" => ["A second hard drive", "A secondary framework or protocol built on top of an existing blockchain to improve scalability", "A type of monitor", "A network protocol"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Hash Rate'?", "options" => ["How fast you can run", "A measure of the total computational power being used by a Proof of Work blockchain", "The speed of a hard drive", "A type of encryption"], "ans" => 1, "xp" => 400],
                ["q" => "What is '51% Attack'?", "options" => ["A discount at a store", "When a single entity gains control of more than half of a blockchain's mining power", "A type of virus", "A hardware failure"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Immutable' in blockchain?", "options" => ["Unable to be changed (once data is written, it cannot be altered)", "Very fast", "Secure", "Free"], "ans" => 0, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Cold Storage' for crypto?", "options" => ["Storing a PC in a fridge", "Keeping private keys completely offline to prevent hacking", "A type of backup", "A digital bank account"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Fork' in blockchain?", "options" => ["A tool for eating", "When a blockchain splits into two separate paths due to a change in protocol", "A type of virus", "A hardware update"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Tokenomics'?", "options" => ["The study of tokens", "The economic model and incentive structure of a specific cryptocurrency", "A type of bank", "A financial software"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Staking'?", "options" => ["Killing a vampire", "Participating in a Proof of Stake system by locking up your coins to support the network", "Selling coins", "Deleting an account"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 3,
        "title" => "Map 35: Software Engineering Lifecycle", "desc" => "Agile, CI/CD, and professional engineering standards.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Agile' methodology?", "options" => ["Being very fast", "An iterative approach to software development and project management", "A programming language", "A type of server"], "ans" => 1, "xp" => 200],
                ["q" => "What is a 'Sprints' in Agile?", "options" => ["Running fast", "A set period of time during which specific work has to be completed and made ready for review", "A software update", "A type of meeting"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Scrum'?", "options" => ["A rugby term", "A framework within which people can address complex adaptive problems while delivering high-value products", "A type of code", "A network protocol"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Kanban'?", "options" => ["A Japanese car", "A visual system for managing work as it moves through a process", "A type of database", "A programming tool"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Unit Testing'?", "options" => ["Testing a whole machine", "Testing individual components of a software application to ensure they work correctly", "Testing in a group", "A type of security test"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Integration Testing'?", "options" => ["Testing alone", "Testing how different modules of an application work together", "A type of hardware test", "A speed test"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'UAT' (User Acceptance Testing)?", "options" => ["Universal Access Tool", "The final phase of software testing where actual users test the software for real-world scenarios", "A type of monitor", "A coding standard"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Refactoring'?", "options" => ["Building a new factory", "The process of restructuring existing computer code without changing its external behavior", "Deleting code", "A software update"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Technical Debt'?", "options" => ["Owing money to a tech company", "The implied cost of additional rework caused by choosing an easy solution now instead of a better approach that would take longer", "A hardware failure", "A high-speed network"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Version Control' (e.g., Git)?", "options" => ["Checking the time", "A system that records changes to a file or set of files over time", "A type of storage", "A programming language"], "ans" => 1, "xp" => 280],
                ["q" => "What is a 'Pull Request' (PR)?", "options" => ["Pulling a lever", "A method of submitting contributions to an open-source project or within a team", "A software update", "A type of database query"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Code Review'?", "options" => ["Reading a book", "The systematic examination of computer source code to find bugs and improve quality", "A type of test", "A meeting with a boss"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'API'?", "options" => ["Application Programming Interface", "Advanced Program Input", "All Project Information", "Automated Path Interface"], "ans" => 0, "xp" => 350],
                ["q" => "What is 'REST' vs 'SOAP'?", "options" => ["Sleeping vs washing", "Two different architectures for building APIs", "Types of computers", "Programming languages"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Dependency' in software?", "options" => ["Needing someone", "A piece of software that another piece of software relies on to function", "A hardware part", "A type of virus"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Legacy Code'?", "options" => ["Old code from a deceased person", "Old source code that is still in use but may be outdated or difficult to maintain", "Code that is free", "Code written by AI"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'IDE'?", "options" => ["Internal Drive Engine", "Integrated Development Environment (e.g., VS Code)", "Instant Data Entry", "International Digital Expert"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Documentation' in engineering?", "options" => ["Writing a book", "The information that describes the software to its users and developers", "A type of code", "A backup system"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Scalability'?", "options" => ["The weight of an app", "The ability of a system to handle a growing amount of work by adding resources", "A type of design", "A software update"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Fault Tolerance'?", "options" => ["Being patient with errors", "The property that enables a system to continue operating properly in the event of a failure", "A type of firewall", "A hardware update"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Product Backlog'?", "options" => ["A log of old products", "A prioritized list of features, enhancements, and bug fixes that need to be addressed", "A type of storage", "A financial record"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'MVP' (Minimum Viable Product)?", "options" => ["Most Valuable Player", "A version of a product with just enough features to be usable by early customers", "A type of PC", "A secure network"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'User Story'?", "options" => ["A fairy tale", "An informal, general explanation of a software feature written from the perspective of the end user", "A type of code", "A help document"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Pair Programming'?", "options" => ["Programming with two PCs", "A software development technique in which two programmers work together at one workstation", "A group project", "A coding class"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $advanced_course_id, "category_id" => 3,
        "title" => "Map 36: Advanced Final Capstone", "desc" => "The ultimate challenge. Prove your mastery across all advanced disciplines.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: A web server is slow due to high traffic. What is the BEST first architectural fix?", "options" => ["Buy a new server", "Implement a Load Balancer and horizontal scaling", "Restart the server", "Change the CSS"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You need to ensure zero data loss during a database update. What do you use?", "options" => ["A backup on a USB", "Database Transactions (ACID properties)", "A very fast internet", "Deleting old data first"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You want to deploy code automatically every time a change is made. What do you set up?", "options" => ["A calendar reminder", "A CI/CD Pipeline", "An email auto-reply", "A manual backup"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: A hacker is trying every possible character combination to guess a password. What is this called?", "options" => ["Phishing", "Brute Force Attack", "Social Engineering", "SQL Injection"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: You need to store sensitive healthcare data in the cloud. Which compliance is most important?", "options" => ["SOC2", "HIPAA", "GDPR", "PCI-DSS"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You want to hide a secret file inside a larger image file. What is this called?", "options" => ["Cryptography", "Steganography", "Hashing", "Obfuscation"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You need to connect two remote office networks securely over the internet. What do you use?", "options" => ["A long Ethernet cable", "A Site-to-Site VPN", "A public cloud link", "A wireless router"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You are building an app that needs to identify faces in photos. Which AI field do you use?", "options" => ["NLP", "Computer Vision", "Reinforcement Learning", "Expert Systems"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: You need to scale a database horizontally across many regions. What is a common technique?", "options" => ["Sharding", "Formatting", "Zipping", "Rebooting"], "ans" => 0, "xp" => 280],
                ["q" => "SCENARIO: Your application needs to run exactly the same on a developer's laptop and a production server. What do you use?", "options" => ["A word document", "Docker Containers", "A faster CPU", "Windows 11 only"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You need to manage 1,000 servers using code. What is this practice called?", "options" => ["Manual Administration", "Infrastructure as Code (IaC)", "Cloud Computing", "Software Engineering"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You want to ensure your data is safe even if a whole cloud data center fails. What do you use?", "options" => ["A local backup", "Multi-AZ or Multi-Region deployment", "A faster internet", "A better password"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: You need to prove to an auditor that you know a password without revealing it. What do you use?", "options" => ["A digital signature", "Zero-Knowledge Proof", "A secure email", "A fingerprint"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are building a decentralized app with no middleman. What technology is best?", "options" => ["A centralized database", "Blockchain / Smart Contracts", "A private server", "A messaging app"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: Your company wants to move from a monolithic app to many small services. What is this called?", "options" => ["Microservices", "Macroservices", "Nanoservices", "Cloudservices"], "ans" => 0, "xp" => 350],
                ["q" => "SCENARIO: You need to analyze petabytes of data to find trends. What do you use?", "options" => ["Excel", "BigQuery / Data Warehouse", "A standard SQL database", "A text file"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: You need to identify if a machine learning model is ready for real users. What is the final stage?", "options" => ["Model Training", "UAT (User Acceptance Testing)", "Unit Testing", "Code Review"], "ans" => 1, "xp" => 400],
                ["q" => "SCENARIO: A system must be restored within 4 hours of a disaster. What is this 4-hour window called?", "options" => ["RPO", "RTO", "SLA", "MTU"], "ans" => 1, "xp" => 400],
                ["q" => "SCENARIO: You want to use a cryptographic hash that is currently considered the industry standard for security. What do you use?", "options" => ["MD5", "SHA-256", "SHA-1", "Base64"], "ans" => 1, "xp" => 400],
                ["q" => "SCENARIO: You are implementing a 'Zero Trust' security model. What is the core principle?", "options" => ["Trust but verify", "Never trust, always verify (no device is trusted by default)", "Trust everyone on the LAN", "Only trust the boss"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "MASTER FINAL: What is the primary benefit of the Cloud + DevOps + Containers synergy?", "options" => ["It's cheaper for everyone", "Faster time-to-market, higher scalability, and improved system reliability", "It removes the need for programmers", "It makes the internet faster"], "ans" => 1, "xp" => 600],
                ["q" => "MASTER FINAL: In a high-security enterprise environment, what is the 'Gold Standard' for identity?", "options" => ["A strong password", "MFA with Hardware Security Keys and Role-Based Access Control (RBAC)", "A fingerprint only", "A secret question"], "ans" => 1, "xp" => 600],
                ["q" => "MASTER FINAL: To prevent future 'Technical Debt' in a large project, what should a lead engineer prioritize?", "options" => ["Fastest possible coding", "Code quality, documentation, and modular design", "Using only free software", "Hiring more people"], "ans" => 1, "xp" => 600],
                ["q" => "MASTER FINAL: What is the most critical factor for an IT department to align with its parent business?", "options" => ["Buying the newest gadgets", "IT Governance and Strategic Alignment", "Having the fastest internet", "Having the most servers"], "ans" => 1, "xp" => 600]
            ]]
        ]
    ]
];

// ==========================================================
// BUSINESS ADMINISTRATION CURRICULUM (Maps 37-48)
// ==========================================================
$biz_maps = [
    // CATEGORY 1: OFFICE OPERATIONS (Maps 37-40)
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 1,
        "title" => "Map 37: Principles of Business Admin", "desc" => "Fundamentals of organizational structure and administrative roles.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary role of Business Administration?", "options" => ["To perform repairs", "To plan, organize, and oversee business operations", "To only manage social media", "To manufacture goods manually"], "ans" => 1, "xp" => 150],
                ["q" => "Which organizational structure has a clear chain of command from top to bottom?", "options" => ["Flat", "Hierarchical", "Circular", "Networked"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'SME' stand for in business?", "options" => ["System Management Entry", "Small to Medium Enterprise", "Sales Marketing Engine", "Strategic Modern Enterprise"], "ans" => 1, "xp" => 150],
                ["q" => "Which department is responsible for managing employee payroll and benefits?", "options" => ["Marketing", "Human Resources", "Logistics", "IT"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Stakeholder'?", "options" => ["A person who owns the land", "Anyone affected by or interested in the business (owners, employees, customers)", "Only the CEO", "A type of office equipment"], "ans" => 1, "xp" => 180],
                ["q" => "What is the purpose of a Mission Statement?", "options" => ["To list all employees", "To define the company's core purpose and focus", "To show the annual profit", "To advertise a specific product"], "ans" => 1, "xp" => 180],
                ["q" => "In a 'Flat' organizational structure, what is usually missing?", "options" => ["Employees", "Middle management layers", "A CEO", "Office space"], "ans" => 1, "xp" => 180],
                ["q" => "Which term describes the internal rules that guide a company's actions?", "options" => ["Marketing", "Policies and Procedures", "Social Media", "Logistics"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Front-office' work?", "options" => ["Cleaning the building", "Customer-facing tasks like sales and reception", "Accounting and payroll", "Server maintenance"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Back-office' work?", "options" => ["Working in the garden", "Administrative support tasks that don't involve direct customer contact", "Selling products", "Reception duties"], "ans" => 1, "xp" => 210],
                ["q" => "What is an 'Executive Assistant'?", "options" => ["A junior manager", "A professional who supports high-level managers with complex admin tasks", "A person who fixes computers", "A temporary office clerk"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Corporate Culture'?", "options" => ["The art on the walls", "The shared values, beliefs, and behaviors within a company", "The company's bank account", "The dress code only"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Outsourcing'?", "options" => ["Working outside the office", "Hiring an external party to perform services or create goods", "Selling office equipment", "Hiring more managers"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Scalability' in a business model?", "options" => ["How heavy the products are", "The ability to handle growth without being hindered by structure or resources", "The number of stairs in the building", "The color of the logo"], "ans" => 1, "xp" => 250],
                ["q" => "What does 'ROI' stand for?", "options" => ["Rate of Interest", "Return on Investment", "Regional Office Index", "Regular Office Income"], "ans" => 1, "xp" => 250],
                ["q" => "Which document outlines the long-term goals and steps to achieve them?", "options" => ["Business Plan", "Receipt", "Time sheet", "Payroll log"], "ans" => 0, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Change Management'?", "options" => ["Counting petty cash", "The process of transitioning individuals or teams to a desired future state", "Changing the office layout", "Updating the software"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Operations Management'?", "options" => ["Fixing machinery", "Designing and controlling the process of production and business operations", "Managing the cleaning staff", "Hiring new employees"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'SWOT' analysis used for?", "options" => ["Accounting", "Evaluating Strengths, Weaknesses, Opportunities, and Threats", "Cleaning the office", "Organizing files"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Workflow'?", "options" => ["The speed of the internet", "The sequence of industrial, administrative, or other processes through which a piece of work passes", "Working in a gym", "The office air conditioning"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Strategic Planning'?", "options" => ["Daily task listing", "Setting long-term goals and determining the best approach to achieve them", "Planning an office party", "Creating a weekly schedule"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Standard Operating Procedure' (SOP)?", "options" => ["A type of software", "Established step-by-step instructions for routine activities", "The company's legal name", "The layout of the office"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'KPI'?", "options" => ["Keep People Informed", "Key Performance Indicator", "Known Product Item", "Key Personal Interaction"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Board of Directors'?", "options" => ["The people who work at the front desk", "A group of individuals elected to represent shareholders and oversee the company", "A list of managers", "A type of office furniture"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 1,
        "title" => "Map 38: Office Procedures & Etiquette", "desc" => "Master the protocols of a professional working environment.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the professional way to answer an office telephone?", "options" => ["'Hello?'", "'What do you want?'", "Identify yourself and the company/department", "Just stay silent until they speak"], "ans" => 2, "xp" => 150],
                ["q" => "What is 'Punctuality'?", "options" => ["Correct grammar", "Being on time", "Being good at math", "Wearing a suit"], "ans" => 1, "xp" => 150],
                ["q" => "What should you do if you are running late for a meeting?", "options" => ["Sneak in and say nothing", "Notify the organizer as soon as possible", "Don't go at all", "Wait until the next day to apologize"], "ans" => 1, "xp" => 150],
                ["q" => "Which term describes a professional's outward appearance and behavior?", "options" => ["Professionalism", "Friendliness", "Casualness", "Complexity"], "ans" => 0, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Office Etiquette'?", "options" => ["The office furniture", "The unwritten rules of social behavior in a workplace", "The company's profit margin", "The software used for emails"], "ans" => 1, "xp" => 180],
                ["q" => "How should you handle confidential information overheard in the office?", "options" => ["Tell your coworkers", "Keep it strictly private", "Post it on social media", "Tell your family"], "ans" => 1, "xp" => 180],
                ["q" => "What is the purpose of an 'Agenda'?", "options" => ["To take notes", "To outline the topics to be discussed in a meeting", "To list attendees", "To record the time"], "ans" => 1, "xp" => 180],
                ["q" => "What does 'RSVP' mean on an invitation?", "options" => ["Please reply", "Bring food", "Wear a suit", "Don't come late"], "ans" => 0, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What are 'Meeting Minutes'?", "options" => ["The duration of the meeting", "The written record of what was discussed and decided", "A list of people who were late", "The time the meeting ended"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Quorum'?", "options" => ["A type of meeting room", "The minimum number of members required to make a meeting's proceedings valid", "A long meeting", "A vote taken in secret"], "ans" => 1, "xp" => 210],
                ["q" => "Which term describes the process of managing multiple tasks at once?", "options" => ["Multitasking", "Prioritizing", "Delegating", "Procrastinating"], "ans" => 0, "xp" => 210],
                ["q" => "What is 'Time Management'?", "options" => ["Watching the clock", "Organizing and planning how to divide your time between specific activities", "Buying a new watch", "Being fast at typing"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Active Listening'?", "options" => ["Hearing music", "Fully concentrating on, understanding, and responding to what is being said", "Nodding without listening", "Interrupting with your own ideas"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Conflict Resolution'?", "options" => ["Starting a fight", "The process of finding a peaceful solution to a disagreement", "Ignoring the problem", "Telling the boss"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Constructive Criticism'?", "options" => ["Complaining about work", "Feedback intended to help someone improve", "Insulting someone's work", "Saying only positive things"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Networking' in a professional sense?", "options" => ["Connecting computers", "Building and maintaining professional relationships", "Using social media only", "Handing out flyers"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Accountability'?", "options" => ["Doing math", "The obligation of an individual to account for their activities and accept responsibility", "Blaming others", "Working long hours"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Diplomacy' in the workplace?", "options" => ["International politics", "The art of dealing with people in a sensitive and effective way", "Being very loud", "Avoiding everyone"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Discretion'?", "options" => ["Being very fast", "The quality of behaving or speaking in such a way as to avoid causing offense or revealing private information", "Working in secret", "Choosing your own hours"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Mentorship'?", "options" => ["Teaching a class", "A relationship in which a more experienced person guides a less experienced one", "Paying for a tutor", "Hiring a new manager"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Emotional Intelligence' (EQ)?", "options" => ["Being smart", "The capacity to be aware of, control, and express one's emotions, and to handle interpersonal relationships judiciously", "Being very emotional", "Crying at work"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Synergy' in a team?", "options" => ["Working alone", "The interaction or cooperation of two or more agents to produce a combined effect greater than the sum of their separate effects", "A type of software", "Competing with coworkers"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cross-training'?", "options" => ["Training for a race", "Training an employee to do different parts of the organization's work", "Working in two companies", "Training outside"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Burnout'?", "options" => ["A fire in the office", "Physical or mental collapse caused by overwork or stress", "Running out of coffee", "Quitting your job"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 1,
        "title" => "Map 39: Business Communication", "desc" => "Advanced writing, professional emails, and internal reporting.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Business Memo' used for?", "options" => ["External advertising", "Internal communication within an organization", "Personal messages", "Legal contracts"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Tone' of a professional email?", "options" => ["Loud and aggressive", "Casual and slang-filled", "Professional, clear, and polite", "Secretive"], "ans" => 2, "xp" => 150],
                ["q" => "Which part of a business letter contains the sender's address and date?", "options" => ["Salutation", "Letterhead / Heading", "Body", "Closing"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Salutation'?", "options" => ["A type of goodbye", "The greeting at the beginning of a letter (e.g., 'Dear Mr. Smith')", "The signature", "The main point of the letter"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does 'Concise' mean in writing?", "options" => ["Very long and detailed", "Brief but comprehensive", "Poorly written", "Written in a different language"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Proofreading'?", "options" => ["Reading for fun", "Reading a text to find and correct errors", "Printing a document", "Writing the first draft"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Jargon'?", "options" => ["A type of container", "Special words or expressions used by a particular profession that are difficult for others to understand", "A foreign language", "Clear communication"], "ans" => 1, "xp" => 180],
                ["q" => "What is an 'Internal Report'?", "options" => ["A news article", "A document prepared for use within a company (e.g., sales update)", "An advertisement", "A letter to a client"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the purpose of a 'Executive Summary'?", "options" => ["To list all employees", "A short section of a document that summarizes the whole report for quick reading", "A list of expenses", "The title page"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Non-verbal Communication'?", "options" => ["Silence", "Communication without words (body language, eye contact, tone of voice)", "Writing an email", "Reading a report"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Paraphrasing'?", "options" => ["Copying exactly", "Rewording something written or spoken by someone else", "Deleting text", "Translating"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Visual Communication'?", "options" => ["Talking face to face", "Using graphs, charts, and images to convey information", "Reading a book", "Listening to a podcast"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Press Release'?", "options" => ["A private letter", "An official statement sent to the media to provide information", "A type of social media post", "A company meeting"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Upward Communication'?", "options" => ["Talking to your coworkers", "Flow of information from subordinates to higher levels (managers/executives)", "Talking to customers", "Communication between two companies"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Downward Communication'?", "options" => ["Talking to your boss", "Flow of information from higher levels to subordinates", "Talking to external vendors", "Gossiping"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Lateral Communication'?", "options" => ["Communication between different levels", "Communication between people at the same level in the organization", "Talking to yourself", "A phone call"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Communication Noise'?", "options" => ["Loud music", "Anything that interferes with the transmission or understanding of a message", "Talking too much", "A broken microphone"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Feedback Loop'?", "options" => ["A round table", "The process in which the receiver's response is sent back to the sender", "A type of echo", "Ignoring a message"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Persuasive Writing'?", "options" => ["Writing a story", "Writing intended to convince the reader to take a specific action or adopt a viewpoint", "Writing instructions", "Writing a report"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Crisis Communication'?", "options" => ["Talking during a fire", "Managing communication during a major negative event affecting the company", "Calling an ambulance", "A fast meeting"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Direct Communication' style?", "options" => ["Being rude", "Stating the main point clearly and immediately", "Talking through a third party", "Using hints"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Indirect Communication' style?", "options" => ["Never speaking", "Delivering the main point gradually or using subtle hints", "Using a megaphone", "Writing a letter"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Intercultural Communication'?", "options" => ["Talking to computers", "Communication between people from different cultural backgrounds", "Learning a new language", "Traveling for business"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Semantic Barrier'?", "options" => ["A physical wall", "Misunderstanding caused by different meanings assigned to words", "A loud noise", "A broken internet"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 1,
        "title" => "Map 40: Record Keeping & Filing", "desc" => "Master the physical and digital organization of critical business data.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Record Keeping'?", "options" => ["Writing a diary", "The systematic procedure of maintaining accurate logs of business transactions and activities", "Saving photos on a phone", "Memory alone"], "ans" => 1, "xp" => 150],
                ["q" => "Which filing system organizes records by the letters of the alphabet?", "options" => ["Numeric", "Geographic", "Alphabetic", "Chronological"], "ans" => 2, "xp" => 150],
                ["q" => "Which filing system organizes records by date?", "options" => ["Alphabetic", "Chronological", "Subject", "Numeric"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Database' in digital record keeping?", "options" => ["A folder on the desktop", "A structured collection of data stored electronically", "A paper notebook", "A type of printer"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Archiving'?", "options" => ["Deleting files", "Moving inactive records to long-term storage", "Sharing files online", "Printing documents"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Records Retention Schedule'?", "options" => ["A calendar for meetings", "A policy stating how long different types of records must be kept before being destroyed", "A list of employee names", "A daily task list"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Indexing' a file?", "options" => ["Putting it in a box", "Assigning a unique identifier or name to a record for easy retrieval", "Counting the pages", "Stapling papers"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Centralized Filing'?", "options" => ["Keeping files on every desk", "Storing all company records in one single location", "Using a cloud server", "Deleting old records"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Decentralized Filing'?", "options" => ["Losing all files", "Storing records at the point of use in different departments", "Storing files on the moon", "One giant file room"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Cross-Reference' in filing?", "options" => ["A mistake in the file", "A notation showing that a record is filed under a different name or category", "Deleting two files", "Copying a file"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Vital Records'?", "options" => ["Medical records only", "Records essential to the operation of the business (e.g., contracts, titles)", "Records about the office lunch", "Old newspapers"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Data Entry'?", "options" => ["Entering a building", "The act of inputting information into a computer system", "Writing on paper", "Sending an email"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Disaster Recovery' for records?", "options" => ["Buying new paper", "A plan to protect and restore records in case of fire, flood, or theft", "Ignoring the problem", "Starting a new business"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Shredding' used for?", "options" => ["Making confetti", "Safely destroying sensitive or confidential documents", "Organizing papers", "Recycling for profit"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Metadata' in digital filing?", "options" => ["The actual file content", "Data about the file (author, date created, size)", "A virus", "A type of software"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Cloud Storage'?", "options" => ["Storing papers in the sky", "Saving data on remote servers accessed via the internet", "A physical hard drive", "A type of filing cabinet"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Version Control'?", "options" => ["Checking the time", "Managing multiple revisions of the same document to ensure the latest is used", "Naming a file 'final2'", "Deleting old files"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Access Control' for records?", "options" => ["Opening a door", "Restricting who can view or edit specific records based on their role", "A type of password", "Keeping files in a safe"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Audit Trail'?", "options" => ["A walk in the woods", "A step-by-step record of the history of a transaction or file access", "A list of managers", "A backup system"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Data Integrity'?", "options" => ["Being honest at work", "Ensuring that data remains accurate, complete, and consistent over time", "A type of encryption", "Having a lot of data"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'EDMS'?", "options" => ["Electronic Document Management System", "Every Document Must Stay", "Extra Digital Management Service", "Efficient Data Marketing System"], "ans" => 0, "xp" => 400],
                ["q" => "What is 'Optical Character Recognition' (OCR)?", "options" => ["A type of eye test", "Technology that converts images of text into machine-readable text", "A way to take photos", "A printer setting"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Encryption' in record keeping?", "options" => ["Writing in secret", "The process of converting data into a code to prevent unauthorized access", "Deleting data", "A type of password"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Redundancy' in data storage?", "options" => ["Having too many employees", "Storing multiple copies of the same data in different locations for safety", "Deleting redundant files", "A slow computer"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 2: HUMAN RESOURCES & OPERATIONS (Maps 41-44)
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 2,
        "title" => "Map 41: Basic HR Principles", "desc" => "Recruitment, contracts, and employee management.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Human Resources' (HR)?", "options" => ["A type of machinery", "The department that manages employees and company culture", "A list of customers", "A financial report"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Recruitment'?", "options" => ["Firing people", "The process of finding and hiring new employees", "Training staff", "Paying payroll"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Job Description'?", "options" => ["The name of a company", "A document outlining the tasks, duties, and responsibilities of a role", "A list of former employees", "A performance review"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Employment Contract'?", "options" => ["A promise", "A legal agreement between an employer and an employee", "A type of insurance", "A daily schedule"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Onboarding'?", "options" => ["Getting on a boat", "The process of integrating a new employee into the company", "Firing a person", "Giving a promotion"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Employee Engagement'?", "options" => ["A wedding", "The level of commitment and passion an employee has for their work", "Being very busy", "Telling people what to do"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Performance Review'?", "options" => ["Watching a movie", "A formal assessment of an employee's work over a period of time", "A salary increase", "A team meeting"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Payroll'?", "options" => ["Rolling a ball", "The total amount of money a company pays its employees", "A type of tax", "A bank account"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Workplace Diversity'?", "options" => ["Having many offices", "Hiring people of different backgrounds, genders, and ethnicities", "Changing your job often", "Having many different products"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Conflict of Interest'?", "options" => ["Disliking your job", "A situation where a person's private interests could influence their professional duties", "Two people fighting", "A broken contract"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Sexual Harassment'?", "options" => ["A type of romance", "Unwelcome conduct of a sexual nature in the workplace", "Being friendly", "A disagreement"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Work-Life Balance'?", "options" => ["Working and sleeping", "The equilibrium between a person's work and personal life", "Working 24 hours", "Being unemployed"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Labor Law'?", "options" => ["Rules for manual labor", "The body of law that mediates the relationship between workers and employers", "Rules for gardening", "A type of contract"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Succession Planning'?", "options" => ["Planning a party", "Identifying and developing new leaders who can replace old ones", "Saving money", "Hiring a new intern"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Employee Turnover'?", "options" => ["A type of pastry", "The rate at which employees leave a company and are replaced by new ones", "Promoting people", "Changing office hours"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Company Policy'?", "options" => ["A government law", "The set of rules and principles that govern how a company operates", "A marketing strategy", "An insurance plan"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Fringe Benefits'?", "options" => ["Haircuts for staff", "Extra benefits provided to employees (e.g., health insurance, car allowance)", "Working from home", "A salary bonus"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Disciplinary Action'?", "options" => ["Exercise at work", "Corrective action taken by an employer in response to employee misconduct", "A promotion", "A training session"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Grievance Procedure'?", "options" => ["A funeral", "A formal process for employees to raise concerns or complaints", "Quitting your job", "A marketing plan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Wrongful Termination'?", "options" => ["A broken computer", "Firing an employee for illegal reasons or in breach of contract", "Quitting without notice", "A layoff"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Human Capital'?", "options" => ["Money owned by humans", "The skills, knowledge, and experience possessed by an individual or population", "The company's office building", "A list of employees"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Collective Bargaining'?", "options" => ["Shopping in bulk", "Negotiation between an employer and a group of employees (usually a union)", "Selling products", "A team meeting"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Organizational Behavior'?", "options" => ["How people act in public", "The study of how people interact within groups and organizations", "A type of psychology only", "Company rules"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Upskilling'?", "options" => ["Moving to a higher floor", "Learning new skills to keep up with changes in the workplace", "A salary increase", "Hiring a new manager"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 2,
        "title" => "Map 42: Finance & Budgeting", "desc" => "Invoices, basic accounting, and managing office expenses.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Budget'?", "options" => ["A type of bank account", "An estimate of income and expenditure for a set period", "A pile of cash", "A list of employees"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Invoice'?", "options" => ["A receipt", "A document sent by a seller to a buyer requesting payment for goods or services", "A bank statement", "A marketing flyer"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Revenue'?", "options" => ["Profit", "The total amount of money received from sales before expenses are deducted", "Spending money", "A type of tax"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Expense'?", "options" => ["Money received", "Money spent or cost incurred in an organization's efforts to generate revenue", "A type of income", "A bank deposit"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Net Profit'?", "options" => ["Total sales", "The amount of money left after all expenses and taxes are paid", "The amount of money in the bank", "Total revenue"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Gross Profit'?", "options" => ["Profit after tax", "Revenue minus the cost of goods sold", "Total revenue", "The salary of the CEO"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Balance Sheet'?", "options" => ["A sheet for balancing books", "A financial statement that reports a company's assets, liabilities, and shareholders' equity", "A list of daily sales", "An expense report"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Liability'?", "options" => ["An asset", "Something a person or company owes, usually a sum of money", "A type of income", "A company car"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Cash Flow'?", "options" => ["Money in a river", "The total amount of money being transferred into and out of a business", "Total profit", "A bank loan"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Accounts Receivable'?", "options" => ["Money you owe", "Money owed to a company by its customers", "The company's bank account", "Payroll"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Accounts Payable'?", "options" => ["Money customers owe you", "Money a company owes to its suppliers", "Total revenue", "Petty cash"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Petty Cash'?", "options" => ["Money for pets", "A small amount of cash on hand for minor office expenses", "The CEO's bonus", "Total profit"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Depreciation'?", "options" => ["Gaining value", "The reduction in the value of an asset over time", "A type of tax", "Saving money"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Audit' in finance?", "options" => ["A large room", "An official inspection of an individual's or organization's accounts", "Spending money", "A type of loan"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Equity'?", "options" => ["Equality at work", "The value of the shares issued by a company", "A type of debt", "A bank statement"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Fiscal Year'?", "options" => ["A calendar year", "A one-year period used for financial reporting and budgeting", "A leap year", "A very busy year"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Break-even Point'?", "options" => ["Breaking a bone", "The point at which total cost and total revenue are equal", "Making a lot of money", "Losing all your money"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Overhead'?", "options" => ["The ceiling", "Ongoing business expenses not directly attributed to creating a product or service", "The salary of the boss", "A type of tax"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Capital'?", "options" => ["A city", "Wealth in the form of money or other assets owned by a person or organization", "The company name", "A type of font"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Inventory'?", "options" => ["A discovery", "A complete list of items such as property, goods in stock, or the contents of a building", "A type of tax", "The company's bank account"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Ledge' in accounting?", "options" => ["A shelf", "A book or computer file for recording and totaling economic transactions", "The edge of a building", "A type of office chair"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Fixed Cost'?", "options" => ["A cost that changes", "A cost that does not change with an increase or decrease in the amount of goods or services produced", "A repair cost", "A salary bonus"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Variable Cost'?", "options" => ["A cost that stays the same", "A corporate expense that changes in proportion to production output", "A type of tax", "The cost of office rent"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Liquidity'?", "options" => ["Being a liquid", "The availability of liquid assets (cash) to a market or company", "Being very fast", "Working underwater"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 2,
        "title" => "Map 43: Meeting & Event Coordination", "desc" => "Plan, host, and follow up on professional company events.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the first step in planning a business meeting?", "options" => ["Order lunch", "Define the objective/purpose of the meeting", "Invite everyone you know", "Book the largest room"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Attendee List'?", "options" => ["A list of items to buy", "A list of people who are invited to or present at an event", "A marketing flyer", "A payroll log"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Venue'?", "options" => ["A type of car", "The place where an event or meeting is held", "A guest speaker", "A type of food"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Itinerary'?", "options" => ["A list of names", "A planned route or journey; a schedule of events", "A receipt", "A map of the office"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Catering'?", "options" => ["Fixing computers", "Providing food and drink at a meeting or event", "Cleaning the venue", "Designing invitations"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'AV' (Audio-Visual) equipment?", "options" => ["A type of car", "Microphones, projectors, and screens used for presentations", "Office chairs", "Computers for the staff"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Webinar'?", "options" => ["A spider's web", "A seminar conducted over the internet", "A physical meeting", "A type of email"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Breakout Session'?", "options" => ["A prison break", "A small group discussion or workshop within a larger conference", "A coffee break", "The end of the meeting"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Logistics' in event planning?", "options" => ["Logic puzzles", "The detailed coordination of a complex operation involving many people, facilities, or supplies", "Writing the agenda", "Designing the logo"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Keynote Speaker'?", "options" => ["A person who fixes keys", "The main speaker at a conference who sets the central theme", "A person who takes notes", "A junior employee"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Budgeting' for an event?", "options" => ["Naming the event", "Allocating financial resources to cover the costs of the event", "Inviting guests", "Choosing the food"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Follow-up' after a meeting?", "options" => ["Chasing someone", "Actions taken after a meeting to ensure decisions are implemented", "The next meeting", "A thank you note only"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Lead Time'?", "options" => ["The time spent leading", "The time between the start and completion of a production process or project", "The duration of a meeting", "A type of clock"], "ans" => 1, "xp" => 250],
                ["q" => "What is an 'Event Brief'?", "options" => ["A short meeting", "A document outlining all the details and requirements for an event", "A pair of glasses", "A list of attendees"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Risk Assessment' for an event?", "options" => ["Counting money", "Identifying potential problems that could occur and planning how to handle them", "A type of insurance", "The cost of the venue"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Contingency Plan'?", "options" => ["A plan for a party", "A backup plan in case the primary plan fails", "A list of guests", "The main agenda"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Vendor Management'?", "options" => ["Managing a shop", "The process of managing relationships with third-party suppliers", "Hiring new employees", "Designing products"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Stakeholder Communication'?", "options" => ["Talking to coworkers only", "Keeping all interested parties informed about the event's progress", "An internal memo", "A secret meeting"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Post-event Evaluation'?", "options" => ["A party after the event", "Reviewing the success of the event and identifying areas for improvement", "Paying the bills", "Cleaning the venue"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Registration'?", "options" => ["A type of car", "The process by which people sign up to attend an event", "The list of employees", "A marketing flyer"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Hybrid Event'?", "options" => ["An event with two names", "An event that combines a physical location with a virtual component", "A car show", "A very fast meeting"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Engagement' during an event?", "options" => ["A wedding", "The level of participation and interest shown by the attendees", "The duration of the event", "The number of speakers"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Sponsorship'?", "options" => ["A type of friendship", "Financial support given by an organization for an event, often in exchange for advertising", "A loan", "A free ticket"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Branding' of an event?", "options" => ["Marking cattle", "Creating a unique image and theme for the event", "Designing invitations only", "The name of the venue"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 2,
        "title" => "Map 44: CRM & Customer Service", "desc" => "Manage client relations and professional support systems.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does 'CRM' stand for?", "options" => ["Company Relationship Management", "Customer Relationship Management", "Common Resource Management", "Corporate Record Marketing"], "ans" => 1, "xp" => 150],
                ["q" => "What is the primary goal of Customer Service?", "options" => ["To make money only", "To ensure customer satisfaction and build loyalty", "To ignore complaints", "To sell as much as possible once"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Client'?", "options" => ["A coworker", "A person or organization using the services of another", "A competitor", "A manager"], "ans" => 1, "xp" => 150],
                ["q" => "Which tool is commonly used to track customer interactions?", "options" => ["A notebook", "CRM Software", "A calendar", "A calculator"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Customer Loyalty'?", "options" => ["Liking a company", "The likelihood that a customer will continue to buy from a business", "Buying things for free", "A type of discount"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Customer Retention'?", "options" => ["Firing a customer", "The ability of a company to keep its customers over time", "Searching for new customers", "Ignoring old customers"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Empathy' in customer service?", "options" => ["Being smart", "The ability to understand and share the feelings of the customer", "Feeling sorry for someone", "Being very loud"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Complaint'?", "options" => ["A type of praise", "An expression of dissatisfaction with a product or service", "A suggestion", "A thank you note"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Upselling'?", "options" => ["Selling things on a hill", "Encouraging a customer to purchase a more expensive or premium version of a product", "Selling things for free", "Ignoring a customer"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Cross-selling'?", "options" => ["Selling to angry customers", "Selling related or complementary products to a customer", "Selling to a competitor", "Selling in a different language"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Customer Journey'?", "options" => ["A trip the customer takes", "The complete sum of experiences that customers go through when interacting with your company", "A marketing flyer", "A list of customers"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Feedback'?", "options" => ["A loud noise", "Information about reactions to a product or service, used as a basis for improvement", "A type of payment", "An advertisement"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Ticket' in customer support?", "options" => ["A bus ticket", "A record of a customer request or issue in a support system", "A fine", "A prize"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'SLA' in customer service?", "options" => ["Service Level Agreement (e.g., promising a reply in 24 hours)", "Super Low Average", "Standard Legal Action", "Secret Loyalty Award"], "ans" => 0, "xp" => 250],
                ["q" => "What is 'Churn Rate'?", "options" => ["Making butter", "The rate at which customers stop doing business with an entity", "The speed of sales", "The number of new customers"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Touchpoint'?", "options" => ["A place to touch the wall", "Any point of interaction between a customer and a business", "A type of computer", "A fingerprint"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Personalization' in CRM?", "options" => ["Talking to yourself", "Tailoring a service or product to accommodate specific individuals", "Hiring new staff", "Changing the company logo"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Automation' in CRM?", "options" => ["A type of car", "Using software to perform repetitive tasks (like sending follow-up emails)", "Fixing machinery", "A new marketing plan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Lead' in sales?", "options" => ["A type of metal", "A person or organization that has shown interest in a product or service", "A manager", "A final sale"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Conversion'?", "options" => ["Changing religion", "The point at which a lead becomes a paying customer", "A type of translation", "A salary increase"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'NPS' (Net Promoter Score)?", "options" => ["A type of tax", "A metric used to measure customer loyalty and satisfaction", "A bank statement", "A marketing award"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'User Experience' (UX)?", "options" => ["Playing a game", "The overall experience of a person using a product, especially in terms of how easy or pleasing it is to use", "A type of software", "Working in a company"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Proactive' customer service?", "options" => ["Acting after a complaint", "Anticipating customer needs and addressing them before they become problems", "Ignoring the customer", "Waiting for instructions"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Customer Advocacy'?", "options" => ["Suing a company", "When customers are so satisfied they actively promote your brand to others", "A type of marketing", "A legal team"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 3: STRATEGY & MANAGEMENT (Maps 45-48)
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 3,
        "title" => "Map 45: Business Ethics & Compliance", "desc" => "Professional integrity, data privacy, and corporate laws.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Business Ethics'?", "options" => ["Doing math at work", "The study of proper business policies and practices regarding potentially controversial issues", "The company's rules on dress code", "A type of accounting"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Compliance' in business?", "options" => ["Being friendly", "The act of obeying relevant laws, regulations, and rules", "A promotion", "A marketing plan"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Whistleblower'?", "options" => ["A person who plays sports", "An employee who reports organizational misconduct to the public or higher authority", "A manager", "A security guard"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Transparency'?", "options" => ["A clear window", "Operating in such a way that it is easy for others to see what actions are performed", "A type of software", "Hiding the truth"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Conflict of Interest'?", "options" => ["Disliking your job", "A situation where a person's private interests could influence their professional duties", "Two people fighting", "A broken contract"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Corporate Social Responsibility' (CSR)?", "options" => ["Working on the weekend", "A business model where companies make a concerted effort to operate in ways that enhance society", "Paying taxes", "A marketing strategy"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Insider Trading'?", "options" => ["Trading in the office", "The illegal practice of trading on the stock exchange to one's own advantage through having access to confidential information", "Selling products", "A team meeting"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Data Privacy'?", "options" => ["A secret password", "The protection of personal information from unauthorized access or use", "A type of firewall", "A cloud storage service"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'POPIA' in South Africa?", "options" => ["A type of food", "Protection of Personal Information Act", "Public Office Performance Interaction Act", "Private Organization Professional Integrity Act"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Anti-Money Laundering' (AML)?", "options" => ["Cleaning money with soap", "A set of laws, regulations, and procedures intended to prevent criminals from disguising illegally obtained funds", "Saving money", "A bank account"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Bribery'?", "options" => ["Giving a gift", "Dishonestly persuading someone to act in one's favor by a gift of money or other inducement", "A salary increase", "A marketing plan"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Code of Conduct'?", "options" => ["A type of computer code", "A set of rules outlining the social norms, religious rules and responsibilities of an individual or organization", "A daily schedule", "A software license"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Intellectual Property' (IP)?", "options" => ["Owning a house", "A category of property that includes intangible creations of the human intellect", "A fast computer", "A smart person"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Copyright'?", "options" => ["The right to copy anything", "A legal right that grants the creator of an original work exclusive rights to its use and distribution", "A brand name", "A type of patent"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Trademark'?", "options" => ["A trade with a mark", "A recognizable sign, design, or expression which identifies products or services of a particular source", "A patent", "A legal contract"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Patent'?", "options" => ["A type of leather", "A government authority or license conferring a right or title for a set period", "A brand name", "A type of insurance"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Fiduciary Duty'?", "options" => ["A type of tax", "A legal obligation of one party to act in the best interest of another", "A duty to work hard", "A salary bonus"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Anti-trust' law?", "options" => ["Not trusting anyone", "Legislation used to prevent a business from becoming a monopoly", "A type of insurance", "A banking rule"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Discrimination' in the workplace?", "options" => ["Hiring the best person", "Unjust or prejudicial treatment of different categories of people", "A promotion", "A training session"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Sustainability' in business?", "options" => ["Working forever", "Meeting the needs of the present without compromising the ability of future generations to meet theirs", "Saving money", "A type of marketing"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Equality' vs 'Equity'?", "options" => ["They are the same", "Equality is giving everyone the same; Equity is giving people what they need to be successful", "Equality is for pay; Equity is for hours", "Equality is for gender; Equity is for race"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Corporate Governance'?", "options" => ["The government of a country", "The system of rules, practices, and processes by which a firm is directed and controlled", "Managing a team", "Buying new software"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Due Diligence'?", "options" => ["Working hard", "Reasonable steps taken by a person in order to satisfy a legal requirement, especially before buying something", "Paying taxes on time", "Hiring a new manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Audit' in ethics?", "options" => ["A large room", "A systematic assessment of how well a company is following its ethical policies", "A performance review", "A bank statement"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 3,
        "title" => "Map 46: Intro to Project Management", "desc" => "Gantt charts, task allocation, and project lifecycle.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Project'?", "options" => ["Routine daily work", "A temporary endeavor undertaken to create a unique product, service, or result", "A type of computer", "A department in a company"], "ans" => 1, "xp" => 150],
                ["q" => "What is the role of a 'Project Manager'?", "options" => ["To do all the work", "To lead the team to achieve the project goals within given constraints", "To pay the employees", "To answer the phones"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Project Lifecycle'?", "options" => ["The age of the project manager", "The sequence of phases that a project goes through from its initiation to its closure", "The project's budget", "A list of tasks"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Stakeholder' in a project?", "options" => ["The person holding the wood", "Anyone who has an interest in or may be affected by the project", "Only the project manager", "A competitor"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Scope'?", "options" => ["A type of mouthwash", "The detailed set of deliverables or features of a project", "The budget of a project", "The deadline"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Scope Creep'?", "options" => ["A person walking slowly", "Uncontrolled changes or continuous growth in a project's scope", "A small project", "A mistake in the plan"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Milestone'?", "options" => ["A heavy stone", "A significant point or event in a project", "A type of task", "A project deadline"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Gantt Chart'?", "options" => ["A map of the office", "A type of bar chart that illustrates a project schedule", "A list of expenses", "A performance review"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Deliverable'?", "options" => ["A person who delivers mail", "A tangible or intangible good or service produced as a result of a project", "A project deadline", "A budget report"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Risk Management' in projects?", "options" => ["Taking risks", "Identifying, analyzing, and responding to project risks", "Buying insurance", "Ignoring problems"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Resource Allocation'?", "options" => ["Buying more things", "Assigning and managing assets in a manner that supports a project's goals", "Hiring new staff", "Spending the budget"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Critical Path'?", "options" => ["A dangerous road", "The sequence of stages determining the minimum time needed for an operation", "The project goal", "A list of tasks"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Agile' project management?", "options" => ["Being very fast", "An iterative approach to managing projects that focuses on continuous improvement and flexibility", "A type of software", "A strictly planned project"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Waterfall' project management?", "options" => ["Working near water", "A linear and sequential approach to project management", "A very fast project", "An iterative project"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Project Charter'?", "options" => ["A map", "A document that formally authorizes the existence of a project", "A list of tasks", "A budget report"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Procurement'?", "options" => ["Cleaning the office", "The act of obtaining goods or services, typically for business purposes", "Selling products", "Hiring staff"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Work Breakdown Structure' (WBS)?", "options" => ["A broken machine", "A hierarchical decomposition of the total scope of work to be carried out", "A daily schedule", "A budget report"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Quality Assurance' (QA)?", "options" => ["Promising good work", "A way of preventing mistakes and avoiding problems when delivering products or services", "A type of test", "Fixing a broken product"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Slack Time' or 'Float'?", "options" => ["A coffee break", "The amount of time a task can be delayed without affecting the project completion date", "Being lazy", "The duration of a meeting"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Project Stakeholder Management'?", "options" => ["Managing a shop", "Identifying the people, groups, or organizations that could impact or be impacted by the project", "Hiring staff", "Selling products"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Constraint' in a project?", "options" => ["A type of law", "Any restriction that defines a project's boundaries (e.g., time, budget)", "A goal", "A significant event"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Dependency' between tasks?", "options" => ["Needing someone", "A relationship in which a task relies on another task to start or finish", "A mistake in the plan", "A significant event"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Post-Mortem' in projects?", "options" => ["A medical exam", "A process for evaluating a project after it has been completed", "Closing a bank account", "Firing the team"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Sponsor' of a project?", "options" => ["A person who buys tickets", "A person or group who provides resources and support for the project and is accountable for success", "A manager", "A competitor"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 3,
        "title" => "Map 47: Strategy & High-Level Leadership", "desc" => "Strategic alignment, corporate vision, and leading teams.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Leadership'?", "options" => ["Telling people what to do", "The action of leading a group of people or an organization", "Having a high salary", "Owning a company"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Vision Statement'?", "options" => ["A medical report", "A declaration of an organization's objectives, intended to guide its internal decision-making", "A mission statement", "A budget report"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Strategic Alignment'?", "options" => ["Being in a straight line", "The process of modifying organizational structure and processes to support the business strategy", "Managing a team", "Buying new software"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Competitive Advantage'?", "options" => ["Having more money", "A condition or circumstance that puts a company in a superior business position", "A type of law", "Cheating in business"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Delegation'?", "options" => ["Doing all the work", "Entrusting a task or responsibility to another person", "Hiring new staff", "Quitting a job"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Servant Leadership'?", "options" => ["Being a servant", "A leadership philosophy in which the main goal of the leader is to serve", "A strict leader", "A leader who does no work"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Transactional Leadership'?", "options" => ["Leadership based on money", "A style of leadership that focuses on supervision, organization, and performance", "A visionary leader", "A type of banking"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Transformational Leadership'?", "options" => ["Changing your clothes", "A style of leadership where a leader works with teams to identify needed change and creating a vision", "A strict leader", "A type of software"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Strategic Thinking'?", "options" => ["Thinking about tomorrow", "Thinking that is focused on finding and developing unique opportunities to create value", "Daydreaming", "Problem solving"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Blue Ocean Strategy'?", "options" => ["Fishing in the ocean", "Simultaneous pursuit of differentiation and low cost to open up a new market space", "Selling products near water", "A type of marketing"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Value Chain'?", "options" => ["A chain made of gold", "The process or activities by which a company adds value to an article", "A list of expenses", "A marketing flyer"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Market Penetration'?", "options" => ["Entering a store", "A measure of the amount of sales or adoption of a product or service compared to the total theoretical market", "A type of tax", "Buying a competitor"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Core Competency'?", "options" => ["Being good at everything", "A defining capability or advantage that distinguishes an enterprise from its competitors", "A daily task", "A professional skill only"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Vertical Integration'?", "options" => ["Standing up straight", "The combination in one company of two or more stages of production normally operated by separate companies", "Hiring more staff", "Merging two companies"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Horizontal Integration'?", "options" => ["Lying down", "The process of a company increasing production of goods or services at the same part of the supply chain", "Quitting a job", "A salary bonus"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Market Cannibalization'?", "options" => ["Eating food", "A reduction in sales volume, sales revenue, or market share of one product as a result of the introduction of a new product by the same producer", "A type of marketing", "A financial loss"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Autocratic Leadership'?", "options" => ["Driving a car", "A leadership style characterized by individual control over all decisions and little input from group members", "A democratic leader", "A leader who does no work"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Democratic Leadership'?", "options" => ["A political system", "A leadership style in which members of the group take a more participative role in the decision-making process", "A strict leader", "A visionary leader"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Laissez-faire Leadership'?", "options" => ["A type of food", "Leaders are hands-off and allow group members to make the decisions", "A strict leader", "A visionary leader"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Micro-management'?", "options" => ["Managing a small team", "A leadership style where a manager closely observes or controls the work of subordinates", "A visionary leader", "A type of software"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Change Agent'?", "options" => ["A person who counts money", "A person from inside or outside an organization who helps an organization transform itself", "A type of software", "A marketing plan"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Conflict Management'?", "options" => ["Starting a fight", "The process of limiting the negative aspects of conflict while increasing the positive aspects of conflict", "Ignoring the problem", "Telling the boss"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Empowerment' in leadership?", "options" => ["Giving someone power", "Giving employees the authority, tools, and resources they need to make decisions", "A salary increase", "Hiring a new manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Team Building'?", "options" => ["Building a house", "The process of creating a team that is cohesive and works well together", "A type of software", "A team meeting"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $biz_course_id, "category_id" => 3,
        "title" => "Map 48: Final Business Capstone", "desc" => "The ultimate challenge. Integrate finance, HR, and strategy into a single project.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: An employee has consistently failed to meet KPIs despite warnings. What is the BEST administrative action?", "options" => ["Fire them immediately", "Initiate a formal Disciplinary Procedure and performance review", "Ignore it and hope they improve", "Tell their coworkers"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: Your department has gone over budget for three months. What is the first priority?", "options" => ["Ask for more money", "Conduct a financial audit and review expenses against the budget", "Fire an employee", "Stop all work"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: A major client is unhappy with the service they received. What do you use to track this?", "options" => ["A notebook", "CRM Software (History and Ticket status)", "A calendar", "An email draft"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You need to plan a 100-person regional conference in 6 months. What tool do you use first?", "options" => ["A menu for lunch", "A Project Management Gantt Chart and Milestone schedule", "An attendee list", "A map of the venue"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: A new data law is passed. What is the department's first step?", "options" => ["Wait for a fine", "Review compliance and update POPIA/Data Privacy policies", "Tell the IT team to handle it alone", "Close the business"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: Two team members have a personality clash affecting the project. What should the manager do?", "options" => ["Ignore it", "Facilitate a Conflict Resolution meeting", "Fire both", "Tell them to grow up"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: The CEO wants a 50-page report summarized in 5 minutes. What do you provide?", "options" => ["A table of contents", "An Executive Summary with key metrics", "The first five pages", "A list of employee names"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You are hiring for a new role. Where do you find the required skills?", "options" => ["A payroll report", "The Job Description", "A mission statement", "A marketing flyer"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: A supplier has sent an incorrect invoice. What is the administrative process?", "options" => ["Pay it anyway", "Verify against the PO (Purchase Order) and request a credit note or correction", "Throw it away", "Call the bank"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You need to store vital contracts safely for 10 years. What do you use?", "options" => ["A desk drawer", "Secure digital archiving with redundancy and offsite backups", "A paper folder in a box", "An email folder"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: The company wants to launch a new product. What analysis should be done first?", "options" => ["A SWOT analysis and market research", "Hiring 10 new people", "Buying an advertisement", "Ordering inventory"], "ans" => 0, "xp" => 280],
                ["q" => "SCENARIO: You step into a meeting and realize no one is taking notes. What do you do?", "options" => ["Ignore it", "Appoint a note-taker to produce formal Meeting Minutes", "Record it on your phone secretly", "Try to remember everything later"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: An employee reports a supervisor for unethical behavior. What procedure is followed?", "options" => ["A marketing plan", "The Whistleblower policy and internal ethics investigation", "Firing the employee", "Telling the supervisor"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: A project is facing scope creep. How do you handle it?", "options" => ["Do the extra work for free", "Formally review the project scope and budget with stakeholders", "Quit the project", "Ignore the extra tasks"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You want to measure the success of a new strategy. What do you check?", "options" => ["The company's social media", "Key Performance Indicators (KPIs) against set targets", "The opinions of the staff", "The total sales only"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are delegating a major project. What is essential to provide?", "options" => ["A list of items to buy", "Clear objectives, authority, and resources", "A salary increase", "A desk in a nice office"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "MASTER FINAL: What is the primary focus of an effective Business Administrator?", "options" => ["Making everyone happy", "Operational efficiency, strategic alignment, and organizational sustainability", "Buying the best computers", "Having the largest office"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: In high-level management, what determines the success of a strategic plan?", "options" => ["The number of pages in the plan", "Proper implementation, monitoring, and flexibility to change", "Having a lot of money", "Hiring a famous CEO"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: Why is a diverse and inclusive workplace beneficial for business?", "options" => ["It's just a trend", "It brings different perspectives, improves innovation, and enhances problem-solving", "It lowers the payroll", "It looks good in advertisements only"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: What is the 'Triple Bottom Line' in modern business?", "options" => ["Profit, Profit, and more Profit", "People, Planet, and Profit", "Sales, Marketing, and IT", "Money, Power, and Fame"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "EXAM: How do HR, Finance, and Operations interact in an organization?", "options" => ["They are separate entities", "They work interdependently; Operations produces, Finance manages money, and HR manages people", "They compete for the CEO's attention", "They only interact during office parties"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the most critical factor for an organization's long-term survival?", "options" => ["Having a lot of cash", "The ability to adapt to changes in the market and external environment", "Having the best marketing", "Having the most employees"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the value of 'Business Intelligence' (BI)?", "options" => ["Knowing your competitors' secrets", "Using data analysis to provide actionable insights for decision-making", "Having a high IQ", "Buying a new computer"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the hallmark of a great leader?", "options" => ["Being very loud and aggressive", "Inspiring others, setting a clear vision, and enabling the team to succeed", "Making all the decisions alone", "Having a very high salary"], "ans" => 1, "xp" => 600]
            ]]
        ]
    ]
];

// ==========================================================
// COMMUNICATION STUDIES & LANGUAGE CURRICULUM (Maps 49-60)
// ==========================================================
$comm_maps = [
    // CATEGORY 1: COMMUNICATION FOUNDATIONS (Maps 49-52)
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 1,
        "title" => "Map 49: The Communication Process", "desc" => "Master the mechanics of how information is encoded, transmitted, and decoded.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "In the communication model, what is 'Encoding'?", "options" => ["Receiving a message", "Converting thoughts into a communicable message", "The background noise", "The medium used"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Decoding'?", "options" => ["Sending an email", "Interpreting and assigning meaning to a received message", "Creating a code", "Ignoring the sender"], "ans" => 1, "xp" => 150],
                ["q" => "Which element refers to the pathway through which a message travels?", "options" => ["Noise", "Channel", "Context", "Feedback"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Feedback' in communication?", "options" => ["The initial message", "The receiver's response to the sender", "A type of interference", "The volume of speech"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'External Noise'?", "options" => ["The sender's thoughts", "Physical sounds or distractions in the environment", "A misunderstanding of words", "A bad mood"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Physiological Noise'?", "options" => ["Traffic sounds", "Biological factors like illness or hearing loss that interfere with communication", "Prejudices or bias", "Using difficult jargon"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Psychological Noise'?", "options" => ["A loud fan", "Mental interference like stress, anxiety, or internal bias", "A broken phone line", "A foreign language"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Semantic Noise'?", "options" => ["Loud music", "Misunderstanding caused by different meanings assigned to words (e.g., jargon)", "Hunger", "Poor Wi-Fi"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Which communication model describes communication as a one-way process?", "options" => ["Transactional Model", "Linear Model", "Interactional Model", "Circular Model"], "ans" => 1, "xp" => 210],
                ["q" => "Which model views communication as simultaneous, where we are both sender and receiver at once?", "options" => ["Linear Model", "Transactional Model", "Transmission Model", "Static Model"], "ans" => 1, "xp" => 210],
                ["q" => "What does 'Context' refer to in communication?", "options" => ["The words used", "The physical, social, or chronological environment where communication occurs", "The speed of talking", "The volume"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Intrapersonal Communication'?", "options" => ["Talking to a group", "Communication with oneself (internal thought)", "Talking to a boss", "Sending a letter"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Interpersonal Communication'?", "options" => ["Public speaking", "Direct communication between two people", "A news broadcast", "Reading a book"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Dyadic Communication'?", "options" => ["Talking to 10 people", "A communication exchange between exactly two people", "Talking to yourself", "Mass media"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Mass Communication'?", "options" => ["A private secret", "Communicating to a large, anonymous audience through a medium (e.g., TV)", "A text message", "A job interview"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Group Communication'?", "options" => ["Talking to yourself", "Communication among 3 or more people focused on a common goal", "A one-on-one meeting", "A billboard"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What does the term 'Field of Experience' refer to in communication?", "options" => ["A literal field", "The sum of a person's culture, experiences, and heredity brought to an interaction", "Professional job skills", "The distance between people"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Asynchronous Communication'?", "options" => ["Real-time talking", "Communication where there is a time gap between sending and receiving (e.g., Email)", "A face-to-face meeting", "Video calls"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Synchronous Communication'?", "options" => ["Sending a letter", "Communication that occurs in real-time (e.g., Phone call, Instant Message)", "An old newspaper", "A billboard"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Meta-communication'?", "options" => ["Communication about communication", "Communication about robots", "A very fast message", "Communication without words"], "ans" => 0, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Communication Competence'?", "options" => ["Being the loudest", "The ability to choose a communication behavior that is both appropriate and effective", "Knowing all languages", "Never making a mistake"], "ans" => 1, "xp" => 400],
                ["q" => "Which principle suggests that communication cannot be taken back?", "options" => ["Communication is Transactional", "Communication is Irreversible", "Communication is Intentional", "Communication is Linear"], "ans" => 1, "xp" => 400],
                ["q" => "What does it mean that communication is 'Unrepeatable'?", "options" => ["You can never say the same word", "The exact same interaction cannot happen twice because context changes", "You shouldn't repeat yourself", "The receiver always forgets"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cognitive Complexity' in communication?", "options" => ["Being confused", "The ability to construct a variety of different frameworks for viewing an issue", "Having a high IQ", "Using long words"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 1,
        "title" => "Map 50: Verbal & Non-Verbal Dynamics", "desc" => "Understand the power of body language, tone, and vocalics.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Non-verbal Communication'?", "options" => ["Writing an email", "Messages expressed by other than linguistic means (body, distance, eye contact)", "Using sign language", "A radio broadcast"], "ans" => 1, "xp" => 150],
                ["q" => "What percentage of communication is often attributed to non-verbal cues?", "options" => ["10%", "Between 65% and 93%", "Exactly 50%", "Less than 5%"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Kinesics'?", "options" => ["The study of touch", "The study of body movement and gestures", "The study of time", "The study of smell"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Proxemics'?", "options" => ["The study of eye contact", "The study of how people use space and distance", "The study of clothing", "The study of handshakes"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Paralanguage' (Vocalics)?", "options" => ["Learning a second language", "Non-linguistic vocal cues like pitch, rate, and volume", "Writing in code", "Sign language"], "ans" => 1, "xp" => 180],
                ["q" => "What are 'Haptics'?", "options" => ["Visual aids", "The study of communication through touch", "The study of facial expressions", "The study of listening"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Chronemics'?", "options" => ["The study of colors", "The study of how people use and structure time", "The study of technology", "The study of aging"], "ans" => 1, "xp" => 180],
                ["q" => "What are 'Oculesics'?", "options" => ["The study of ears", "The study of eye behavior (eye contact, pupils)", "The study of smell", "The study of posture"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "Which non-verbal gesture has different meanings across cultures (e.g., 'thumbs up')?", "options" => ["Universal Expression", "Emblem", "Illustrator", "Adaptor"], "ans" => 1, "xp" => 210],
                ["q" => "What is an 'Illustrator' in non-verbal communication?", "options" => ["A person who draws", "Gestures that accompany and complement verbal messages (e.g., pointing)", "Scratching your head", "A nervous habit"], "ans" => 1, "xp" => 210],
                ["q" => "What is an 'Adaptor'?", "options" => ["A type of power plug", "Unconscious bodily movements in response to an environment (e.g., fidgeting)", "A professional handshake", "A smile"], "ans" => 1, "xp" => 210],
                ["q" => "What are 'Artifacts' in communication?", "options" => ["Old fossils", "Personal objects and clothing that communicate something about us", "The words we use", "Electronic devices"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Emotional Contagion'?", "options" => ["A physical virus", "The process by which emotions are transferred from one person to another", "Hiding your feelings", "A type of sadness"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Micro-expression'?", "options" => ["A long speech", "Brief, involuntary facial expressions that reveal true emotions", "A small emoji", "A quiet whisper"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Congruence' in communication?", "options" => ["Being different", "When verbal and non-verbal messages match", "A mathematical equation", "Speaking loudly"], "ans" => 1, "xp" => 250],
                ["q" => "What is the 'Social Distance' zone (Proxemics)?", "options" => ["0-18 inches", "4-12 feet", "12-25 feet", "Over 100 feet"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the 'Intimate Distance' zone?", "options" => ["4-12 feet", "0-18 inches", "12-25 feet", "Everywhere"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Deception Detection'?", "options" => ["Finding a lost key", "The ability to identify when someone is lying based on non-verbal cues", "Using a lie detector machine", "Reading a person's mind"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Non-verbal Leakage'?", "options" => ["Crying", "When non-verbal cues betray a message different from the verbal one", "A leaky faucet", "Losing your voice"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Posture' communication?", "options" => ["The way you type", "The way one holds one's body (communicates confidence, interest, etc.)", "The color of your skin", "The speed of your walk"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "How does 'Environment' communicate?", "options" => ["It doesn't", "The layout, lighting, and decor of a room can signal formality or comfort", "Through weather reports", "By using a loudspeaker"], "ans" => 1, "xp" => 400],
                ["q" => "Which non-verbal cue is most essential for establishing trust in Western cultures?", "options" => ["Crossed arms", "Sustained eye contact", "Looking at the floor", "A fast speaking rate"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Territoriality'?", "options" => ["Owning a pet", "A stationary area we claim as our own", "A country", "A type of travel"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Mirroring'?", "options" => ["Looking in a mirror", "Subtly mimicking another person's non-verbal behavior to build rapport", "Being rude", "Talking at the same time"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 1,
        "title" => "Map 51: Active Listening & Feedback", "desc" => "Go beyond hearing into deep processing and supportive responding.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the difference between 'Hearing' and 'Listening'?", "options" => ["None", "Hearing is physiological; Listening is psychological/intentional", "Listening is for music only", "Hearing is more important"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Active Listening'?", "options" => ["Listening while running", "Fully concentrating and responding to the speaker", "Listening in the background", "Ignoring the speaker"], "ans" => 1, "xp" => 150],
                ["q" => "What is the first stage of the listening process?", "options" => ["Responding", "Attending/Receiving", "Remembering", "Evaluating"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Mindful Listening'?", "options" => ["Being distracted", "Giving careful and thoughtful attention to the messages we receive", "Listening to a podcast", "Listening while sleeping"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Pseudolistening'?", "options" => ["Listening to fake news", "An imitation of true listening (pretending to listen)", "Listening very fast", "Listening to music"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Stage Hogging'?", "options" => ["Acting in a play", "Turning the conversation to oneself instead of listening", "A type of agriculture", "Being a loud speaker"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Selective Listening'?", "options" => ["Listening to everything", "Only responding to the parts of a speaker's remarks that interest you", "Listening to the radio", "Choosing a new song"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Insulated Listening'?", "options" => ["Listening in a cold room", "Avoiding or failing to acknowledge specific topics", "Wearing headphones", "A type of quiet talk"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Defensive Listening'?", "options" => ["Listening for a goal", "Taking innocent comments as personal attacks", "Listening to a debate", "Being very quiet"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Ambushing' in listening?", "options" => ["A surprise party", "Listening carefully only to collect information to attack the speaker later", "A hidden microphone", "Listening while hiding"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Paraphrasing' as a listening response?", "options" => ["Repeating exactly", "Restating the speaker's message in your own words to check understanding", "Asking a new question", "Staying silent"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Empathetic Listening'?", "options" => ["Listening for errors", "Listening to understand the speaker's perspective and feelings", "Listening for facts only", "A type of counseling"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Critical Listening'?", "options" => ["Listening for gossip", "Listening to evaluate the quality or merit of a message (e.g., a political speech)", "Listening to a movie", "Listening while angry"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Information Listening'?", "options" => ["Listening to music", "Listening to learn or understand new information", "Listening to a secret", "Listening for tone"], "ans" => 1, "xp" => 250],
                ["q" => "Which listening style focuses on relationships and emotional connection?", "options" => ["Task-oriented", "Relational", "Critical", "Analytical"], "ans" => 1, "xp" => 250],
                ["q" => "Which listening style focuses on efficiency and getting a job done?", "options" => ["Relational", "Task-oriented", "Empathetic", "Passive"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Supportive' response?", "options" => ["Disagreeing", "Expression of care, concern, or interest (e.g., 'I can see that was hard')", "Telling them what to do", "Ignoring the feeling"], "ans" => 1, "xp" => 300],
                ["q" => "What is an 'Evaluating' response?", "options" => ["Asking a question", "Judging the speaker's thoughts or behaviors (e.g., 'That was a bad idea')", "Staying silent", "Nodding"], "ans" => 1, "xp" => 300],
                ["q" => "What is the 'Understanding' stage of listening?", "options" => ["Just hearing sound", "The act of interpreting the message according to its context", "Remembering it later", "Evaluating the truth"], "ans" => 1, "xp" => 300],
                ["q" => "How can you improve 'Remembering' in the listening process?", "options" => ["Taking notes or repeating information", "Closing your eyes", "Talking over the speaker", "Leaving the room"], "ans" => 0, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Prompting' in listening?", "options" => ["Interrupting", "Using silence and brief statements to encourage the speaker to continue", "Telling a joke", "Ending the talk"], "ans" => 1, "xp" => 400],
                ["q" => "What is an 'Analyzing' response?", "options" => ["Agreeing", "Offering an interpretation of a speaker's message (e.g., 'Maybe you feel that way because...')", "Asking for a fact", "Crying"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Advising' in a listening context?", "options" => ["Listening quietly", "Offering a solution or suggestion (e.g., 'You should...')", "Asking a question", "Telling a story"], "ans" => 1, "xp" => 400],
                ["q" => "What is the biggest barrier to listening in the digital age?", "options" => ["Paper books", "Information Overload and distraction", "Too much silence", "Short pencils"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 1,
        "title" => "Map 52: Digital Discourse & Ethics", "desc" => "Navigate social media, online etiquette, and the ethics of digital identity.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Netiquette'?", "options" => ["A fish net", "The correct way to behave on the internet", "A computer virus", "An internet service provider"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Flaming' in online discourse?", "options" => ["A fire emoji", "Sending angry or insulting messages over the internet", "Posting a photo", "Closing a tab"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Cyberbullying'?", "options" => ["Working online", "The use of electronic communication to bully a person", "Playing a game", "Watching a video"], "ans" => 1, "xp" => 150],
                ["q" => "What does it mean if a post is 'Public'?", "options" => ["Only friends can see it", "Anyone on the internet can see it", "It is hidden", "It is deleted"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Digital Citizenship'?", "options" => ["Living in a city", "The responsible and respectful use of technology", "Buying a new phone", "Having a lot of followers"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Phishing'?", "options" => ["Catching a fish", "A scam where people try to steal personal info via email/links", "A type of software", "A social network"], "ans" => 1, "xp" => 180],
                ["q" => "What is an 'Internet Troll'?", "options" => ["A monster", "Someone who purposely posts provocative or off-topic content to upset others", "A helpful user", "A computer repairman"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Doxxing'?", "options" => ["Fixing a PC", "Publishing private info about someone with malicious intent", "Writing a document", "Sending a DM"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Digital Footprint'?", "options" => ["A footprint in sand", "The trail of data you leave behind on the internet", "The size of your mouse", "A virus"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Information Overload'?", "options" => ["A fast computer", "The state of having too much information to process", "A large hard drive", "A new update"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Misinformation'?", "options" => ["A secret", "False or inaccurate information, regardless of intent", "A type of email", "A loud noise"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Disinformation'?", "options" => ["A factual report", "False information deliberately spread to deceive others", "A typo", "A deleted post"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is an 'Echo Chamber'?", "options" => ["A loud room", "An environment where a person only encounters information that reflects their own beliefs", "A recording studio", "A computer lab"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Online Anonymity'?", "options" => ["Using your real name", "The ability to interact online without revealing your identity", "Being very famous", "Deleting your account"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Slacktivism'?", "options" => ["Hard work", "Actions taken online (like sharing a post) that require little effort or commitment to a cause", "Professional activism", "Being lazy"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Terms of Service' (ToS)?", "options" => ["A greeting", "The legal agreement between a user and an app/service", "A phone bill", "A software update"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Two-Factor Authentication' (2FA)?", "options" => ["Two people talking", "A security process requiring two different methods to verify identity", "Typing twice", "A long password"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Bot' in social media?", "options" => ["A robot person", "An automated account controlled by software rather than a human", "A type of emoji", "A computer hardware"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Astroturfing'?", "options" => ["Planting grass", "Creating fake grassroots support for a person or cause online", "Buying followers", "A type of game"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Media Convergence'?", "options" => ["Media breaking up", "The merging of traditional media (like newspapers) with digital media and technology", "A new TV channel", "A group of reporters"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Algorithm' bias in social media?", "options" => ["A fast computer", "When computer rules systematically favor or ignore certain types of information/people", "A math error", "A type of virus"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Deepfake'?", "options" => ["A very deep lake", "AI-generated media that replaces one person's face or voice with another convincingly", "A secure password", "A hidden post"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cancel Culture'?", "options" => ["Stopping a show", "A modern form of ostracism where someone is thrust out of social or professional circles", "A type of religion", "Deleting an app"], "ans" => 1, "xp" => 400],
                ["q" => "What is the primary concern of 'Data Mining'?", "options" => ["Finding gold", "Companies collecting and analyzing user data for profit or influence", "Building a network", "Fixing a phone"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 2: LANGUAGE & LINGUISTICS (Maps 53-56)
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 2,
        "title" => "Map 53: Structure of Language", "desc" => "Master the rules of grammar, syntax, and phonology.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Linguistics'?", "options" => ["The study of history", "The scientific study of language and its structure", "Learning many languages", "Writing a book"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Phonology'?", "options" => ["The study of phones", "The study of speech sounds in a language", "The study of words", "The study of sentences"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Morphology'?", "options" => ["The study of shapes", "The study of how words are formed from smaller parts (morphemes)", "The study of grammar", "The study of meaning"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Syntax'?", "options" => ["A tax on language", "The arrangement of words to create well-formed sentences", "The spelling of words", "The tone of voice"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Semantics'?", "options" => ["The study of grammar", "The study of meaning in language", "The study of sounds", "The study of social context"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Pragmatics'?", "options" => ["The study of rules", "The study of language in its context of use", "The study of dictionaries", "The study of prefixes"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Morpheme'?", "options" => ["A large word", "The smallest unit of meaning in a language (e.g., 'un-', 'happi-')", "A type of sound", "A single letter"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Phoneme'?", "options" => ["A phone app", "The smallest unit of sound in a language", "A whole word", "A sentence"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Etymology'?", "options" => ["The study of insects", "The study of the history and origin of words", "The study of future language", "The study of poems"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Orthography'?", "options" => ["The study of rocks", "The conventional spelling system of a language", "The study of speech", "The study of meaning"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Lexicography'?", "options" => ["Taking photos", "The process of compiling and writing dictionaries", "The study of grammar", "A type of printing"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Discourse Analysis'?", "options" => ["Counting words", "The study of how language is used in texts and contexts", "Learning to type", "A type of code"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Dialect'?", "options" => ["A foreign language", "A particular form of a language specific to a region or group", "The official language", "Poor grammar"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Standard Language'?", "options" => ["A language with no rules", "The variety of language used in formal settings and education", "Slang", "A dead language"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Jargon'?", "options" => ["A new word", "Special words or expressions used by a particular profession", "A foreign accent", "Clear writing"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Slang'?", "options" => ["Formal language", "Informal words and phrases more common in speech than writing", "Scientific terms", "Old words"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Bilingualism'?", "options" => ["Talking to yourself", "The ability to speak two languages fluently", "Speaking very fast", "Knowing one language well"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Code-switching'?", "options" => ["Learning to code", "Alternating between two or more languages or varieties in conversation", "Changing your name", "Fixing a computer"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Language Acquisition'?", "options" => ["Buying a book", "The process by which humans learn a language", "Translating a text", "Deleting a language"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Creole' language?", "options" => ["A secret code", "A stable natural language developed from the mixing of parent languages", "A very old language", "A type of slang"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Prescriptive' grammar?", "options" => ["Following usage", "Rules on how a language SHOULD be used", "Describing how people talk", "A doctor's note"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Descriptive' linguistics?", "options" => ["Making new rules", "Describing how language is ACTUALLY used by people", "Correcting others", "Writing a poem"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cognitive Linguistics'?", "options" => ["Study of old text", "Study of the relationship between language and the mind", "Learning to read", "A type of sign language"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Universal Grammar'?", "options" => ["A book of rules", "The theory that all humans have an innate ability to learn language", "A language for the world", "A type of internet code"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 2,
        "title" => "Map 54: Semantics & Pragmatics", "desc" => "Explore the meaning of words and the intent behind their use.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Denotation'?", "options" => ["The emotional meaning", "The literal, dictionary definition of a word", "The opposite of a word", "The sound of a word"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Connotation'?", "options" => ["The dictionary meaning", "The emotional or cultural associations attached to a word", "The spelling", "A type of grammar"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Synonym'?", "options" => ["A word with opposite meaning", "A word with a similar meaning", "A word that sounds the same", "A very long word"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Antonym'?", "options" => ["A similar word", "A word with the opposite meaning", "A type of prefix", "A name for a person"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Ambiguity'?", "options" => ["Being very clear", "When a word or sentence has more than one possible meaning", "A spelling error", "A type of loud noise"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Metaphor'?", "options" => ["A literal fact", "A figure of speech comparing two things without using 'like' or 'as'", "A type of punctuation", "A long story"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Simile'?", "options" => ["A joke", "A comparison using 'like' or 'as'", "A direct lie", "A type of greeting"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Idiom'?", "options" => ["A spelling mistake", "A group of words whose meaning is not deducible from the individual words (e.g., 'kick the bucket')", "Formal language", "A type of accent"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Irony'?", "options" => ["Being serious", "Expressing meaning by using language that normally signifies the opposite", "A type of metal", "A very fast speech"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Sarcasm'?", "options" => ["Being kind", "The use of irony to mock or convey contempt", "A type of praise", "A foreign language"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Euphemism'?", "options" => ["A rude word", "A mild or indirect expression substituted for one considered harsh or blunt", "A technical term", "A type of question"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Hyperbole'?", "options" => ["Understating a fact", "Exaggerated statements or claims not meant to be taken literally", "A type of circle", "A quiet whisper"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Cooperation Principle' (Grice)?", "options" => ["Fighting with others", "The idea that people contribute what is required for a conversation to be successful", "Talking only about yourself", "Never speaking"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Maxim of Quality'?", "options" => ["Speaking a lot", "Be truthful; do not say what you believe to be false", "Being very fast", "Using long words"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Maxim of Quantity'?", "options" => ["Being truthful", "Be as informative as required, but no more", "Being polite", "Using jargon"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Maxim of Relevance'?", "options" => ["Talking about everything", "Be relevant to the topic at hand", "Being funny", "Asking many questions"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Speech Act'?", "options" => ["Acting in a play", "An utterance that has a performative function (e.g., promising, apologizing)", "A long speech", "A type of accent"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Illocutionary Force'?", "options" => ["A physical punch", "The intended effect of a speech act", "The volume of a voice", "A type of computer"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Politeness Theory'?", "options" => ["Being rude", "How we manage 'face' (public image) and social distance through language", "Having good manners only", "A type of grammar"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Face-threatening Act'?", "options" => ["Hitting someone", "An utterance that challenges or damages another person's public image", "Closing your eyes", "A type of smile"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Implicature'?", "options" => ["A literal statement", "What is suggested in an utterance even though not expressed literally", "A spelling error", "A type of accent"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Presupposition'?", "options" => ["An ending", "Implicit assumptions made by a speaker before an utterance", "A dictionary definition", "A question"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Deixis'?", "options" => ["A type of sound", "Words whose meaning depends on context (e.g., 'here', 'there', 'you')", "A whole book", "A sign language"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Entailment'?", "options" => ["A physical tail", "A relationship between sentences where if A is true, B must also be true", "A type of greeting", "A story ending"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 2,
        "title" => "Map 55: Socio-Linguistics & Identity", "desc" => "Examine how language intersects with society, class, and culture.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Sociolinguistics'?", "options" => ["Study of old books", "The study of how language is affected by social factors (class, region, gender)", "Learning to speak", "The study of animal sounds"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Accent'?", "options" => ["The words you choose", "The distinctive way in which a group of people pronounce a language", "Good grammar", "A foreign name"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Social Prestige' in language?", "options" => ["Being rich", "The high value placed on certain varieties of a language", "Knowing many words", "Speaking loudly"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Language Taboo'?", "options" => ["A new language", "Words or topics considered offensive or forbidden in a society", "A type of dictionary", "A professional term"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Diglossia'?", "options" => ["Having no voice", "When two varieties of a language are used by a single community (e.g., formal vs. casual)", "Speaking two different languages", "A type of throat infection"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Linguistic Relativism'?", "options" => ["Language has no rules", "The idea that language influences the way its speakers think", "Everything is the same", "Only one language is true"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Lingua Franca'?", "options" => ["A language from France", "A language used as a common bridge between people who speak different native languages", "A secret language", "A dead language"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Language Death'?", "options" => ["A scary story", "When a language loses its last native speakers", "Forgetting a word", "A silent room"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Overt Prestige'?", "options" => ["Hiding your accent", "Using a standard language variety to sound educated or high-status", "Using slang with friends", "Speaking very softly"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Covert Prestige'?", "options" => ["Being famous", "Using non-standard language to signal belonging or 'coolness' within a subculture", "Writing a formal letter", "Learning a new language"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Idiolect'?", "options" => ["A type of logic", "An individual's unique way of speaking", "A whole group's dialect", "A foreign language"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Ethno-linguistics'?", "options" => ["Study of space", "The study of the relationship between language and culture", "The study of numbers", "Learning to read"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Language Vitality'?", "options" => ["How fast people talk", "The likelihood that a language will continue to be used in the future", "A type of health food", "The volume of a language"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Language Planning'?", "options" => ["Writing a diary", "Deliberate efforts to influence the function or structure of a language", "Organizing a speech", "A school lesson"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Gender-neutral' language?", "options" => ["Language for men only", "Language that avoids bias towards a particular sex or social gender", "Language for women only", "No language"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Linguistic Discrimination'?", "options" => ["Hiring a translator", "Unfair treatment of people based on their use of language or accent", "Learning a second language", "Correcting a typo"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Pidgin'?", "options" => ["A type of bird", "A simplified language used for communication between people with different languages", "A professional dialect", "A very old text"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Isogloss'?", "options" => ["A type of glass", "A line on a map marking the boundary of a linguistic feature", "A dictionary entry", "A type of grammar"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Linguistic Imperialism'?", "options" => ["Being polite", "The dominance of one language over others, often through political or economic power", "Traveling for work", "Learning English"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Register' in sociolinguistics?", "options" => ["A cash machine", "The variety of language used for a particular purpose or social setting (e.g., formal vs. casual)", "A list of names", "A type of font"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Accommodation Theory'?", "options" => ["Living in a hotel", "The idea that we adjust our speech to be more like (or different from) our conversation partner", "Buying a house", "Ignoring your friend"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Convergent' speech?", "options" => ["Talking to yourself", "Adjusting speech to sound more like the listener to gain approval", "Talking faster", "Using loud volume"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Divergent' speech?", "options" => ["Agreeing with everyone", "Emphasizing speech differences to signal social distance or unique identity", "Repeating a word", "Staying silent"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Linguistic Insecurity'?", "options" => ["A virus", "Feelings of anxiety or self-consciousness about one's accent or language use", "A secure password", "Being very confident"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 2,
        "title" => "Map 56: Professional Writing & Rhetoric", "desc" => "Master the art of persuasion and formal written communication.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Rhetoric'?", "options" => ["A type of bird", "The art of effective or persuasive speaking or writing", "Good handwriting", "Counting numbers"], "ans" => 1, "xp" => 150],
                ["q" => "Who are the 'Classical' masters of rhetoric?", "options" => ["The Romans", "The Greeks (e.g., Aristotle)", "The British", "The Americans"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Ethos'?", "options" => ["Emotional appeal", "Appeal to credibility and character", "Logical proof", "A type of soup"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Pathos'?", "options" => ["A straight road", "Appeal to emotion", "Appeal to logic", "Appeal to time"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Logos'?", "options" => ["A company logo", "Appeal to logic and reason", "Appeal to authority", "A type of speech"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Rhetorical Question'?", "options" => ["A very hard question", "A question asked to make a point rather than to get an answer", "A question with no answer", "A secret question"], "ans" => 1, "xp" => 180],
                ["q" => "What is the 'Active Voice'?", "options" => ["Speaking loudly", "When the subject of the sentence performs the action (e.g., 'The chef cooked')", "Whispering", "The target of the action"], "ans" => 1, "xp" => 180],
                ["q" => "What is the 'Passive Voice'?", "options" => ["Being angry", "When the subject of the sentence receives the action (e.g., 'The meal was cooked')", "Speaking fast", "A type of font"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Clarity' in professional writing?", "options" => ["Using long words", "Ensuring the message is easy to understand and unambiguous", "Being very vague", "Writing in code"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Conciseness'?", "options" => ["Writing 20 pages", "Conveying information briefly without sacrificing clarity", "Using complicated jargon", "Repeating yourself"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Tone' in a document?", "options" => ["The font color", "The writer's attitude toward the reader or the subject matter", "The paper quality", "The price of the book"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Style' in writing?", "options" => ["The way you dress", "The unique way a writer uses language to express themselves", "A type of computer", "A marketing plan"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Plagiarism'?", "options" => ["Helping a friend", "Using someone else's work or ideas without credit", "Learning to read", "Correcting a typo"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Citation'?", "options" => ["A type of ticket", "A reference to a source of information used in a document", "A spelling error", "A type of sign language"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Thesis Statement'?", "options" => ["The final page", "The main point or argument of a piece of writing", "The title", "A list of sources"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Audience Analysis'?", "options" => ["Watching a movie", "Understanding the needs and background of your readers", "Talking to yourself", "A performance review"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Logical Fallacy'?", "options" => ["A true fact", "An error in reasoning that invalidates an argument", "A type of math", "A correct answer"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Ad Hominem'?", "options" => ["Attacking the topic", "Attacking the person instead of the argument", "Being very polite", "Using logic"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Slippery Slope'?", "options" => ["A winter sport", "Arguing that one small step will inevitably lead to a chain of negative events", "A true conclusion", "A technical term"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Parallelism' in writing?", "options" => ["Writing in two lines", "Using consistent grammatical structures for related ideas", "Being very random", "Using two languages"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Kairos'?", "options" => ["A name", "The rhetorical concept of 'timeliness' or the opportune moment", "A type of logic", "A type of emotion"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Propaganda'?", "options" => ["Factual news", "Information, often biased or misleading, used to promote a political cause", "A type of advertisement", "A dictionary"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Spin' in communication?", "options" => ["Rotating a ball", "The strategic biased interpretation of an event to influence public opinion", "Telling the truth", "A type of dance"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Syntactic Variety'?", "options" => ["Using the same sentence", "Varying sentence length and structure to keep writing engaging", "Varying the font", "Varying the language"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 3: ADVANCED APPLICATIONS (Maps 57-60)
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 3,
        "title" => "Map 57: Intercultural Communication", "desc" => "Bridge the gap between cultures, values, and global identities.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Culture'?", "options" => ["Just the food", "The shared beliefs, values, and practices of a group", "The government", "A type of music"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Intercultural Communication'?", "options" => ["Talking to computers", "Communication between people from different cultural backgrounds", "Talking to yourself", "A phone call"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Ethnocentrism'?", "options" => ["Loving all cultures", "Judging other cultures by the standards of your own (thinking yours is superior)", "Learning a language", "Traveling"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Cultural Relativism'?", "options" => ["Hating your culture", "The practice of understanding a culture on its own terms without judgment", "A type of science", "Moving to a new country"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'High-context' culture?", "options" => ["Communication is literal", "Communication relies heavily on non-verbal cues and relationships (e.g., Japan)", "Loud communication", "A computer network"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Low-context' culture?", "options" => ["Communication is hidden", "Communication is direct, explicit, and literal (e.g., USA, Germany)", "No communication", "Using many codes"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Individualism'?", "options" => ["Working in groups", "Placing high value on personal goals and independence", "Loving your family only", "Being lonely"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Collectivism'?", "options" => ["Working alone", "Placing high value on group harmony and interdependence", "A collection of stamps", "Being rude"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Power Distance'?", "options" => ["The distance of a race", "The extent to which less powerful members accept unequal power distribution", "The power of a battery", "A type of accent"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Uncertainty Avoidance'?", "options" => ["Taking risks", "The degree to which people feel threatened by unknown or ambiguous situations", "Running away", "A type of law"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Monochronic' time?", "options" => ["Time for everything", "Focusing on doing one thing at a time and strict schedules", "Doing many things", "A type of clock"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Polychronic' time?", "options" => ["No time", "A more flexible approach to time where multiple things happen at once", "One single minute", "A fast computer"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Culture Shock'?", "options" => ["An electrical shock", "Disorientation felt when experiencing an unfamiliar way of life", "Liking a new food", "Learning a word"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Acculturation'?", "options" => ["Losing your culture", "The process of adopting the traits of another culture while keeping your own", "Deleting a language", "Staying at home"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Assimilation'?", "options" => ["Keeping your habits", "The process of fully integrating into a new culture and losing your original cultural identity", "A type of math", "Traveling for fun"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Stereotyping'?", "options" => ["Being fair", "Oversimplified generalizations about a group of people", "Taking a photo", "A type of printing"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Prejudice'?", "options" => ["A kind thought", "A preconceived opinion that is not based on reason or actual experience", "A true fact", "A type of logic"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Intercultural Empathy'?", "options" => ["Feeling sorry for others", "The ability to perceive the world as it is perceived by someone from a different culture", "Learning to read", "A type of food"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Third Culture'?", "options" => ["A bad culture", "A hybrid culture formed when two people from different backgrounds create a common ground", "A third language", "A space colony"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Global Citizenship'?", "options" => ["Owning the world", "Identifying as a member of the global community rather than just a nation", "Traveling for free", "Knowing every map"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Biculturalism'?", "options" => ["Speaking two words", "Being proficient in the traditions and values of two different cultures", "Having two names", "Moving twice"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cross-cultural' training?", "options" => ["A sport", "Education to help people work effectively with those from other cultures", "Learning to type", "A medical test"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Xenophobia'?", "options" => ["Love of travel", "Dislike or prejudice against people from other countries", "A type of phobia of heights", "A new computer brand"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cultural Competence'?", "options" => ["Being the best", "The ability to interact effectively with people of different cultures", "Knowing all history", "Being a tour guide"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 3,
        "title" => "Map 58: Public Speaking & Presentation", "desc" => "Command the room, manage anxiety, and deliver powerful speeches.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Public Speaking'?", "options" => ["Talking to yourself", "Giving a speech or presentation to a live audience", "Writing a letter", "Listening to radio"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Glossophobia'?", "options" => ["Fear of glass", "The fear of public speaking", "Fear of water", "Fear of heights"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Introduction' of a speech?", "options" => ["The goodbye", "The beginning that grabs attention and states the main goal", "The list of facts", "A break for lunch"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Extemporaneous' speech?", "options" => ["A memorized script", "A carefully prepared and practiced speech delivered in a conversational manner", "Reading from a book", "Talking without any thought"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Impromptu' speaking?", "options" => ["Long preparation", "Speaking without any advanced preparation", "Reading a poem", "A recorded video"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Transition' in a speech?", "options" => ["The end", "Words or phrases that connect one idea to the next", "The main point", "A joke"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Visual Aid'?", "options" => ["Hearing music", "Objects, slides, or images used to support a speech", "A type of glasses", "A quiet voice"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Eye Contact' importance?", "options" => ["To see the clock", "To build trust and connection with the audience", "To look at your notes", "To scare people"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Inflection' in your voice?", "options" => ["Speaking flat", "The modulation of pitch and tone to convey meaning and emotion", "A type of accent", "Being very quiet"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Projection'?", "options" => ["Hiding your voice", "Using your voice forcefully so it can be heard by everyone", "A movie screen", "Singing a song"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Enunciation'?", "options" => ["Mumbling", "Speaking each word clearly and distinctly", "Using slang", "A type of grammar"], "ans" => 1, "xp" => 210],
                ["q" => "What is the 'Conclusion' of a speech?", "options" => ["The middle", "The end that summarizes the points and provides a closing thought", "The first joke", "A list of sources"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Audience Disposition'?", "options" => ["The size of the room", "The audience's attitude or emotional state toward the topic", "The number of chairs", "The price of tickets"], "ans" => 1, "xp" => 250],
                ["q" => "What is an 'Attention-Getter'?", "options" => ["A loud scream", "A hook used at the start (a quote, story, or fact) to engage listeners", "The first slide", "A type of hat"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Persuasive' speaking?", "options" => ["Telling a story", "Speaking to change the audience's mind or motivate them to action", "Giving facts only", "A medical lecture"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Informative' speaking?", "options" => ["Selling a product", "Speaking to share knowledge or explain a topic", "Starting a fight", "Telling a lie"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Stage Presence'?", "options" => ["Being on a stage", "The energy and confidence a speaker projects to the audience", "The clothes you wear only", "Standing very still"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Call to Action'?", "options" => ["A phone call", "A specific request for the audience to DO something after the speech", "The title", "A thank you"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Articulation'?", "options" => ["Having a fast pulse", "The physical production of specific speech sounds", "A type of logic", "The length of a sentence"], "ans" => 1, "xp" => 300],
                ["q" => "How should you handle 'Q&A'?", "options" => ["Ignore questions", "Listen fully, repeat the question, and answer honestly and briefly", "Argue with the audience", "Leave the room"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Monotone'?", "options" => ["A loud voice", "A voice that does not vary in pitch or expression", "A beautiful song", "A clear speech"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Pacing'?", "options" => ["Running", "The speed at which you deliver your speech", "The number of steps on a stage", "The volume of your voice"], "ans" => 1, "xp" => 400],
                ["q" => "What are 'Filler Words'?", "options" => ["Strong adjectives", "Words like 'um', 'ah', and 'like' that fill silence and distract", "Important technical terms", "Foreign words"], "ans" => 1, "xp" => 400],
                ["q" => "What is the best way to manage speech anxiety?", "options" => ["Stop talking forever", "Preparation, practice, and controlled breathing", "Drinking 10 coffees", "Avoiding the audience"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 3,
        "title" => "Map 59: Media Literacy & Analysis", "desc" => "Critically evaluate the information you consume from TV, News, and Web.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Media Literacy'?", "options" => ["Knowing how to use a mouse", "The ability to access, analyze, evaluate, and create media", "Watching TV all day", "Having a lot of followers"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Mass Media'?", "options" => ["Private talk", "Tools that reach a very large audience (News, Radio, Web)", "A type of exercise", "A math equation"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Media Bias'?", "options" => ["A balanced report", "The perceived bias of journalists and news producers in selection of many events and stories", "A type of radio", "A movie review"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Gatekeeping' in media?", "options" => ["Opening a gate", "The process through which information is filtered for dissemination (deciding what news to show)", "A security guard", "A type of login"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Agenda-setting Theory'?", "options" => ["Planning a party", "The idea that media doesn't tell us what to think, but what to think ABOUT", "A type of school lesson", "A marketing flyer"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Framing' in a news story?", "options" => ["Putting a photo in a frame", "How information is presented and organized to influence the reader's perspective", "A type of crime", "A construction job"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Clickbait'?", "options" => ["A type of fish", "Headlines designed to attract clicks by using exaggeration or suspense", "A factual news title", "A type of software"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Sensationalism'?", "options" => ["Being very calm", "The use of exciting or shocking stories at the expense of accuracy to provoke interest", "A type of music", "A scientific report"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Media Consolidation'?", "options" => ["Deleting media", "When a few large companies own most media outlets", "A type of file backup", "A new TV channel"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Citizen Journalism'?", "options" => ["A professional reporter", "Ordinary people reporting news via social media and blogs", "A government news", "Reading the paper"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Confirmation Bias'?", "options" => ["Being balanced", "The tendency to search for and believe info that confirms your existing beliefs", "A type of math", "A correct answer"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Filter Bubble'?", "options" => ["A soap bubble", "A state of isolation that results from personalized searches and algorithms", "A clean computer", "A type of camera"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Primary Source'?", "options" => ["A textbook", "First-hand evidence of an event (e.g., eyewitness, original document)", "A summary", "A Wikipedia page"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Secondary Source'?", "options" => ["An original letter", "An interpretation or analysis of a primary source (e.g., a textbook, documentary)", "An eyewitness", "A physical object"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Fact-checking'?", "options" => ["Writing a story", "Verifying the accuracy of claims made in media", "Adding photos", "Changing the title"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Media Representation'?", "options" => ["A lawyer", "How groups of people are depicted in the media", "A type of advertisement", "The price of media"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Propaganda'?", "options" => ["Objective facts", "Biased information used to promote a particular political point of view", "A type of music", "A dictionary"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Fake News'?", "options" => ["A new newspaper", "Information that is intentionally false or misleading", "A typo", "A gossip column"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Native Advertising'?", "options" => ["A TV commercial", "Ads that look like the regular content of the website (e.g., sponsored articles)", "A large billboard", "A free sample"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Critical Thinking' in media?", "options" => ["Hating everything", "Evaluating information with a questioning and logical mindset", "Believing everything", "Fast reading"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the 'Digital Divide'?", "options" => ["A mathematical term", "The gap between those who have access to technology and those who do not", "A broken phone", "A new computer brand"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Information Privacy'?", "options" => ["Sharing everything", "The right to control how your personal data is collected and used online", "A type of password", "A hidden post"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'User-Generated Content'?", "options" => ["A movie", "Content like reviews, posts, and videos created by the users of a service", "A professional news", "A software code"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Viral' content?", "options" => ["A computer virus", "Content that spreads rapidly and widely across the internet", "A slow website", "A deleted post"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $comm_course_id, "category_id" => 3,
        "title" => "Map 60: Capstone: Master of Discourse", "desc" => "The ultimate challenge. Integrate linguistics, rhetoric, and digital ethics.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: You are in a meeting where two coworkers from different cultures disagree on a deadline. What should you check first?", "options" => ["The font used in the email", "High-context vs Low-context time orientations (Monochronic vs Polychronic)", "Who is louder", "The office air conditioning"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: A politician gives a speech focused on their years of service and integrity. Which rhetorical appeal are they using?", "options" => ["Logos", "Ethos", "Pathos", "Kairos"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You see a sensational news headline that makes you very angry. What is the BEST first media literacy step?", "options" => ["Share it immediately", "Fact-check the source and check for 'emotional contagion' or clickbait framing", "Comment with an insult", "Delete your account"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: A friend is crying and telling you a story. You nod and say 'I can't imagine how that feels.' What are you doing?", "options" => ["Pseudolistening", "Empathetic Listening", "Stage Hogging", "Analyzing"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: You find an old letter. You want to study how the words used have changed meaning over 100 years. What is this?", "options" => ["Phonology", "Etymology and Diachronic Semantics", "Syntax", "Morphology"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: An email says 'The report was finished.' You want it to sound more direct. How do you change it?", "options" => ["'The report is over.'", "'I finished the report.' (Active Voice)", "'Finished was the report.'", "'Someone did it.'"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You are talking to a boss and accidentally use a swear word. You feel awkward because you broke which rule?", "options" => ["Syntax", "Pragmatics and Register", "Phonology", "Etymology"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You notice you stand much closer to your best friend than to your bank teller. What is this a study of?", "options" => ["Haptics", "Proxemics", "Chronemics", "Vocalics"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: You are giving a speech and notice people in the back are leaning forward. What should you adjust?", "options" => ["Your clothing", "Your projection (volume)", "Your logic", "Your conclusion"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: Someone says 'You're so smart' after you make a mistake. Which semantic concept is this?", "options" => ["Simile", "Irony / Sarcasm", "Denotation", "Euphemism"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You are browsing social media and see only news that you agree with. What are you likely inside of?", "options" => ["A computer virus", "An Echo Chamber / Filter Bubble", "A public library", "A group chat"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: A speaker says 'If we let them change the lunch menu, soon the whole office will collapse into chaos.' What fallacy is this?", "options" => ["Ad Hominem", "Slippery Slope", "Circular Logic", "Red Herring"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: You need to send a complex legal document via email. What should you prioritize?", "options" => ["Emoji use", "Clarity, Conciseness, and Formal Tone", "Using all caps", "A very long signature"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: A child learns to say 'goed' instead of 'went'. They have understood a rule but applied it incorrectly. What is this?", "options" => ["Linguistics error", "Morphological overregularization", "Syntax failure", "Phonology problem"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are writing a book and use a dictionary to check the history of a word. What is this called?", "options" => ["Geology", "Etymology", "Biology", "Sociology"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are in a foreign country and people find your 'thumbs up' offensive. What is the gesture called?", "options" => ["An Illustrator", "An Emblem", "An Adaptor", "An Artifact"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "MASTER FINAL: What is the primary purpose of studying communication as a process?", "options" => ["To talk as much as possible", "To understand and improve the shared creation of meaning between individuals", "To win every argument", "To learn to code"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: Which field explains why we talk differently to a baby than to a judge?", "options" => ["Biology", "Sociolinguistics (Register/Context)", "Phonology", "Morphology"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: To be a truly 'competent' communicator, one must be...", "options" => ["Both effective and appropriate", "Fast and loud", "Silent and mysterious", "Always correct"], "ans" => 0, "xp" => 400],
                ["q" => "MASTER FINAL: What is the biggest ethical responsibility of a digital citizen?", "options" => ["Buying a fast PC", "Verifying information and communicating with respect", "Getting the most likes", "Following every account"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "EXAM: How does 'Noise' affect the transactional model of communication?", "options" => ["It makes it better", "It distorts the message and requires better encoding/decoding strategies", "It stops communication completely", "It only affects the radio"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: Why is 'Ethos' often considered the most important rhetorical appeal?", "options" => ["Because emotions are weak", "Because if the audience does not trust the speaker, logic and emotion will fail", "Because authority is always right", "Because it is the shortest word"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: In a globalized world, what is the value of 'Cultural Intelligence' (CQ)?", "options" => ["It's not valuable", "It allows for effective collaboration across diverse linguistic and social boundaries", "It makes you a better driver", "It increases your internet speed"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the core goal of Media Literacy in a democracy?", "options" => ["To delete the news", "To empower citizens to think critically and detect disinformation", "To buy more media", "To follow one leader"], "ans" => 1, "xp" => 600]
            ]]
        ]
    ]
];

// ==========================================================
// CUSTOMER SERVICE CURRICULUM (Maps 61-72)
// ==========================================================
$cs_maps = [
    // CATEGORY 1: FOUNDATIONS OF SERVICE (Maps 61-64)
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 1,
        "title" => "Map 61: Introduction to Service Excellence", "desc" => "Understand the core philosophy of world-class service.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary goal of Customer Service?", "options" => ["To ignore the client", "To provide assistance and build loyalty", "To argue with customers", "To only make a one-time sale"], "ans" => 1, "xp" => 150],
                ["q" => "Who is an 'Internal Customer'?", "options" => ["A paying client", "A colleague or coworker within your company", "A government official", "A delivery driver"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'External Customer'?", "options" => ["The manager", "A person who buys products or services from the business", "The IT department", "A former employee"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'First Impression' mean in service?", "options" => ["The final bill", "The initial impact a service provider makes on a customer", "The second meeting", "A type of training"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which behavior demonstrates 'Professionalism'?", "options" => ["Arriving late", "Punctuality, politeness, and expert knowledge", "Using slang", "Ignoring emails"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Proactive' service?", "options" => ["Waiting for a complaint", "Anticipating needs before the customer asks", "Reacting after a mistake", "Doing nothing"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Reactive' service?", "options" => ["Solving problems only after they occur", "Planning for the future", "Starting a business", "Greeting a customer"], "ans" => 0, "xp" => 180],
                ["q" => "Why is 'Brand Image' important in service?", "options" => ["It doesn't matter", "It represents the customer's perception and trust in the company", "It's just a logo", "It determines the office color"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Customer Centricity'?", "options" => ["Focusing on profit only", "Putting the customer at the center of every business decision", "Ignoring the staff", "Working alone"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Service Level'? ", "options" => ["The height of a desk", "The measurable performance standard agreed upon for a service", "The number of floors in a building", "The salary of a clerk"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Empathy'?", "options" => ["Feeling sorry for someone", "The ability to understand and share the feelings of another", "Being very smart", "Talking loudly"], "ans" => 1, "xp" => 210],
                ["q" => "What is the difference between 'Service' and 'Experience'?", "options" => ["They are the same", "Service is the task; Experience is how the customer feels during the process", "Experience is for managers only", "Service is always free"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Reliability' in service?", "options" => ["Being funny", "Performing the promised service dependably and accurately", "Having a lot of employees", "Being the cheapest"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Responsiveness'?", "options" => ["Talking back", "Willingness to help customers and provide prompt service", "Waiting for instructions", "Hiding from clients"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Assurance'?", "options" => ["Insurance", "The knowledge and courtesy of employees and their ability to convey trust", "A guarantee of money back", "A type of software"], "ans" => 1, "xp" => 250],
                ["q" => "What are 'Tangibles' in service?", "options" => ["Feelings", "Physical facilities, equipment, and appearance of personnel", "The company's history", "The phone line"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Customer Lifetime Value' (CLV)?", "options" => ["The price of one product", "The total revenue a business can expect from a single customer account", "The weight of a client", "The number of visits"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Service Culture'?", "options" => ["The art in the office", "The shared values and goals that prioritize customer satisfaction", "The dress code only", "The country's tradition"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Personalization'?", "options" => ["Talking to yourself", "Treating customers as individuals with unique needs", "Hiring a personal assistant", "Changing your name"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Authenticity' in service?", "options" => ["Being fake", "Providing sincere and genuine interactions", "Using a script perfectly", "Having a high price"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'World Class Service'?", "options" => ["Working in another country", "Consistently exceeding customer expectations and setting industry benchmarks", "Being a large company", "Having a lot of technology"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Customer Advocacy'?", "options" => ["Suing a client", "When satisfied customers recommend your business to others", "A legal department", "A complaint"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Moments of Truth'?", "options" => ["A lie detector test", "Any interaction where a customer forms an opinion about a service", "The final payment", "A legal contract"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Sustainable' service?", "options" => ["Service that ends fast", "Service that can be maintained consistently over the long term", "Service with no staff", "Service that is free"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 1,
        "title" => "Map 62: Communication Skills", "desc" => "Master the verbal and non-verbal tools of the trade.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Active Listening'?", "options" => ["Hearing music", "Fully concentrating and responding to the customer", "Ignoring the speaker", "Talking over the client"], "ans" => 1, "xp" => 150],
                ["q" => "Which is an example of a 'Closed Question'?", "options" => ["'How do you feel?'", "'Did you receive the order?'", "'What is the problem?'", "'Tell me more.'"], "ans" => 1, "xp" => 150],
                ["q" => "Which is an example of an 'Open Question'?", "options" => ["'Is it blue?'", "'Can you describe what happened?'", "'Are you happy?'", "'Is it 5 o'clock?'"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Tone of Voice'?", "options" => ["How loud you are", "The emotional quality and attitude expressed through speech", "The words you choose", "Your accent"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Paraphrasing'?", "options" => ["Repeating exactly", "Restating the customer's point in your own words to check understanding", "Writing a poem", "Staying silent"], "ans" => 1, "xp" => 180],
                ["q" => "Why is 'Eye Contact' important in person?", "options" => ["To see their eye color", "To build trust and show you are paying attention", "To intimidate them", "To look for reflections"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Positive Language'?", "options" => ["Telling lies", "Focusing on what can be done rather than what cannot", "Being very loud", "Using long words"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Jargon'?", "options" => ["A type of food", "Technical terms that customers may not understand", "Clear communication", "A foreign language"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Non-verbal Communication'?", "options" => ["Writing a letter", "Body language, facial expressions, and gestures", "Speaking a language", "Sending an email"], "ans" => 1, "xp" => 210],
                ["q" => "What does 'Mirroring' involve?", "options" => ["Looking in a mirror", "Subtly mimicking a customer's posture or tone to build rapport", "Arguing with a client", "Hiding your face"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Verbal Cues'?", "options" => ["Hand gestures", "Words or sounds that signal you are listening (e.g., 'I see', 'Uh-huh')", "The way you dress", "The layout of the office"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Clarity' in speech?", "options" => ["Using slang", "Speaking in a way that is easy to understand", "Whispering", "Using complex metaphors"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Inflection'?", "options" => ["Being flat", "Changing the pitch of your voice to emphasize words", "An infection", "The speed of typing"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Empathetic Responding'?", "options" => ["Giving a refund", "Acknowledging a customer's feelings before providing a solution", "Ignoring the complaint", "Asking for money"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Summarizing'?", "options" => ["A vacation", "Briefly repeating the main points of a conversation", "Deleting the record", "Asking a new question"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Assertiveness'?", "options" => ["Being aggressive", "Expressing your views clearly and respectfully without being pushy", "Being shy", "Agreeing with everything"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Intercultural Communication'?", "options" => ["Talking to computers", "Communicating effectively with people from different backgrounds", "Learning to code", "Traveling for fun"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Semantic Barrier'?", "options" => ["A physical wall", "Misunderstandings caused by different meanings of words", "Loud noise", "A broken phone"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Digital Etiquette'?", "options" => ["Buying a new PC", "Rules for professional behavior in emails and chats", "Social media marketing", "Fixing a phone"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'De-escalation' through words?", "options" => ["Starting a fight", "Using calm and neutral language to lower a customer's anger", "Being rude", "Hanging up the phone"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Rapport'?", "options" => ["A legal report", "A harmonious connection and mutual trust with a customer", "The end of a shift", "A bill"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Emotional Intelligence' (EQ)?", "options" => ["Having a high IQ", "The ability to manage your own and others' emotions effectively", "Being very emotional", "Crying with a customer"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Feedback'?", "options" => ["A loud noise", "Information given about a service or product's performance", "A type of payment", "A marketing flyer"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Golden Rule' of service communication?", "options" => ["The customer is always wrong", "Treat the customer as you would want to be treated", "Charge as much as possible", "Talk as fast as you can"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 1,
        "title" => "Map 63: Professionalism & Grooming", "desc" => "Represent your company with visual and behavioral excellence.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Personal Grooming'?", "options" => ["Fixing a computer", "Maintaining a clean and neat physical appearance", "Selling a product", "Buying new shoes"], "ans" => 1, "xp" => 150],
                ["q" => "Why is a 'Uniform' used?", "options" => ["To make everyone look the same", "To create a professional and recognizable brand identity", "Because it's cheaper", "To save time in the morning"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Punctuality'?", "options" => ["Good grammar", "Being on time", "Talking fast", "Being smart"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Body Language'?", "options" => ["Speaking a language", "Communication using posture, eye contact, and gestures", "Exercise at work", "Eating healthy"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Professionalism'?", "options" => ["Having a degree", "The conduct, aims, or qualities that characterize a profession", "Working for a long time", "Earning a high salary"], "ans" => 1, "xp" => 180],
                ["q" => "Which is NOT appropriate for office wear?", "options" => ["A suit", "Clean shirt", "Dirty or torn clothing", "Polished shoes"], "ans" => 2, "xp" => 180],
                ["q" => "What is 'Work Ethic'?", "options" => ["A type of law", "The principle that hard work is intrinsically virtuous or worthy of reward", "Working at night", "Being the boss"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Integrity'?", "options" => ["Being smart", "The quality of being honest and having strong moral principles", "Knowing the rules", "Following orders"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Accountability'?", "options" => ["Doing math", "Taking responsibility for one's actions and decisions", "Blaming others", "Working long hours"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Reliability'?", "options" => ["Being fast", "Consistently performing well and being trustworthy", "Having a new car", "Being friendly"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Discretion'?", "options" => ["Being loud", "Behaving or speaking in such a way as to avoid causing offense or revealing private info", "Working in secret", "Choosing your own hours"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Adaptability'?", "options" => ["Staying the same", "The ability to adjust to new conditions and changes", "Being stubborn", "Moving to a new city"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Time Management'?", "options" => ["Watching a clock", "Organizing and planning how to divide your time between activities", "Buying a watch", "Running fast"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Organization'?", "options" => ["A company", "The ability to stay focused on different tasks and manage time/space well", "Hiring people", "Cleaning the floor"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Civility'?", "options" => ["Living in a city", "Polite and courteous behavior", "Talking loudly", "Following the law"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Initiative'?", "options" => ["Waiting for orders", "The ability to assess and initiate things independently", "Starting a new company", "Taking a break"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Diplomacy'?", "options" => ["International politics", "The art of dealing with people in a sensitive and effective way", "Being a manager", "Ignoring problems"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Personal Branding'?", "options" => ["Marking cattle", "How you present yourself to others in the professional world", "Having a logo", "Wearing a name tag"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Emotional Resilience'?", "options" => ["Being sad", "The ability to recover quickly from difficulties or stress", "Having a lot of power", "Working alone"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Collaboration'?", "options" => ["Competing", "Working with others to produce or create something", "Hiring employees", "Taking orders"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Servant Leadership'?", "options" => ["Being a servant", "A philosophy where the main goal is to serve others", "Being a boss", "Working for free"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Work-Life Balance'?", "options" => ["Working and sleeping", "The equilibrium between professional and personal life", "Working 24 hours", "Being unemployed"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Mentorship'?", "options" => ["Teaching a class", "A relationship where a more experienced person guides a less experienced one", "Paying a tutor", "Hiring a new staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Professional Etiquette'?", "options" => ["Furniture", "The code of behavior that delineates expectations for social behavior in a workplace", "The salary", "The company rules"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 1,
        "title" => "Map 64: Phone & Digital Etiquette", "desc" => "Handle remote customer interactions with precision and care.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the standard professional phone greeting?", "options" => ["'Hello?'", "'Who is this?'", "Identify yourself and the company", "Say nothing until they speak"], "ans" => 2, "xp" => 150],
                ["q" => "What is 'Digital Etiquette'?", "options" => ["Buying a PC", "Correct behavior on digital platforms (email, chat)", "Fixing a phone", "Social media"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Hold Etiquette'?", "options" => ["Holding a box", "Asking permission and providing a reason before placing a caller on hold", "Putting them on hold instantly", "Hanging up"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Voicemail Etiquette'?", "options" => ["Deleting messages", "Providing a clear, concise message with your name and number", "Yelling into the phone", "Saying nothing"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What should you avoid in professional emails?", "options" => ["Proper grammar", "Using ALL CAPS (shouting)", "A clear subject line", "Your contact info"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Response Time'?", "options" => ["The time you start work", "The duration between receiving a query and answering it", "The speed of light", "The time it takes to print"], "ans" => 1, "xp" => 180],
                ["q" => "What is an 'Auto-responder'?", "options" => ["A robot person", "An automated email reply used when you are away", "A phone charger", "A type of virus"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'BCC' in email?", "options" => ["Blind Carbon Copy (hiding recipients)", "Business Computer Code", "Big Company Communication", "Before Closing Call"], "ans" => 0, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Chat Etiquette'?", "options" => ["Using slang", "Professional and quick communication in instant messaging", "Talking to yourself", "Deleting messages"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Cold Call'?", "options" => ["A call in winter", "Calling a potential customer who has had no prior contact", "Hanging up", "A technical support call"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Warm Transfer'?", "options" => ["Heating a phone", "Introducing the caller to the person you are transferring them to", "A fast transfer", "A broken call"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Phonetic Alphabet' used for?", "options" => ["Spelling names", "To clearly communicate letters over the phone (Alpha, Bravo, etc.)", "Singing", "Writing a letter"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Video Call' etiquette?", "options" => ["Sitting in the dark", "Muting when not speaking and having a professional background", "Eating loudly", "Turning off the camera always"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Spam'?", "options" => ["A type of food only", "Unsolicited, bulk digital messages", "An important email", "A secure file"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Netiquette'?", "options" => ["A fish net", "Internet etiquette", "A computer network", "A type of firewall"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Attachment' etiquette?", "options" => ["Hiding a file", "Ensuring file sizes are manageable and relevant to the email", "Deleting emails", "Using viruses"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Emoji' use in professional settings?", "options" => ["Always use them", "Use sparingly and only if appropriate for the company culture", "Never use them", "Use 10 at a time"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Screen Sharing'?", "options" => ["Buying a new monitor", "Displaying your computer screen to others during a call", "Taking a photo", "Hiding your work"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Synchronous' communication?", "options" => ["Email", "Real-time communication (Phone, Live Chat)", "A letter", "A billboard"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Asynchronous' communication?", "options" => ["Phone call", "Communication with a time lag (Email, Forum)", "A meeting", "A video call"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Active Listening' on the phone?", "options" => ["Hanging up", "Using verbal cues and not interrupting", "Typing loudly", "Listening to music"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cybersecurity' in service?", "options" => ["Fixing hardware", "Protecting customer data and avoiding phishing scams", "Buying software", "Using a password manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Social Media' service?", "options" => ["Posting photos", "Handling customer queries and complaints on public platforms", "Advertising only", "Playing games"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Accessibility' in digital service?", "options" => ["Having a fast PC", "Ensuring services can be used by people with disabilities", "Having free Wi-Fi", "A login screen"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 2: COMMUNICATION & CONFLICT (Maps 65-68)
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 2,
        "title" => "Map 65: Managing Difficult Customers", "desc" => "Psychology and tactics for handling high-pressure interactions.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the first thing to do with an angry customer?", "options" => ["Yell back", "Listen calmly and let them vent", "Hang up", "Laugh at them"], "ans" => 1, "xp" => 150],
                ["q" => "Why do customers usually get 'difficult'?", "options" => ["They are bad people", "They feel ignored, frustrated, or treated unfairly", "They like to fight", "They have too much money"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'De-escalation'?", "options" => ["Starting a fire", "Reducing the intensity of a conflict", "Buying more time", "Winning an argument"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Ventilating'?", "options" => ["Opening a window", "Allowing a customer to express their anger fully without interruption", "Taking a break", "Changing the subject"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Active Listening'?", "options" => ["Nodding without listening", "Fully engaging and confirming you understand the issue", "Talking while they talk", "Ignoring the client"], "ans" => 1, "xp" => 180],
                ["q" => "Which phrase is helpful for de-escalation?", "options" => ["'Calm down!'", "'I understand why you are frustrated.'", "'It's not my fault.'", "'You are wrong.'"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Body Language' in conflict?", "options" => ["Crossing your arms", "Keeping an open, neutral posture", "Staring aggressively", "Looking at your watch"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Patience'?", "options" => ["Being fast", "The capacity to accept delay or trouble without getting angry", "Being smart", "Working alone"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Objectivity'?", "options" => ["Being emotional", "Basing decisions on facts rather than personal feelings", "Bolding text", "Hiding the truth"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Assertiveness'?", "options" => ["Being a bully", "Standing your ground respectfully and clearly", "Being weak", "Giving in to everything"], "ans" => 1, "xp" => 210],
                ["q" => "How should you handle an abusive customer?", "options" => ["Abuse them back", "Respectfully set boundaries and end the call if necessary per policy", "Cry", "Do nothing"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Empathy'?", "options" => ["Feeling sorry", "Understanding the customer's perspective", "Agreeing with a lie", "Being loud"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Reframing'?", "options" => ["Changing a photo", "Looking at a problem from a different, more positive perspective", "Deleting a file", "Asking a question"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Mirroring' emotions?", "options" => ["Being angry too", "Usually a bad idea; you should stay calm if they are angry", "Copying a smile", "Hiding your face"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Problem Ownership'?", "options" => ["Buying a company", "Taking personal responsibility for seeing a solution through", "Blaming the boss", "Doing nothing"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Consistency'?", "options" => ["Being different", "Applying rules and service standards the same way for everyone", "Being fast", "Knowing the name"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Boundary Setting'?", "options" => ["Building a wall", "Establishing clear limits on what behavior is acceptable", "Hiring a guard", "Ignoring the rules"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Negotiation'?", "options" => ["Fighting", "A discussion aimed at reaching an agreement", "A command", "A secret"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Escalation'?", "options" => ["A fire", "Moving a customer to a higher level of authority (manager)", "Walking up stairs", "Ignoring a call"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Resolution'?", "options" => ["A new year goal", "The action of solving a problem or dispute", "A promise", "A refund"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Post-traumatic Growth' in service?", "options" => ["Being sad", "Learning and becoming better after a difficult interaction", "Quitting a job", "A medical term"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Internalization'?", "options" => ["Taking things personally", "When you let a customer's anger affect your self-worth (should be avoided)", "Hiding your thoughts", "Fixing a PC"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Debriefing'?", "options" => ["Changing clothes", "Discussing a difficult interaction with a supervisor to process and learn", "Sending a bill", "Taking a break"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Customer Service Recovery' paradox?", "options" => ["Losing money", "When a customer is MORE loyal after a problem is fixed well than if no problem happened", "A secret", "A type of sales"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 2,
        "title" => "Map 66: Conflict De-escalation", "desc" => "Master the verbal and behavioral tools to calm any storm.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Conflict'?", "options" => ["A group of people", "A serious disagreement or argument", "A new product", "A type of weather"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'De-escalation'?", "options" => ["Starting a fire", "Reducing the intensity of a conflict", "Buying more time", "Winning an argument"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Body Language'?", "options" => ["Speaking a language", "Communication using posture, eye contact, and gestures", "Exercise at work", "Eating healthy"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Patience'?", "options" => ["Being fast", "The capacity to accept delay or trouble without getting angry", "Being smart", "Working alone"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Active Listening'?", "options" => ["Nodding without listening", "Fully engaging and confirming you understand the issue", "Talking while they talk", "Ignoring the client"], "ans" => 1, "xp" => 180],
                ["q" => "Which phrase is helpful for de-escalation?", "options" => ["'Calm down!'", "'I understand why you are frustrated.'", "'It's not my fault.'", "'You are wrong.'"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Objectivity'?", "options" => ["Being emotional", "Basing decisions on facts rather than personal feelings", "Bolding text", "Hiding the truth"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Assertiveness'?", "options" => ["Being a bully", "Standing your ground respectfully and clearly", "Being weak", "Giving in to everything"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Reframing'?", "options" => ["Changing a photo", "Looking at a problem from a different, more positive perspective", "Deleting a file", "Asking a question"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Mirroring' emotions?", "options" => ["Being angry too", "Usually a bad idea; you should stay calm if they are angry", "Copying a smile", "Hiding your face"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Problem Ownership'?", "options" => ["Buying a company", "Taking personal responsibility for seeing a solution through", "Blaming the boss", "Doing nothing"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Consistency'?", "options" => ["Being different", "Applying rules and service standards the same way for everyone", "Being fast", "Knowing the name"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Boundary Setting'?", "options" => ["Building a wall", "Establishing clear limits on what behavior is acceptable", "Hiring a guard", "Ignoring the rules"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Negotiation'?", "options" => ["Fighting", "A discussion aimed at reaching an agreement", "A command", "A secret"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Escalation'?", "options" => ["A fire", "Moving a customer to a higher level of authority (manager)", "Walking up stairs", "Ignoring a call"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Resolution'?", "options" => ["A new year goal", "The action of solving a problem or dispute", "A promise", "A refund"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Post-traumatic Growth' in service?", "options" => ["Being sad", "Learning and becoming better after a difficult interaction", "Quitting a job", "A medical term"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Internalization'?", "options" => ["Taking things personally", "When you let a customer's anger affect your self-worth (should be avoided)", "Hiding your thoughts", "Fixing a PC"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Debriefing'?", "options" => ["Changing clothes", "Discussing a difficult interaction with a supervisor to process and learn", "Sending a bill", "Taking a break"], "ans" => 1, "xp" => 300],
                ["q" => "What is the 'Customer Service Recovery' paradox?", "options" => ["Losing money", "When a customer is MORE loyal after a problem is fixed well than if no problem happened", "A secret", "A type of sales"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Active Listening'?", "options" => ["Nodding without listening", "Fully engaging and confirming you understand the issue", "Talking while they talk", "Ignoring the client"], "ans" => 1, "xp" => 400],
                ["q" => "Which phrase is helpful for de-escalation?", "options" => ["'Calm down!'", "'I understand why you are frustrated.'", "'It's not my fault.'", "'You are wrong.'"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Objectivity'?", "options" => ["Being emotional", "Basing decisions on facts rather than personal feelings", "Bolding text", "Hiding the truth"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Assertiveness'?", "options" => ["Being a bully", "Standing your ground respectfully and clearly", "Being weak", "Giving in to everything"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 2,
        "title" => "Map 67: Problem Solving & Recovery", "desc" => "Turn bad experiences into brand loyalty with expert recovery.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Service Recovery'?", "options" => ["Taking a break", "The process of fixing a service failure to retain a customer", "Charging a fee", "Deleting a record"], "ans" => 1, "xp" => 150],
                ["q" => "What is the first step in solving a problem?", "options" => ["Blaming someone", "Identifying and defining the problem clearly", "Giving a refund immediately", "Ignoring it"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Root Cause'?", "options" => ["A type of plant", "The fundamental reason why a problem occurred", "A manager", "A bill"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Compensating' a customer?", "options" => ["Giving extra work", "Providing something (discount, refund, gift) to make up for a mistake", "Yelling at them", "Nothing"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is an 'Apology'?", "options" => ["A type of argument", "An expression of regret for a mistake", "A legal contract", "A fine"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Brainstorming'?", "options" => ["A headache", "Generating many ideas to solve a problem", "Working alone", "Following a script"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Implementation'?", "options" => ["Thinking about it", "Putting a solution into action", "Deleting a solution", "Asking for help"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Evaluation'?", "options" => ["A party", "Reviewing if a solution worked and why", "Paying a bill", "Cleaning the office"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Prevention'?", "options" => ["Acting after a mistake", "Taking steps to ensure a problem does not happen again", "Ignoring the issue", "Waiting for orders"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Continuous Improvement'?", "options" => ["Staying the same", "The ongoing effort to improve products, services, or processes", "Hiring new staff", "Buying new software"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Standardization'?", "options" => ["Being unique", "Ensuring the same quality and method for all service delivery", "Working alone", "Hiding the rules"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Customer Feedback'?", "options" => ["A loud noise", "Information from customers about their satisfaction", "A type of payment", "A marketing flyer"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Empathy' in recovery?", "options" => ["Giving money", "Showing the customer you care about the trouble they faced", "Ignoring the feeling", "Being loud"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Responsibility'?", "options" => ["Blaming others", "Taking charge of a situation to fix it", "Being a manager", "Working long hours"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Flexibility'?", "options" => ["Strictly following rules", "Being able to bend rules to help a customer when appropriate", "Being weak", "Doing nothing"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Timeliness'?", "options" => ["Being late", "Solving the problem as quickly as possible", "Taking a break", "Watching the clock"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Follow-up' in recovery?", "options" => ["Ignoring the customer", "Contacting the customer after a fix to ensure they are still happy", "Sending a bill", "Asking for a tip"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Data Driven' solving?", "options" => ["Using feelings", "Using metrics and reports to find and fix service issues", "Asking a friend", "Guessing"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Service Design'?", "options" => ["The office decor", "The deliberate planning of a service to improve customer satisfaction", "A new logo", "A marketing plan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Six Sigma'?", "options" => ["A brand of car", "A set of techniques for process improvement and reducing errors", "A programming language", "A type of phone"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Kaizen'?", "options" => ["A type of food", "A Japanese philosophy of continuous improvement", "A secret code", "A type of sales"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Benchmarking'?", "options" => ["Buying a bench", "Comparing your service performance against the best in the industry", "Cleaning the office", "Hiring staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Value Added'?", "options" => ["Charging more", "Providing extra benefits that exceed the basic service", "Deleting data", "Hiding the price"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Total Quality Management' (TQM)?", "options" => ["Managing a shop", "An organization-wide effort to install a permanent climate of quality", "Hiring new staff", "Selling products"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 2,
        "title" => "Map 68: Sales and Emotional Intelligence", "desc" => "Leverage psychological tools to drive value and connection.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Emotional Intelligence' (EQ)?", "options" => ["Having a high IQ", "The ability to manage your own and others' emotions effectively", "Being very emotional", "Crying with a customer"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Empathy' in sales?", "options" => ["Giving money", "Understanding the customer's needs and perspectives", "Ignoring the feeling", "Being loud"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Rapport'?", "options" => ["A legal report", "A harmonious connection and mutual trust with a customer", "The end of a shift", "A bill"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Upselling'?", "options" => ["Selling things on a hill", "Encouraging a customer to purchase a more expensive or premium version of a product", "Selling things for free", "Ignoring a customer"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Cross-selling'?", "options" => ["Selling to angry customers", "Selling related or complementary products to a customer", "Selling to a competitor", "Selling in a different language"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Active Listening' in sales?", "options" => ["Nodding without listening", "Fully engaging and confirming you understand the issue", "Talking while they talk", "Ignoring the client"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Assertiveness'?", "options" => ["Being a bully", "Standing your ground respectfully and clearly", "Being weak", "Giving in to everything"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Consistency'?", "options" => ["Being different", "Applying rules and service standards the same way for everyone", "Being fast", "Knowing the name"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Reframing'?", "options" => ["Changing a photo", "Looking at a problem from a different, more positive perspective", "Deleting a file", "Asking a question"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Mirroring' emotions?", "options" => ["Being angry too", "Usually a bad idea; you should stay calm if they are angry", "Copying a smile", "Hiding your face"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Problem Ownership'?", "options" => ["Buying a company", "Taking personal responsibility for seeing a solution through", "Blaming the boss", "Doing nothing"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Consistency'?", "options" => ["Being different", "Applying rules and service standards the same way for everyone", "Being fast", "Knowing the name"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Boundary Setting'?", "options" => ["Building a wall", "Establishing clear limits on what behavior is acceptable", "Hiring a guard", "Ignoring the rules"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Negotiation'?", "options" => ["Fighting", "A discussion aimed at reaching an agreement", "A command", "A secret"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Escalation'?", "options" => ["A fire", "Moving a customer to a higher level of authority (manager)", "Walking up stairs", "Ignoring a call"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Resolution'?", "options" => ["A new year goal", "The action of solving a problem or dispute", "A promise", "A refund"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Post-traumatic Growth' in service?", "options" => ["Being sad", "Learning and becoming better after a difficult interaction", "Quitting a job", "A medical term"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Internalization'?", "options" => ["Taking things personally", "When you let a customer's anger affect your self-worth (should be avoided)", "Hiding your thoughts", "Fixing a PC"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Debriefing'?", "options" => ["Changing clothes", "Discussing a difficult interaction with a supervisor to process and learn", "Sending a bill", "Taking a break"], "ans" => 1, "xp" => 300],
                ["q" => "What is the 'Customer Service Recovery' paradox?", "options" => ["Losing money", "When a customer is MORE loyal after a problem is fixed well than if no problem happened", "A secret", "A type of sales"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Active Listening'?", "options" => ["Nodding without listening", "Fully engaging and confirming you understand the issue", "Talking while they talk", "Ignoring the client"], "ans" => 1, "xp" => 400],
                ["q" => "Which phrase is helpful for de-escalation?", "options" => ["'Calm down!'", "'I understand why you are frustrated.'", "'It's not my fault.'", "'You are wrong.'"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Objectivity'?", "options" => ["Being emotional", "Basing decisions on facts rather than personal feelings", "Bolding text", "Hiding the truth"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Assertiveness'?", "options" => ["Being a bully", "Standing your ground respectfully and clearly", "Being weak", "Giving in to everything"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 3: STRATEGY & EXPERIENCE (Maps 69-72)
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 3,
        "title" => "Map 69: Measuring Satisfaction (NPS/CSAT)", "desc" => "Understand the data that drives service strategy.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does 'CSAT' stand for?", "options" => ["Customer Service and Technology", "Customer Satisfaction Score", "Common Service Action Team", "Corporate Sales and Training"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'NPS' stand for?", "options" => ["Net Promoter Score", "National Phone Service", "New Product Sale", "Net Profit Statement"], "ans" => 0, "xp" => 150],
                ["q" => "What is a 'Promoter' in NPS?", "options" => ["A person who hates the brand", "A customer who is highly likely to recommend the brand (score 9-10)", "A manager", "A marketing flyer"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Detractor' in NPS?", "options" => ["A loyal fan", "A customer who is unhappy and likely to damage the brand (score 0-6)", "A competitive company", "A type of discount"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'CES'?", "options" => ["Customer Effort Score (how easy it was to solve an issue)", "Common Entry System", "Corporate Expense Sheet", "Customer Email Service"], "ans" => 0, "xp" => 180],
                ["q" => "What is 'Churn Rate'?", "options" => ["Making butter", "The percentage of customers who stop using your service over time", "The speed of sales", "The number of new users"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Qualitative' feedback?", "options" => ["Numbers and stats", "Descriptive feedback like comments and reviews", "A type of tax", "A bank statement"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Quantitative' feedback?", "options" => ["Comments and stories", "Numerical data like ratings and scores", "A secret code", "A job interview"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Passive' customer in NPS?", "options" => ["An angry client", "A customer who is satisfied but not enthusiastic (score 7-8)", "A person who never buys", "A company owner"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Benchmarking'?", "options" => ["Buying a bench", "Comparing your scores against industry standards or competitors", "Cleaning the office", "Hiring staff"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'First Response Time' (FRT)?", "options" => ["The end of a call", "The time it takes for a customer to receive the first reply to their query", "The speed of light", "A technical term"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Average Handle Time' (AHT)?", "options" => ["The weight of a product", "The average duration of a customer interaction", "The time spent on break", "The number of staff"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Sentiment Analysis'?", "options" => ["A math test", "Using technology to identify the emotional tone behind customer comments", "Ignoring the customer", "A type of sales"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Touchpoint'?", "options" => ["A place to touch the wall", "Any interaction between a customer and a brand", "A fingerprint", "A type of screen"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Customer Journey Mapping'?", "options" => ["A vacation map", "Visualizing every step a customer takes with your company", "A GPS device", "A marketing plan"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Closing the Loop'?", "options" => ["A magic trick", "Following up with a customer after they provide feedback to resolve their issue", "Ending a call", "A type of sales"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Voice of the Customer' (VoC)?", "options" => ["Talking loudly", "The process of capturing customer requirements, expectations, and preferences", "A radio show", "A legal contract"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Retention' strategy?", "options" => ["Firing customers", "Plans aimed at keeping current customers and reducing churn", "Finding new clients", "Selling products"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Mystery Shopping'?", "options" => ["Shopping at night", "Hiring people to pose as customers to test service quality", "Buying a gift", "A type of sales"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Service Level Agreement' (SLA)?", "options" => ["A type of cable", "A contract specifying the standards of service expected", "A fast internet connection", "A programming language"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Omnichannel' service?", "options" => ["Using one phone", "Providing a seamless customer experience across all channels (Web, Phone, In-person)", "A new TV station", "A marketing award"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Customer Success'?", "options" => ["A customer getting rich", "A proactive strategy to ensure customers achieve their desired outcomes using your product", "Winning a game", "Selling one product"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Loyalty Program'?", "options" => ["A secret code", "A marketing strategy that rewards customers for repeat business", "A type of tax", "A bank statement"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'User Experience' (UX)?", "options" => ["Playing a game", "The overall experience of a person using a product, especially in terms of how easy or pleasing it is to use", "A type of software", "Working in a company"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 3,
        "title" => "Map 70: CRM Systems and Data", "desc" => "Manage client relations and professional support systems.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does 'CRM' stand for?", "options" => ["Company Relationship Management", "Customer Relationship Management", "Common Resource Management", "Corporate Record Marketing"], "ans" => 1, "xp" => 150],
                ["q" => "What is the primary goal of Customer Service?", "options" => ["To make money only", "To ensure customer satisfaction and build loyalty", "To ignore complaints", "To sell as much as possible once"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Client'?", "options" => ["A coworker", "A person or organization using the services of another", "A competitor", "A manager"], "ans" => 1, "xp" => 150],
                ["q" => "Which tool is commonly used to track customer interactions?", "options" => ["A notebook", "CRM Software", "A calendar", "A calculator"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Customer Loyalty'?", "options" => ["Liking a company", "The likelihood that a customer will continue to buy from a business", "Buying things for free", "A type of discount"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Customer Retention'?", "options" => ["Firing a customer", "The ability of a company to keep its customers over time", "Searching for new customers", "Ignoring old customers"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Empathy' in customer service?", "options" => ["Being smart", "The ability to understand and share the feelings of the customer", "Feeling sorry for someone", "Being very loud"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Complaint'?", "options" => ["A type of praise", "An expression of dissatisfaction with a product or service", "A suggestion", "A thank you note"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Upselling'?", "options" => ["Selling things on a hill", "Encouraging a customer to purchase a more expensive or premium version of a product", "Selling things for free", "Ignoring a customer"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Cross-selling'?", "options" => ["Selling to angry customers", "Selling related or complementary products to a customer", "Selling to a competitor", "Selling in a different language"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Customer Journey'?", "options" => ["A trip the customer takes", "The complete sum of experiences that customers go through when interacting with your company", "A marketing flyer", "A list of customers"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Feedback'?", "options" => ["A loud noise", "Information about reactions to a product or service, used as a basis for improvement", "A type of payment", "An advertisement"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Ticket' in customer support?", "options" => ["A bus ticket", "A record of a customer request or issue in a support system", "A fine", "A prize"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'SLA' in customer service?", "options" => ["Service Level Agreement (e.g., promising a reply in 24 hours)", "Super Low Average", "Standard Legal Action", "Secret Loyalty Award"], "ans" => 0, "xp" => 250],
                ["q" => "What is 'Churn Rate'?", "options" => ["Making butter", "The rate at which customers stop doing business with an entity", "The speed of sales", "The number of new customers"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Touchpoint'?", "options" => ["A place to touch the wall", "Any point of interaction between a customer and a business", "A type of computer", "A fingerprint"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Personalization' in CRM?", "options" => ["Talking to yourself", "Tailoring a service or product to accommodate specific individuals", "Hiring new staff", "Changing the company logo"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Automation' in CRM?", "options" => ["A type of car", "Using software to perform repetitive tasks (like sending follow-up emails)", "Fixing machinery", "A new marketing plan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Lead' in sales?", "options" => ["A type of metal", "A person or organization that has shown interest in a product or service", "A manager", "A final sale"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Conversion'?", "options" => ["Changing religion", "The point at which a lead becomes a paying customer", "A type of translation", "A salary increase"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'NPS' (Net Promoter Score)?", "options" => ["A type of tax", "A metric used to measure customer loyalty and satisfaction", "A bank statement", "A marketing award"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'User Experience' (UX)?", "options" => ["Playing a game", "The overall experience of a person using a product, especially in terms of how easy or pleasing it is to use", "A type of software", "Working in a company"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Proactive' customer service?", "options" => ["Acting after a complaint", "Anticipating customer needs and addressing them before they become problems", "Ignoring the customer", "Waiting for instructions"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Customer Advocacy'?", "options" => ["Suing a company", "When customers are so satisfied they actively promote your brand to others", "A type of marketing", "A legal team"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 3,
        "title" => "Map 71: Service Leadership", "desc" => "Manage service teams, KPIs, and cultural alignment.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Service Leadership'?", "options" => ["Telling people what to do", "The ability to inspire and enable a team to provide excellent service", "Being the loudest", "Doing all the work alone"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'KPI' in service management?", "options" => ["Key Performance Indicator", "Known Personal Interest", "Keeping People Informed", "Key Process Item"], "ans" => 0, "xp" => 150],
                ["q" => "What is 'Onboarding'?", "options" => ["Getting on a boat", "The process of integrating a new employee into the service team", "Firing a person", "Giving a refund"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Employee Engagement'?", "options" => ["A wedding", "The emotional commitment an employee has to the organization and its goals", "Being very busy", "Telling people what to do"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Coaching'?", "options" => ["Yelling at staff", "Providing feedback and guidance to help employees improve skills", "Playing sports", "Doing the work for them"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Empowerment'?", "options" => ["Giving someone power", "Giving employees the authority to make decisions to help customers", "Taking away authority", "A technical term"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Service Culture'?", "options" => ["The art in the office", "The shared values and goals that prioritize customer satisfaction", "The dress code only", "The country's tradition"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Turnover'?", "options" => ["A type of pastry", "The rate at which employees leave and are replaced", "A promotion", "Changing shifts"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Quality Assurance' (QA)?", "options" => ["Promising good work", "The systematic monitoring and evaluation of service delivery", "A type of test", "Fixing a broken product"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Performance Review'?", "options" => ["Watching a movie", "A formal assessment of an employee's work over a period", "A salary increase", "A team meeting"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Conflict Management'?", "options" => ["Starting a fight", "The process of limiting the negative aspects of conflict", "Ignoring the problem", "Telling the boss"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Motivation'?", "options" => ["Tiredness", "The reason or reasons one has for acting or behaving in a particular way", "A type of training", "A salary bonus"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Strategic Planning'?", "options" => ["Daily task listing", "Setting long-term goals and determining the best approach", "Planning a party", "Creating a weekly schedule"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'SOP'?", "options" => ["A type of software", "Standard Operating Procedure (established step-by-step instructions)", "The company name", "The layout of the office"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Change Management'?", "options" => ["Counting petty cash", "The process of transitioning individuals or teams to a desired future state", "Changing the office layout", "Updating the software"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Resource Allocation'?", "options" => ["Buying more things", "Assigning and managing assets to support service goals", "Hiring new staff", "Spending the budget"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Emotional Intelligence' (EQ) in leadership?", "options" => ["Having a high IQ", "Managing your emotions and the team's emotions effectively", "Being very emotional", "Crying at work"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Synergy' in a team?", "options" => ["Working alone", "Cooperation producing a combined effect greater than the sum of separate effects", "A type of software", "Competing with coworkers"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Delegation'?", "options" => ["Doing all the work", "Entrusting a task or responsibility to another person", "Hiring new staff", "Quitting a job"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Servant Leadership'?", "options" => ["Being a servant", "A leadership philosophy where the main goal is to serve", "A strict leader", "A leader who does no work"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Burnout'?", "options" => ["A fire in the office", "Physical or mental collapse caused by overwork or stress", "Running out of coffee", "Quitting your job"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Accountability'?", "options" => ["Doing math", "The obligation to accept responsibility for one's actions", "Blaming others", "Working long hours"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Transparency'?", "options" => ["A clear window", "Operating in a way that is easy for others to see what actions are performed", "Hiding the truth", "A type of software"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Organizational Behavior'?", "options" => ["How people act in public", "The study of how people interact within groups", "Company rules", "A type of psychology only"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $cs_course_id, "category_id" => 3,
        "title" => "Map 72: Customer Experience Strategy (Capstone)", "desc" => "The ultimate challenge. Design a holistic strategy for brand excellence.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: An angry customer is shouting in a physical store. What is the BEST first step?", "options" => ["Shout back to show authority", "Calmly invite them to a private area to listen", "Call the police immediately", "Ignore them and help others"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: Your team's NPS score has dropped from 50 to 20. What do you do first?", "options" => ["Fire the team", "Analyze qualitative feedback from detractors to find the root cause", "Increase prices", "Ignore the score"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You need to ensure a new service process is the same across 10 branches. What do you use?", "options" => ["A verbal instruction", "Standard Operating Procedures (SOPs) and training", "A hope for the best", "A marketing flyer"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: A customer is using abusive language to a staff member. What is the policy?", "options" => ["The staff must accept it", "The staff can respectfully set a boundary and terminate the interaction per policy", "The staff should abuse them back", "The staff should cry"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: A customer wants a refund that is against company policy, but they are a loyal VIP. What is the move?", "options" => ["Refuse strictly", "Use 'Empowerment' to offer a one-time exception or a high-value alternative", "Change the policy for everyone", "Give them cash from the drawer"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You notice a staff member is always late and looks unkempt. How do you handle it?", "options" => ["Publicly embarrass them", "Have a private coaching session about Professionalism and Grooming", "Fire them instantly", "Do nothing"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: A customer emails with a complex technical problem. What is the best tone?", "options" => ["Short and blunt", "Empathetic, clear, and professional", "Use lots of emojis", "Reply in 2 weeks"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You are launching a new product. How do you prepare the service team?", "options" => ["Tell them on the day", "Provide comprehensive training, FAQs, and role-playing", "Give them a manual only", "Don't tell them"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: A client's data has been leaked. What is the priority?", "options" => ["Hide it", "Crisis communication and immediate transparency per data laws (POPIA/GDPR)", "Delete the records", "Blame the IT guy"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: A customer journey map shows people quit at the 'Payment' stage. What is the fix?", "options" => ["Reduce prices", "Improve the 'User Experience' (UX) and simplify the payment touchpoint", "Add more ads", "Ignore it"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: Your department has no budget for refunds. How do you recover service?", "options" => ["Ignore problems", "Use 'Value Added' recovery like personal apologies, future vouchers, or extra support", "Lie to the customer", "Quit your job"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You are hiring for a high-stress reception role. What is the key skill to look for?", "options" => ["Typing speed", "High Emotional Intelligence (EQ) and Resilience", "Height", "Strength"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: Two team members are arguing in front of customers. What is the administrative response?", "options" => ["Join the argument", "Immediate private intervention and disciplinary review", "Ignore it", "Tell the customers to judge"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You want to improve long-term loyalty. What metric do you prioritize?", "options" => ["Daily sales", "Customer Lifetime Value (CLV) and Retention Rate", "The number of phone calls", "The office cleanliness"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: A customer is complaining on Twitter/X. How do you respond?", "options" => ["Argue publicly", "Publicly acknowledge with empathy and move to a private DM for resolution", "Delete their post", "Ignore it"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are standardizing service across 50 people. What tool is best?", "options" => ["A large meeting", "A robust CRM system with mandatory logging and clear SOPs", "A WhatsApp group", "A notebook"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "MASTER FINAL: What defines a truly successful Customer Service Strategy?", "options" => ["Being the cheapest", "Alignment of people, processes, and technology to consistently deliver value", "Having the most employees", "Using the newest computers"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: In service leadership, what is the 'Internal Service Quality' link?", "options" => ["Good lighting", "The idea that happy, well-supported employees lead to happy customers", "Buying better software", "Hiring more managers"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: What is 'Service Innovation'?", "options" => ["Fixing errors", "Deliberately creating new ways to deliver value and exceed customer expectations", "Hiring staff", "Selling more"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: What is the biggest ethical risk in modern customer service?", "options" => ["Talking too much", "Data privacy breaches and algorithmic bias in CRM", "Being late", "Using the wrong font"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "EXAM: How does 'Empathy' impact the bottom line?", "options" => ["It doesn't", "It increases retention, word-of-mouth, and lifetime value", "It makes products cheaper", "It speeds up the internet"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: Why is 'Consistency' more important than occasional 'Wow' moments?", "options" => ["It's not", "Consistency builds long-term trust and predictable quality for the brand", "It's easier to do", "It saves money"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the value of 'Root Cause Analysis' in service?", "options" => ["Assigning blame", "Ensuring that failures are fixed permanently rather than just patched", "Finding a new manager", "Calculating profit"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the core of 'Service Excellence'?", "options" => ["The product only", "A human-centric approach to problem-solving and relationship management", "Having a lot of money", "Being a famous company"], "ans" => 1, "xp" => 600]
            ]]
        ]
    ]
];

// ==========================================================
// ENTREPRENEURIAL SKILLS CURRICULUM (Maps 73-84)
// ==========================================================
$entrep_maps = [
    // CATEGORY 1: IDEATION & VALIDATION (Maps 73-76)
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 1,
        "title" => "Map 73: The Entrepreneurial Mindset", "desc" => "Develop the grit, vision, and problem-solving skills of a founder.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary definition of an Entrepreneur?", "options" => ["An employee with a high salary", "A person who organizes and operates a business, taking on financial risk", "A government official", "A person who only works alone"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Intrapreneurship'?", "options" => ["Working for yourself", "Acting like an entrepreneur within a large organization", "Hiring your family", "Selling products in a different country"], "ans" => 1, "xp" => 150],
                ["q" => "Which trait is most associated with successful founders?", "options" => ["Fear of failure", "Resilience and adaptability", "Strictly following orders", "Avoiding all risks"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Opportunity Cost'?", "options" => ["The price of an advertisement", "The value of the next best alternative foregone when making a decision", "The total cost of starting a business", "The cost of hiring an accountant"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Pain Point' in entrepreneurship?", "options" => ["A physical injury", "A specific problem that prospective customers are experiencing", "The cost of office rent", "A disagreement between partners"], "ans" => 1, "xp" => 180],
                ["q" => "What does it mean to have a 'Growth Mindset'?", "options" => ["Thinking you are already perfect", "Believing that abilities can be developed through dedication and hard work", "Only caring about company size", "Expecting instant success"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Bootstrapping'?", "options" => ["Buying expensive shoes", "Starting a company with little capital, relying on personal savings and revenue", "Getting a massive bank loan", "Selling shares to the public"], "ans" => 1, "xp" => 180],
                ["q" => "Why is 'Failure' often seen differently in entrepreneurship?", "options" => ["It's always bad", "It's viewed as a learning opportunity and a stepping stone to success", "It's illegal", "It means you should never try again"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'First-Mover Advantage'?", "options" => ["Being the fastest runner", "The advantage gained by the first significant occupant of a market segment", "Having the first office in a building", "Hiring the first employee"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Design Thinking'?", "options" => ["Drawing pretty pictures", "A human-centered approach to innovation and problem solving", "Choosing a logo", "Thinking about fashion"], "ans" => 1, "xp" => 210],
                ["q" => "What is an 'Iterative Process'?", "options" => ["Doing something once", "A process for arriving at a decision or a desired result by repeating rounds of analysis", "A mathematical error", "Hiring a consultant"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Risk Mitigation'?", "options" => ["Taking as much risk as possible", "The process of reducing the impact or likelihood of potential risks", "Ignoring problems", "Closing a business early"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Serial Entrepreneur'?", "options" => ["Someone who sells breakfast food", "An individual who continuously comes up with new ideas and starts new businesses", "A person who works for 20 years at one job", "A business lawyer"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Disruptive Innovation'?", "options" => ["Innovation that breaks the law", "An innovation that creates a new market and value network, displacing established firms", "A loud office environment", "A software bug"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Vision' in entrepreneurship?", "options" => ["A medical term for eyes", "The long-term goal and direction of the company", "A type of dream", "A marketing flyer"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Social Entrepreneurship'?", "options" => ["Hiring friends", "Startups that develop solutions to social, cultural, or environmental issues", "Hosting office parties", "Using social media for sales"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Self-Efficacy'?", "options" => ["Being selfish", "One's belief in one's ability to succeed in specific situations", "Working without help", "Earning a high salary"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Blue Ocean' vs 'Red Ocean'?", "options" => ["Blue is new markets; Red is existing crowded markets", "Blue is cold; Red is hot", "Blue is for technology; Red is for food", "There is no difference"], "ans" => 0, "xp" => 300],
                ["q" => "What is 'Lean Startup' methodology?", "options" => ["A startup with no employees", "Focusing on creating a product and getting feedback as quickly as possible to reduce waste", "A startup with no money", "Working in a small office"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Pivot'?", "options" => ["Closing the business", "A fundamental change in business strategy when the current path isn't working", "Spinning in a circle", "Firing the CEO"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Creative Destruction'?", "options" => ["Breaking office furniture", "The process where new innovations replace older, less efficient technologies and businesses", "A type of art", "A failed startup"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Effectuation'?", "options" => ["Being effective", "A way of thinking where entrepreneurs start with the resources they have to create effects", "Following a 50-page plan", "Waiting for investors"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Founder's Dilemma'?", "options" => ["Choosing a name", "The choice between maintaining control (King) vs. growing the company's value (Rich)", "Deciding where to open an office", "Hiring the first manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Scalability'?", "options" => ["How heavy a product is", "The ability of a business to grow its revenue significantly while keeping costs low", "The height of an office building", "A type of marketing"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 1,
        "title" => "Map 74: Market Research & Validation", "desc" => "Prove your idea works before spending a cent.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Market Research'?", "options" => ["Selling products at a market", "Gathering info about consumers' needs and preferences", "Buying a new office", "Designing a logo"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Primary Research'?", "options" => ["Data you collect yourself (surveys, interviews)", "Data someone else collected (articles, reports)", "Research done in primary school", "The first page of a report"], "ans" => 0, "xp" => 150],
                ["q" => "What is 'Secondary Research'?", "options" => ["The second part of a study", "Using existing data and reports from others", "Research that is not important", "Research done with 2 people"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Target Market'?", "options" => ["A store named Target", "A specific group of consumers at which a product or service is aimed", "Everyone in the world", "Your competitors"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Customer Validation'?", "options" => ["Being nice to customers", "Proving that customers will actually pay for your solution", "Counting the number of people in a store", "Asking your mom if she likes your idea"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Focus Group'?", "options" => ["A group of managers", "A small group of people whose reactions are studied in market research", "A team of researchers", "A type of social media group"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Quantitative Research'?", "options" => ["Research about quality", "Research focused on numbers and statistics", "Asking for opinions", "Writing a story"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Qualitative Research'?", "options" => ["Research about quantity", "Research focused on understanding underlying reasons and opinions", "A spreadsheet of numbers", "A math test"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Persona' in marketing?", "options" => ["Your real personality", "A fictional character that represents a segment of your target audience", "The CEO's public image", "A type of advertisement"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'TAM'?", "options" => ["Total Available Market", "Target Area Management", "Total Asset Money", "Technical Admin Mode"], "ans" => 0, "xp" => 210],
                ["q" => "What is 'SAM'?", "options" => ["Serviceable Addressable Market", "Sales and Marketing", "Small Area Management", "Single Asset Model"], "ans" => 0, "xp" => 210],
                ["q" => "What is 'SOM'?", "options" => ["Serviceable Obtainable Market (the portion you can actually capture)", "Sales on Monday", "System of Management", "Standard Office Mode"], "ans" => 0, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Competitive Analysis'?", "options" => ["Fighting with others", "Evaluating the strengths and weaknesses of current and potential competitors", "Watching sports", "Winning a race"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Product-Market Fit'?", "options" => ["When a product fits in a box", "When a product satisfies a strong market demand", "When a store has enough shelf space", "When a product is cheap"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Smoke Test' in validation?", "options" => ["Testing for fire", "Testing demand by offering a product before it exists (e.g., a landing page)", "Burning a prototype", "A factory inspection"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Early Adopter'?", "options" => ["Someone who wakes up early", "The first group of people to use a new product or technology", "A person who buys used goods", "A professional critic"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Ethnographic Research'?", "options" => ["Research on history", "Observing customers in their natural environment", "Online surveys only", "Reading a dictionary"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'B2B'?", "options" => ["Business to Business", "Back to Base", "Buy to Build", "Better to Best"], "ans" => 0, "xp" => 300],
                ["q" => "What is 'B2C'?", "options" => ["Business to Consumer", "Back to Center", "Better to Create", "Budget to Capital"], "ans" => 0, "xp" => 300],
                ["q" => "What is 'Psychographics'?", "options" => ["A horror movie", "Study of consumers based on psychological traits, values, and lifestyles", "Physical measurements", "Geographic locations"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'A/B Testing'?", "options" => ["Testing two products", "Comparing two versions of something (like a webpage) to see which performs better", "A school grade", "A type of battery"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Net Promoter Score' (NPS)?", "options" => ["A financial report", "A metric used to measure customer loyalty", "The price of a share", "A sports score"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Customer Acquisition Cost' (CAC)?", "options" => ["The salary of a salesman", "The total cost of convincing a potential customer to buy a product", "The price of the product", "Office rent"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Churn Rate'?", "options" => ["The speed of production", "The percentage of customers who stop using your service over time", "Making butter", "The amount of new sales"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 1,
        "title" => "Map 75: Business Models & Value", "desc" => "Define how your company creates, delivers, and captures value.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Business Model'?", "options" => ["A 3D model of an office", "The plan for how a company generates revenue and makes a profit", "A fashion model", "A type of computer"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Value Proposition'?", "options" => ["A price tag", "A statement explaining why a customer should choose your product over others", "A wedding proposal", "A list of expenses"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Business Model Canvas'?", "options" => ["A large painting", "A strategic management tool used to visualize business components", "A type of tent", "A marketing flyer"], "ans" => 1, "xp" => 150],
                ["q" => "Which of these is a 'Revenue Stream'?", "options" => ["Paying rent", "Money coming in from sales, subscriptions, or fees", "Hiring staff", "Buying inventory"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Subscription' model?", "options" => ["A one-time payment", "Customers pay a recurring fee at regular intervals", "Getting things for free", "A government grant"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Freemium' model?", "options" => ["Everything is free forever", "Offering basic services for free while charging for premium features", "A high-priced product", "Free advertising"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Direct-to-Consumer' (DTC) model?", "options" => ["Selling through a retailer", "Selling products directly to customers without intermediaries", "Selling to other businesses", "Giving products away"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Value Creation'?", "options" => ["Making money", "The process of making a product or service that is useful to customers", "Printing a receipt", "Hiring a manager"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Cost Structure'?", "options" => ["The shape of a building", "The types and relative proportions of fixed and variable costs in a business", "The price of the product", "Total profit"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Channel' in the Business Model Canvas?", "options" => ["A TV station", "The way a company communicates with and reaches its customer segments", "A river", "A type of product"], "ans" => 1, "xp" => 210],
                ["q" => "What are 'Key Activities'?", "options" => ["The office party", "The most important things a company must do to make its business model work", "Daily exercise", "Watching competitors"], "ans" => 1, "xp" => 210],
                ["q" => "What are 'Key Resources'?", "options" => ["Money only", "The assets required to offer and deliver the value proposition", "A list of friends", "Old documents"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Value Delivery'?", "options" => ["Using a courier", "The process of getting the value proposition to the customer", "Paying the staff", "Deleting a file"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Value Capture'?", "options" => ["Taking a photo", "The process of retaining a portion of the value created as profit", "Buying a competitor", "Hiring an accountant"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Loss Leader'?", "options" => ["A manager who fails", "A product sold at a loss to attract customers for other profitable products", "A broken machine", "A bad marketing plan"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Platform' business model?", "options" => ["A stage", "A business that facilitates interactions between two or more groups (e.g., Uber, Airbnb)", "A type of shoe", "A standard office"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Unfair Advantage'?", "options" => ["Cheating", "Something that cannot be easily copied or bought by competitors", "Having more money", "Being the loudest"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Unit Economics'?", "options" => ["The economy of a country", "The direct revenues and costs associated with a single unit of a product sold", "The cost of one machine", "Government taxes"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Market Fragmentation'?", "options" => ["Breaking a glass", "A market with no clear dominant leader, composed of many small players", "A type of marketing", "A global monopoly"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Economies of Scale'?", "options" => ["A weighing scale", "The cost advantage that arises with increased output of a product", "The size of an office", "A type of tax"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Monetization' strategy?", "options" => ["Printing money", "The process of converting an asset or service into cash/revenue", "Giving things for free", "Fixing a computer"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Recurring Revenue'?", "options" => ["A one-time sale", "Revenue that is likely to continue in the future (like a monthly fee)", "A lucky win", "A bank loan"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Disintermediation'?", "options" => ["Hiring a middleman", "Cutting out the 'middleman' in a supply chain", "Closing the business", "Working with more partners"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Scalable' Business Model?", "options" => ["A model made of clay", "A model where revenue can increase significantly without a matching increase in costs", "A model for a small shop", "A model that is free"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 1,
        "title" => "Map 76: Legal & MVP Basics", "desc" => "Register your business and build your Minimum Viable Product.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'MVP'?", "options" => ["Most Valuable Player", "Minimum Viable Product", "Main Visual Project", "Managed Value Process"], "ans" => 1, "xp" => 150],
                ["q" => "What is the purpose of an MVP?", "options" => ["To make as much money as possible", "To test the core product idea with real users using minimal resources", "To show off to investors", "To build a perfect product"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Sole Proprietorship'?", "options" => ["A business with 100 owners", "A business owned and operated by one single person", "A government department", "A large corporation"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Limited Liability'?", "options" => ["Being responsible for everything", "Legal protection where owners aren't personally responsible for business debts", "Having no responsibility", "A type of insurance"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Partnership Agreement'?", "options" => ["A handshake", "A legal document outlining how a business is run by two or more people", "A marriage certificate", "A marketing plan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Intellectual Property' (IP)?", "options" => ["Owning a house", "Creations of the mind (inventions, designs, logos) protected by law", "A fast computer", "A smart person"], "ans" => 1, "xp" => 180],
                ["q" => "What does 'Trademark' protect?", "options" => ["An invention", "Brand names and logos", "A book's text", "A secret recipe"], "ans" => 1, "xp" => 180],
                ["q" => "What does 'Copyright' protect?", "options" => ["A logo", "Original creative works (writing, music, art)", "A business name", "A machine"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'CIPC' in South Africa?", "options" => ["A type of bank", "Companies and Intellectual Property Commission (where you register businesses)", "A police department", "A marketing agency"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'PTY (LTD)'?", "options" => ["A public company", "Proprietary Limited (a private company structure)", "A non-profit", "A sports club"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Shareholder'?", "options" => ["A person who works for free", "A person or institution that owns at least one share of a company's stock", "A manager", "A customer"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Articles of Association'?", "options" => ["A list of products", "A document that defines a company's purpose and the duties of its directors", "A marketing flyer", "An invoice"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Compliance'?", "options" => ["Being friendly", "The act of obeying laws, regulations, and rules", "A type of advertising", "Hiring staff"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Tax Clearance'?", "options" => ["Paying no tax", "A document from the tax authority showing that a company's tax affairs are in order", "A type of bank loan", "A secret password"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'UI' in product design?", "options" => ["User Interface (how the product looks)", "User Interaction", "Universal Interest", "Unit Integration"], "ans" => 0, "xp" => 250],
                ["q" => "What is 'UX'?", "options" => ["User Experience (how the user feels using the product)", "Unit Exercise", "Universal Exchange", "User Extra"], "ans" => 0, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'NDA'?", "options" => ["New Digital Account", "Non-Disclosure Agreement (to keep secrets)", "No Data Allowed", "National Debt Agency"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Prototyping'?", "options" => ["Building a perfect product", "Creating a preliminary version of a product to test a concept", "Mass manufacturing", "Advertising"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Market Entry' strategy?", "options" => ["Opening the door", "The planned method of delivering goods or services to a new target market", "A type of tax", "Hiring a manager"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Product Life Cycle'?", "options" => ["The time it takes to build a product", "The stages a product goes through from launch to being taken off the market", "The batteries in a product", "A marketing flyer"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Vetting'?", "options" => ["A medical check for pets", "The process of performing a background check on someone before hiring or partnering", "Cleaning an office", "Selling shares"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Employment Equity'?", "options" => ["Paying everyone the same", "Ensuring equal opportunities for people from designated groups in the workplace", "Giving everyone a desk", "A type of tax"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'B-BBEE'?", "options" => ["A type of bee", "Broad-Based Black Economic Empowerment", "Business Building and Economic Entry", "Basic Business and Employee Equity"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Board of Directors'?", "options" => ["The people who answer the phones", "A group of individuals elected to represent shareholders and oversee the company", "A list of customers", "A marketing team"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 2: FINANCE & GROWTH (Maps 77-80)
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 2,
        "title" => "Map 77: Entrepreneurial Finance", "desc" => "Manage cash flow, burn rate, and financial projections.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Cash Flow'?", "options" => ["Money in a river", "The total amount of money being transferred in and out of a business", "Total profit", "A bank loan"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Burn Rate'?", "options" => ["How fast the office is heating", "The rate at which a new company uses up its startup capital before generating positive cash flow", "The speed of sales", "The cost of electricity"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Runway' in a startup?", "options" => ["A path for airplanes", "The amount of time the business has until it runs out of money (Capital / Burn Rate)", "The size of the office", "A marketing plan"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Seed Capital'?", "options" => ["Money for gardening", "The initial funding used when starting a business", "A government tax", "Profit from sales"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Equity' in a business?", "options" => ["Being fair", "Ownership interest in a corporation (shares)", "A type of debt", "A bank statement"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Balance Sheet'?", "options" => ["A sheet for balancing", "A financial statement that reports assets, liabilities, and equity", "A list of daily sales", "A marketing flyer"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Profit and Loss' (P&L) statement?", "options" => ["A list of all users", "A summary of the revenues, costs, and expenses incurred during a specific period", "A bank loan document", "An invoice"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Gross Margin'?", "options" => ["Profit after tax", "Revenue minus Cost of Goods Sold (COGS)", "Total revenue", "The CEO's salary"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Break-even Point'?", "options" => ["Breaking a bone", "The point at which total revenue equals total costs", "Making a lot of money", "Losing everything"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Working Capital'?", "options" => ["Capital for a job", "The difference between current assets and current liabilities", "The office building", "A bank loan"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'ROI'?", "options" => ["Rate of Interest", "Return on Investment", "Regional Office Income", "Regular Office Index"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Accounts Receivable'?", "options" => ["Money you owe", "Money owed to the business by its customers", "The bank account", "Payroll"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Accounts Payable'?", "options" => ["Money customers owe you", "Money the business owes to its suppliers", "Total revenue", "Petty cash"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Depreciation'?", "options" => ["Gaining value", "The decrease in value of an asset over time", "A type of tax", "Saving money"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Pro Forma' statement?", "options" => ["A document for pros", "A financial projection based on assumptions and hypothetical scenarios", "A legal contract", "A receipt"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Valuation'?", "options" => ["A medical test", "The process of determining the present worth of an asset or a company", "The company name", "A marketing plan"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Debt Financing'?", "options" => ["Selling shares", "Borrowing money that must be repaid with interest", "Winning a prize", "Spending profit"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Equity Financing'?", "options" => ["Borrowing from a bank", "Raising capital by selling shares of the company", "Paying taxes", "A salary increase"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Capital Expenditure' (CapEx)?", "options" => ["Money for lunch", "Funds used by a company to acquire, upgrade, and maintain physical assets", "Daily utility bills", "Taxes"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Operating Expenditure' (OpEx)?", "options" => ["Buying a building", "The day-to-day expenses to keep the business running (rent, salaries)", "Selling shares", "Investing in stocks"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Liquidity'?", "options" => ["Being a liquid", "How quickly an asset can be converted into cash", "Being very fast", "Working underwater"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Financial Modeling'?", "options" => ["Fashion for bankers", "The task of building an abstract representation of a real world financial situation", "Drawing a bank", "Buying software"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Financial Audit'?", "options" => ["A large room", "An objective examination and evaluation of the financial statements of an organization", "A marketing plan", "Hiring staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'SaaS' metrics like LTV?", "options" => ["Lifetime Value (total revenue from one customer)", "Large Total Volume", "Legal Tax Value", "Lower Tier Version"], "ans" => 0, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 2,
        "title" => "Map 78: Branding & Digital Presence", "desc" => "Create a voice and identity that attracts and keeps customers.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Brand'?", "options" => ["Just a logo", "The perception and emotional response customers have toward a company", "The company name only", "An advertisement"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'SEO'?", "options" => ["Searching for Every Option", "Search Engine Optimization", "Secure Entry Office", "System Entry Order"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Social Media Marketing'?", "options" => ["Talking to friends", "Using social platforms to connect with your audience and build your brand", "Watching videos", "Deleting comments"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Content Marketing'?", "options" => ["Selling data", "Creating and sharing valuable free content to attract and convert prospects", "Buying advertisements", "Printing flyers"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Brand Identity'?", "options" => ["The CEO's ID card", "The visible elements of a brand, such as color, design, and logo", "A secret code", "A list of employees"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Call to Action' (CTA)?", "options" => ["A phone call", "A prompt that tells the user to perform a specific action (e.g., 'Buy Now')", "The title of a page", "A thank you message"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Email Marketing'?", "options" => ["Sending spam", "Sending commercial messages to a group of people using email", "Writing a letter", "Calling a customer"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Brand Awareness'?", "options" => ["Knowing the rules", "The extent to which customers are able to recall or recognize a brand", "Being smart", "Reading the news"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'PPC'?", "options" => ["Pay Per Click (advertising)", "Private Project Code", "Price Per Customer", "Public Phone Call"], "ans" => 0, "xp" => 210],
                ["q" => "What is an 'Influencer'?", "options" => ["A boss", "A person who has the power to affect the purchasing decisions of others", "A fast computer", "A government official"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Engagement Rate'?", "options" => ["A wedding", "A metric that measures the level of interaction an audience has with your content", "The speed of sales", "The number of employees"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'User-Generated Content' (UGC)?", "options" => ["Professional news", "Content created by customers rather than the brand (e.g., reviews)", "A software code", "A textbook"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Landing Page'?", "options" => ["An airplane runway", "A standalone web page created specifically for a marketing campaign", "The home page", "A deleted page"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Conversion Rate'?", "options" => ["Changing money", "The percentage of users who take a desired action (e.g., making a purchase)", "A type of translation", "A salary increase"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Backlinking'?", "options" => ["Walking backwards", "When one website links to another (important for SEO)", "A broken link", "A type of virus"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Keywords'?", "options" => ["Passwords", "Specific words and phrases people use in search engines", "The title of a book", "A list of managers"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Brand Loyalty'?", "options" => ["Liking a company", "The tendency of consumers to continuously purchase one brand's products over others", "Buying things for free", "A type of discount"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Viral Marketing'?", "options" => ["A computer virus", "A method of marketing where consumers are encouraged to share information about a product via the internet", "A hospital ad", "A slow marketing plan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Omnichannel'?", "options" => ["Using one phone", "Providing a seamless customer experience across all channels (Online, Store, Phone)", "A new TV station", "A marketing award"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Affiliate Marketing'?", "options" => ["Selling to family", "A performance-based marketing where a business rewards affiliates for each visitor or customer brought", "A type of loan", "A bank statement"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Retargeting'?", "options" => ["Aiming a gun", "Showing ads to people who have already visited your website", "Finding new customers", "Deleting old ads"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Copywriting'?", "options" => ["Copying a book", "The act of writing text for the purpose of advertising or other forms of marketing", "A legal right", "Typing fast"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Aesthetics' in branding?", "options" => ["The office furniture", "The visual principles of beauty and taste in design", "The price of the product", "The marketing plan"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Niche' market?", "options" => ["A hole in the wall", "A small, specialized segment of the market for a particular kind of product or service", "The whole world", "A type of store"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 2,
        "title" => "Map 79: Sales & Networking", "desc" => "Close deals, build partnerships, and expand your reach.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Sales Funnel'?", "options" => ["A tool for pouring oil", "The journey potential customers go through on the way to purchase", "A list of employees", "A type of factory"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Networking'?", "options" => ["Connecting computers", "Building and maintaining relationships with other professionals", "Using social media only", "Talking to yourself"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Prospect'?", "options" => ["A gold miner", "A potential customer who has been qualified", "A current employee", "A competitor"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Lead Generation'?", "options" => ["Creating metal", "The process of identifying and cultivating potential customers", "Hiring staff", "Selling products"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Closing the Deal'?", "options" => ["Locking the office", "The final stage of the sales process where the customer makes a purchase", "Ending a meeting", "Sending an email"], "ans" => 1, "xp" => 180],
                ["q" => "What is an 'Elevator Pitch'?", "options" => ["A ride in an elevator", "A short, persuasive speech used to spark interest in what your organization does", "A 50-page document", "A movie script"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'B2B Sales'?", "options" => ["Selling to individuals", "Selling products or services from one business to another", "A type of bank loan", "Selling at a market"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Cold Calling'?", "options" => ["A call in winter", "Making an unsolicited visit or telephone call to someone in an attempt to sell goods", "Hanging up", "A technical support call"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Referral'?", "options" => ["A mistake", "When a customer recommends your business to another person", "A type of tax", "A bank loan"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'CRM'?", "options" => ["Company Relationship Management", "Customer Relationship Management", "Common Resource Model", "Corporate Record Management"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Handling Objections'?", "options" => ["Arguing with a client", "Addressing customer concerns and barriers to purchase", "Ignoring the client", "Crying during a sale"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Social Selling'?", "options" => ["Selling at a party", "Using social media to find, connect with, and nurture sales prospects", "Hiring friends", "Giving products for free"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Active Listening' in sales?", "options" => ["Nodding without listening", "Fully focusing on what is being said to understand the customer's needs", "Talking while they talk", "Ignoring the client"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Upselling'?", "options" => ["Selling things on a hill", "Encouraging a customer to purchase a more expensive version of a product", "Selling things for free", "Ignoring a customer"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Cross-selling'?", "options" => ["Selling to angry customers", "Selling related or complementary products to a customer", "Selling to a competitor", "Selling in a different language"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Soft Sell'?", "options" => ["Selling a pillow", "An advertising or sales approach that is subtle and low-pressure", "Selling for a low price", "Hiring a salesman"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Strategic Partnership'?", "options" => ["A friendship", "A mutually beneficial relationship between two companies to reach a common goal", "A legal battle", "A bank loan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Sales Qualification'?", "options" => ["Getting a degree", "Determining if a lead is likely to become a customer", "Hiring staff", "Selling products"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Negotiation'?", "options" => ["Fighting", "A discussion aimed at reaching an agreement", "A command", "A secret"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Empathy' in sales?", "options" => ["Feeling sorry", "Understanding the customer's perspective and feelings", "Agreeing with a lie", "Being loud"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Value-Based Selling'?", "options" => ["Selling for the lowest price", "Selling based on the benefits and outcomes the customer receives", "Selling expensive items only", "Giving things for free"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Sales Velocity'?", "options" => ["The speed of a car", "The speed at which leads move through your sales funnel and generate revenue", "The volume of a voice", "A type of tax"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Rapport'?", "options" => ["A legal report", "A harmonious connection and mutual trust with a person", "The end of a shift", "A bill"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Golden Rule' of networking?", "options" => ["Ask for money first", "Give more value than you take", "Hand out 1,000 flyers", "Talk as fast as you can"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 2,
        "title" => "Map 80: Operations & Supply Chain", "desc" => "Manage the engine room of your business.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Operations Management'?", "options" => ["Fixing machinery", "Designing and controlling the process of production and business operations", "Managing the cleaning staff", "Hiring employees"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Supply Chain'?", "options" => ["A chain made of metal", "The network of individuals and companies involved in creating and delivering a product", "A list of office keys", "A type of software"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Logistics'?", "options" => ["Logic puzzles", "The detailed coordination of a complex operation involving many people or supplies", "Writing an agenda", "Designing a logo"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Inventory'?", "options" => ["A discovery", "A complete list of items such as property or goods in stock", "A type of tax", "A bank account"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Just-in-Time' (JIT) production?", "options" => ["Being late", "A strategy to increase efficiency by receiving goods only as they are needed in production", "A fast computer", "A government law"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Outsourcing'?", "options" => ["Working outside the office", "Hiring an external party to perform services", "Selling office equipment", "Hiring more managers"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Quality Control'?", "options" => ["Being nice to staff", "A system of maintaining standards in manufactured products", "A type of sales", "Hiring an accountant"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Procurement'?", "options" => ["Cleaning the office", "The act of obtaining goods or services for business purposes", "Selling products", "Hiring staff"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Workflow'?", "options" => ["The speed of the internet", "The sequence of processes through which a piece of work passes from start to finish", "Working in a gym", "The office air conditioning"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Vendor Management'?", "options" => ["Managing a shop", "The process of managing relationships with third-party suppliers", "Hiring new employees", "Designing products"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Scalability'?", "options" => ["How heavy a product is", "The ability of a system to handle a growing amount of work by adding resources", "The number of stairs in the building", "The color of the logo"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Standard Operating Procedure' (SOP)?", "options" => ["A type of software", "Established step-by-step instructions for routine activities", "The company's legal name", "The layout of the office"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Lead Time'?", "options" => ["The time spent leading", "The time between the start and completion of a production process", "The duration of a meeting", "A type of clock"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Customer Fulfillment'?", "options" => ["Making customers happy", "The process of receiving, packaging, and shipping orders to customers", "Hiring more staff", "Asking for money"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'E-commerce'?", "options" => ["Selling only books", "Commercial transactions conducted electronically over the internet", "A type of engine", "A fast computer"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Cloud Computing' for ops?", "options" => ["Working in the rain", "Using remote servers to store and process data rather than a local server", "A weather report", "A type of airplane"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Automation'?", "options" => ["A type of car", "The use of technology to perform tasks with reduced human assistance", "Fixing machinery", "A new marketing plan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Risk Management' in ops?", "options" => ["Taking risks", "Identifying and preparing for potential operational failures", "Buying insurance", "Ignoring problems"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Asset Management'?", "options" => ["Managing a bank account", "The process of tracking and managing the physical and digital assets of an organization", "A type of backup", "A software update"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Lean Manufacturing'?", "options" => ["A small factory", "A systematic method for waste minimization within a manufacturing system", "Making low-calorie food", "Working alone"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Six Sigma'?", "options" => ["A brand of car", "A set of techniques for process improvement and reducing errors", "A programming language", "A type of phone"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Agile' in operations?", "options" => ["Being very fast", "An iterative approach that focuses on flexibility and speed", "A programming language", "A type of server"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Bottleneck'?", "options" => ["A part of a bottle", "A point in a process where the workflow is slowed down or stopped", "A very fast PC", "A type of cooling"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Operational Efficiency'?", "options" => ["Being nice to everyone", "The ability of an organization to deliver products in the most cost-effective manner", "Having the most employees", "Using the newest computers"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 3: STRATEGY & CAPSTONE (Maps 81-84)
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 3,
        "title" => "Map 81: Funding & Capital Raising", "desc" => "Pitch to investors and secure the resources to grow.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Venture Capital'?", "options" => ["A bank loan", "Funding provided to startups that have high growth potential", "A government grant", "Personal savings"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Angel Investor'?", "options" => ["A religious figure", "A wealthy individual who provides capital for a business start-up", "A manager", "A government agency"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Crowdfunding'?", "options" => ["Borrowing from one person", "Raising small amounts of money from a large number of people, typically via the internet", "A bank loan", "Selling products at a market"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Equity' in investment?", "options" => ["Being fair", "Giving up a percentage of ownership in exchange for capital", "A type of debt", "A bank statement"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Pitch Deck'?", "options" => ["A pack of cards", "A brief presentation used to provide an overview of your business plan to investors", "A 50-page document", "A movie script"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Valuation'?", "options" => ["A medical test", "Determining the present worth of a company", "The company name", "A marketing plan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Burn Rate'?", "options" => ["Heating an office", "The rate at which a company is losing money", "The speed of sales", "The cost of electricity"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Bootstrapping'?", "options" => ["Buying shoes", "Self-funding your startup using your own savings and revenue", "Getting a massive loan", "Selling shares"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Due Diligence'?", "options" => ["Working hard", "The investigation an investor does before making an investment", "Paying taxes", "Hiring a manager"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Series A' round?", "options" => ["The first ever money", "The first significant round of venture capital financing", "A bank loan", "A small prize"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Dilution'?", "options" => ["Adding water", "When a founder's ownership percentage decreases as new shares are issued to investors", "Selling the company", "Hiring staff"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Convertible Debt'?", "options" => ["A car loan", "A loan that can be converted into equity at a later date", "A gift", "A type of tax"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is an 'Exit Strategy'?", "options" => ["The door to the building", "A founder's plan to sell their ownership in a company to investors or another company", "A fire drill", "Quitting your job"], "ans" => 1, "xp" => 250],
                ["q" => "What is an 'IPO'?", "options" => ["Internal Project Order", "Initial Public Offering (selling shares to the public)", "International Price Office", "Instant Profit Only"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Acquisition'?", "options" => ["Buying a competitor", "When one company purchases most or all of another company's shares", "Starting a business", "Closing a shop"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Term Sheet'?", "options" => ["A dictionary", "A non-binding document outlining the basic terms and conditions of an investment", "A school test", "A list of users"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Pre-money' valuation?", "options" => ["Money in the bank", "The value of a company before it receives investment", "The profit from last year", "A bank loan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Post-money' valuation?", "options" => ["Money after tax", "The value of a company after it receives investment (Pre-money + Investment)", "The total sales", "The CEO's salary"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Burn Multiple'?", "options" => ["Burning a forest", "A metric that evaluates how much a startup is burning in order to generate net new revenue", "A type of tax", "A salary bonus"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Smart Money'?", "options" => ["Money with brains", "Capital that comes with valuable advice, connections, and expertise from investors", "A bank loan", "Winning a prize"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Cap Table'?", "options" => ["A table for hats", "A spreadsheet showing the ownership stakes in a company", "A financial audit", "A list of office furniture"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Vesting'?", "options" => ["Wearing a vest", "The process of earning the right to shares over time", "Selling shares quickly", "Giving away profit"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Down Round'?", "options" => ["A sad song", "A round of financing where a company's valuation is lower than its previous round", "A type of tax", "Closing the company"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Valley of Death'?", "options" => ["A scary movie", "The period between starting a business and generating positive cash flow when risk is highest", "A failed startup", "A type of marketing"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 3,
        "title" => "Map 82: Growth & Scaling", "desc" => "Take your business from a small team to a global player.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Growth Hacking'?", "options" => ["Hacking into computers", "Using low-cost, creative strategies to help a business acquire as many customers as possible", "Hiring more people", "Buying ads"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Scaling'?", "options" => ["Weighing a product", "The process of growing a business's revenue much faster than its costs", "Buying a larger office", "Increasing the font size"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Viral Loop'?", "options" => ["A computer virus", "A mechanism where users invite others, creating exponential growth", "A circular logic", "A type of marketing"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Customer Acquisition Cost' (CAC)?", "options" => ["The salary of a salesman", "The total cost of convincing a potential customer to buy", "The price of the product", "Office rent"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Lifetime Value' (LTV)?", "options" => ["The weight of a client", "The total revenue a business expects from a single customer over time", "The duration of a sale", "The age of the founder"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Blitzscaling'?", "options" => ["A type of weather", "Prioritizing speed over efficiency in an environment of uncertainty", "Slow and steady growth", "Closing the business"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Product-Led Growth'?", "options" => ["Marketing leading growth", "When the product itself is the main driver of customer acquisition and retention", "Sales leading growth", "Hiring more managers"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Global Expansion'?", "options" => ["Buying a map", "Entering international markets to grow a business", "Moving to a new city", "Hiring remote staff"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Franchising'?", "options" => ["A type of law", "Granting a license to another to trade under your brand name for a fee", "Selling your company", "Starting a new business"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Market Saturation'?", "options" => ["Getting wet", "When the volume of a product in a market has reached its maximum potential", "A type of marketing", "Buying a competitor"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Retention Rate'?", "options" => ["Firing people", "The percentage of customers who continue to use a service over time", "Changing shifts", "Promoting people"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Organic Growth'?", "options" => ["Gardening", "Growth achieved through internal expansion and sales rather than acquisition", "Buying a competitor", "Getting a loan"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Inorganic Growth'?", "options" => ["Artificial food", "Growth through mergers or acquisitions", "Slow sales", "Hiring an intern"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Strategic Pivot'?", "options" => ["Closing the business", "A fundamental change in the company's direction to foster growth", "A type of dance", "A small change"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Sustainability' in growth?", "options" => ["Growing forever", "Growing at a pace that can be maintained without crashing", "Saving money", "A type of marketing"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Leverage'?", "options" => ["A tool for moving heavy things", "Using existing resources or debt to increase the potential return of an investment", "Being loud", "Working alone"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Human Capital' in scaling?", "options" => ["Money owned by humans", "The collective skills and knowledge of the team needed for growth", "The company building", "A list of employees"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Organizational Culture' during scaling?", "options" => ["The art in the office", "The shared values that must be maintained even as the company grows", "The company rules", "The dress code"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Leadership Transition'?", "options" => ["Hiring a new intern", "The process of moving from a founder-led to a manager-led organization", "Changing clothes", "Moving offices"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Bureaucracy' in large startups?", "options" => ["A type of desk", "Complex systems and rules that can slow down growth and innovation", "A fast computer", "A government law"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Exit' in entrepreneurship?", "options" => ["The door", "Selling the company or going public (IPO)", "Quitting your job", "A fire drill"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Acqui-hiring'?", "options" => ["Hiring a new manager", "Acquiring a company primarily for its talented employees", "Firing a team", "A type of training"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Synergy' in mergers?", "options" => ["Fighting", "The combined power of two companies being greater than the sum of their parts", "A type of software", "Buying a competitor"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Global Mindset'?", "options" => ["Buying a map", "The ability to recognize and adapt to cultural and economic differences worldwide", "Knowing every country", "Speaking many languages"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 3,
        "title" => "Map 83: Ethics, Leadership & Future", "desc" => "Lead with integrity and prepare for the next wave of technology.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Entrepreneurial Ethics'?", "options" => ["Doing math at work", "The application of moral principles to business behavior and decisions", "The company's rules on dress code", "A type of accounting"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Corporate Social Responsibility' (CSR)?", "options" => ["Working on the weekend", "A model where companies operate in ways that enhance society", "Paying taxes", "A marketing strategy"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Transparency'?", "options" => ["A clear window", "Operating in a way that is easy for others to see what actions are performed", "A type of software", "Hiding the truth"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Integrity' in leadership?", "options" => ["Being smart", "The quality of being honest and having strong moral principles", "Knowing the rules", "Following orders"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Social Impact'?", "options" => ["Being famous", "The effect an organization's actions have on the well-being of the community", "A type of advertising", "The number of followers"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Sustainability'?", "options" => ["Working forever", "Meeting current needs without compromising future generations", "Saving money", "A type of marketing"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Conscious Capitalism'?", "options" => ["Greed is good", "A philosophy that businesses should serve all stakeholders, not just owners", "Being unconscious", "A government law"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Triple Bottom Line'?", "options" => ["Profit, Profit, Profit", "People, Planet, Profit", "Sales, Marketing, IT", "Money, Power, Fame"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Servant Leadership'?", "options" => ["Being a servant", "A leadership philosophy where the main goal of the leader is to serve", "A strict leader", "A leader who does no work"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Emotional Intelligence' (EQ)?", "options" => ["Being smart", "The capacity to be aware of and handle interpersonal relationships judiciously", "Being emotional", "Crying at work"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Delegation'?", "options" => ["Doing all the work", "Entrusting a task or responsibility to another person", "Hiring new staff", "Quitting a job"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Team Building'?", "options" => ["Building a house", "The process of creating a team that works well together", "A type of software", "A team meeting"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Conflict Resolution'?", "options" => ["Starting a fight", "The process of finding a peaceful solution to a disagreement", "Ignoring the problem", "Telling the boss"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Change Management'?", "options" => ["Counting cash", "The process of transitioning individuals or teams to a future state", "Changing the office", "Updating software"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Mentorship'?", "options" => ["Teaching a class", "A relationship where a more experienced person guides a less experienced one", "Paying for a tutor", "Hiring a new manager"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Authentic Leadership'?", "options" => ["Being fake", "Leading in a way that is true to one's own values and personality", "Following a script", "Being loud"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Disruptive Technology'?", "options" => ["Technology that breaks", "An innovation that significantly alters the way industries operate", "A loud computer", "A type of firewall"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Artificial Intelligence' (AI) in startups?", "options" => ["A robot person", "Simulation of human intelligence by machines to improve business processes", "A fast computer", "A fake computer"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Blockchain' for entrepreneurship?", "options" => ["A metal chain", "A decentralized ledger that can improve transparency and trust in transactions", "A security fence", "A backup system"], "ans" => 1, "xp" => 300],
                ["q" => "What is the 'Fourth Industrial Revolution'?", "options" => ["A war", "The fusion of technologies blurring the lines between physical, digital, and biological spheres", "A history lesson", "A new computer brand"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Future-Proofing'?", "options" => ["Buying a time machine", "Anticipating the future and developing methods of minimizing the effects of shocks", "Saving money only", "Hiring more staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Inclusive Innovation'?", "options" => ["Hiring everyone", "Innovation that includes and benefits people who are marginalized or excluded", "A private secret", "A type of code"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Economic Sovereignty'?", "options" => ["Owning a bank", "The power of a country or group to make its own economic decisions", "Free data", "High-quality data"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Visionary Leadership'?", "options" => ["Being a boss", "The ability to see the potential for change and inspire others to achieve a common goal", "Having good eyes", "Following orders"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $entrep_course_id, "category_id" => 3,
        "title" => "Map 84: Capstone: The Pitch Deck", "desc" => "The final exam. Bring it all together in one world-class business pitch.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: You have an idea but no money. What is the BEST first step?", "options" => ["Ask for a $1M loan", "Build an MVP and validate the idea with potential customers", "Rent a fancy office", "Hire 10 employees"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: Your burn rate is $10k/month and you have $20k in the bank. What is your runway?", "options" => ["2 months", "1 year", "6 months", "10 days"], "ans" => 0, "xp" => 200],
                ["q" => "SCENARIO: A customer loves your product but wants a feature you didn't plan. What is this?", "options" => ["A distraction", "Valuable feedback for the next iteration", "A reason to quit", "A mistake"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You need to pitch to an investor in 2 minutes. What do you use?", "options" => ["A 50-page report", "An Elevator Pitch", "A silent video", "A list of every expense"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: Your product isn't selling. What is the entrepreneurial term for changing direction?", "options" => ["Failing", "Pivoting", "Quitting", "Closing"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You want to ensure you aren't personally liable for company debts. What structure do you use?", "options" => ["Sole Proprietor", "PTY (LTD) / Private Company", "Partnership", "Informal group"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You need to protect your unique invention from being copied. What do you apply for?", "options" => ["A Trademark", "A Patent", "A Copyright", "A secret"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You are spending $100 to get one customer who spends $500. Is this good unit economics?", "options" => ["No, you lost money", "Yes, LTV is higher than CAC", "It depends on the weather", "No, CAC is too low"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: You are hiring your first employee. What should you prioritize?", "options" => ["Low salary", "Cultural fit and multi-skilled adaptability", "Family members only", "Being the boss"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: A competitor launches a similar product for half the price. What analysis do you do?", "options" => ["SWOT and Competitive Analysis", "Give up immediately", "Call the police", "Lower your price blindly"], "ans" => 0, "xp" => 280],
                ["q" => "SCENARIO: You want to grow exponentially using your current users. What do you build?", "options" => ["A billboard", "A Viral Loop / Referral System", "A larger factory", "A new website"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You need to raise $500k for expansion. Which group do you pitch to?", "options" => ["Your bank only", "Venture Capitalists or Angel Investors", "Your employees", "Random people on the street"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: You found a way to cut costs while doubling output. What is this called?", "options" => ["Operational Efficiency", "Marketing", "Branding", "Ethics"], "ans" => 0, "xp" => 350],
                ["q" => "SCENARIO: You discover your business is harming the environment. What is the ethical action?", "options" => ["Hide it", "Implement a CSR / Sustainability strategy", "Close the business", "Blame the supplier"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You want to reach a global audience with low costs. What do you prioritize?", "options" => ["Opening 10 offices", "Digital Marketing and E-commerce", "Hiring 100 sales people", "Printing 1 million flyers"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are preparing a pitch deck. What should the first slide be?", "options" => ["The budget", "The Problem and Value Proposition", "The CEO's photo", "A list of all tools"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "MASTER FINAL: What is the primary focus of an effective Entrepreneur?", "options" => ["Making everyone happy", "Problem-solving, value creation, and sustainable growth", "Buying the best computers", "Having the largest office"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: In high-level scaling, what determines the success of the company?", "options" => ["The number of employees", "Strategic alignment, culture, and operational efficiency", "Having a lot of money", "Hiring a famous CEO"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: Why is validation more important than the idea itself?", "options" => ["It's not", "Because an idea is worthless if no one is willing to pay for it", "Because ideas are free", "Because validation is fun"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: What is 'Grit' in entrepreneurship?", "options" => ["Sand", "Passion and perseverance for long-term goals", "Being angry", "Working 1 hour a day"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "EXAM: How do Marketing, Finance, and Ops interact in a startup?", "options" => ["They are separate", "They are interdependent; Marketing finds users, Ops delivers value, and Finance manages the capital", "They compete for resources", "They only interact during parties"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the most critical factor for a startup's long-term survival?", "options" => ["A lot of cash", "Adaptability and Product-Market Fit", "A famous founder", "The best logo"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the true goal of an entrepreneur's journey?", "options" => ["Getting a job", "Building a scalable and repeatable business model that creates value", "Winning a prize", "Hiring friends"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the hallmark of a world-class founder?", "options" => ["Being aggressive", "Vision, resilience, and the ability to empower a high-performing team", "Making all decisions alone", "A high salary"], "ans" => 1, "xp" => 600]
            ]]
        ]
    ]
];

// ==========================================================
// FINANCIAL ADMINISTRATION CURRICULUM (Maps 85-96)
// ==========================================================
$fin_maps = [
    // CATEGORY 1: TRANSACTIONAL OPERATIONS (Maps 85-88)
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 1,
        "title" => "Map 85: Intro to Financial Admin", "desc" => "The role of the finance office, ethics, and basic accounting cycles.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary objective of Financial Administration?", "options" => ["To hide money from the government", "To record, manage, and report on the financial health of an entity", "To only pay the CEO", "To fix computer hardware"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Accounting Equation'?", "options" => ["Revenue - Tax = Profit", "Assets = Liabilities + Equity", "Cash + Bank = Debt", "Income x 2 = Wealth"], "ans" => 1, "xp" => 150],
                ["q" => "Which of these is considered a 'Current Asset'?", "options" => ["A building", "Cash and Bank balances", "Long-term loans", "The company's brand name"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Double-Entry' bookkeeping?", "options" => ["Writing the same number twice", "The system where every transaction affects at least two accounts (Debit and Credit)", "Hiring two accountants", "Printing two copies of every receipt"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What does 'GAAP' stand for?", "options" => ["General Asset and Account Plan", "Generally Accepted Accounting Principles", "Global Accounting and Audit Policy", "Government Approved Account Process"], "ans" => 1, "xp" => 180],
                ["q" => "What is the 'Fiscal Year'?", "options" => ["A year with 13 months", "A 12-month period used for financial reporting", "The calendar year only", "The time it takes to audit a firm"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Liability'?", "options" => ["Something you own", "Something the business owes to an outside party", "Total sales revenue", "An employee's salary"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Equity'?", "options" => ["The amount of debt", "The owner's residual interest in the assets after deducting liabilities", "The total cash in the safe", "A type of tax"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the 'General Ledger'?", "options" => ["A book of company rules", "The master record of all the company's financial transactions", "A list of employee names", "A marketing plan"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Debit' (DR)?", "options" => ["An entry on the right side of an account", "An entry on the left side of an account", "A type of bank card only", "A refund from a customer"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Credit' (CR)?", "options" => ["An entry on the left side of an account", "An entry on the right side of an account", "Getting a loan", "The same as revenue"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Trial Balance' used for?", "options" => ["To calculate tax", "To ensure total debits equal total credits", "To see who is the best employee", "To pay suppliers"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Depreciation'?", "options" => ["An increase in value", "The systematic reduction of the recorded cost of a fixed asset over its useful life", "A bank fee", "Hiding money"], "ans" => 1, "xp" => 250],
                ["q" => "What are 'Fixed Assets'?", "options" => ["Cash in hand", "Long-term resources like machinery, buildings, and vehicles", "Stationery and pens", "Monthly sales"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Liquidity'?", "options" => ["Water in the office", "The ability of a business to pay its short-term debts", "The total value of a company", "The speed of the internet"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Chart of Accounts'?", "options" => ["A map of the office", "A list of all accounts used by a business to record transactions", "A list of customers", "A graph of sales"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the 'Accrual' basis of accounting?", "options" => ["Recording transactions only when cash moves", "Recording transactions when they occur, regardless of when cash is exchanged", "Paying taxes early", "Ignoring small expenses"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Cash-based' accounting?", "options" => ["Accounting with no computers", "Recording income and expenses only when cash is actually received or paid", "Using only physical coins", "Hiding money in a safe"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Materiality' in finance?", "options" => ["The type of paper used", "Whether an omission or misstatement of info could influence decisions", "The weight of the safe", "The office furniture"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Financial Audit'?", "options" => ["A type of meeting", "An independent examination of financial statements to ensure accuracy and compliance", "Counting the inventory", "Buying new software"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Ethical Conduct' in finance?", "options" => ["Following the boss blindly", "Maintaining integrity, objectivity, and confidentiality", "Making as much profit as possible at any cost", "Deleting records to save space"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Prudence' in accounting?", "options" => ["Being very fast", "Being cautious and not overstating assets or income", "Paying the highest tax", "Ignoring risks"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Transparency'?", "options" => ["A glass window", "Clear and open disclosure of financial information", "Hiding debt from investors", "Using secret codes"], "ans" => 1, "xp" => 400],
                ["q" => "What is the primary role of an 'Administrator' in finance?", "options" => ["To sign all checks", "To facilitate, organize, and ensure the accuracy of financial processes", "To trade on the stock market", "To fix the printer"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 1,
        "title" => "Map 86: Banking & Cash Management", "desc" => "Reconciliations, internal controls, and electronic payments.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Bank Reconciliation'?", "options" => ["Closing a bank account", "Matching the bank statement balance with the company's internal records", "Asking for a loan", "Changing the bank password"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Petty Cash'?", "options" => ["Money for pets", "A small amount of physical cash for minor office expenses", "The CEO's bonus", "Money in a bank vault"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'EFT'?", "options" => ["Electronic Fund Transfer", "Easy Financial Time", "Every Fund Total", "End Fast Transaction"], "ans" => 0, "xp" => 150],
                ["q" => "What is 'Float' in a cash drawer?", "options" => ["Money in a river", "The starting amount of cash used to provide change to customers", "The profit for the day", "A type of tax"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Debit Order'?", "options" => ["A customer paying cash", "An instruction to a bank to pay a third party a recurring amount from your account", "A refund to a supplier", "Closing the business"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Cash Flow' management?", "options" => ["Counting coins", "Ensuring there is enough cash on hand to meet obligations as they fall due", "Making the most profit", "Hiding money"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Stop Payment'?", "options" => ["Quitting your job", "An instruction to a bank not to honor a specific check or payment", "Unplugging the ATM", "A bank holiday"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Internal Control' over cash?", "options" => ["A locked door", "Policies like 'separation of duties' to prevent theft and errors", "Having only one person touch money", "Having no records"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Separation of Duties'?", "options" => ["Firing half the staff", "Ensuring the person who handles cash is not the same person who records it", "Working in two offices", "Dividing the profit"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Overdraft'?", "options" => ["A draft in the office", "When you spend more money than is in your bank account", "A type of investment", "A salary bonus"], "ans" => 1, "xp" => 210],
                ["q" => "What is an 'Outstanding Check'?", "options" => ["A check that is very good", "A check written by the company but not yet cleared by the bank", "A fake check", "A check for a million dollars"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Deposit in Transit'?", "options" => ["Money lost in the mail", "A deposit made but not yet reflected on the bank statement", "A bank loan", "Money given to a supplier"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'KYC' in banking?", "options" => ["Keep Your Cash", "Know Your Customer (anti-fraud process)", "Kill Your Costs", "Key Yield Calculation"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Money Laundering'?", "options" => ["Cleaning money with soap", "Disguising the origins of illegally obtained money", "Saving money in a bank", "Spending too much cash"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Swift Code' used for?", "options" => ["Speeding up a PC", "Identifying a specific bank during international transfers", "A secret password", "A type of tax"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Fiduciary' relationship?", "options" => ["Being friends", "A relationship of trust where one party manages money for another", "A legal battle", "Hiring a new intern"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Interest' income?", "options" => ["Liking your job", "Money earned for lending money or keeping it in a savings account", "Total sales", "A type of tax"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Bank Charge'?", "options" => ["A physical charge", "A fee the bank takes for maintaining your account or processing transactions", "A battery for the safe", "A loan from the bank"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Liquidity Ratio'?", "options" => ["A math problem", "A metric used to determine a company's ability to pay off its short-term debts", "The speed of sales", "The office rent"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Cash Equivalents'?", "options" => ["Fake money", "Short-term, highly liquid investments that are easily converted to cash", "A type of coin", "The company's furniture"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Lockbox' system?", "options" => ["A safe in the wall", "A service where a bank receives and processes payments on behalf of a company", "A type of computer code", "A marketing plan"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Float' in banking reconciliation?", "options" => ["Being lazy", "The time it takes for money to move from one account to another", "The size of the bank", "The number of customers"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Positive Pay'?", "options" => ["Paying employees more", "An anti-fraud service where a bank only pays checks that match a provided list", "Winning a prize", "Being happy at work"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Cash Accounting' cycle?", "options" => ["A bike ride", "The process of tracking cash from receipt to disbursement", "A type of tax", "Buying shares"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 1,
        "title" => "Map 87: Invoicing & Accounts Receivable", "desc" => "Master the billing process, credit terms, and debt collection.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'Invoice'?", "options" => ["A receipt", "A document requesting payment for goods or services", "A bank statement", "A marketing flyer"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Accounts Receivable' (AR)?", "options" => ["Money you owe", "Money customers owe to the business", "The company bank account", "Tax paid"], "ans" => 1, "xp" => 150],
                ["q" => "What are 'Credit Terms'?", "options" => ["Names for employees", "The agreed-upon timeframe and conditions for payment (e.g., Net 30)", "A type of bank loan", "The price of the product"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Statement' in AR?", "options" => ["A speech", "A summary of all invoices and payments for a customer over a period", "A legal contract", "A marketing plan"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Aging' of accounts receivable?", "options" => ["Getting old", "Categorizing unpaid invoices by how long they have been outstanding", "Deleting old records", "Giving a discount"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Credit Limit'?", "options" => ["The amount of debt", "The maximum amount of credit a business allows a customer to have", "A bank fee", "The price of an office"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Bad Debt'?", "options" => ["Money you owe", "Money owed to you that is unlikely to be collected", "A type of tax", "A bank loan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Write-off'?", "options" => ["Writing a book", "Removing an uncollectible debt from the accounting records", "Paying a bill", "Giving a bonus"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Credit Note'?", "options" => ["A thank you note", "A document issued by a seller to reduce the amount the buyer owes", "A bank loan", "A marketing flyer"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Pro-forma Invoice'?", "options" => ["A formal invoice", "A preliminary bill sent in advance of a shipment of goods", "A final receipt", "A type of tax"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Dunning'?", "options" => ["A type of food", "The process of methodically communicating with customers to collect debts", "Closing a business", "Hiring staff"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Factoring' of receivables?", "options" => ["Math", "Selling your unpaid invoices to a third party at a discount for immediate cash", "Hiring an accountant", "Deleting records"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Sales Tax' (VAT)?", "options" => ["Profit", "An indirect tax on the consumption of goods and services", "A salary bonus", "A bank fee"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Revenue Recognition'?", "options" => ["Making money", "The accounting principle that determines when revenue is officially recorded", "Hiding profit", "Buying a new PC"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Trade Credit'?", "options" => ["Trading goods", "When a business buys goods and pays for them at a later date", "A bank loan", "A type of share"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Remittance Advice'?", "options" => ["A doctor's note", "A document sent by a customer to a seller informing them that an invoice has been paid", "A marketing flyer", "An invoice"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Collection Ratio'?", "options" => ["A math problem", "A metric showing how well a company is collecting its receivables", "Total sales", "A type of tax"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Provision for Doubtful Debts'?", "options" => ["Hiding money", "An estimation of the amount of accounts receivable that may become bad debt", "A bank account", "Paying tax"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Customer Credit Risk'?", "options" => ["Buying a computer", "The risk that a customer will not be able to pay their bills", "A marketing plan", "Hiring staff"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Automation' in AR?", "options" => ["A type of car", "Using software to automatically generate and send invoices/reminders", "Fixing a phone", "Doing math on paper"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'DSO' (Days Sales Outstanding)?", "options" => ["A type of tax", "The average number of days it takes for a company to collect payment after a sale", "The speed of light", "Total profit"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Recourse' in factoring?", "options" => ["A type of food", "The right of the factor to claim back money if a customer doesn't pay", "Closing a shop", "Hiring a new manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Deferred Revenue'?", "options" => ["Total profit", "Money received for goods or services not yet delivered", "A bank loan", "A salary bonus"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Consignment' sale?", "options" => ["A fast sale", "When a seller sends goods to a buyer but retains ownership until they are sold", "A type of tax", "Hiding inventory"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 1,
        "title" => "Map 88: Accounts Payable & Vendors", "desc" => "Manage suppliers, purchase orders, and outgoing payments.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Accounts Payable' (AP)?", "options" => ["Money customers owe you", "Money the business owes to its suppliers", "Total profit", "A salary bonus"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Purchase Order' (PO)?", "options" => ["A receipt", "A formal request/contract from a buyer to a seller to purchase goods", "A bank statement", "A marketing flyer"], "ans" => 1, "xp" => 150],
                ["q" => "Who is a 'Vendor'?", "options" => ["An employee", "A person or company offering something for sale (a supplier)", "A manager", "A customer"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Disbursement'?", "options" => ["Receiving money", "The act of paying out money from a fund or account", "A type of tax", "Hiding cash"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Three-way Matching'?", "options" => ["Matching 3 people", "Verifying the Purchase Order, Receiving Report, and Vendor Invoice before payment", "Buying 3 items", "Closing 3 accounts"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Vendor Statement'?", "options" => ["A marketing speech", "A report from a supplier showing all open and paid invoices", "A bank loan", "An employee review"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Accrued Expense'?", "options" => ["Money you already paid", "An expense that has been incurred but not yet paid", "Total profit", "A type of tax"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Trade Discount'?", "options" => ["A marketing flyer", "A reduction in the price of a product offered by a seller to a buyer", "A bank fee", "Hiring staff"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Cash Discount' (e.g., 2/10 Net 30)?", "options" => ["Getting money for free", "A discount for paying an invoice early", "A type of tax", "A bank loan"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Blanket Purchase Order'?", "options" => ["A warm PO", "A PO that covers multiple deliveries over a period of time", "A fake PO", "A deleted record"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Expenditure'?", "options" => ["Receiving money", "The action of spending funds", "A type of share", "Hiring a new manager"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Authorized Signatory'?", "options" => ["A person who writes books", "Someone with legal authority to sign documents/payments on behalf of a company", "A junior clerk", "A customer"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'E-procurement'?", "options" => ["Using a courier", "Purchasing goods and services over the internet", "A type of battery", "Hiding records"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Vouching'?", "options" => ["Yelling", "The process of verifying accounting entries against supporting documents", "Writing a diary", "Buying an office"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Use Tax'?", "options" => ["Profit", "A tax on goods purchased from out of state/province for use in the business", "A salary bonus", "A bank fee"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Inventory Turnover'?", "options" => ["A type of pastry", "A ratio showing how many times a company has sold and replaced inventory during a period", "The speed of sales", "The number of employees"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Supply Chain' management?", "options" => ["Managing a safe", "Handling the entire production flow of a good or service", "Hiring staff", "Selling shares"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Master Vendor File'?", "options" => ["A list of employees", "A centralized database of all information about suppliers", "A bank statement", "An invoice"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Capital Commitment'?", "options" => ["Getting married", "A firm obligation to spend money on capital projects in the future", "A type of tax", "Saving money"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Procure-to-Pay' (P2P)?", "options" => ["A fast computer", "The end-to-end process from requesting goods to paying the supplier", "A bank loan", "A type of share"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Conflict of Interest' in AP?", "options" => ["Being rude", "When an employee has a personal interest in a supplier that might affect their objectivity", "A disagreement", "Hiring family"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Vendor Auditing'?", "options" => ["Asking for a gift", "Reviewing a supplier's performance and compliance with contracts", "Cleaning an office", "Selling shares"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Duplicate Payment' prevention?", "options" => ["Paying twice", "Controls to ensure an invoice is not paid more than once", "Getting a refund", "A type of tax"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Strategic Sourcing'?", "options" => ["Buying from a shop", "An approach to supply chain management that formalizes the way info is gathered and used", "Buying the cheapest item only", "Working alone"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 2: COMPLIANCE & REPORTING (Maps 89-92)
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 2,
        "title" => "Map 89: Payroll Administration", "desc" => "Gross pay, deductions, tax compliance, and benefits.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Gross Pay'?", "options" => ["Pay after tax", "The total amount of employee's earnings before any deductions are made", "A type of bonus", "The cost of a building"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Net Pay'?", "options" => ["Total earnings", "The amount an employee takes home after all deductions (Take-home pay)", "Pay before tax", "A bank loan"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'PAYE' in South Africa?", "options" => ["Pay All Your Expenses", "Pay As You Earn (income tax)", "Professional Account Yearly Entry", "Price After Yearly Earnings"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Deduction'?", "options" => ["A math problem", "An amount taken out of an employee's gross pay (tax, pension, etc.)", "A bonus", "A salary increase"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'UIF' in South Africa?", "options" => ["Unemployment Insurance Fund", "Universal Interest Fee", "Unique Financial Item", "Underground Infrastructure Fund"], "ans" => 0, "xp" => 180],
                ["q" => "What is 'SDL'?", "options" => ["Skills Development Levy", "Simple Debt Loan", "Salary Deduction Level", "Secret Deposit Log"], "ans" => 0, "xp" => 180],
                ["q" => "What is a 'Payslip'?", "options" => ["A mistake at work", "A document given to an employee outlining their pay and deductions", "A bank statement", "A marketing flyer"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Overtime'?", "options" => ["Time spent at lunch", "Hours worked beyond the standard work week, usually paid at a higher rate", "A new computer", "Being late"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Fringe Benefit'?", "options" => ["Haircuts for staff", "Extra benefits (like a car or housing) that are taxable", "A salary increase", "Hiring family"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Leave Pay'?", "options" => ["Pay when you quit", "Pay received while on annual or sick leave", "A bonus for working hard", "Money given to a supplier"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Statutory Deduction'?", "options" => ["A voluntary payment", "A deduction required by law (like tax)", "Paying for lunch", "A bank fee"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Payroll Tax'?", "options" => ["Profit tax", "Tax an employer withholds from employees' salaries and pays to the government", "A type of bank loan", "Tax on buildings"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Garnishment'?", "options" => ["Cooking", "A legal process where a portion of an employee's pay is withheld to pay a debt", "A salary bonus", "A type of tax"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Payroll Software'?", "options" => ["A game", "Programs used to automate the calculation of pay and tax", "A website", "Fixing hardware"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Remuneration'?", "options" => ["Firing someone", "Money paid for work or a service", "A type of bank loan", "A secret password"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Basic Salary'?", "options" => ["The highest salary", "The fixed amount of money an employee earns before bonuses or overtime", "Pay after tax", "A marketing flyer"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Severance Pay'?", "options" => ["Pay for training", "Amount paid to an employee whose employment is terminated by the company", "A salary bonus", "Hiring a manager"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Cost to Company' (CTC)?", "options" => ["Total profit", "The total amount an employer spends on an employee per year (Salary + Benefits + Taxes)", "Pay after tax", "The price of an office"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Payroll Audit'?", "options" => ["Watching a movie", "An examination of a company's payroll records to ensure accuracy and compliance", "A team meeting", "Hiring staff"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Medical Aid' contribution?", "options" => ["A type of tax", "Payments made by the employer/employee toward healthcare insurance", "A salary bonus", "A bank loan"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Ghost Employee' fraud?", "options" => ["A scary story", "Creating fake employees in the payroll system to steal money", "An employee who is lazy", "A type of virus"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'TCC' (Tax Compliance Certificate)?", "options" => ["Total Cash Code", "A document from SARS showing the company is up to date with taxes", "A payroll record", "A bank statement"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'IRP5'?", "options" => ["A type of tax", "A South African tax certificate issued to employees showing earnings and tax paid", "A marketing plan", "A bank loan"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Provident Fund'?", "options" => ["A safe", "A retirement fund where employer and employee make regular contributions", "A type of bank account", "A marketing award"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 2,
        "title" => "Map 90: Taxation Fundamentals", "desc" => "Direct and indirect taxes, SARS compliance, and deadlines.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'VAT'?", "options" => ["Value Added Tax", "Very Awesome Time", "Variable Asset Total", "Virtual Account Transfer"], "ans" => 0, "xp" => 150],
                ["q" => "What is 'Income Tax'?", "options" => ["Tax on spending", "Tax levied by a government directly on income", "A bank fee", "Hiding money"], "ans" => 1, "xp" => 150],
                ["q" => "Who is 'SARS'?", "options" => ["A type of illness", "South African Revenue Service", "Special Account Revenue System", "Strategic Area Resource Service"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Taxpayer'?", "options" => ["A person who hates taxes", "Any individual or entity that is required to pay tax", "Only big companies", "A government official"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Tax Deductible'?", "options" => ["Something that increases tax", "An expense that can be subtracted from gross income to reduce tax owed", "A salary bonus", "A bank loan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Output VAT'?", "options" => ["VAT paid on purchases", "VAT charged by a business to its customers on sales", "A salary bonus", "Total profit"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Input VAT'?", "options" => ["VAT charged on sales", "VAT paid by a business to its suppliers on purchases", "A bank loan", "Total revenue"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Tax Compliance'?", "options" => ["Breaking the law", "The process of meeting all tax obligations as required by law", "Paying no tax", "Being friendly to SARS"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Provisional Tax'?", "options" => ["Fake tax", "Payments made toward income tax in advance based on estimated income", "A final tax bill", "A type of share"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Corporate Tax'?", "options" => ["Tax on individuals", "Tax paid by companies on their profits", "A marketing plan", "Hiring staff"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Tax Return'?", "options" => ["Getting money back", "The formal document submitted to the tax authority reporting income and tax owed", "A bank statement", "An invoice"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Tax Avoidance'?", "options" => ["Hiding money illegally", "Using legal methods to minimize tax liability", "Breaking the law", "Ignoring tax laws"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Tax Evasion'?", "options" => ["Saving money legally", "The illegal non-payment or underpayment of taxes", "A salary bonus", "A bank fee"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Capital Gains Tax' (CGT)?", "options" => ["Tax on salary", "Tax on the profit made from selling an asset (like property or shares)", "A type of sales tax", "Hiding profit"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Zero-rated' item in VAT?", "options" => ["An item with no value", "Goods or services where VAT is 0% (e.g., basic foods like bread)", "An illegal item", "A type of loan"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Exempt' from VAT?", "options" => ["Everything", "Goods and services on which VAT is not charged at all (e.g., certain educational services)", "Basic food", "Hiding income"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Customs Duty'?", "options" => ["Being a polite person", "Tax on goods imported into or exported from a country", "A salary increase", "Office rent"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Dividend Tax'?", "options" => ["Tax on spending", "Tax levied on dividends received by shareholders", "A bank fee", "Total profit"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Double Taxation'?", "options" => ["Paying twice for lunch", "Being taxed twice on the same income (e.g., in two different countries)", "Winning a prize", "Saving money"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Tax Rebate'?", "options" => ["A tax increase", "A refund from the tax authority when you have overpaid tax", "A bank loan", "A secret password"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Transfer Pricing'?", "options" => ["The price of a taxi", "Setting prices for transactions between related companies to manage tax", "Buying a new PC", "Hiring staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Tax Resident'?", "options" => ["A person who lives in a house", "Someone who is liable for tax in a country because they live there for a certain period", "A visitor", "A manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Tax Amnesty'?", "options" => ["A type of law", "A limited-time opportunity for taxpayers to pay taxes without penalties", "A marketing plan", "Hiring an accountant"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Secondary Tax on Companies' (STC)?", "options" => ["A primary tax", "A legacy South African tax on dividends paid by companies (replaced by Dividend Tax)", "A type of sales tax", "Hiding profit"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 2,
        "title" => "Map 91: Bookkeeping & The Ledger", "desc" => "Advanced journal entries, suspense accounts, and year-end closing.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Journal Entry'?", "options" => ["Writing a diary", "The record of a financial transaction in the accounting books", "A list of employees", "A marketing plan"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'General Ledger'?", "options" => ["A book of rules", "The master record of all the company's financial transactions", "A list of employee names", "A marketing plan"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Debit' (DR)?", "options" => ["An entry on the right side of an account", "An entry on the left side of an account", "A type of bank card only", "A refund from a customer"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Credit' (CR)?", "options" => ["An entry on the left side of an account", "An entry on the right side of an account", "Getting a loan", "The same as revenue"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Trial Balance' used for?", "options" => ["To calculate tax", "To ensure total debits equal total credits", "To see who is the best employee", "To pay suppliers"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Suspense Account'?", "options" => ["A scary story", "A temporary account used when the correct destination for an entry is unknown", "A bank loan", "A marketing plan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Posting' in bookkeeping?", "options" => ["Sending mail", "The process of transferring journal entries to the ledger", "Writing a letter", "Deleting records"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Subsidiary Ledger'?", "options" => ["A smaller book", "A group of accounts that contain the details of a general ledger control account", "Hiring family", "Selling products"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'Adjusting Entry'?", "options" => ["Fixing a chair", "Entries made at the end of an accounting period to assign income and expenses to the correct period", "A salary bonus", "Buying an office"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Depreciation'?", "options" => ["An increase in value", "The systematic reduction of the recorded cost of a fixed asset over its useful life", "A bank fee", "Hiding money"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Closing Entry'?", "options" => ["Locking the office", "Entries made at the end of the year to reset temporary accounts to zero", "Ending a meeting", "Firing staff"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Control Account'?", "options" => ["Controlling a remote", "An account in the general ledger that summarizes the detail in a subsidiary ledger", "Managing a team", "Buying new software"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Bad Debt'?", "options" => ["Money you owe", "Money owed to you that is unlikely to be collected", "A type of tax", "A bank loan"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Write-off'?", "options" => ["Writing a book", "Removing an uncollectible debt from the accounting records", "Paying a bill", "Giving a bonus"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Petty Cash' book?", "options" => ["A story for kids", "A record of small office expenditures paid with cash", "A bank statement", "A marketing flyer"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Reconciliation' in bookkeeping?", "options" => ["Being friends", "The process of ensuring two sets of records are in agreement", "A bank loan", "Deleting records"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Materiality' in bookkeeping?", "options" => ["The type of paper", "Whether an error is large enough to influence financial decisions", "The office building", "A type of tax"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Cost of Goods Sold' (COGS)?", "options" => ["Total sales", "The direct costs of producing the goods sold by a company", "Paying taxes", "Office rent"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Inventory Valuation'?", "options" => ["Counting items", "Calculating the value of goods on hand at the end of a period", "Selling shares", "Hiring a manager"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Accrued Income'?", "options" => ["Money you already received", "Income that has been earned but not yet received", "Total profit", "A type of tax"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Goodwill' in accounting?", "options" => ["Being a nice person", "The excess value paid for a company above its physical assets", "Total cash", "Hiring staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Impairment'?", "options" => ["Getting better", "A permanent reduction in the value of an asset", "A salary bonus", "Total profit"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Owner's Drawings'?", "options" => ["A painting by the boss", "When an owner takes cash or assets out of the business for personal use", "A type of loan", "Hiring a new manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Statement of Retained Earnings'?", "options" => ["A list of employees", "A report showing how a company's profits have changed over time", "A bank statement", "A marketing plan"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 2,
        "title" => "Map 92: Financial Reporting", "desc" => "Produce Balance Sheets, Income Statements, and Cash Flow reports.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'Income Statement'?", "options" => ["A bank statement", "A report showing profit or loss over a specific period", "A list of employee salaries", "A marketing flyer"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Balance Sheet'?", "options" => ["A sheet for balancing", "A financial statement that reports assets, liabilities, and equity at a point in time", "A list of daily sales", "A marketing flyer"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Revenue'?", "options" => ["Profit", "The total amount of money received from sales", "Spending money", "A type of tax"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Expense'?", "options" => ["Money received", "Money spent to generate revenue", "A type of income", "A bank deposit"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Net Profit'?", "options" => ["Total sales", "The amount of money left after all expenses and taxes are paid", "The amount of money in the bank", "Total revenue"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Gross Profit'?", "options" => ["Profit after tax", "Revenue minus the cost of goods sold", "Total revenue", "The salary of the CEO"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Cash Flow' statement?", "options" => ["Money in a river", "A report showing how cash moved into and out of the business", "Total profit", "A bank loan"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Liability'?", "options" => ["An asset", "Something a person or company owes, usually a sum of money", "A type of income", "A company car"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Working Capital'?", "options" => ["Capital for a job", "The difference between current assets and current liabilities", "The office building", "A bank loan"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'ROI'?", "options" => ["Rate of Interest", "Return on Investment", "Regional Office Income", "Regular Office Index"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Operating Profit'?", "options" => ["Total sales", "Profit from core business activities before interest and tax", "Paying the CEO", "A type of loan"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'EPS'?", "options" => ["Easy Pay System", "Earnings Per Share", "Every Project Solution", "Electronic Process Service"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'IFRS'?", "options" => ["International Financial Reporting Standards", "Income For Really Smart", "Internal Fund Resource System", "Independent Financial Record Service"], "ans" => 0, "xp" => 250],
                ["q" => "What is 'Consolidated' financial statement?", "options" => ["A broken report", "Combined results of a parent company and its subsidiaries", "Hiring an accountant", "Deleting records"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Auditor's Opinion'?", "options" => ["An angry shout", "A formal statement on the accuracy of financial statements", "A type of tax", "Hiring a new manager"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Fiscal' year-end?", "options" => ["A holiday", "The end of the 12-month period used for financial reporting", "The calendar year end", "A type of bank account"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Solvency'?", "options" => ["Solving a problem", "The ability of a company to meet its long-term debts", "Being very fast", "Working underwater"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Financial Modeling'?", "options" => ["Fashion for bankers", "Building an abstract representation of a real-world financial situation", "Drawing a bank", "Buying software"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Financial Audit'?", "options" => ["A large room", "An objective examination of financial statements", "A marketing plan", "Hiring staff"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Debt-to-Equity' ratio?", "options" => ["A type of share", "A metric used to measure a company's financial leverage", "A bank loan", "Total profit"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Liquidity'?", "options" => ["Being a liquid", "How quickly an asset can be converted into cash", "Being very fast", "Working underwater"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'EBITDA'?", "options" => ["Every Boss Is Totally Done Always", "Earnings Before Interest, Taxes, Depreciation, and Amortization", "Each Bank Item Total Daily Account", "Electronic Banking in the Digital Age"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Profit Margin'?", "options" => ["The edge of a building", "The percentage of revenue that exceeds costs", "Total revenue", "The CEO's salary"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Financial Sustainability'?", "options" => ["Working forever", "The ability to generate enough income to meet goals for the foreseeable future", "Saving money", "A type of marketing"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 3: STRATEGY & RISK (Maps 93-96)
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 3,
        "title" => "Map 93: Auditing & Internal Control", "desc" => "Advanced forensics, fraud detection, and system checks.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'Internal Audit'?", "options" => ["A government inspection", "A process to check and improve a company's internal controls and efficiency", "Firing staff", "Counting cash"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Separation of Duties'?", "options" => ["Firing half the staff", "Ensuring the person who handles cash is not the same person who records it", "Working in two offices", "Dividing the profit"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Fraud' in finance?", "options" => ["Working hard", "Deception intended to result in financial gain", "A bank fee", "Being late"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Compliance'?", "options" => ["Being friendly", "Obeying laws, regulations, and rules", "A promotion", "A marketing plan"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Forensic Accounting'?", "options" => ["Studying fossils", "Using accounting skills to investigate fraud or legal disputes", "Working in a lab", "Deleting records"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Vulnerability' in financial systems?", "options" => ["A strong password", "A weakness that could be exploited to cause a financial loss", "A fast computer", "Hiring more staff"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Risk Assessment'?", "options" => ["Taking risks", "Identifying and analyzing potential problems that could occur", "Buying insurance", "Ignoring issues"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Whistleblower'?", "options" => ["A sports coach", "An employee who reports illegal or unethical behavior", "A manager", "A customer"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Due Diligence'?", "options" => ["Working hard", "The investigation an auditor or investor does before making a decision", "Paying taxes", "Hiring a manager"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Anti-Money Laundering' (AML)?", "options" => ["Cleaning money", "Laws and procedures to prevent criminals from making illegal money look legal", "Saving money", "A bank loan"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Shadow Audit'?", "options" => ["An audit in the dark", "An informal or secondary audit to check a primary audit", "Hiring family", "Deleting records"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Inventory Shrinkage'?", "options" => ["Smaller items", "Loss of products due to theft, damage, or errors", "A type of sales tax", "Hiring more staff"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Sarbanes-Oxley' (SOX)?", "options" => ["A type of car", "A law that set new standards for all US public company boards and accounting firms", "A salary bonus", "A bank fee"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Internal Control Environment'?", "options" => ["The office air", "The overall attitude and awareness of a company regarding controls", "Hiring more managers", "Paying taxes"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Data Integrity'?", "options" => ["Being honest at work", "Ensuring that data remains accurate and consistent over time", "A type of encryption", "Having a lot of data"], "ans" => 1, "xp" => 250],
                ["q" => "What is an 'Unqualified Opinion' in auditing?", "options" => ["A bad opinion", "A report stating that financial statements are fair and accurate", "An angry shout", "Hiring family"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Substantive Testing'?", "options" => ["Eating food", "Audit procedures to find errors in transactions or balances", "Writing a diary", "Buying an office"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Sampling' in auditing?", "options" => ["Eating small bits of food", "Checking a small portion of transactions to represent the whole", "Testing new computers", "Hiring staff"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Material Misstatement'?", "options" => ["A small typo", "A significant error that could change the interpretation of financial statements", "A bank statement", "A marketing plan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Segregation of Duties' (SoD)?", "options" => ["Firing staff", "Ensuring no one person has total control over a transaction from start to finish", "Dividing profit", "Hiring a new manager"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Ethics' in finance?", "options" => ["Doing math", "Following moral principles in all professional activities", "Making as much money as possible", "Ignoring rules"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Fiduciary Duty'?", "options" => ["A type of tax", "A legal obligation of one party to act in the best interest of another", "Working long hours", "Buying an office"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Regulatory Compliance'?", "options" => ["Hiring staff", "Following laws and regulations relevant to the industry", "Winning a prize", "Spending profit"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Corporate Governance'?", "options" => ["Government work", "The system of rules, practices, and processes by which a company is directed", "Managing a team", "Buying new software"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 3,
        "title" => "Map 94: Cost Control & Budgeting", "desc" => "Strategic spending, variance analysis, and future planning.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Budgeting'?", "options" => ["Counting cash", "Planning future income and expenses", "Selling shares", "Hiring staff"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Fixed Cost'?", "options" => ["A cost that changes", "A cost that stays the same regardless of production (e.g., rent)", "A salary bonus", "A bank fee"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Variable Cost'?", "options" => ["A cost that stays the same", "A cost that changes in proportion to production volume", "Total profit", "Office rent"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Variance Analysis'?", "options" => ["A science experiment", "Comparing actual financial results against the budgeted amounts", "Ignoring errors", "Spending more money"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Zero-based Budgeting'?", "options" => ["Having zero money", "Starting every budget from zero and justifying every expense", "Giving up on budgeting", "Paying zero tax"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Overhead'?", "options" => ["The ceiling", "Ongoing business expenses not directly linked to a specific product or service", "The CEO's salary", "A type of tax"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Break-even Point'?", "options" => ["Breaking a bone", "The point where total revenue equals total expenses", "Making a lot of money", "Losing everything"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Direct Cost'?", "options" => ["A cost from the bank", "A price that can be completely attributed to the production of specific goods", "Total profit", "Office rent"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Cost Control'?", "options" => ["Counting money", "The process of managing and reducing business expenses to increase profit", "Buying everything", "Hiring family"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Standard Costing'?", "options" => ["Being unique", "Estimating the expected cost of a production process", "Hiring a new intern", "Selling products"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Favorable Variance'?", "options" => ["Losing money", "When actual costs are lower than budgeted costs", "Paying more tax", "A salary decrease"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Adverse Variance'?", "options" => ["Making money", "When actual costs are higher than budgeted costs", "A salary bonus", "Winning a prize"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Cost Benefit Analysis'?", "options" => ["A math test", "Comparing the costs of an action against the benefits it will provide", "Buying an office", "Hiring staff"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Economies of Scale'?", "options" => ["A weighing scale", "Cost advantages gained by a company when it increases production", "Office size", "A type of tax"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Operating Leverage'?", "options" => ["A tool for moving safe", "The degree to which a company uses fixed costs in its operations", "A bank loan", "Hiring staff"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Sunk Cost'?", "options" => ["Money in a boat", "A cost that has already been incurred and cannot be recovered", "A type of investment", "A salary bonus"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Activity Based Costing' (ABC)?", "options" => ["Simple math", "A costing method that identifies activities and assigns costs based on their consumption", "Hiring staff", "Selling products"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Margin of Safety'?", "options" => ["A safe in the office", "The amount by which actual sales exceed the break-even point", "A type of insurance", "Hiding money"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Cash Flow Projection'?", "options" => ["Spending money", "An estimate of the timing and amounts of cash inflows and outflows", "A bank statement", "An invoice"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Capital Budgeting'?", "options" => ["A map", "Planning for long-term investments in assets like machinery or buildings", "Paying taxes", "Hiring staff"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Lean' finance?", "options" => ["A small safe", "Focusing on eliminating waste and non-value-added activities in financial processes", "Hiring fewer people", "Working faster"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Rolling Forecast'?", "options" => ["A spinning globe", "A budget that is continuously updated throughout the year", "A final bill", "A bank loan"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Allocation' of overheads?", "options" => ["Spending money", "The process of assigning indirect costs to different departments or products", "Buying a new PC", "Hiring a manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Internal Rate of Return' (IRR)?", "options" => ["The interest rate", "A metric used to estimate the profitability of potential investments", "A type of tax", "A salary bonus"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 3,
        "title" => "Map 95: Financial Risk & Insurance", "desc" => "Mitigate loss, understand policy coverage, and manage interest risk.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Financial Risk'?", "options" => ["Taking a walk", "The possibility of losing money on an investment or business venture", "Working hard", "Buying new software"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Insurance'?", "options" => ["A type of debt", "A contract where a company provides protection against financial loss in exchange for a premium", "A bank loan", "Hiding money"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Premium' in insurance?", "options" => ["A prize", "The amount you pay regularly to maintain an insurance policy", "A type of safe", "A bank fee"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Deductible' (or Excess)?", "options" => ["A discount", "The amount the policyholder must pay out of pocket before the insurance covers a claim", "Hiding money", "A type of tax"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Credit Risk'?", "options" => ["Buying a computer", "The risk that a borrower will fail to make required payments", "A marketing plan", "Hiring staff"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Market Risk'?", "options" => ["Selling products", "The risk of losses in positions arising from movements in market prices", "Hiring a new manager", "Office rent"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Operational Risk'?", "options" => ["Buying a factory", "The risk of loss resulting from inadequate or failed internal processes, people, or systems", "A bank loan", "Selling shares"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Interest Rate Risk'?", "options" => ["Being bored", "The danger that the value of an investment will change due to a change in interest rates", "A bank fee", "Hiring family"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Hedging'?", "options" => ["Gardening", "Using financial instruments to offset the risk of any adverse price movements", "Selling shares", "Buying an office"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Diversification'?", "options" => ["Hiring different people", "Spreading investments across different assets to reduce risk", "Winning a prize", "Spending profit"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Public Liability Insurance'?", "options" => ["Insurance for buildings", "Protects a business if a member of the public is injured on their premises", "A type of tax", "Hiring staff"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Professional Indemnity Insurance'?", "options" => ["Insurance for a car", "Protects against legal liability for breach of professional duty", "A bank loan", "Total profit"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Currency Risk'?", "options" => ["Losing your wallet", "The risk that a change in exchange rates will negatively affect a business transaction", "Hiring family", "Deleting records"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Asset Protection'?", "options" => ["Buying a safe", "Legal strategies used to protect wealth from claims of creditors", "Printing money", "Hiring a manager"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Solvency' risk?", "options" => ["A math problem", "The risk that a company will not have enough assets to cover its long-term liabilities", "Buying a new PC", "Hiring staff"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Fidelity' insurance?", "options" => ["Insurance for a dog", "Protects against losses caused by dishonest acts of employees (e.g., theft)", "A type of tax", "A bank loan"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Quantitative Risk Analysis'?", "options" => ["A feeling", "Using mathematical models to calculate the probability of a risk", "Asking a friend", "Guessing"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Qualitative Risk Analysis'?", "options" => ["A math test", "Evaluating risks based on subjective factors like impact and probability", "Selling shares", "Hiring family"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Contingency Fund'?", "options" => ["Money for a party", "A reserve of money set aside for unexpected costs or emergencies", "Total profit", "A bank loan"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Risk Appetite'?", "options" => ["Being hungry", "The level of risk an organization is willing to accept in pursuit of its goals", "Winning a prize", "Spending money"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Systemic Risk'?", "options" => ["Risk to one PC", "The possibility that an event at the company level could trigger severe instability in an entire industry", "A type of computer virus", "Hiring a new manager"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Reinsurance'?", "options" => ["Insurance for a bank", "When insurance companies buy insurance to protect themselves from huge claims", "A bank fee", "Hiring staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Indemnity'?", "options" => ["Being rude", "Restoring a person or entity to their financial position before a loss occurred", "A type of tax", "Hiding money"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Golden Rule' of risk management?", "options" => ["Take as much risk as possible", "Never risk more than you can afford to lose", "Ignore all risks", "Talk as fast as you can"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fin_course_id, "category_id" => 3,
        "title" => "Map 96: Final Financial Capstone", "desc" => "The ultimate challenge. Manage a full business cycle and prove your mastery.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: A bank reconciliation shows $500 more in the bank than on the books. What is the MOST likely cause?", "options" => ["The bank gave you free money", "An unrecorded EFT deposit from a customer", "Hacking", "A math error on the safe"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: An employee's gross pay is $1000. Total deductions are $200. What is their net pay?", "options" => ["$1200", "$800", "$1000", "$500"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You notice an invoice has been paid twice. What should you request from the vendor?", "options" => ["A thank you note", "A credit note or a refund", "A new invoice", "A bank statement"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You need to buy a $50k machine. Which budget handles this?", "options" => ["Petty Cash", "Capital Budget (CapEx)", "Operating Budget (OpEx)", "Marketing Budget"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: A customer hasn't paid an invoice for 90 days. What is the first step in the aging process?", "options" => ["Sue them", "Categorize the debt in the '90 Days+' column and send a dunning letter", "Delete the record", "Give them a discount"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You are spending $20k a month and only making $15k. What is your 'Burn Rate'?", "options" => ["$35k", "$5k per month", "$15k", "$20k"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You suspect an employee is stealing cash from the till. Which control did you likely fail to implement?", "options" => ["Good lighting", "Separation of duties and regular cash counts", "A new computer", "Paying a high salary"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You want to know if the business can pay its debts today. Which ratio do you check?", "options" => ["Total Sales", "Liquidity/Current Ratio", "The color of the logo", "The number of staff"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: SARS is conducting a VAT audit. Which documents must you produce?", "options" => ["Your diary", "Tax Invoices (Input and Output records)", "A list of employee hobbies", "Bank passwords"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You buy a vehicle for $100k. It will last 5 years. What is the yearly depreciation (Straight Line)?", "options" => ["$50k", "$20k", "$100k", "$5k"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: The trial balance doesn't match by $10. Where do you put the $10 temporarily?", "options" => ["In your pocket", "A Suspense Account", "In the CEO's bonus", "Delete it"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You are hiring a person to manage the safe and the books. What risk is this?", "options" => ["No risk", "Operational Risk (specifically lack of separation of duties)", "Market Risk", "Interest Rate Risk"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: The company profit is high, but there is no cash in the bank. Why?", "options" => ["Someone stole it", "Profit is on an accrual basis; cash might be tied up in unpaid invoices (AR)", "The bank is closed", "A math error"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You need to prove you know a password without revealing it. What do you use?", "options" => ["A signature", "A zero-knowledge proof", "A secure email", "A fingerprint"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: A supplier offers a 5% discount if you pay in 7 days. This is an example of what?", "options" => ["A bribe", "A cash discount (settlement discount)", "A mistake", "A bank fee"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are preparing financial statements for international investors. Which standard do you use?", "options" => ["Local rules", "IFRS (International Financial Reporting Standards)", "Excel defaults", "SARS rules only"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "MASTER FINAL: What is the most critical duty of a Financial Administrator?", "options" => ["Counting coins", "Ensuring data integrity, compliance, and accurate financial reporting", "Making as much money as possible", "Fixing computers"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: Why is the 'Accounting Equation' always balanced?", "options" => ["Because math is fun", "Because every transaction has a double-sided effect on assets and their funding sources", "It isn't always balanced", "The bank balances it for you"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: What does 'Fiduciary Responsibility' imply?", "options" => ["Being a boss", "An ethical and legal obligation to act solely in the interest of the business/shareholders", "Having a lot of money", "Paying tax"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: What is the purpose of 'Internal Controls'?", "options" => ["To control people", "To safeguard assets, ensure accurate reporting, and prevent fraud", "To save money", "To speed up work"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "EXAM: How do Payroll, Tax, and AR interact in a business?", "options" => ["They are separate", "They are interdependent; sales (AR) generate cash, which pays salaries (Payroll) and taxes (Tax)", "They compete for the CEO's attention", "They only interact during parties"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the value of 'Financial Transparency'?", "options" => ["Hiding debt", "Building trust with stakeholders, lenders, and investors", "Lowering tax", "Spending more money"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the hallmark of a world-class Finance Office?", "options" => ["Being aggressive", "Precision, ethical conduct, and strategic data-driven insight", "Having a lot of money", "Using the newest safe"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the true goal of financial administration?", "options" => ["Getting a job", "Providing a reliable and clear foundation for organizational decision-making", "Winning a prize", "Hiring friends"], "ans" => 1, "xp" => 600]
            ]]
        ]
    ]
];

// ==========================================================
// FIRE FIGHTING CURRICULUM (Maps 97-108)
// ==========================================================
$fire_maps = [
    // CATEGORY 1: FIRE SCIENCE & SAFETY (Maps 97-100)
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 1,
        "title" => "Map 97: The Chemistry of Fire", "desc" => "Understand the fire triangle, stages of fire, and thermal dynamics.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What three elements make up the 'Fire Triangle'?", "options" => ["Water, Wood, Air", "Fuel, Heat, Oxygen", "Gas, Spark, Wind", "Smoke, Heat, Ash"], "ans" => 1, "xp" => 150],
                ["q" => "What is the fourth element added to create the 'Fire Tetrahedron'?", "options" => ["Carbon Dioxide", "Chemical Chain Reaction", "Nitrogen", "Pressure"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Flash Point' of a liquid?", "options" => ["The temperature at which it boils", "The lowest temperature where it gives off enough vapor to ignite", "The point where it turns to ash", "The temperature of the flame"], "ans" => 1, "xp" => 150],
                ["q" => "Which stage of fire is characterized by rapid growth and high heat?", "options" => ["Incipient", "Growth", "Fully Developed", "Decay"], "ans" => 2, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Flashover'?", "options" => ["The end of a fire", "A transition where all combustible materials in a room ignite simultaneously", "A small spark", "When water hits a fire"], "ans" => 1, "xp" => 180],
                ["q" => "What causes a 'Backdraft'?", "options" => ["Too much water", "A sudden introduction of oxygen into an oxygen-depleted, hot environment", "Cold wind", "Using the wrong extinguisher"], "ans" => 1, "xp" => 180],
                ["q" => "How does heat travel through a solid object (like a metal beam)?", "options" => ["Convection", "Conduction", "Radiation", "Evaporation"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Convection'?", "options" => ["Heat transfer through direct contact", "Heat transfer through the movement of hot air or liquids", "Heat transfer through waves", "Heat created by friction"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Radiation' in fire science?", "options" => ["Nuclear waste", "Heat transfer through electromagnetic waves", "Hot air rising", "Touching a hot pipe"], "ans" => 1, "xp" => 210],
                ["q" => "Which stage of fire has the lowest oxygen levels?", "options" => ["Incipient", "Growth", "Decay", "Fully Developed"], "ans" => 2, "xp" => 210],
                ["q" => "What is 'Pyrolysis'?", "options" => ["A type of extinguisher", "Chemical decomposition of a solid fuel by heat", "Mixing water with foam", "The sound of a fire alarm"], "ans" => 1, "xp" => 210],
                ["q" => "What is the normal concentration of oxygen in the air we breathe?", "options" => ["10%", "21%", "50%", "100%"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "At what oxygen percentage does fire usually begin to die out?", "options" => ["21%", "15-16%", "5%", "2%"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Thermal Layering'?", "options" => ["Putting on a jacket", "The tendency of gases to form into layers according to temperature", "Using two hoses", "Building a brick wall"], "ans" => 1, "xp" => 250],
                ["q" => "What is the 'Neutral Plane'?", "options" => ["The floor of the building", "The interface between the hot gas layer and the cooler air below", "A safe area outside", "The cockpit of a plane"], "ans" => 1, "xp" => 250],
                ["q" => "Which fuel type has the highest heat release rate?", "options" => ["Wood", "Polyurethane foam (plastics)", "Paper", "Cotton"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is an 'Endothermic' reaction?", "options" => ["A reaction that releases heat", "A reaction that absorbs heat", "A reaction that explodes", "A reaction that uses no air"], "ans" => 1, "xp" => 300],
                ["q" => "What is an 'Exothermic' reaction?", "options" => ["A reaction that absorbs heat", "A reaction that releases heat", "A reaction with water", "A reaction that stops fire"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Vapor Density'?", "options" => ["The color of smoke", "The weight of a gas compared to air", "The speed of a flame", "The thickness of liquid"], "ans" => 1, "xp" => 300],
                ["q" => "If a gas has a vapor density of 0.5, will it rise or sink in air?", "options" => ["Sink", "Rise", "Stay in the middle", "Turn into liquid"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the 'Autoignition Temperature'?", "options" => ["The temperature of a spark", "The lowest temperature at which a substance will ignite without an external spark", "The boiling point", "The freezing point"], "ans" => 1, "xp" => 400],
                ["q" => "What color smoke often indicates an oxygen-starved, under-ventilated fire?", "options" => ["White", "Dark Grey or Yellow-Grey", "Clear", "Bright Blue"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Specific Gravity' in fire science?", "options" => ["The weight of smoke", "The weight of a liquid compared to water", "The force of a fire hose", "The height of a flame"], "ans" => 1, "xp" => 400],
                ["q" => "Which class of fire involves combustible metals like Magnesium?", "options" => ["Class A", "Class B", "Class D", "Class K"], "ans" => 2, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 1,
        "title" => "Map 98: Fire Classes & Extinguishers", "desc" => "Select the right tool for the job. Mastering Classes A through K.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does a 'Class A' fire involve?", "options" => ["Flammable liquids", "Ordinary combustibles like wood and paper", "Electrical equipment", "Cooking oils"], "ans" => 1, "xp" => 150],
                ["q" => "What does a 'Class B' fire involve?", "options" => ["Metals", "Flammable liquids and gases", "Wood", "Electricity"], "ans" => 1, "xp" => 150],
                ["q" => "What does a 'Class C' fire involve?", "options" => ["Kitchen fats", "Energized electrical equipment", "Gasoline", "Paper"], "ans" => 1, "xp" => 150],
                ["q" => "What acronym is used to remember how to operate an extinguisher?", "options" => ["STOP", "PASS", "FAST", "FIRE"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "In the acronym PASS, what does the first 'P' stand for?", "options" => ["Push the button", "Pull the pin", "Point the nozzle", "Press the lever"], "ans" => 1, "xp" => 180],
                ["q" => "What does the 'S' at the end of PASS stand for?", "options" => ["Scream", "Sweep side to side", "Stop the fire", "Spray the top"], "ans" => 1, "xp" => 180],
                ["q" => "Which extinguisher is best for a Class K fire (Commercial Kitchen)?", "options" => ["Water", "Wet Chemical", "CO2", "Dry Powder"], "ans" => 1, "xp" => 180],
                ["q" => "What happens if you use a standard Water extinguisher on a Class C (Electrical) fire?", "options" => ["The fire goes out faster", "You risk severe electric shock", "The extinguisher explodes", "It creates toxic gas"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary cooling agent in a 'Class A' water extinguisher?", "options" => ["Pressure", "Water", "Foam", "Carbon"], "ans" => 1, "xp" => 210],
                ["q" => "How does a CO2 extinguisher put out a fire?", "options" => ["By cooling it", "By displacing oxygen (smothering)", "By a chemical reaction", "By using high pressure water"], "ans" => 1, "xp" => 210],
                ["q" => "What color is the label for a Dry Powder (DCP) extinguisher in many standards?", "options" => ["Red", "Blue", "Black", "Yellow"], "ans" => 1, "xp" => 210],
                ["q" => "What color is the label for a CO2 extinguisher?", "options" => ["Blue", "Black", "Cream", "Green"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which fire class involves combustible metals?", "options" => ["Class B", "Class D", "Class K", "Class E"], "ans" => 1, "xp" => 250],
                ["q" => "Why should you never use water on a Magnesium fire?", "options" => ["It's too expensive", "It can cause a violent explosion and release hydrogen gas", "It makes the metal rusty", "It has no effect"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Saponification'?", "options" => ["A type of wood fire", "A process where wet chemicals turn cooking fats into a soapy foam", "The sound of an alarm", "A gas leak"], "ans" => 1, "xp" => 250],
                ["q" => "How far should you stand from a fire when starting to use a portable extinguisher?", "options" => ["1 meter", "2 to 3 meters", "10 meters", "Inside the flames"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Halon' (now being phased out)?", "options" => ["A type of wood", "A gas that interrupts the fire's chemical chain reaction", "A fire hose brand", "A type of smoke"], "ans" => 1, "xp" => 300],
                ["q" => "What is an 'AFFF' extinguisher?", "options" => ["Aqueous Film Forming Foam", "Always Find Fire Fast", "Air Force Fire Fighting", "Automatic Fire Flow Filter"], "ans" => 0, "xp" => 300],
                ["q" => "When should you NOT use a portable extinguisher?", "options" => ["When the fire is small", "When the fire is spreading beyond the point of origin or blocking your exit", "If you are alone", "If it is daytime"], "ans" => 1, "xp" => 300],
                ["q" => "What is the 'Pressure Gauge' used for on an extinguisher?", "options" => ["To see how heavy it is", "To ensure it is fully charged and ready for use", "To measure the room temperature", "To see the date of manufacture"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "How often should portable fire extinguishers typically be professionally inspected?", "options" => ["Every week", "Annually", "Every 10 years", "Never"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Dry Chemical' agent?", "options" => ["Frozen water", "A powder that interferes with the chemical reaction of a fire", "A type of gas", "A liquid foam"], "ans" => 1, "xp" => 400],
                ["q" => "Where should you aim the nozzle of the extinguisher?", "options" => ["At the top of the flames", "At the base of the fire", "At the smoke", "At the ceiling"], "ans" => 1, "xp" => 400],
                ["q" => "Which fire class is specifically for commercial deep-fat fryers?", "options" => ["Class A", "Class B", "Class K (or F)", "Class D"], "ans" => 2, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 1,
        "title" => "Map 99: PPE & Breathing Apparatus", "desc" => "Protect yourself. Mastering Bunker Gear and SCBA systems.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does 'PPE' stand for in fire fighting?", "options" => ["Public Protection Entry", "Personal Protective Equipment", "Private Part Exit", "Pressure Power Engine"], "ans" => 1, "xp" => 150],
                ["q" => "What is another common name for a fire fighter's protective suit?", "options" => ["Tracksuit", "Bunker Gear or Turnout Gear", "Wetsuit", "Hazmat Shield"], "ans" => 1, "xp" => 150],
                ["q" => "What does 'SCBA' stand for?", "options" => ["Self-Contained Breathing Apparatus", "System Command Battery Access", "Secure Cold Breathing Air", "Standard Combined Belt Assembly"], "ans" => 0, "xp" => 150],
                ["q" => "What is the primary purpose of the fire fighter's helmet?", "options" => ["To look cool", "To protect the head from impact and falling debris", "To hold a flashlight only", "To keep the hair dry"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What part of the PPE protects the neck and ears from heat?", "options" => ["The boots", "The Nomex hood", "The gloves", "The belt"], "ans" => 1, "xp" => 180],
                ["q" => "Why are turnout coats made with multiple layers?", "options" => ["To make them heavier", "To provide thermal protection, moisture barriers, and durability", "To look more professional", "To store more tools"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'PASS' device on an SCBA system?", "options" => ["A tool to pull a pin", "A motion-sensing alarm that sounds if a fire fighter is motionless", "A radio", "A spare air tank"], "ans" => 1, "xp" => 180],
                ["q" => "How should fire fighter gloves be designed?", "options" => ["Thin for better grip", "Heat-resistant and providing protection from cuts/punctures", "Waterproof only", "Made of wool"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the 'Facepiece' of an SCBA?", "options" => ["The oxygen tank", "The mask that fits over the fire fighter's face", "The regulator", "The harness"], "ans" => 1, "xp" => 210],
                ["q" => "What does the 'Regulator' do in an SCBA system?", "options" => ["Changes the air color", "Reduces high-pressure air from the cylinder to a breathable pressure", "Stores the air", "Cleans the mask"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Cylinder' in SCBA terms?", "options" => ["A type of hat", "The high-pressure tank that stores the air supply", "The hose", "The alarm"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Positive Pressure' in an SCBA mask?", "options" => ["Being happy", "Pressure inside the mask is higher than outside to prevent smoke from entering", "High speed air flow", "Using a fan"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the standard air pressure in a full 45-minute SCBA cylinder?", "options" => ["100 psi", "4500 psi", "10 psi", "500 psi"], "ans" => 1, "xp" => 250],
                ["q" => "What is the 'Skip-Breathing' technique?", "options" => ["Holding your breath", "A method of conserving air by taking a breath, holding, and exhaling slowly", "Breathing very fast", "Not using a mask"], "ans" => 1, "xp" => 250],
                ["q" => "What should you check during your 'Daily SCBA Inspection'?", "options" => ["The color of the tank", "Cylinder pressure, leaks, and alarm functionality", "The brand name", "The price"], "ans" => 1, "xp" => 250],
                ["q" => "When should you clean your PPE?", "options" => ["Every 5 years", "After every exposure to smoke or hazardous materials", "Only when it looks dirty", "Once a week"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Off-gassing' from PPE?", "options" => ["A fuel leak", "The release of trapped toxic chemicals from gear after a fire", "A steam leak", "Using a radio"], "ans" => 1, "xp" => 300],
                ["q" => "Why are fire fighter boots usually made with steel toes and shanks?", "options" => ["To be heavy for exercise", "To protect against crushing and puncture from nails/sharp debris", "To keep the feet warm", "To look like work boots"], "ans" => 1, "xp" => 300],
                ["q" => "What is the 'Low-Air Alarm' on an SCBA?", "options" => ["A bell for lunch", "An alarm that triggers when 25-33% of air remains in the cylinder", "A signal the fire is out", "A radio alert"], "ans" => 1, "xp" => 300],
                ["q" => "What is an 'IDLH' environment?", "options" => ["Internally Dead Long House", "Immediately Dangerous to Life or Health", "Instant Danger Level High", "In-Depth Low Heat"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Buddy Breathing'?", "options" => ["Talking to a friend", "Sharing air from one SCBA system between two fire fighters in an emergency", "Taking turns sleeping", "A type of exercise"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Drag Strap' on the back of turnout coats for?", "options" => ["Carrying tools", "Dragging an incapacitated fire fighter to safety", "Holding a hose", "Attaching a flashlight"], "ans" => 1, "xp" => 400],
                ["q" => "How often should SCBA cylinders be hydrostatically tested?", "options" => ["Every month", "Every 3 to 5 years (depending on type)", "Every 50 years", "Never"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Donning' process?", "options" => ["Taking off gear", "Putting on protective equipment and SCBA", "Cleaning the gear", "Ordering new gear"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 1,
        "title" => "Map 100: Fire Safety & Prevention", "desc" => "Stop the fire before it starts. Inspections, alarms, and public safety.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the primary purpose of a Smoke Alarm?", "options" => ["To put out the fire", "To alert occupants of smoke or fire early", "To call the police", "To record video"], "ans" => 1, "xp" => 150],
                ["q" => "Where is the best place to install a smoke alarm?", "options" => ["Inside a cupboard", "On every level of the home and near bedrooms", "In the garage only", "Next to the floor"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Fire Drill'?", "options" => ["A tool for making holes", "A practice exercise to ensure everyone knows how to evacuate safely", "A type of hose", "A meeting to buy gear"], "ans" => 1, "xp" => 150],
                ["q" => "Which of these is a common cause of house fires?", "options" => ["Reading books", "Unattended cooking", "Using a TV", "Sleeping with a fan"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Fire Door'?", "options" => ["A door made of fire", "A reinforced door designed to slow the spread of fire and smoke", "A door that is always open", "A door for firefighters only"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Fire Sprinkler' system?", "options" => ["A garden tool", "An automatic system that sprays water when high heat is detected", "A manual hose", "A type of alarm"], "ans" => 1, "xp" => 180],
                ["q" => "What should you do if your clothes catch fire?", "options" => ["Run as fast as you can", "Stop, Drop, and Roll", "Jump in a pool", "Call for help only"], "ans" => 1, "xp" => 180],
                ["q" => "What is the 'EDITH' plan?", "options" => ["Every Door Is The Home", "Exit Drills In The Home", "Easy Danger In The House", "Early Detection Is The Help"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Fire Inspector'?", "options" => ["A person who starts fires", "An official who checks buildings for fire hazards and code compliance", "A person who fixes trucks", "An insurance agent"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Housekeeping' in fire safety?", "options" => ["Cleaning for guests", "Keeping work and living areas free of clutter and flammable waste", "Hiring a maid", "Decorating a room"], "ans" => 1, "xp" => 210],
                ["q" => "Why should you never use an elevator during a fire?", "options" => ["It's too slow", "Smoke can cause it to malfunction or trap you on the fire floor", "It uses too much power", "It's for emergency gear only"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Fire Assembly Point'?", "options" => ["Where fire trucks meet", "A safe designated area where people gather after evacuating a building", "The center of the fire", "A fire station"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Arson'?", "options" => ["A type of hose", "The criminal act of deliberately setting fire to property", "Accidental cooking fire", "A forest fire caused by lightning"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Fire Wall' in a building?", "options" => ["A computer program", "A fire-resistant wall that prevents fire from spreading between sections", "A brick wall outside", "A decorative wall"], "ans" => 1, "xp" => 250],
                ["q" => "Which gas is known as the 'Silent Killer' in house fires?", "options" => ["Oxygen", "Carbon Monoxide (CO)", "Nitrogen", "Helium"], "ans" => 1, "xp" => 250],
                ["q" => "What is the purpose of 'Fire Safety Education' for the public?", "options" => ["To scare people", "To teach prevention and survival skills to reduce injury and loss", "To sell extinguishers", "To recruit firefighters"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Fire Loading'?", "options" => ["The weight of a fire truck", "The amount of combustible material in a given space", "The pressure in a hose", "The number of firefighters on a scene"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Structural Integrity'?", "options" => ["Being honest", "The ability of a building to remain standing during a fire", "The height of a building", "The type of paint used"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Pre-Incident Planning'?", "options" => ["Taking photos after a fire", "Firefighters visiting a building before a fire to learn its layout and hazards", "A daily workout", "Writing a report"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Manual Call Point'?", "options" => ["A phone in the office", "A wall-mounted box that anyone can press to trigger a fire alarm", "A doorbell", "A firefighter's radio"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Hydrant' inspection?", "options" => ["Washing the truck", "Checking that water sources are working and provide enough pressure", "Cleaning the station", "Hiring new staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Defensible Space' in wildfire prevention?", "options" => ["A brick wall", "Clearing vegetation around a home to prevent fire from reaching it", "A large fence", "A swimming pool"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Fire Code'?", "options" => ["A secret password", "A set of laws that buildings must follow for fire safety", "A training manual", "A type of computer code"], "ans" => 1, "xp" => 400],
                ["q" => "What is the role of a 'Fire Marshal'?", "options" => ["Starting fires", "Leading fire prevention efforts and investigating fire causes", "Driving the truck", "Fixing the gear"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 2: OPERATIONS & SUPPRESSION (Maps 101-104)
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 2,
        "title" => "Map 101: Hoses, Nozzles & Streams", "desc" => "Master water delivery. Flow rates, pressure, and hose management.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the standard diameter for a 'Small' attack line?", "options" => ["5 inches", "1.5 to 1.75 inches", "0.5 inches", "10 inches"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Supply Line'?", "options" => ["A line for air", "A large hose used to bring water from a hydrant to the truck", "A line for food", "A radio frequency"], "ans" => 1, "xp" => 150],
                ["q" => "What is the 'Nozzle'?", "options" => ["The pump", "The device at the end of a hose used to control and shape the water stream", "The water tank", "The hose connector"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'GPM' in fire fighting?", "options" => ["Gases Per Minute", "Gallons Per Minute", "Gear Pressure Mode", "Ground Point Marker"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Solid Stream'?", "options" => ["A spray of mist", "A compact stream of water with high reach and penetration", "A block of ice", "A steam leak"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Fog Stream'?", "options" => ["A compact line", "A stream of fine water droplets used for cooling and protecting firefighters", "Real fog outside", "A broken hose"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Friction Loss'?", "options" => ["Losing your gloves", "Loss of water pressure caused by water rubbing against the inside of the hose", "A slippery floor", "A broken pump"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Water Hammer'?", "options" => ["A tool for breaking walls", "A dangerous surge of pressure caused by closing a nozzle or valve too quickly", "A very large hose", "The sound of a pump"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Coupling'?", "options" => ["A type of truck", "The metal fitting used to connect hoses together", "A firefighting team", "A type of ladder"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Lugan' or 'Pin' on a coupling?", "options" => ["A decoration", "A grip point used to tighten or loosen couplings with a wrench", "A type of light", "A GPS tracker"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Siamese' connection?", "options" => ["A twin firefighter", "A fitting that combines two hoses into one", "A type of cat", "A water hydrant"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Wye' connection?", "options" => ["A question", "A fitting that splits one hose into two", "A way to say yes", "A station sign"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Soft Suction' hose used for?", "options" => ["Drinking water", "Connecting a pumper to a pressurized hydrant", "Vacuuming smoke", "Cleaning the truck"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Hard Suction' hose used for?", "options" => ["A heavy attack line", "Drafting water from a static source like a pond or pool", "A supply line from a hydrant", "A type of ladder"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Head Pressure'?", "options" => ["Headaches", "Pressure created by the weight of water due to height or elevation", "The pressure in a helmet", "The speed of a truck"], "ans" => 1, "xp" => 250],
                ["q" => "Which stream type is best for cooling a hot gas layer at the ceiling?", "options" => ["Solid Stream", "Fog Stream", "Straight Stream", "No stream"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Master Stream'?", "options" => ["A stream from the captain", "A large-volume water stream (usually over 350 GPM) used for big fires", "A stream of air", "A small garden hose"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Deck Gun'?", "options" => ["A firefighter's pistol", "A large master stream nozzle mounted on a fire truck", "A tool for cleaning the truck", "A type of radio"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Ground Monitor'?", "options" => ["A person who watches the ground", "A portable master stream device that sits on the ground", "A type of camera", "A station computer"], "ans" => 1, "xp" => 300],
                ["q" => "How should you carry a hose up a ladder?", "options" => ["Hold it with both hands", "Draped over the shoulder with the nozzle uncharged", "Drag it behind you", "You should never do this"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Hose Roll'?", "options" => ["A type of bread", "A method of folding a hose for storage (e.g., donut roll, straight roll)", "A hose that is leaking", "A team exercise"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Spanner Wrench'?", "options" => ["A tool for tightening bolts", "A tool specifically for tightening or loosening hose couplings", "A tool for breaking windows", "A type of screwdriver"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Forward Lay'?", "options" => ["Running forward", "Laying hose from the water source to the fire", "A type of jump", "A firefighting tactic"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Reverse Lay'?", "options" => ["Running backward", "Laying hose from the fire to the water source", "Lying down", "A type of alarm"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 2,
        "title" => "Map 102: Search & Rescue Operations", "desc" => "Save lives in zero visibility. Techniques for entry, search, and victim removal.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Primary Search'?", "options" => ["A slow, thorough search", "A quick, systematic search for victims in a fire building", "Searching the station", "A search done by police"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Secondary Search'?", "options" => ["A fast search", "A thorough search conducted after the fire is under control", "A search with a dog", "A second look at a map"], "ans" => 1, "xp" => 150],
                ["q" => "How should you move in a smoke-filled room?", "options" => ["Run standing up", "Stay low to the floor where visibility and air are better", "Walk backwards", "Stay in the middle of the room"], "ans" => 1, "xp" => 150],
                ["q" => "What tool is used to help find victims through walls or thick smoke?", "options" => ["A flashlight", "Thermal Imaging Camera (TIC)", "A radio", "A megaphone"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the 'Right-Hand Search' method?", "options" => ["Using only your right hand", "Keeping your right hand on the wall to navigate and avoid getting lost", "A type of handshake", "A search done at night"], "ans" => 1, "xp" => 180],
                ["q" => "What should you do before entering a room to search?", "options" => ["Take off your mask", "Check the door for heat with the back of your hand", "Yell your name", "Call the station"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Victim Drag'?", "options" => ["A type of game", "A technique to move an unconscious person to safety while staying low", "Dragging gear", "A slow firefighter"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Forcible Entry'?", "options" => ["Being rude", "Using tools to gain access to a locked building or room", "Driving through a gate", "Hiring a locksmith"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the 'Irons' tool set?", "options" => ["A golf set", "A Halligan bar and a Flat-Head Axe", "Two heavy hoses", "A metal ladder"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'V.E.I.S.'?", "options" => ["Very Easy Internal Search", "Vent-Enter-Isolate-Search", "Video Entry Internal System", "Verify Every Important Signal"], "ans" => 1, "xp" => 210],
                ["q" => "Why should you close the door after entering a room to search it?", "options" => ["To have privacy", "To isolate the room and slow the spread of fire/smoke into the room", "To keep it warm", "To hide from victims"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Triage'?", "options" => ["A type of tree", "The process of prioritizing victims based on the severity of their injuries", "A team of three", "A firefighting tool"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Where are victims often found in a house fire?", "options" => ["In the kitchen only", "Near exits, windows, and in bathrooms or closets", "Under the bed only", "On the roof"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'RIT' or 'RIC' team?", "options" => ["A team that cleans", "Rapid Intervention Team (designated to rescue trapped firefighters)", "Radio Information Team", "Regional Inspection Crew"], "ans" => 1, "xp" => 250],
                ["q" => "What is the 'Mayday' call used for?", "options" => ["Celebrating May 1st", "An emergency signal for a firefighter in immediate life-threatening danger", "A call for water", "A radio check"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Self-Rescue'?", "options" => ["Saving yourself", "Techniques a firefighter uses to escape a life-threatening situation alone", "A medical kit", "Quitting the job"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Sizing Up' the scene?", "options" => ["Measuring the building", "Ongoing evaluation of the situation to determine tactics and safety", "Weighing the gear", "Counting the staff"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Litter' in rescue?", "options" => ["Trash on the floor", "A stretcher used for carrying victims", "A group of animals", "A type of small fire"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Extrication'?", "options" => ["Extinguishing a fire", "Freeing a person trapped in a vehicle or machinery", "Exiting a building", "Finding a fire hydrant"], "ans" => 1, "xp" => 300],
                ["q" => "What are 'Cribbing' blocks used for?", "options" => ["Building a house", "Stabilizing a vehicle or heavy object during a rescue", "Keeping a door open", "Storing tools"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'High-Angle' rescue?", "options" => ["Working on a hill", "Rescue operations involving ropes and harnesses on steep terrain or high buildings", "Using a tall ladder only", "A type of water rescue"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Confined Space' rescue?", "options" => ["Rescuing in a park", "Rescue in small, tight areas like tanks, tunnels, or silos", "A rescue outside", "A rescue on a truck"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'O-A-T-H' rope signal?", "options" => ["A promise", "A method of communication using tugs on a rope", "A firefighting oath", "A type of knot"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Victim Removal'?", "options" => ["Firing a person", "The physical act of moving a rescued person to a safe zone", "Deleting a record", "Taking a photo of a victim"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 2,
        "title" => "Map 103: Ventilation & Salvage", "desc" => "Control the path of fire and protect property from smoke and water damage.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Ventilation' in fire fighting?", "options" => ["Putting on a fan", "The systematic removal of hot gases and smoke from a building", "Blowing air onto a fire", "Opening all the doors"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Natural Ventilation'?", "options" => ["Using a fan", "Using wind and existing openings (doors/windows) to clear smoke", "The fire going out on its own", "Rain"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Mechanical Ventilation'?", "options" => ["Opening a window", "Using fans or blowers (PPV/NPV) to move air", "Hand-fanning a room", "Waiting for the wind"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Vertical Ventilation'?", "options" => ["Opening a door", "Creating an opening in the roof to let heat and smoke rise out", "Opening two windows", "Using a horizontal fan"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'PPV'?", "options" => ["Positive Pressure Ventilation", "Professional Power Vault", "Private Property Value", "Pressure Pump Valve"], "ans" => 0, "xp" => 180],
                ["q" => "What is 'NPV'?", "options" => ["New Power Valve", "Negative Pressure Ventilation (sucking smoke out)", "Normal Pressure Value", "National Protection Vault"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Hydraulic Ventilation'?", "options" => ["Using a mechanical fan", "Using a fog stream from a hose to pull smoke out of a window", "Opening a fire hydrant", "Using a truck's engine"], "ans" => 1, "xp" => 180],
                ["q" => "What is the primary danger of improper ventilation?", "options" => ["It's too loud", "It can pull the fire toward firefighters or spread it to new areas", "It costs money", "It breaks the glass"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Salvage' in fire fighting?", "options" => ["Starting a fire", "Protecting property from smoke and water damage during or after a fire", "Selling old gear", "Finding lost items"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Salvage Cover'?", "options" => ["A blanket for sleeping", "A large waterproof tarp used to cover furniture and electronics", "A firefighter's jacket", "A truck cover"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Overhaul'?", "options" => ["Repairing the truck", "Searching for hidden fire or embers after the main fire is out", "Buying new gear", "Cleaning the station"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Water Chute'?", "options" => ["A water slide", "A device made from tarps to channel water out of a building", "A broken hose", "A type of pump"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Scupper'?", "options" => ["A type of boat", "An opening in a wall to allow water to drain off a floor", "A firefighting tool", "A station alarm"], "ans" => 1, "xp" => 250],
                ["q" => "Why is it important to check walls and ceilings during Overhaul?", "options" => ["To see the paint", "To find fire that may be burning inside hidden spaces", "To measure the room", "To look for spiders"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Floor Runner'?", "options" => ["A person who runs", "A long tarp used to protect carpets and floors from dirty boots", "A type of ladder", "A station rug"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Mop-up'?", "options" => ["Cleaning the floor", "The final stage of extinguishing a fire, ensuring no hot spots remain", "Getting a refund", "Washing the hose"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Horizontal Ventilation'?", "options" => ["Opening a roof", "Using windows and doors on the same level to clear smoke", "Using a vertical fan", "Opening one floor"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Cross Ventilation'?", "options" => ["Using the roof", "Opening windows on opposite sides of a building to let the wind through", "Blowing air in circles", "A type of drill"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Kerf Cut'?", "options" => ["A deep hole", "A small inspection cut in a roof to see if fire is below", "A type of bandage", "A way to cut wood"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Louver Cut'?", "options" => ["A small hole", "A rectangular roof opening where the decking is pivoted on the rafters", "A round hole", "A decorative cut"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Loss Control'?", "options" => ["Losing your keys", "The combination of Salvage and Overhaul to minimize overall fire damage", "Finding a lost truck", "A type of insurance"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Water Vacuuming'?", "options" => ["Drinking water", "Using specialized machines to suck up water from floors after a fire", "Cleaning a pool", "Using a hose"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Debris Removal'?", "options" => ["Throwing away trash", "Removing burnt and water-soaked items from a building", "Selling gear", "Fixing a wall"], "ans" => 1, "xp" => 400],
                ["q" => "What is the goal of 'Property Conservation'?", "options" => ["Selling the building", "Returning the property to a safe and usable state as much as possible", "Buying new property", "Moving to a new station"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 2,
        "title" => "Map 104: Wildland Fire Fighting", "desc" => "Battling the elements. Topography, weather, and defensive lines.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Wildland Fire'?", "options" => ["A house fire", "An unplanned fire burning in natural vegetation (forest, grass, brush)", "A fire in a bin", "A campfire"], "ans" => 1, "xp" => 150],
                ["q" => "What three factors most affect wildland fire behavior?", "options" => ["Water, Air, Gas", "Fuel, Weather, Topography", "Trees, Birds, Rain", "Time, Day, Year"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Fire Line' (or Firebreak)?", "options" => ["A line of firefighters", "A strip of land cleared of all fuel to stop a fire's spread", "A burning log", "A water hose"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Topography'?", "options" => ["A map of trees", "The physical features and slope of the land", "The weather forecast", "A type of fuel"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Does fire travel faster uphill or downhill?", "options" => ["Downhill", "Uphill (due to preheating of fuel)", "Same speed", "It depends on the trees"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Crown Fire'?", "options" => ["A fire on the ground", "A fire that spreads through the tops of trees", "A fire in a cave", "A fire on a roof"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Surface Fire'?", "options" => ["A fire in the trees", "A fire that burns loose debris and grass on the ground", "A fire underground", "A fire on a lake"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Ground Fire'?", "options" => ["A fire in the grass", "A fire that burns deep in the organic matter/soil (peat/roots)", "A fire on a building", "A fire in a truck"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Fuel Moisture'?", "options" => ["Drinking water for staff", "The amount of water contained in vegetation fuel", "Rain in the forest", "The price of fuel"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Aspect' in wildland fire?", "options" => ["A type of bug", "The direction a slope faces (e.g., North, South)", "The color of a flame", "A firefighter's rank"], "ans" => 1, "xp" => 210],
                ["q" => "Which slope aspect (in the Southern Hemisphere) typically has drier fuel?", "options" => ["South", "North (gets more sun)", "East", "West"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Spot Fire'?", "options" => ["A very small fire", "A new fire started by flying embers outside the main fire area", "A fire on a dog", "A fire in a house"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Direct Attack' in wildland fire?", "options" => ["Using a helicopter only", "Applying water or building fire lines directly at the fire's edge", "Watching from a distance", "Setting a backfire"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Indirect Attack'?", "options" => ["Not doing anything", "Building fire lines at a distance from the fire and letting it burn toward them", "Using one hose", "Quitting the job"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Pulaski'?", "options" => ["A type of truck", "A combination tool with an axe on one side and an adze (hoe) on the other", "A firefighting team", "A type of radio"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'McLeod'?", "options" => ["A Scottish firefighter", "A tool with a large hoe on one side and a heavy rake on the other", "A type of hose", "A GPS device"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Backfire'?", "options" => ["An accident", "A fire set deliberately in the path of a wildfire to consume fuel", "A truck engine problem", "A small spark"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Fire Shelter'?", "options" => ["A safe house", "A portable, tent-like foil device used as a last resort for survival", "A fire station", "A metal box"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Relative Humidity' importance?", "options" => ["The weight of air", "Low humidity makes fuels drier and easier to ignite", "High humidity causes rain", "It doesn't affect fire"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Slash' in wildland fuel?", "options" => ["A cut on a tree", "Debris left over from logging or thinning forests", "A type of water spray", "A fast runner"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Incident Command System' (ICS) in wildland?", "options" => ["A type of radio", "A standardized management system for organizing fire response", "A secret code", "A computer game"], "ans" => 1, "xp" => 400],
                ["q" => "What is a 'Heli-tack' crew?", "options" => ["A team of mechanics", "Firefighters who are transported to remote fires by helicopter", "A team that uses ladders", "Staff at the station"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Containment' percentage?", "options" => ["Total profit", "How much of the fire perimeter is surrounded by a fire line", "The number of trees saved", "The amount of water used"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Black' area in wildland fire?", "options" => ["A safe zone", "An area that has already been burned and is relatively safe", "A dangerous zone", "A new forest"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 3: SPECIALIZED HAZARDS & COMMAND (Maps 105-108)
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 3,
        "title" => "Map 105: HazMat & Chemical Safety", "desc" => "Detect and contain dangerous materials. Identification and mitigation.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What does 'HazMat' stand for?", "options" => ["Hazardous Materials", "High Area Zone Match", "Heating and Zero Management", "Heavy Air Zone Marker"], "ans" => 0, "xp" => 200],
                ["q" => "Which document contains safety info about a specific chemical?", "options" => ["A map", "SDS (Safety Data Sheet)", "A bank statement", "A payroll log"], "ans" => 1, "xp" => 200],
                ["q" => "What is the 'Hot Zone'?", "options" => ["The kitchen", "The area immediately surrounding a hazardous material release", "A safe place for staff", "A fire station"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Decontamination' (Decon)?", "options" => ["Washing a truck", "The process of removing hazardous materials from people and equipment", "Cleaning the station", "Hiring new staff"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What color in the NFPA 704 diamond represents Health hazards?", "options" => ["Red", "Blue", "Yellow", "White"], "ans" => 1, "xp" => 240],
                ["q" => "What color represents Flammability in the NFPA diamond?", "options" => ["Blue", "Red", "Yellow", "White"], "ans" => 1, "xp" => 240],
                ["q" => "What color represents Instability/Reactivity?", "options" => ["Blue", "Yellow", "Red", "White"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'UN Number'?", "options" => ["A phone number", "A 4-digit code that identifies specific dangerous chemicals", "A secret password", "A type of tax"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Vapor Pressure'?", "options" => ["The weight of air", "The pressure a liquid exerts as it turns into a gas", "The speed of a fan", "The force of a hose"], "ans" => 1, "xp" => 280],
                ["q" => "What is a 'Cryogenic' liquid?", "options" => ["Hot liquid", "A liquid that is extremely cold (e.g., Liquid Nitrogen)", "A type of fuel", "A cleaning agent"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'BLEVE'?", "options" => ["Boiling Liquid Expanding Vapor Explosion", "Big Loud Every Vertical Exit", "Base Level Early Value Entry", "Blue Liquid Extra Vault Exit"], "ans" => 0, "xp" => 280],
                ["q" => "What is 'Containment' in HazMat?", "options" => ["Deleting a file", "Keeping a material in its original container or a designated area", "Throwing it away", "Letting it run into a drain"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the 'Cold Zone'?", "options" => ["The fridge", "A safe area where no contamination is present, used for command/medical", "The fire area", "The decontamination area"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Absorption' in spill control?", "options" => ["Drinking water", "Using materials (like sand or socks) to soak up a liquid", "Evaporating the spill", "Hiring a maid"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Adsorption'?", "options" => ["Soaking up", "When a liquid sticks to the surface of a solid material", "A type of math", "A firefighting team"], "ans" => 1, "xp" => 350],
                ["q" => "What is a 'Placard' on a truck?", "options" => ["A driver's license", "A diamond-shaped sign identifying the hazardous cargo", "A company logo", "A marketing flyer"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Toxicology'?", "options" => ["The study of fire", "The study of the adverse effects of chemicals on living organisms", "Learning to read", "A type of engineering"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Acute' exposure?", "options" => ["A long exposure", "A short-term, high-concentration exposure to a chemical", "A small cut", "A type of radio call"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Chronic' exposure?", "options" => ["A fast exposure", "Long-term, low-concentration exposure to a chemical", "A big fire", "A type of hose"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Shelter-in-Place'?", "options" => ["Running outside", "Staying inside a building and sealing it to avoid outdoor contamination", "Closing a bank account", "Going to the fire station"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Diking' a spill?", "options" => ["Cleaning it", "Building a barrier to prevent a liquid from flowing further", "Drying it with heat", "Sucking it up with a vacuum"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'ERG'?", "options" => ["Early Record Guide", "Emergency Response Guidebook (used to identify HazMat)", "Extra Radio Group", "Electronic Response Gate"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Vapor Suppression'?", "options" => ["Turning off a fan", "Using foam or water spray to lower the release of flammable gases", "Ignoring the gas", "Setting a fire"], "ans" => 1, "xp" => 500],
                ["q" => "What is the primary role of an 'Incident Commander' in HazMat?", "options" => ["To clean the spill", "To manage the scene and ensure safety and containment strategy", "To drive the truck", "To talk to the media only"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 3,
        "title" => "Map 106: Vehicle & Machinery Rescue", "desc" => "Advanced extrication. Stabilization, cutting, and patient care.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is the first priority at a vehicle accident scene?", "options" => ["Cutting the car", "Scene safety and traffic control", "Taking photos", "Calling the news"], "ans" => 1, "xp" => 200],
                ["q" => "What are 'Wheel Chocks' used for?", "options" => ["To clean the tires", "To prevent a vehicle from rolling during a rescue", "To lock the doors", "To support the roof"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Stabilization' of a vehicle?", "options" => ["Fixing the engine", "Using blocks and struts to stop a vehicle from moving or shaking", "Painting the car", "Hiring a driver"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Glass Management'?", "options" => ["Washing windows", "Controlled breaking or removal of windows to prevent injury from shards", "Buying new glass", "A type of mirror"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What are the 'Jaws of Life'?", "options" => ["A medical kit", "Hydraulic spreaders and cutters used for extrication", "A type of ladder", "A firefighter's rank"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Cribbing'?", "options" => ["Building a crib", "Stacks of wood or plastic used to support a stabilized load", "A type of hose", "A station alarm"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Dash Displacement'?", "options" => ["Cleaning the dash", "Lifting or pushing a crushed dashboard off a trapped victim", "Painting the car", "A radio call"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Roof Peel'?", "options" => ["Opening a sunroof", "Cutting and removing the entire roof of a car to access victims", "A type of fruit", "Cleaning the roof"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'LOTO'?", "options" => ["Level Out Total Output", "Lock-Out / Tag-Out (disabling machinery power)", "Local Office Time Order", "Low Oxygen Total Oven"], "ans" => 1, "xp" => 280],
                ["q" => "Why should you disconnect the battery in a crashed car?", "options" => ["To save power", "To prevent airbag deployment and fire from electrical shorts", "To listen to the radio", "To clean the battery"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Patient Packaging'?", "options" => ["Putting someone in a box", "Preparing a victim for safe transport on a backboard or litter", "A type of mail", "Buying a hospital"], "ans" => 1, "xp" => 280],
                ["q" => "What is the 'Golden Hour' in trauma?", "options" => ["A sunset", "The critical first hour after injury where survival chances are highest if treated", "A payday", "An office party"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Reciprocating Saw' used for?", "options" => ["Cutting food", "Cutting through metal and vehicle pillars", "A tool for cleaning", "A type of fan"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Hard Protection' for a victim?", "options" => ["A helmet", "Using a heavy shield or board to protect a victim while tools are cutting", "A brick wall", "A firefighter's jacket"], "ans" => 1, "xp" => 350],
                ["q" => "What is a 'High-Pressure Bag'?", "options" => ["A grocery bag", "An inflatable air bag used to lift heavy vehicles or machinery", "A type of scuba tank", "A fire hose"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Shoring'?", "options" => ["Working on a beach", "Using struts to support a collapsing structure or trench", "Cleaning the truck", "Closing a door"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Trench Rescue'?", "options" => ["Rescue in a park", "Rescuing someone from a collapsed excavation or deep ditch", "A rescue in a building", "A water rescue"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Cave-in'?", "options" => ["A secret hiding place", "The collapse of the walls of an excavation", "A type of firefighting gear", "A small fire"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Hydraulic Fluid' safety?", "options" => ["Drinking fluid", "Fluid in tools is under high pressure and can be dangerous if leaking", "Using fluid for a fire", "A cleaning liquid"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Command' role at an accident?", "options" => ["Managing one tool", "Directing the overall rescue strategy and prioritizing safety", "Driving the truck", "Taking notes"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Industrial Extrication'?", "options" => ["Fixing a machine", "Rescue involving complex factory or construction equipment", "Rescuing a car", "A forest fire"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Patient Assessment'?", "options" => ["Evaluating a person's injuries and level of consciousness", "Hiring a new staff", "Selling products", "Cleaning a station"], "ans" => 0, "xp" => 500],
                ["q" => "What is 'Spinal Immobilization'?", "options" => ["Moving someone fast", "Using boards and collars to keep the spine still during rescue", "A type of medicine", "A firefighting oath"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Scene Restoration'?", "options" => ["Starting a fire", "Cleaning up debris and ensuring the scene is safe after a rescue", "Painting the truck", "Hiring a manager"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 3,
        "title" => "Map 107: Incident Command & Strategy", "desc" => "Manage the chaos. Strategic planning and resource organization.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Incident Command' (IC)?", "options" => ["Managing a radio", "The person responsible for all incident activities and management", "A type of truck", "A fire station"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'ICS'?", "options" => ["Incident Command System", "International Cold System", "Internal Code Standard", "Instant Communication Service"], "ans" => 0, "xp" => 200],
                ["q" => "What is a 'Unified Command'?", "options" => ["A secret team", "When multiple agencies share command responsibility", "One person in charge of everything", "A type of radio"], "ans" => 1, "xp" => 200],
                ["q" => "What is 'Size-up'?", "options" => ["Measuring gear", "The rapid mental process of evaluating a situation", "Counting money", "A daily workout"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Strategic' planning?", "options" => ["Handling one hose", "The high-level overall plan for the entire incident", "Daily chores", "A payroll report"], "ans" => 1, "xp" => 240],
                ["q" => "What is 'Tactical' implementation?", "options" => ["Hiring staff", "Specific actions taken to achieve strategic goals", "Marketing", "Buying a new PC"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Division' in ICS?", "options" => ["Math", "An organization level responsible for operations within a specific geographic area", "A type of hose", "A firefighting team"], "ans" => 1, "xp" => 240],
                ["q" => "What is a 'Group' in ICS?", "options" => ["A team of friends", "An organization level responsible for a specific functional task (e.g., Search)", "A geographic area", "A radio frequency"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Span of Control'?", "options" => ["Having a fast pulse", "The number of subordinates one supervisor can manage effectively (usually 3 to 7)", "The size of a truck", "The duration of a fire"], "ans" => 1, "xp" => 280],
                ["q" => "What is 'Accountability' in ICS?", "options" => ["Doing math", "Knowing where every person is and what they are doing at all times", "Blaming others", "Working alone"], "ans" => 1, "xp" => 280],
                ["q" => "What is an 'IAP'?", "options" => ["Incident Action Plan", "Internal Air Pressure", "Independent Account Process", "Instant Area Protection"], "ans" => 0, "xp" => 280],
                ["q" => "What is 'Staging' area?", "options" => ["A theater stage", "A location where resources wait for tactical assignment", "A safe place for victims", "The fire station"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Logistics' Section?", "options" => ["Logic puzzles", "The part of ICS responsible for providing facilities, services, and materials", "Managing a team", "Fighting the fire"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Operations' Section?", "options" => ["Fixing the truck", "The part of ICS responsible for all tactical activities", "Writing reports", "Paying the bills"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Planning' Section?", "options" => ["Thinking about tomorrow", "The part of ICS responsible for collecting and evaluating info and maintaining status", "Managing a team", "Buying new gear"], "ans" => 1, "xp" => 350],
                ["q" => "What is 'Finance' Section?", "options" => ["Counting money", "The part of ICS responsible for tracking costs and administrative records", "Selling products", "Hiring staff"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Mutual Aid'?", "options" => ["Being friends", "An agreement to help other fire departments when they are overwhelmed", "A government grant", "A type of insurance"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Demobilization'?", "options" => ["Moving to a new station", "The orderly return of resources to their original locations", "Hiring new staff", "Cleaning the truck"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Post-Incident Analysis'?", "options" => ["Taking photos after", "A meeting to review what happened and how to improve", "Buying new gear", "A payroll log"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Chain of Command'?", "options" => ["A heavy chain", "The orderly line of authority within an organization", "A type of radio", "A station alarm"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Common Terminology' in ICS?", "options" => ["Using slang", "Using standard words that everyone understands to avoid confusion", "Using secret codes", "Talking fast"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Safety Officer' role?", "options" => ["Starting fires", "Monitoring the incident for hazards and ensuring firefighter safety", "Driving the truck", "Fixing the gear"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Liaison Officer' role?", "options" => ["Talking to media", "Acting as the contact point for assisting and cooperating agencies", "Managing one tool", "Cleaning the station"], "ans" => 1, "xp" => 500],
                ["q" => "What is 'Public Information Officer' (PIO)?", "options" => ["A news reporter", "The person who provides information to the media and the public", "Hiring staff", "Driving the truck"], "ans" => 1, "xp" => 500]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $fire_course_id, "category_id" => 3,
        "title" => "Map 108: Final Fire Capstone", "desc" => "The ultimate test. Combine all your skills to save lives and property.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: You arrive at a house fire and see grey smoke puffing in and out of windows. What is this a sign of?", "options" => ["The fire is out", "Potential Backdraft", "Rain", "A small fire"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: A victim is found unconscious in a hallway. What is the priority?", "options" => ["Checking their ID", "Immediate removal to a safe environment (Stay Low)", "Calling their family", "Wait for the fire to go out"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You are fighting an electrical fire. Which extinguisher do you grab?", "options" => ["Water", "CO2 or DCP", "Foam", "Wet Chemical"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: Your SCBA low-air alarm sounds. What must you do?", "options" => ["Ignore it", "Notify your partner and exit the IDLH environment immediately", "Take off your mask", "Scream for help"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: A truck with a 'Yellow' placard is leaking liquid. What is the first step?", "options" => ["Cleaning it with water", "Identifying the material using the ERG and staying upwind", "Driving the truck away", "Drinking the liquid"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You need to break a car window for extrication. Where do you hit it?", "options" => ["The center", "A bottom corner", "The top only", "Use your head"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: The Incident Commander tells you to 'Vent the roof'. What are you doing?", "options" => ["Painting it", "Vertical Ventilation", "Cleaning it", "Horizontal Ventilation"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You find hot spots in a wall after a fire. What process is this?", "options" => ["Starting a fire", "Overhaul", "Salvage", "Triage"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: A wildland fire is traveling toward a town. You are told to build a 'Fire Line'. What are you doing?", "options" => ["Hiring staff", "Clearing vegetation to bare mineral soil to stop fire spread", "Using a large hose", "Waiting for rain"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You are in a zero-visibility room and lose your partner. What do you call on the radio?", "options" => ["Hello", "Mayday", "SOS", "Over and out"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You need to move a 100kg victim from a 2nd floor window. What tool is best?", "options" => ["A small rope", "An aerial ladder or specialized lowering system", "A trampoline", "Carry them on your back"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You notice your hose has a 'Water Hammer' sound. What should you tell the nozzle operator?", "options" => ["Close it faster", "Close and open the nozzle slowly", "Run away", "Unplug the pump"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: You see a coworker not wearing their Nomex hood. What do you do?", "options" => ["Ignore it", "Remind them of the safety hazard and insist they wear all PPE", "Laugh", "Tell the media"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: A car is on its side with a victim inside. What is the first priority?", "options" => ["Cutting the door", "Stabilizing the vehicle using struts and cribbing", "Calling a lawyer", "Taking a photo"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are assigned to 'Staging'. What are you doing?", "options" => ["Acting", "Waiting with your crew in a designated area for orders", "Fighting the fire", "Cleaning gear"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: A fire is involving a large Magnesium pile. What do you do?", "options" => ["Spray water", "Use a specialized Class D agent or let it burn out under control", "Use a foam extinguisher", "Blow air on it"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "MASTER FINAL: What is the most critical factor for success in fire fighting operations?", "options" => ["The size of the truck", "Teamwork, training, and effective communication", "Having the newest gear", "Being the strongest"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: In any fire incident, what is the highest priority?", "options" => ["Protecting property", "Life Safety (Public and Personnel)", "Incident Stabilization", "Cleaning the station"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: Why is the 'Incident Command System' (ICS) used worldwide?", "options" => ["It's a secret", "It provides a clear, scalable management structure for any emergency", "It's a game", "It's for big cities only"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: What is the true goal of fire fighting?", "options" => ["To look like a hero", "To protect life, property, and the environment through prevention and suppression", "To get a high salary", "To drive fast trucks"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "EXAM: How do Heat, Fuel, and Oxygen interact to create fire?", "options" => ["They are separate", "They combine in a chemical chain reaction to sustain combustion", "They only interact in a lab", "They don't interact"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: Why is 'Situational Awareness' vital for every firefighter?", "options" => ["To see the time", "To recognize changing hazards and ensure the safety of the crew", "To look for food", "To win a prize"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the hallmark of a world-class fire department?", "options" => ["Being aggressive", "Professionalism, discipline, and a commitment to continuous learning", "Having the most money", "Using the newest station"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the core responsibility of a firefighter to their community?", "options" => ["To drive a truck", "To provide a reliable and expert safety net in times of crisis", "To write reports", "To hire staff"], "ans" => 1, "xp" => 600]
            ]]
        ]
    ]
];

// ==========================================================
// GENERIC MANAGEMENT CURRICULUM (Maps 109-120)
// ==========================================================
$gm_maps = [
    // CATEGORY 1: MANAGEMENT FOUNDATIONS (Maps 109-112)
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 1,
        "title" => "Map 109: Roles & Responsibilities", "desc" => "Understand the fundamental functions of a manager and the skills required for success.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What are the four primary functions of management?", "options" => ["Planning, Organizing, Leading, Controlling", "Hiring, Firing, Selling, Buying", "Typing, Printing, Filing, Calling", "Meetings, Emails, Lunches, Travel"], "ans" => 0, "xp" => 150],
                ["q" => "A manager who focuses on day-to-day operations is typically at which level?", "options" => ["Top Level", "Middle Level", "Lower/First-line Level", "Board Level"], "ans" => 2, "xp" => 150],
                ["q" => "What is 'Efficiency' in management?", "options" => ["Doing the right things", "Doing things right (minimizing resource waste)", "Working 24 hours a day", "Having the most employees"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Effectiveness'?", "options" => ["Doing things right", "Doing the right things (achieving goals)", "Being very fast", "Saving money"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "Which skill involves the ability to work well with other people?", "options" => ["Technical Skills", "Conceptual Skills", "Interpersonal/Human Skills", "Mechanical Skills"], "ans" => 2, "xp" => 180],
                ["q" => "Conceptual skills are most important for which level of management?", "options" => ["First-line managers", "Top-level managers", "Junior supervisors", "Interns"], "ans" => 1, "xp" => 180],
                ["q" => "What is the 'Managerial Role' of a Liaison?", "options" => ["Distributing mail", "Maintaining a network of outside contacts who provide favors", "Fixing computers", "Disciplining staff"], "ans" => 1, "xp" => 180],
                ["q" => "Which role involves searching for opportunities and initiating projects to bring about change?", "options" => ["Disturbance Handler", "Entrepreneur", "Resource Allocator", "Negotiator"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Stakeholder Theory'?", "options" => ["Managers only care about owners", "Managers should consider the interests of anyone affected by the business", "The company belongs to the bank", "The office building is most important"], "ans" => 1, "xp" => 210],
                ["q" => "What is an 'Organizational Chart'?", "options" => ["A map of the city", "A visual representation of a company's structure and hierarchy", "A list of customer names", "A profit report"], "ans" => 1, "xp" => 210],
                ["q" => "What does 'Authority' refer to in management?", "options" => ["Being mean", "The right to give orders and expect them to be obeyed", "Having the most money", "Knowing everything"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Delegation'?", "options" => ["Doing all the work yourself", "Assigning responsibility and authority to others", "Firing people", "Hiring a new boss"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Span of Control'?", "options" => ["The size of a desk", "The number of subordinates a manager can effectively supervise", "The duration of a meeting", "The length of the work day"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Unity of Command'?", "options" => ["Having 10 bosses", "The principle that an employee should report to only one supervisor", "Working as a single team", "A military exercise"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Centralization'?", "options" => ["Decision-making authority is kept at the top levels", "Everyone makes their own rules", "The office is in the center of town", "Using one computer system"], "ans" => 0, "xp" => 250],
                ["q" => "What is 'Decentralization'?", "options" => ["Losing control", "Authority is distributed throughout the organization", "Closing the main office", "Moving to the cloud"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Corporate Social Responsibility' (CSR)?", "options" => ["Making the most profit", "A business's concern for the welfare of society", "The CEO's hobby", "Paying taxes"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Flat' structure?", "options" => ["A building with one floor", "An organization with few or no levels of middle management", "A boring company", "A business with no profit"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Accountability'?", "options" => ["Doing math", "The requirement to justify results to those above you in the chain", "Blaming others", "Working alone"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Technical Skill' for a manager?", "options" => ["Fixing a car", "Specialized knowledge or expertise needed to perform specific tasks", "Turning on a computer only", "Knowing how to type fast"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the primary purpose of 'Controlling'?", "options" => ["To punish people", "To monitor performance and make sure goals are being met", "To stop all spending", "To watch employees on camera"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Ethics' in management?", "options" => ["Following orders", "Principles of right and wrong that guide decision-making", "Making the most money", "Avoiding the law"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Organizational Culture'?", "options" => ["The art in the office", "The system of shared values and beliefs that govern how people behave", "The country's history", "The dress code only"], "ans" => 1, "xp" => 400],
                ["q" => "What is the 'Scientific Management' approach associated with?", "options" => ["Art", "Analyzing workflows to improve economic efficiency (Frederick Taylor)", "Psychology", "Religion"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 1,
        "title" => "Map 110: Planning & Goal Setting", "desc" => "Master the art of setting objectives and designing the roadmap to achievement.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Strategic Planning'?", "options" => ["Daily task lists", "Long-term planning (3-5 years) done by top management", "Buying office supplies", "Hiring an intern"], "ans" => 1, "xp" => 150],
                ["q" => "What does the 'S' in SMART goals stand for?", "options" => ["Smart", "Specific", "Simple", "Standard"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Tactical Planning'?", "options" => ["Daily chores", "Short-term planning (1 year or less) to implement strategic goals", "Marketing a logo", "Starting a new company"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Mission Statement'?", "options" => ["A list of items to buy", "A statement of the organization's purpose", "The annual profit", "A secret password"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Operational Planning'?", "options" => ["Strategic goal setting", "Detailed planning for day-to-day activities and work groups", "Fixing a machine", "A type of insurance"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'SWOT Analysis'?", "options" => ["Accounting", "Strengths, Weaknesses, Opportunities, Threats", "Cleaning the office", "A sports drill"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Contingency Planning'?", "options" => ["Planning a party", "Developing alternative courses of action if things go wrong", "Saving money only", "Hiring a lawyer"], "ans" => 1, "xp" => 180],
                ["q" => "What is a 'Vision Statement'?", "options" => ["A medical test", "A broad description of what the organization aspires to become in the future", "A list of managers", "A marketing flyer"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'MBO'?", "options" => ["Manage By Orders", "Management by Objectives", "Most Better Office", "Main Business Order"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Forecasting'?", "options" => ["Looking at the past", "Estimating future conditions based on current data", "A weather report only", "A type of tax"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Benchmark'?", "options" => ["A seat in a park", "A standard or point of reference against which things may be compared", "A type of computer", "The CEO's bonus"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Policy' vs 'Procedure'?", "options" => ["They are the same", "Policy is a general guide; Procedure is a step-by-step instruction", "Policy is for managers; Procedure is for staff", "Procedure is a law"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Environmental Scanning'?", "options" => ["Cleaning the park", "Collecting info about external forces (competitors, economy) that affect the business", "Using a printer", "Hiring an expert"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'PESTEL' analysis used for?", "options" => ["Accounting", "Analyzing the macro-environment (Political, Economic, Social, etc.)", "Killing bugs", "A math test"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Competitive Advantage'?", "options" => ["Cheating", "A condition that puts a company in a superior business position", "Having more money", "A type of law"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Business Continuity'?", "options" => ["Paying employees", "The ability to maintain essential functions during and after a disaster", "Working 24 hours", "Never changing"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Top-Down' planning?", "options" => ["Planning from the basement", "Goals are set at the top and passed down to lower levels", "Planning while standing", "A type of meeting"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Bottom-Up' planning?", "options" => ["Planning from the roof", "Lower-level employees participate in setting goals", "A type of exercise", "Buying a new office"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Strategic Alignment'?", "options" => ["A straight line", "Ensuring all departments' goals support the overall organizational strategy", "Managing a team", "Hiring more managers"], "ans" => 1, "xp" => 300],
                ["q" => "What is a 'Single-use Plan'?", "options" => ["A recurring plan", "A plan developed to carry out a course of action that is not likely to be repeated", "A plan for one person", "A secret plan"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is the 'Planning Horizon'?", "options" => ["The sunset", "The length of time into the future that a plan covers", "The size of the office", "A type of marketing"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Crisis Management'?", "options" => ["Daily chores", "Preparation for and response to major, unpredictable negative events", "Hiring staff", "Selling products"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Strategic Control'?", "options" => ["Controlling a remote", "Monitoring the strategy to ensure it is achieving the desired results", "Firing a manager", "Buying new software"], "ans" => 1, "xp" => 400],
                ["q" => "What is the biggest benefit of planning?", "options" => ["It guarantees success", "It provides direction, reduces uncertainty, and minimizes waste", "It makes everyone happy", "It costs nothing"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 1,
        "title" => "Map 111: Organizing & Structuring", "desc" => "Design efficient departments, delegate authority, and manage resources.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Organizing' in management?", "options" => ["Cleaning a desk", "Arranging resources and activities to achieve goals", "Typing a letter", "Filing papers"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Departmentalization'?", "options" => ["Closing an office", "Grouping related activities into units or departments", "Firing employees", "Writing a report"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Functional' structure?", "options" => ["A structure that works", "Grouping employees by similar skills (e.g., Marketing, Finance)", "A structure with no boss", "A type of computer"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Divisional' structure?", "options" => ["Math", "Grouping employees by product, geography, or customer", "A team of three", "A secret meeting"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Matrix' structure?", "options" => ["A movie", "Employees report to two bosses (Functional and Project)", "A structure made of glass", "A new marketing plan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Work Specialization'?", "options" => ["Doing everything", "Dividing work activities into separate job tasks", "Being the best", "Working alone"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Chain of Command'?", "options" => ["A heavy metal chain", "The continuous line of authority that extends from the top to the bottom", "A type of radio", "A station alarm"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Authority'?", "options" => ["Being mean", "The right inherent in a managerial position to tell people what to do", "Knowing everything", "Having a high salary"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Responsibility'?", "options" => ["The obligation to perform any assigned duties", "Doing math", "Blaming others", "Working long hours"], "ans" => 0, "xp" => 210],
                ["q" => "What is 'Accountability'?", "options" => ["Doing math", "Being answerable for the outcome of an assigned task", "Working alone", "Knowing the rules"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Delegation'?", "options" => ["Doing all work", "The assignment of authority to another person to carry out activities", "Firing people", "Hiring staff"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Cross-Functional Team'?", "options" => ["An angry team", "A work team composed of individuals from various functional specialties", "A sports team", "A group of managers only"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Virtual' organization?", "options" => ["A video game", "A small core organization that outsources major business functions", "An office on the moon", "A type of database"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Mechanistic' organization?", "options" => ["A robot company", "A rigid and tightly controlled structure", "A factory only", "A new computer brand"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Organic' organization?", "options" => ["A farm", "A highly adaptive and flexible structure", "A type of food", "A secret organization"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Unity of Direction'?", "options" => ["Walking one way", "The whole organization should move toward the same objective", "A type of compass", "A manager's order"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Line' authority?", "options" => ["Drawing a line", "Authority that entitles a manager to direct the work of an employee", "A type of computer cable", "Working in a line"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Staff' authority?", "options" => ["Authority over a stick", "Positions created to support, assist, and advise line authority holders", "Firing the staff", "Hiring new managers"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Formalization'?", "options" => ["Wearing a suit", "The degree to which jobs within an organization are standardized", "Naming a company", "Buying new furniture"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Organizational Design'?", "options" => ["Drawing a logo", "Creating or changing an organization's structure", "Decorating an office", "Building a website"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Downsizing'?", "options" => ["Buying a small desk", "A planned reduction in the number of employees", "Moving to a new building", "Reducing the price of products"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Outsourcing'?", "options" => ["Working outside", "Hiring external providers to handle functions once done internally", "Selling products", "Hiring more managers"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Project' structure?", "options" => ["A building project", "Employees continuously work on projects", "A list of tasks", "A type of budget"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Learning Organization'?", "options" => ["A school", "An organization that has developed the capacity to continuously learn and change", "A library", "A group of students"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 1,
        "title" => "Map 112: Leading & Team Management", "desc" => "Inspire your team, manage dynamics, and lead through influence.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Leading' in management?", "options" => ["Walking in front", "Motivating, directing, and influencing people to work hard", "Giving orders only", "Being the loudest"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Motivation'?", "options" => ["Being tired", "The processes that account for an individual's intensity and persistence toward a goal", "A salary bonus", "Working 24 hours"], "ans" => 1, "xp" => 150],
                ["q" => "Which theory is about 'Hierarchy of Needs'?", "options" => ["Taylor's Theory", "Maslow's Theory", "Herzberg's Theory", "McGregor's Theory"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Theory X'?", "options" => ["Employees are lazy and must be controlled", "Employees love work", "A secret project", "A type of computer"], "ans" => 0, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Theory Y'?", "options" => ["Employees are lazy", "Employees are self-directed and enjoy work", "A math equation", "A type of tax"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Autocratic' leadership?", "options" => ["Driving a car", "A leader who dictates work methods and limits participation", "A leader who does no work", "A team-based leader"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Democratic' leadership?", "options" => ["A political system", "A leader who involves employees in decision-making", "A strict leader", "A leader who hides"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Laissez-faire' leadership?", "options" => ["A type of food", "A 'hands-off' leader who lets the group make decisions", "A strict leader", "A fast leader"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Emotional Intelligence' (EQ)?", "options" => ["Being smart", "The ability to manage your own and others' emotions", "Being emotional", "Having a high IQ"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Team'?", "options" => ["A group of friends", "A group whose individual efforts result in performance greater than the sum of parts", "A list of employees", "A soccer club"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Groupthink'?", "options" => ["Thinking as a group", "When group members seek agreement at the expense of realistic appraisal", "A new strategy", "A team meeting"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Conflict'?", "options" => ["Perceived incompatible differences resulting in interference", "A team meeting", "A type of weather", "A promotion"], "ans" => 0, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Transformational' leadership?", "options" => ["Changing clothes", "Leaders who inspire followers to transcend self-interest for the good of the firm", "A strict boss", "Hiring staff"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Transactional' leadership?", "options" => ["Leadership about money", "Leaders who lead primarily by using social exchanges (rewards for work)", "A visionary leader", "A type of banking"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Servant' leadership?", "options" => ["Being a servant", "A leader who focuses on the needs of others and the community", "A leader who does no work", "A strict leader"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Charismatic' leadership?", "options" => ["A funny leader", "An enthusiastic, self-confident leader whose personality influences people", "A quiet leader", "A manager"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is the 'Storming' stage of team development?", "options" => ["A weather event", "Conflict as members resist group constraints", "The team finishing a project", "The first meeting"], "ans" => 1, "xp" => 300],
                ["q" => "What is the 'Norming' stage?", "options" => ["Being normal", "Developing close relationships and cohesiveness", "Fighting", "Ending the team"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Power'?", "options" => ["Electricity", "The capacity to influence the behavior of others", "Money", "A fast car"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Legitimate' power?", "options" => ["Being smart", "Power derived from a person's position in the hierarchy", "Being loud", "Giving gifts"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Expert' power?", "options" => ["Being a boss", "Influence based on special skills or knowledge", "Having money", "Giving orders"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Referent' power?", "options" => ["Reading a book", "Influence based on possession of desirable resources or personal traits", "Hiring people", "Writing a report"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Synergy'?", "options" => ["A type of energy", "When the whole is greater than the sum of its parts", "A type of software", "Working alone"], "ans" => 1, "xp" => 400],
                ["q" => "What is the key to effective leadership?", "options" => ["Being mean", "Adapting your style to the situation and the people", "Having a high salary", "Using the newest technology"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 2: CORE COMPETENCIES (Maps 113-116)
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 2,
        "title" => "Map 113: Control & Monitoring", "desc" => "Ensure goals are met through performance standards and corrective action.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Controlling'?", "options" => ["Being a bully", "Monitoring activities to ensure they are being accomplished as planned", "Stopping all work", "Watching cameras"], "ans" => 1, "xp" => 150],
                ["q" => "What is the first step in the Control Process?", "options" => ["Firing staff", "Measuring actual performance", "Setting standards", "Correcting errors"], "ans" => 2, "xp" => 150],
                ["q" => "What is a 'Standard'?", "options" => ["A type of car", "A performance goal against which actual performance is compared", "A company flag", "The office floor"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Variance'?", "options" => ["A math error", "The difference between actual performance and the standard", "Spending money", "Total profit"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Feedforward' control?", "options" => ["Control after work", "Control that takes place before a work activity is done", "Giving food to staff", "A type of radio"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Concurrent' control?", "options" => ["Control during work", "Control that takes place while a work activity is in progress", "Control at night", "A type of tax"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Feedback' control?", "options" => ["A loud noise", "Control that takes place after a work activity is done", "A type of payment", "A marketing flyer"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Corrective Action'?", "options" => ["Being mean", "Steps taken to get performance back on track", "Firing everyone", "Buying new gear"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Management by Exception' (MBE)?", "options" => ["Managing everything", "Managers only focus on significant deviations from the standard", "Managing for fun", "A type of insurance"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Budget' as a control tool?", "options" => ["A pile of cash", "A financial plan used to monitor and limit spending", "A list of employees", "A marketing flyer"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Quality Control'?", "options" => ["Being nice", "The process of ensuring products or services meet specific requirements", "A type of sales", "Hiring an expert"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'TQM'?", "options" => ["Total Quality Management", "Technical Quality Mode", "Team Quantity Management", "Total Quick Market"], "ans" => 0, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Ratio Analysis'?", "options" => ["Math homework", "Using financial ratios to evaluate a company's performance", "Dividing food", "A type of graph"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Inventory Control'?", "options" => ["Counting pens", "Managing the amount of stock held by a business", "Buying a new truck", "Selling shares"], "ans" => 1, "xp" => 250],
                ["q" => "What is an 'Internal Audit'?", "options" => ["A government check", "A systematic review of a company's operations and controls by its own staff", "Firing staff", "Counting cash"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Management Information System' (MIS)?", "options" => ["A type of PC", "A system used to provide managers with needed information", "A secret code", "A station alarm"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Balanced Scorecard'?", "options" => ["A sports score", "A performance metric used to see multiple perspectives (Financial, Customer, etc.)", "A math test", "A type of budget"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Bureaucratic' control?", "options" => ["Control by a desk", "Using rules, regulations, and authority to maintain control", "Control by a computer", "A new strategy"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Clan' control?", "options" => ["Control by family only", "Control based on shared values, norms, and traditions", "Control by a boss", "A type of tax"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'ROI'?", "options" => ["Rate of Interest", "Return on Investment", "Regional Office Income", "Regular Office Index"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Liquidity'?", "options" => ["Water", "How quickly a company can turn assets into cash to pay debts", "The speed of sales", "The office rent"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Benchmarking'?", "options" => ["Buying a bench", "Comparing your performance against industry leaders", "Cleaning the office", "Hiring staff"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Employee Theft' prevention?", "options" => ["Watching everyone", "Implementing controls and a positive culture to reduce shrinkage", "Firing everyone", "Using a safe"], "ans" => 1, "xp" => 400],
                ["q" => "What is the final goal of control?", "options" => ["To stop all work", "To ensure the organization reaches its goals efficiently and effectively", "To make more money", "To hire more people"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 2,
        "title" => "Map 114: Business Ethics & Law", "desc" => "Navigate the moral and legal landscape of professional management.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Business Ethics'?", "options" => ["Doing math", "Moral principles that guide business behavior", "Company rules on dress code", "A type of accounting"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Compliance'?", "options" => ["Being nice", "The act of obeying laws and regulations", "A promotion", "A marketing plan"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Code of Conduct'?", "options" => ["A type of code", "A set of rules for social behavior in a workplace", "A daily schedule", "A software license"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Transparency'?", "options" => ["A window", "Operating in a way that is easy for others to see actions", "Hiding the truth", "A type of software"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Whistleblowing'?", "options" => ["Playing sports", "Reporting organizational misconduct to higher authorities", "A manager", "A security guard"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Conflict of Interest'?", "options" => ["Disliking your job", "Personal interests influencing professional duties", "Two people fighting", "A broken contract"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Corporate Social Responsibility' (CSR)?", "options" => ["Working on weekends", "Operating in ways that enhance society and the environment", "Paying taxes", "A marketing strategy"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Insider Trading'?", "options" => ["Trading in the office", "Illegal trading based on non-public confidential information", "Selling products", "A team meeting"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Bribery'?", "options" => ["Giving a gift", "Dishonestly persuading someone by money or inducements", "A salary increase", "A marketing plan"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Fiduciary Duty'?", "options" => ["A type of tax", "A legal obligation to act in the best interest of another", "Working hard", "A salary bonus"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Intellectual Property' (IP)?", "options" => ["Owning a house", "Creations of the mind protected by law", "A fast computer", "A smart person"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Copyright'?", "options" => ["The right to copy", "Exclusive legal right to original creative work", "A brand name", "A type of patent"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is a 'Trademark'?", "options" => ["A trade mark", "A recognizable sign or design identifying products", "A patent", "A legal contract"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Data Privacy'?", "options" => ["A password", "Protection of personal info from unauthorized access", "A firewall", "Cloud storage"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Discrimination'?", "options" => ["Hiring the best", "Unjust treatment based on category (race, gender, etc.)", "A promotion", "A training session"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Sustainability'?", "options" => ["Working forever", "Meeting current needs without compromising the future", "Saving money", "Marketing"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Labor Law'?", "options" => ["Rules for manual labor", "Law mediating relationship between workers and employers", "Gardening rules", "A type of contract"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Consumer Protection'?", "options" => ["Protecting a store", "Laws protecting people from unfair business practices", "Buying shares", "A bank statement"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Liability' in law?", "options" => ["An asset", "Legal responsibility for one's acts or omissions", "Hiring staff", "A marketing flyer"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Due Diligence'?", "options" => ["Working hard", "Reasonable steps taken to satisfy legal requirements", "Paying taxes", "Hiring a manager"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Equality' vs 'Equity'?", "options" => ["They are the same", "Equality is same for all; Equity is giving what is needed for success", "Equality for pay; Equity for hours", "A type of law"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Work-Life Balance'?", "options" => ["Working and sleeping", "Equilibrium between work and personal life", "Working 24 hours", "Being unemployed"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Corporate Governance'?", "options" => ["Government work", "Rules and processes by which a firm is directed", "Managing a team", "Buying software"], "ans" => 1, "xp" => 400],
                ["q" => "What is the primary value of integrity?", "options" => ["It makes money", "It builds long-term trust and a sustainable reputation", "It saves time", "It is free"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 2,
        "title" => "Map 115: Performance Management", "desc" => "Develop your people through appraisal, feedback, and career paths.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Performance Management'?", "options" => ["Firing people", "Process of ensuring employees meet goals and improve skills", "Paying payroll", "Hiring staff"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Performance Appraisal'?", "options" => ["Watching a movie", "Formal evaluation of an employee's work over a period", "A salary increase", "A team meeting"], "ans" => 1, "xp" => 150],
                ["q" => "What is '360-Degree Feedback'?", "options" => ["Spinning around", "Feedback from bosses, peers, subordinates, and customers", "A long meeting", "A type of bonus"], "ans" => 1, "xp" => 150],
                ["q" => "What are 'KPIs'?", "options" => ["Key Performance Indicators", "Keeping People Informed", "Known Product Items", "Key Process Interactions"], "ans" => 0, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Coaching'?", "options" => ["Yelling at staff", "Providing guidance to help employees improve specific skills", "Doing the work for them", "Playing sports"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Mentoring'?", "options" => ["Teaching a class", "A long-term relationship where a senior person guides a junior one", "Paying for a tutor", "Hiring staff"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Employee Development'?", "options" => ["Buying new chairs", "Improving the skills and knowledge of the workforce", "Firing people", "Writing a report"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Self-Appraisal'?", "options" => ["Boss judging you", "Evaluating your own performance", "Talking to yourself", "A secret"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Constructive Criticism'?", "options" => ["Complaining", "Feedback intended to help someone improve", "Insulting work", "Saying only positive things"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Development Plan'?", "options" => ["A map", "A document outlining an employee's growth goals and steps", "A list of tasks", "A budget"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Incentive'?", "options" => ["A tax", "A reward offered to motivate hard work", "A punishment", "A type of gear"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Merit Pay'?", "options" => ["A loan", "Salary increase based on performance", "A fine", "A gift"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Succession Planning'?", "options" => ["Planning a party", "Identifying and developing future leaders for key roles", "Saving money", "Hiring interns"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Job Rotation'?", "options" => ["Spinning a chair", "Moving employees between different jobs to increase skills", "Firing people", "Working at night"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Underperformance'?", "options" => ["Working too hard", "Failing to meet the required standards of a role", "A type of bonus", "Being fast"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Disciplinary Action'?", "options" => ["Exercise at work", "Steps taken by an employer in response to misconduct", "A promotion", "A meeting"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Intrinsic' motivation?", "options" => ["Money", "Motivation coming from internal satisfaction of the work", "A bonus", "A car"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Extrinsic' motivation?", "options" => ["Love of work", "Motivation from external rewards like money or status", "Internal feelings", "Doing a hobby"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Employee Engagement'?", "options" => ["A wedding", "Emotional commitment to the organization and its goals", "Being busy", "Talking to coworkers"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Talent Management'?", "options" => ["Being a singer", "Strategic process of attracting, developing, and keeping employees", "Fixing computers", "Selling products"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Burnout'?", "options" => ["A fire", "Physical or mental collapse caused by overwork or stress", "Running out of coffee", "Quitting"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Emotional Resilience'?", "options" => ["Being sad", "The ability to recover quickly from difficulties", "Having power", "Working alone"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Soft Skill'?", "options" => ["Typing", "Interpersonal skills like communication and teamwork", "Fixing hardware", "Coding"], "ans" => 1, "xp" => 400],
                ["q" => "What is the most effective way to improve performance?", "options" => ["Fear", "Clear communication, regular feedback, and support", "Ignoring errors", "Increasing work hours only"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 2,
        "title" => "Map 116: Financial Management for Managers", "desc" => "Understand budgets, cash flow, and cost control without being an accountant.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is a 'Budget'?", "options" => ["A pile of cash", "A financial plan for a set period", "A list of employees", "A marketing plan"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Revenue'?", "options" => ["Profit", "Total money received from sales", "Spending money", "Tax"], "ans" => 1, "xp" => 150],
                ["q" => "What is an 'Expense'?", "options" => ["Money received", "Money spent to generate revenue", "Profit", "A bank deposit"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Profit'?", "options" => ["Total sales", "Revenue minus expenses", "A loan", "Hiding money"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Fixed Cost'?", "options" => ["Cost that changes", "Cost that stays the same regardless of production (e.g. rent)", "A bonus", "A bank fee"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Variable Cost'?", "options" => ["Cost that stays same", "Cost that changes with production volume", "Profit", "Office rent"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Cash Flow'?", "options" => ["Money in a river", "Movement of money in and out of a business", "Total profit", "A bank loan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Overhead'?", "options" => ["The ceiling", "Ongoing expenses not linked to a specific product", "Boss's salary", "Tax"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Break-even Point'?", "options" => ["Breaking a bone", "When total revenue equals total costs", "Making money", "Losing everything"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Accounts Receivable'?", "options" => ["Money you owe", "Money customers owe you", "The bank account", "Payroll"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Accounts Payable'?", "options" => ["Money customers owe you", "Money you owe to suppliers", "Total revenue", "Petty cash"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'ROI'?", "options" => ["Return on Investment", "Rate of Interest", "Regular Office Item", "Regional Office Income"], "ans" => 0, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Depreciation'?", "options" => ["Gaining value", "Decrease in asset value over time", "A tax", "Saving money"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Working Capital'?", "options" => ["Building", "Current assets minus current liabilities", "A loan", "Office rent"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Balance Sheet'?", "options" => ["Balancing books", "Report of assets, liabilities, and equity", "Daily sales list", "Invoice"], "ans" => 1, "xp" => 250],
                ["q" => "What is a 'Profit and Loss' (P&L)?", "options" => ["Employee names", "Summary of revenue, costs, and expenses", "A bank loan", "A secret"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Variance Analysis'?", "options" => ["Science experiment", "Comparing actual results to budgeted amounts", "Ignoring errors", "Spending more"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Gross Margin'?", "options" => ["Profit after tax", "Revenue minus Cost of Goods Sold", "Total revenue", "CEO salary"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'CapEx'?", "options" => ["Money for lunch", "Capital Expenditure (buying long-term assets)", "Daily bills", "Taxes"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'OpEx'?", "options" => ["Buying building", "Operating Expenditure (day-to-day running costs)", "Selling shares", "Stock investing"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Audit'?", "options" => ["A room", "Examination of accounts for accuracy", "Spending money", "A loan"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Fiscal Year'?", "options" => ["Leap year", "12-month period for financial reporting", "Calendar year only", "13 months"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Asset'?", "options" => ["Debt", "Something the business owns that has value", "Expense", "Hiring people"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Liability'?", "options" => ["Something you own", "Something the business owes", "Total revenue", "Bonus"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],

    // CATEGORY 3: STRATEGY & CAPSTONE (Maps 117-120)
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 3,
        "title" => "Map 117: Change Management", "desc" => "Lead through transitions, manage resistance, and evolve the organization.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Change Management'?", "options" => ["Changing clothes", "Process of transitioning teams to a future state", "Counting cash", "Hiring staff"], "ans" => 1, "xp" => 150],
                ["q" => "Why do people often resist change?", "options" => ["They are lazy", "Fear of the unknown and loss of security", "They hate managers", "It's too fast"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Lewin's 3-Step Model'?", "options" => ["Run, Hide, Fight", "Unfreeze, Change, Refreeze", "Hire, Train, Fire", "Plan, Do, Review"], "ans" => 1, "xp" => 150],
                ["q" => "What is a 'Change Agent'?", "options" => ["A secret spy", "Someone who facilitates and supports the change process", "A robot", "The CEO only"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Incremental' change?", "options" => ["Sudden change", "Small, gradual adjustments over time", "Closing a company", "Massive layoff"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Transformational' change?", "options" => ["Small change", "Radical and fundamental shift in the organization", "Changing a logo", "Hiring an intern"], "ans" => 1, "xp" => 180],
                ["q" => "What is the key to reducing resistance to change?", "options" => ["Fear", "Communication and participation", "Ignoring complaints", "Paying more"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Force Field Analysis'?", "options" => ["A sci-fi game", "A tool to look at forces for and against change", "Protecting a building", "A math test"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Kotter's 8-Step Process'?", "options" => ["Cooking steps", "A methodology for leading organizational change", "Hiring steps", "Daily chores"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Urgency' in change?", "options" => ["Being late", "Establishing a reason why change is needed NOW", "Running fast", "A medical term"], "ans" => 1, "xp" => 210],
                ["q" => "What is a 'Guiding Coalition'?", "options" => ["A military group", "A group of influential people who lead the change effort", "A list of tasks", "A secret meeting"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Empowerment' during change?", "options" => ["Giving electricity", "Removing obstacles and giving people authority to act", "Being a boss", "Writing a report"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Short-term Wins' importance?", "options" => ["Losing weight", "Building momentum and showing that the change is working", "Ending the project early", "Paying bonuses"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Organizational Development' (OD)?", "options" => ["Building a house", "System-wide application of behavioral science to planned development", "Hiring staff", "Selling products"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Agility'?", "options" => ["Running", "The ability of an organization to react fast to change", "Strength", "Being old"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Culture' role in change?", "options" => ["No role", "It can be the biggest barrier or the biggest support for change", "It's about art", "It only affects food"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Change Fatigue'?", "options" => ["Being sleepy", "Exhaustion caused by too many changes in a short time", "A broken machine", "Quitting work"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Stakeholder Mapping'?", "options" => ["Drawing a map", "Identifying everyone affected by change and their level of influence", "Selling products", "Hiring staff"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Refreezing'?", "options" => ["Cold weather", "Stabilizing the change so it becomes the new permanent state", "Ice cream", "Stopping work"], "ans" => 1, "xp" => 300],
                ["q" => "What is the biggest mistake in change management?", "options" => ["Changing too much", "Failure to communicate the vision and rationale for change", "Hiring experts", "Using new software"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Innovation'?", "options" => ["Copying others", "Successful implementation of creative ideas", "Fixing a phone", "Buying new PC"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Disruptive' change?", "options" => ["Loud change", "Change that alters the whole industry (e.g. Uber)", "Broken change", "Type of alarm"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Process Reengineering'?", "options" => ["Fixing truck", "Redesigning work processes to improve efficiency", "Hiring staff", "Writing diary"], "ans" => 1, "xp" => 400],
                ["q" => "What is the final goal of change management?", "options" => ["To finish work", "To successfully adapt the organization to survive and thrive", "To make profit", "To hire more people"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 3,
        "title" => "Map 118: Conflict & Negotiation", "desc" => "Turn disagreements into productive outcomes using strategic negotiation.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Conflict Management'?", "options" => ["Starting a fight", "Process of limiting negative aspects and increasing positive aspects of conflict", "Ignoring problems", "Firing people"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Functional' conflict?", "options" => ["Conflict that breaks things", "Constructive conflict that supports goals", "A broken machine", "A quiet office"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Dysfunctional' conflict?", "options" => ["Conflict that works", "Destructive conflict that hinders performance", "A secret meeting", "A type of tax"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Negotiation'?", "options" => ["Giving orders", "Process where two parties exchange goods/services and try to agree on an exchange rate", "Fighting", "Silent treatment"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Distributive' bargaining?", "options" => ["Sharing fairly", "Win-lose negotiation over a fixed amount of resources", "Giving things for free", "A bank loan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Integrative' bargaining?", "options" => ["Lose-lose", "Win-win negotiation that seeks to expand the resources so both parties win", "Fighting for more", "Buying products"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'BATNA'?", "options" => ["Best Alternative To a Negotiated Agreement", "Better Account Total Net Asset", "Big Area Technical New Access", "Business Admin Task New Area"], "ans" => 0, "xp" => 180],
                ["q" => "What is 'Mediation'?", "options" => ["A judge making a choice", "A neutral third party facilitating a solution", "Fighting", "A team meeting"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Arbitration'?", "options" => ["A friendly talk", "A third party with authority to dictate an agreement", "Ignoring the issue", "Hiring staff"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Avoiding' conflict style?", "options" => ["Fighting", "Ignoring the conflict and hoping it goes away", "Winning", "Talking"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Collaborating' style?", "options" => ["Winning at all costs", "Trying to find a win-win solution that satisfies everyone", "Giving in", "Hiding"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Compromising' style?", "options" => ["Giving up everything", "Each party giving up something to reach a middle ground", "Winning", "Starting a fight"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Accommodating' style?", "options" => ["Fighting", "Putting the other party's needs above your own", "Winning", "Ignoring"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Competing' style?", "options" => ["Helping others", "Pursuing your own interests at the expense of others", "Sharing", "Quitting"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Emotional Intelligence' in conflict?", "options" => ["Being smart", "Ability to remain calm and understand the other person's perspective", "Crying", "Losing control"], "ans" => 1, "xp" => 250],
                ["q" => "What is the value of 'Empathy' in negotiation?", "options" => ["Giving money", "Understanding the other party's interests and underlying needs", "Winning", "Lying"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Anchor' in negotiation?", "options" => ["A boat tool", "The first offer made which sets the stage for the talk", "The end of a sale", "A type of tax"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Reservation Price'?", "options" => ["Buying a hotel", "The worst offer you are willing to accept before walking away", "The total profit", "A bank fee"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'ZOPA'?", "options" => ["Zone of Possible Agreement", "Zero Office Profit Area", "Zone of Partial Access", "Zebra Office Payment Act"], "ans" => 0, "xp" => 300],
                ["q" => "What is 'Non-Verbal' cue in negotiation?", "options" => ["Words", "Body language and tone of voice", "Writing a letter", "Sending email"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Diplomacy'?", "options" => ["Politics", "Dealing with people in a sensitive and effective way", "Being aggressive", "Ignoring problems"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Assertiveness'?", "options" => ["Being bully", "Standing up for your interests without attacking others", "Shy", "Giving in"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Rapport'?", "options" => ["A report", "A harmonious connection and trust built with the other party", "Shift end", "A bill"], "ans" => 1, "xp" => 400],
                ["q" => "What is the biggest secret to winning a negotiation?", "options" => ["Being loud", "Preparation and understanding the other side's needs", "Having more money", "Threatening to quit"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 3,
        "title" => "Map 119: Strategic Management", "desc" => "Design the future of the firm through analysis, choice, and implementation.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Strategic Management'?", "options" => ["Daily task lists", "Ongoing process of creating and implementing strategies for competitive advantage", "Hiring staff", "Selling products"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Competitive Advantage'?", "options" => ["Cheating", "What an organization does better than others", "Having more money", "A type of law"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Core Competency'?", "options" => ["Being good at everything", "Unique skills or assets that give a firm a competitive edge", "Professional skills", "Writing reports"], "ans" => 1, "xp" => 150],
                ["q" => "What is 'Vertical Integration'?", "options" => ["Standing up", "When a firm grows by gaining control of its suppliers or distributors", "Hiring more staff", "Merging companies"], "ans" => 1, "xp" => 150]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Horizontal Integration'?", "options" => ["Lying down", "When a firm grows by combining with competitors", "Quitting", "Salary bonus"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Diversification'?", "options" => ["Hiring different people", "Moving into new industries or products", "Winning prize", "Spending profit"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Blue Ocean Strategy'?", "options" => ["Fishing", "Creating a new, uncontested market space", "Selling near water", "Marketing plan"], "ans" => 1, "xp" => 180],
                ["q" => "What is 'Porter's Five Forces'?", "options" => ["A team of five", "A framework to analyze industry competition", "A math test", "A station alarm"], "ans" => 1, "xp" => 180]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Cost Leadership'?", "options" => ["Being cheap", "Strategy to be the lowest-cost producer in the industry", "Winning prize", "Giving money away"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Differentiation' strategy?", "options" => ["Being the same", "Developing unique products that customers value more than competitors'", "Deleting records", "Hiring manager"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'Focus' strategy?", "options" => ["Staring at something", "Concentrating on a narrow market segment (niche)", "The whole world", "Advertising only"], "ans" => 1, "xp" => 210],
                ["q" => "What is 'BCG Matrix' used for?", "options" => ["Math", "Analyzing a company's portfolio of businesses (Stars, Cash Cows, etc.)", "Cleaning office", "Selling shares"], "ans" => 1, "xp" => 210]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'Market Penetration'?", "options" => ["Entering store", "Increasing sales of current products in current markets", "A type of tax", "Buying competitor"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Market Development'?", "options" => ["Building a shop", "Selling current products in new markets", "Slow sales", "Hiring intern"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Product Development'?", "options" => ["Buying factory", "Selling new products in current markets", "A loan", "Selling shares"], "ans" => 1, "xp" => 250],
                ["q" => "What is 'Global Strategy'?", "options" => ["Buying a map", "Standardizing products and marketing worldwide", "Moving to new city", "Hiring remote staff"], "ans" => 1, "xp" => 250]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "What is 'Scenario Planning'?", "options" => ["Acting", "Identifying multiple future possibilities and planning for them", "Saving money", "Hiring staff"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Merger'?", "options" => ["Hiring manager", "Two companies combining to form a new one", "Closing a shop", "A car accident"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Acquisition'?", "options" => ["Starting a business", "One company buying another", "Selling a truck", "Deleting a file"], "ans" => 1, "xp" => 300],
                ["q" => "What is 'Synergy'?", "options" => ["A type of fuel", "When 1+1=3 (combined power is greater than sum of parts)", "A bonus", "Working alone"], "ans" => 1, "xp" => 300]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "What is 'First-mover' advantage?", "options" => ["Being fast runner", "Benefit from being the first to enter a market", "Having first office", "Hiring first person"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Sustainable' strategy?", "options" => ["Growing forever", "Strategy that provides long-term success without harming resources", "Saving money", "Marketing"], "ans" => 1, "xp" => 400],
                ["q" => "What is 'Corporate Culture' in strategy?", "options" => ["Art", "Values that must support the strategy to make it successful", "Rules", "Dress code"], "ans" => 1, "xp" => 400],
                ["q" => "What is the biggest threat to any strategy?", "options" => ["Being cheap", "Poor execution and failure to adapt to environmental changes", "Hiring staff", "Buying new gear"], "ans" => 1, "xp" => 400]
            ]]
        ]
    ],
    [
        "id" => ++$highest_id, "course_id" => $gm_course_id, "category_id" => 3,
        "title" => "Map 120: Generic Management Capstone", "desc" => "The final exam. Integrate all functions of management to lead a high-performing organization.",
        "levels" => [
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: Your department's productivity has dropped by 20%. What is the FIRST step?", "options" => ["Fire the lowest performers", "Measure actual performance and compare it to standards to find the cause", "Increase everyone's hours", "Buy new software"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You are launching a new 5-year vision. What level of planning is this?", "options" => ["Operational", "Strategic", "Tactical", "Routine"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: An employee is struggling with a specific task but has potential. What do you provide?", "options" => ["A final warning", "Coaching and training", "A pay cut", "Nothing"], "ans" => 1, "xp" => 200],
                ["q" => "SCENARIO: You need to hire someone to bridge the gap between IT and Sales. What skill is most important?", "options" => ["Typing", "Interpersonal/Human Skills", "Mechanical knowledge", "Singing"], "ans" => 1, "xp" => 200]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: A major competitor has closed down. What is this in a SWOT analysis?", "options" => ["Strength", "Opportunity", "Weakness", "Threat"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You are delegating a project. What MUST you give along with the responsibility?", "options" => ["A car", "Authority to make decisions", "A bonus upfront", "A secret password"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: Two managers are fighting over resources. What is the BEST resolution style for a long-term win?", "options" => ["Avoiding", "Collaborating", "Competing", "Accommodating"], "ans" => 1, "xp" => 240],
                ["q" => "SCENARIO: You notice an employee is not following the safety SOP. What do you do?", "options" => ["Ignore it", "Take immediate corrective action", "Tell their friends", "Wait for the annual review"], "ans" => 1, "xp" => 240]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "SCENARIO: A company wants to be 'King of the Market' by being the cheapest. What is this strategy?", "options" => ["Differentiation", "Cost Leadership", "Focus", "Niche"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You need to prove you know a password without revealing it. What is this called?", "options" => ["A signature", "A zero-knowledge proof", "A secure email", "A fingerprint"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: The team is in the 'Storming' stage. What should the leader do?", "options" => ["Dissolve the team", "Help members resolve conflicts and define roles", "Ignore the fighting", "Hire a new team"], "ans" => 1, "xp" => 280],
                ["q" => "SCENARIO: You want to measure if customers are likely to recommend you. What metric do you use?", "options" => ["Net Profit", "Net Promoter Score (NPS)", "Employee turnover", "Office rent"], "ans" => 1, "xp" => 280]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "SCENARIO: A new law requires you to protect personal data. Which act is this in SA?", "options" => ["Labor Law", "POPIA", "Companies Act", "Traffic Act"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You are spending $10k on ads to get $100k in sales. What is the ROI?", "options" => ["$10k", "900% (or 10:1)", "50%", "Zero"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: You need to move authority away from the head office. What is this process?", "options" => ["Centralization", "Decentralization", "Downsizing", "Outsourcing"], "ans" => 1, "xp" => 350],
                ["q" => "SCENARIO: An employee reports unethical behavior by a supervisor. How do you handle it?", "options" => ["Tell the supervisor", "Follow the Whistleblower policy and protect the employee", "Ignore it", "Fire the employee"], "ans" => 1, "xp" => 350]
            ]],
            ["offset" => 60, "questions" => [
                ["q" => "MASTER FINAL: What is the primary focus of an effective Manager?", "options" => ["Making everyone happy", "Planning, Organizing, Leading, and Controlling to reach goals", "Buying the best computers", "Having the largest office"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: In high-level management, what determines the success of a firm?", "options" => ["The number of employees", "Strategic alignment and operational excellence", "Having a lot of money", "Hiring a famous CEO"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: Why is EQ often more important than IQ for a manager?", "options" => ["It's not", "Because management is about people, and EQ drives collaboration and trust", "Because it's easier to learn", "Because IQ is for interns only"], "ans" => 1, "xp" => 400],
                ["q" => "MASTER FINAL: What is the 'Triple Bottom Line'?", "options" => ["Profit, Profit, Profit", "People, Planet, Profit", "Sales, Marketing, IT", "Money, Power, Fame"], "ans" => 1, "xp" => 400]
            ]],
            ["offset" => -60, "questions" => [
                ["q" => "EXAM: How do Planning and Controlling interact?", "options" => ["They are separate", "They form a loop where planning sets the goals and control checks if they are met", "They compete for the CEO's attention", "They only interact during parties"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the most critical factor for an organization's survival?", "options" => ["A lot of cash", "The ability to adapt to changes in the environment", "Having the best marketing", "Having the most employees"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the hallmark of a world-class manager?", "options" => ["Being aggressive", "Strategic vision, integrity, and the ability to empower others", "Having a lot of money", "Using the newest software"], "ans" => 1, "xp" => 600],
                ["q" => "EXAM: What is the true goal of generic management skills?", "options" => ["Getting a job", "Providing a repeatable and scalable foundation for organizational success", "Winning a prize", "Hiring friends"], "ans" => 1, "xp" => 600]
            ]]
        ]
    ]
];

// Combine everything
$final_data = array_merge($cleaned_data, $new_maps, $inter_maps, $advanced_maps, $biz_maps, $comm_maps, $cs_maps, $entrep_maps, $fin_maps, $fire_maps, $gm_maps);

set_config('journey_data', json_encode($final_data), 'local_sisizathu');

echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px; background-color: #f9f9f9; padding: 40px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 600px; margin-left: auto; margin-right: auto;'>";
echo "<h2 style='color: #2e7d32;'>✅ Massive Curriculum Upgrade Complete!</h2>";
echo "<p style='font-size: 16px; color: #555;'>Successfully wiped incorrect maps and loaded <b>12 Professional Maps</b> (72 levels, 288 rigorous questions) into the Basic Computer Skills course.</p>";
echo "<div style='text-align: left; background: #fff; padding: 15px; border-radius: 8px; font-size: 14px; color: #333; margin-bottom: 30px;'>";
echo "<strong>Categories Loaded:</strong><br>";
echo "1. Foundational Modules (4 Maps)<br>";
echo "2. Core Competencies (4 Maps)<br>";
echo "3. Valedictory Capstone (4 Maps)";
echo "</div>";
echo "<p><a href='gamified_journey.php' style='padding:12px 24px; background:#F37021; color:white; font-weight: bold; border-radius:8px; text-decoration:none; display: inline-block;'>Return to Gamified Journey</a></p>";
echo "</div>";