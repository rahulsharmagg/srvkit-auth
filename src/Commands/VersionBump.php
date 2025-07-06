<?php

namespace SrvKit\Auth\Commands;

// define('ROOTPATH', __DIR__.'/../..');

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VersionBump extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'SrvKit Auth';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'version:bump';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Bumps the application version and updates CHANGELOG.md.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'version:bump [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Format the git log for CHANGELOG.md file
     * @param  string $gitLog [description]
     * @return string         [description]
     */
    protected function formatChangelog($gitLog): string
    {
        $entries = [
            'Added' => [],
            'Changed' => [],
            'Removed' => [],
            'Fixed' => []
        ];

        $lines = explode("\n", trim($gitLog));
        foreach($lines as $line){
            if (preg_match('/\b(feat|add|implement|new)\b/i', $line)) {
                $entries['Added'][] = "$line";
            } elseif (preg_match('/\b(fix|bugfix|patch|resolve)\b/i', $line)) {
                $entries['Fixed'][] = "$line";
            } elseif (preg_match('/\b(remove|delete|deprecate)\b/i', $line)) {
                $entries['Removed'][] = "$line";
            } else {
                $entries['Changed'][] = "$line";
            }
        }

        $formattedLog = "";
        foreach ($entries as $section => $items) {
            if (!empty($items)) {
                $_items = array_map('trim', $items);
                $formattedLog .= "### $section\n\n" . implode("\n", $_items) . "\n\n";
            }
        }

        return $formattedLog ?: "### Changed\n\n- No notable changes recorded.\n\n";
    }

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {

        chdir(ROOTPATH);

        $versionFile = ROOTPATH . 'src/Config/Version.php';
        $changelogFile = ROOTPATH . 'CHANGELOG.md';

        $trueValues = ['y', 'yes'];
        $falseValues = ['n', 'no'];

        $validation = [
            'required',
            static function ($value, $data, &$error, $field) use($trueValues, $falseValues) {
                $haystack = array_merge($trueValues, $falseValues);
                if(in_array(strtolower($value), $haystack)){
                    return true;
                }

                $error = 'The input is not valid.';
                return false;
            }
        ];

        // Ensure Version file exists
        if (!file_exists($versionFile)) {
            CLI::write('Version file not found.', 'yellow');
            $response = CLI::prompt('Would you like to create `src\Config\Version.php`', null, $validation);
            $vRule = [
                'required',
                static function ($value, $data, &$error){
                    if(preg_match("/\d+\.\d+\.\d+/", $value)){
                        return true;
                    }
                    $error = 'Invalid version number. Version number should be like 1.0.59';
                    return false;
                }
            ];

            if(in_array($response, $trueValues)){
                $response = CLI::prompt('Enter initial version [major.minor.patch]', '0.1.0', $vRule);
                shell_exec('php spark make:config Version');
                if(file_exists($versionFile)){
                    $fileContent = file_get_contents($versionFile);
                    $writeVersion = 
                    <<<EOD

                        public const VERSION = "$response";

                    EOD;
                    $vfPattern = '/(?<=\{)([\s\S]*?)(?=\})/';
                    $updatedContent = preg_replace($vfPattern, $writeVersion, $fileContent);
                    file_put_contents($versionFile, $updatedContent);
                    CLI::write('Initialize the version file with `'.$response.'` version.', 'green');
                }else{
                    CLI::error('Unable to create version file.');
                    return;
                }
            } elseif(in_array($response, $falseValues)){
                CLI::error('Stopping the version bump.');
                return;
            }
        }

        // Ensure changelog file exists
        if(!file_exists($changelogFile)){
            CLI::write('The `CHANGELOG.md` file does not exists.', 'cyan');
            $response = CLI::prompt('Would you like to create `CHANGELOG.md` file.', null, $validation);
            if(in_array($response, array_merge($trueValues))){
                file_put_contents($changelogFile, 
                    <<<EOD
                    # Changelog

                    All notable changes to this project will be documented in this file.
                    The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

                    ## [Unreleased]


                    EOD);
                CLI::write('Created `CHANGELOG.md`', 'green');
            }elseif (in_array($response, array_merge($falseValues))) {
                CLI::error('CHANGELOG.md file is required.');
                return;
            }
        }

        // Match version in Version.php
        $matchPattern = "/(?<=public const VERSION = [\"\'])[^\"\']*/";
        $versionFileContents = file_get_contents($versionFile);
        $matched = preg_match($matchPattern, $versionFileContents, $matches);

        if (!$matched) {
            CLI::error('Version not found.');
            return;
        }

        $currentVersion = $matches[0];
        [$major, $minor, $patch] = explode('.', $currentVersion);

        // Ask user for type of version bump
        CLI::write("Current version: " . CLI::color($currentVersion, 'yellow'));
        $choices = ['patch', 'minor', 'major'];
        $choice = CLI::prompt(CLI::color('Choose version type to bump', 'cyan'), $choices);

        if ($choice == 'major') {
            $major++;
            $minor = 0;
            $patch = 0;
        } elseif ($choice == 'minor') {
            $minor++;
            $patch = 0;
        } else {
            $patch++;
        }

        $suggestedVersion = "{$major}.{$minor}.{$patch}";
        $newVersion = CLI::prompt(CLI::color('Enter new version', 'cyan'), $suggestedVersion);

        // Update the version file        
        $updatedContent = preg_replace($matchPattern, $newVersion, $versionFileContents);
        file_put_contents($versionFile, $updatedContent);

        //Update CHANGELOG.md
        $now = date('F d, Y');
        $log = shell_exec("git log --pretty=format:\"  - %s\" v$currentVersion...HEAD");

        // Format the CHANGELOG.md
        $parsedLog = $this->formatChangelog($log);
        $existingContent = file_get_contents($changelogFile);
        $newEntry = "## [$newVersion] - $now\n\n" . $parsedLog . "\n\n";
        $updatedChangelog = preg_replace("/## \[Unreleased\]\n+/", "## [Unreleased]\n\n$newEntry", $existingContent, 1);
        file_put_contents($changelogFile, $updatedChangelog);

        // Wait for local changelog edit.
        $mEdit = CLI::prompt(CLI::color('Now you can make adjustments to CHANGELOG.md Then press enter to continue.', 'cyan'), null);

        // Convert absolute paths to relative for Git
        $gitVersionFile = str_replace(ROOTPATH, '', realpath($versionFile));
        $gitChangelogFile = str_replace(ROOTPATH, '', realpath($changelogFile));

        // Add files to Git and commit
        shell_exec("git add \"$gitVersionFile\"");
        shell_exec("git add \"$gitChangelogFile\"");
        shell_exec("git commit -m \"Bump version to $newVersion\"");

        CLI::write("Skipping CHANGELOG update, you can edit command at `app\Commands\VersionBump.php`", 'cyan');
        CLI::write("Version updated to " . CLI::color($newVersion, 'white'), 'cyan');
        CLI::write("You can finish the release.", 'cyan');
    }
}
