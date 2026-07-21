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

$target_course_id = $basic_course ? $basic_course->id : 15; 
$wrong_course_id = $inter_course ? $inter_course->id : 18;
$advanced_course_id = $advanced_course ? $advanced_course->id : 21; // Fallback ID

// 1. Fetch current data and REMOVE maps from the wrong course
$current_data_json = get_config('local_sisizathu', 'journey_data') ?: '[]';
$current_data = json_decode($current_data_json, true);
$cleaned_data = [];
$highest_id = 0;

if ($map['course_id'] != $wrong_course_id && $map['course_id'] != $target_course_id && $map['course_id'] != $advanced_course_id) {
    $cleaned_data[] = $map; 
}

foreach ($current_data as $map) {
    if ($map['course_id'] != $wrong_course_id && $map['course_id'] != $target_course_id) {
        $cleaned_data[] = $map; // Keep maps belonging to other unrelated courses
    }
    if ($map['id'] > $highest_id) $highest_id = $map['id'];
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

// Combine everything
$final_data = array_merge($cleaned_data, $new_maps, $inter_maps, $advanced_maps);

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