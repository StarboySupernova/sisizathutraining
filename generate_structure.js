#!/usr/bin/env node

/* eslint-disable */ // ← Prevents ESLint from breaking this file

/**
 * Advanced Project Structure Generator
 * With smart folder & tech-specific emojis
 */

const fs = require("fs");
const path = require("path");

const OUTPUT_FILE = "project_structure.md";

const IGNORED_DIRS = new Set([
  "node_modules",
  ".git",
  "dist",
  "build",
  ".next",
  "out",
  "DerivedData",
  "Pods",
  ".build",
  "vendor",
  "__MACOSX",
  ".vscode",
  ".idea",
]);

const ALLOWED_EXT = new Set([
  ".js",
  ".ts",
  ".jsx",
  ".tsx",
  ".json",
  ".html",
  ".css",
  ".scss",
  ".md",
  ".swift",
  ".m",
  ".mm",
  ".h",
  ".c",
  ".cpp",
  ".plist",
  ".xml",
  ".sh",
  ".php",
  ".blade.php",
]);

const EXT_TO_LANG = {
  ".js": "JavaScript",
  ".ts": "TypeScript",
  ".jsx": "JSX",
  ".tsx": "TSX",
  ".json": "JSON",
  ".html": "HTML",
  ".css": "CSS",
  ".scss": "SCSS",
  ".md": "Markdown",
  ".swift": "Swift",
  ".m": "Objective-C",
  ".mm": "Objective-C++",
  ".h": "Header",
  ".c": "C",
  ".cpp": "C++",
  ".plist": "Plist",
  ".xml": "XML",
  ".sh": "Shell",
  ".php": "PHP",
  ".blade.php": "Blade",
};

// Tech-specific emoji mapping
function getFileEmoji(filename) {
  const ext = path.extname(filename).toLowerCase();
  const name = filename.toLowerCase();

  // React / Frontend
  if (
    name.includes("app.") ||
    name.includes("page.") ||
    name.includes("layout.")
  )
    return "📄⚛️ ";
  if (name.includes("component") || name.includes("comp.")) return "🧩 ";
  if (ext === ".tsx" || ext === ".jsx") return "⚛️ ";
  if (ext === ".ts") return "🔷 ";
  if (ext === ".js") return "🟨 ";

  // Styling
  if (ext === ".css" || ext === ".scss") return "🎨 ";
  if (name.includes("tailwind")) return "🌬️ ";
  if (name.includes("vite.config")) return "⚡ ";
  if (name.includes("webpack")) return "📦 ";

  // Config & Data
  if (ext === ".json") return "🔧 ";
  if (name.includes("config") || name.includes("setup")) return "⚙️ ";
  if (name.includes("env")) return "🔒 ";

  // Sanity Studio
  if (name.includes("sanity") || name.includes("schema")) return "🧠 ";
  if (name.includes("desk") || name.includes("structure")) return "📋 ";

  // Backend / PHP / Laravel
  if (ext === ".php" || ext === ".blade.php") return "🐘 ";
  if (name.includes("controller")) return "🎮 ";
  if (name.includes("model")) return "📊 ";
  if (name.includes("route")) return "🛤️ ";

  // Default files
  if (ext === ".md") return "📝 ";
  if (ext === ".sh") return "🐚 ";

  return "📄 ";
}

function getFolderEmoji(folderName) {
  const name = folderName.toLowerCase();

  if (name === "src" || name === "source") return "📂 ";
  if (name === "components" || name === "comp") return "🧩 ";
  if (name === "pages" || name === "routes") return "📑 ";
  if (name === "styles" || name === "css" || name === "scss") return "🎨 ";
  if (name === "public") return "🌐 ";
  if (name === "studio") return "🧠 ";
  if (name === "web") return "🌐 ";
  if (name.includes("sanity")) return "🧠 ";
  if (name.includes("api") || name === "server") return "🔌 ";
  if (name.includes("utils") || name === "lib") return "🛠️ ";
  if (name.includes("assets") || name.includes("images")) return "🖼️ ";
  if (name.includes("tests") || name.includes("__tests__")) return "🧪 ";

  return "📁 ";
}

