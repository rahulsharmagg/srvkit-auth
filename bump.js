const fs = require('fs');
const path = require('path');
const readline = require('readline');
const { execSync } = require('child_process');

// === CONFIG ===
const versionFile = path.join(__dirname, 'src', 'Config', 'Version.php');
const changelogFile = path.join(__dirname, 'CHANGELOG.md');

const versionPattern = /VERSION\s*=\s*['"](\d+)\.(\d+)\.(\d+)['"]/;

// === HELPERS ===
function prompt(question, defaultValue = '') {
  const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
  });
  return new Promise(resolve => {
    rl.question(`${question}${defaultValue ? ` [${defaultValue}]` : ''}: `, answer => {
      rl.close();
      resolve(answer.trim() || defaultValue);
    });
  });
}

function run(command) {
  return execSync(command, { encoding: 'utf-8' }).trim();
}

function formatChangelog(log) {
  const lines = log.split('\n');
  const sections = { Added: [], Changed: [], Removed: [], Fixed: [] };

  lines.forEach(line => {
    if (/feat|add|implement|new/i.test(line)) {
      sections.Added.push(line);
    } else if (/fix|bugfix|patch|resolve/i.test(line)) {
      sections.Fixed.push(line);
    } else if (/remove|delete|deprecate/i.test(line)) {
      sections.Removed.push(line);
    } else {
      sections.Changed.push(line);
    }
  });

  return Object.entries(sections)
    .filter(([, items]) => items.length)
    .map(([section, items]) => `### ${section}\n\n${items.join('\n')}\n`)
    .join('\n') || '### Changed\n\n- No notable changes recorded.\n';
}

// === MAIN ===
(async () => {
  console.log('📦 Starting version bump...\n');

  // 1. Ensure Version.php exists
  if (!fs.existsSync(versionFile)) {
    const create = (await prompt('Version.php not found. Create it now? (y/n)', 'y')).toLowerCase();
    if (create !== 'y') return console.log('❌ Aborted.');

    const versionDir = path.dirname(versionFile);
    fs.mkdirSync(versionDir, { recursive: true });

    const initial = await prompt('Enter initial version (e.g. 0.1.0)', '0.1.0');
    const versionStub = `<?php
namespace SrvKit\\Auth\\Config;

class Version
{
    public const VERSION = '${initial}';
}
`;
    fs.writeFileSync(versionFile, versionStub);
    console.log(`✅ Created Version.php with version ${initial}`);
  }

  // 2. Ensure CHANGELOG.md exists
  if (!fs.existsSync(changelogFile)) {
    const create = (await prompt('CHANGELOG.md not found. Create it now? (y/n)', 'y')).toLowerCase();
    if (create !== 'y') return console.log('❌ Aborted.');

    fs.writeFileSync(changelogFile, `# Changelog

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0/).

## [Unreleased]

`);
    console.log('✅ Created CHANGELOG.md');
  }

  // 3. Extract current version
  const content = fs.readFileSync(versionFile, 'utf-8');
  const match = content.match(versionPattern);
  if (!match) return console.error('❌ Could not find version constant.');

  let [major, minor, patch] = match.slice(1).map(Number);
  const currentVersion = `${major}.${minor}.${patch}`;
  console.log(`🔍 Current version: ${currentVersion}`);

  // 4. Ask for bump type
  const bumpType = await prompt('Select version bump (patch/minor/major)', 'patch');
  switch (bumpType) {
    case 'major': major++; minor = 0; patch = 0; break;
    case 'minor': minor++; patch = 0; break;
    default: patch++; break;
  }
  const suggestedVersion = `${major}.${minor}.${patch}`;
  const newVersion = await prompt('Enter new version', suggestedVersion);

  // 5. Update Version.php
  const updated = content.replace(versionPattern, `VERSION = '${newVersion}'`);
  fs.writeFileSync(versionFile, updated);
  console.log(`✅ Updated Version.php to ${newVersion}`);

  // 6. Git log since last tag
  let log = '';
  try {
    run(`git rev-parse v${currentVersion}`);
    log = run(`git log --pretty=format:"  - %s" v${currentVersion}...HEAD`);
  } catch {
    console.warn(`⚠️  Git tag 'v${currentVersion}' not found. Using full log.`);
    log = run(`git log --pretty=format:"  - %s"`);
  }

  const parsedLog = formatChangelog(log);
  const now = new Date().toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric'
  });

  // 7. Update CHANGELOG.md
  const changelogContent = fs.readFileSync(changelogFile, 'utf-8');
  const newEntry = `## [${newVersion}] - ${now}\n\n${parsedLog}\n`;
  const updatedChangelog = changelogContent.replace(/## \[Unreleased\]\n+/, `## [Unreleased]\n\n${newEntry}`);
  fs.writeFileSync(changelogFile, updatedChangelog);
  console.log('✅ CHANGELOG.md updated');

  // 8. Wait for manual edits
  await prompt('📝 Make any manual changes to CHANGELOG.md. Press Enter to continue...');

  // 9. Git commit
  run(`git add "${versionFile}" "${changelogFile}"`);
  run(`git commit -m "Bump version to ${newVersion}"`);
  console.log(`✅ Committed version bump to ${newVersion}`);

  console.log('\n🎉 Done. You can now tag the release with:\n');
  console.log(`   git tag v${newVersion}`);
  console.log(`   git push origin v${newVersion}`);
})();
