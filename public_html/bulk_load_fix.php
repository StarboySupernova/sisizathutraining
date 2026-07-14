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

$target_course_id = $basic_course ? $basic_course->id : 15; // Fallback if not found
$wrong_course_id = $inter_course ? $inter_course->id : 18;

// 1. Fetch current data and REMOVE maps from the wrong course
$current_data_json = get_config('local_sisizathu', 'journey_data') ?: '[]';
$current_data = json_decode($current_data_json, true);
$cleaned_data = [];
$highest_id = 0;

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

// Merge cleaned data (unrelated courses) with our new 12 maps
$final_data = array_merge($cleaned_data, $new_maps);
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