// Walk function (collects files)
function walk(dir, fileList = []) {
  try {
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    for (const entry of entries) {
      if (IGNORED_DIRS.has(entry.name) || entry.name === OUTPUT_FILE) continue;

      const fullPath = path.join(dir, entry.name);
      if (entry.isDirectory()) {
        walk(fullPath, fileList);
      } else {
        fileList.push(fullPath);
      }
    }
  } catch (err) {
    console.warn(`Warning: Could not read ${dir}`);
  }
  return fileList;
}

function writeLine(stream, line = "") {
  stream.write(line + "\n");
}

// Stats
function computeStats(files) {
  const stats = { totalFiles: 0, languages: {} };
  files.forEach((f) => {
    const ext = path.extname(f).toLowerCase();
    if (!ALLOWED_EXT.has(ext)) return;
    stats.totalFiles++;
    const lang = EXT_TO_LANG[ext] || ext.slice(1).toUpperCase();
    stats.languages[lang] = (stats.languages[lang] || 0) + 1;
  });
  return stats;
}

function formatLanguageStats(stats) {
  const total = stats.totalFiles || 1;
  return Object.entries(stats.languages)
    .sort((a, b) => b[1] - a[1])
    .map(
      ([lang, count]) =>
        `- ${lang}: ${count} files (${((count / total) * 100).toFixed(1)}%)`,
    );
}

// Smart Tree with emojis
function printTree(dir, prefix = "", stream) {
  try {
    let entries = fs
      .readdirSync(dir, { withFileTypes: true })
      .filter(
        (entry) => !IGNORED_DIRS.has(entry.name) && entry.name !== OUTPUT_FILE,
      )
      .sort((a, b) => {
        if (a.isDirectory() && !b.isDirectory()) return -1;
        if (!a.isDirectory() && b.isDirectory()) return 1;
        return a.name.localeCompare(b.name);
      });

    entries.forEach((entry, index) => {
      const isLast = index === entries.length - 1;
      const connector = isLast ? "└── " : "├── ";
      const newPrefix = prefix + (isLast ? "    " : "│   ");

      if (entry.isDirectory()) {
        const emoji = getFolderEmoji(entry.name);
        writeLine(stream, prefix + connector + emoji + entry.name);
        printTree(path.join(dir, entry.name), newPrefix, stream);
      } else {
        const emoji = getFileEmoji(entry.name);
        writeLine(stream, prefix + connector + emoji + entry.name);
      }
    });
  } catch (err) {}
}

// ====================== MAIN ======================
(function main() {
  const args = process.argv.slice(2);
  const NO_CONTENT = args.includes("--no-content");
  const root = process.cwd();
  const output = fs.createWriteStream(OUTPUT_FILE, { encoding: "utf8" });

  console.log("🔍 Scanning project...");
  const files = walk(root);
  const stats = computeStats(files);

  writeLine(output, "# Project Overview\n");
  writeLine(output, "## Project Summary");
  writeLine(output, `- Total Files (tracked): ${stats.totalFiles}\n`);
  writeLine(output, "### Language Breakdown");
  formatLanguageStats(stats).forEach((line) => writeLine(output, line));
  writeLine(output, "");

  writeLine(output, "## Project Structure\n");
  writeLine(output, "```");
  writeLine(output, ".");
  printTree(root, "", output);
  writeLine(output, "```");

  if (!NO_CONTENT) {
    writeLine(output, "\n# File Contents (selected)\n");
    files.forEach((file) => {
      const ext = path.extname(file).toLowerCase();
      if (!ALLOWED_EXT.has(ext)) return;
      try {
        let content = fs.readFileSync(file, "utf8");
        if (content.length > 15000)
          content = content.slice(0, 15000) + "\n... (truncated)";
        writeLine(output, `## \`${path.relative(root, file)}\``);
        writeLine(output, "```");
        writeLine(output, content);
        writeLine(output, "```");
      } catch (e) {}
    });
  } else {
    writeLine(
      output,
      "\n# File Contents\n(Skipped — run with `--content` to include)",
    );
  }

  output.end();
  console.log(`✅ Done! Generated → ${OUTPUT_FILE}`);
})();
