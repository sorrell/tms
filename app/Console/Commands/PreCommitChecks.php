<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PreCommitChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs prep and checks before a commit is made';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $checks = [
            'ide-helper' => false, 
            'phpstan' => false,
            'npm-lint' => false,
            'typescript' => false,
            'tests:php' => false,
        ];

        $this->info("Running pre-commit checks and prep");

        $this->newLine(2);
        $this->info("========== Running ide-helper:actions ==========");
        
        // Get hash of existing file
        $helperPath = base_path('_ide_helper_actions.php');
        $originalHash = file_exists($helperPath) ? md5_file($helperPath) : null;
        
        // Run the command
        $this->call('ide-helper:actions');
        
        // Compare hashes
        $newHash = file_exists($helperPath) ? md5_file($helperPath) : null;
        
        if ($originalHash !== $newHash) {
            $this->error('The ide-helper:actions file has changed. Please commit the updated _ide_helper_actions.php file.');
            $checks['ide-helper'] = false;
        } else {
            $checks['ide-helper'] = true;
        }

        $this->newLine(2);
        $this->info("========== Running phpstan ==========");
        try {
            $process = new \Symfony\Component\Process\Process(['php', './vendor/bin/phpstan', 'analyse']);
            $output = '';
            $process->run(function ($type, $buffer) use (&$output) {
                $output .= $buffer;
                $this->output->write($buffer);
            });
            $checks['phpstan'] = str_contains($output, '[OK] No errors');
        } catch (\Exception $e) {
            $this->error("Failed to run phpstan: " . $e->getMessage());
        }

        $this->newLine(2);
        $this->info("========== Running npm lint ==========");
        try {
            $process = new \Symfony\Component\Process\Process(['npm', 'run', 'lint']);
            $process->run(function ($type, $buffer) {
                $this->output->write($buffer);
            });
            $checks['npm-lint'] = $process->isSuccessful();
        } catch (\Exception $e) {
            $this->error("Failed to run npm lint: " . $e->getMessage());
        }

        $this->newLine(2);
        $this->info("========== Running TypeScript check ==========");
        try {
            $process = new \Symfony\Component\Process\Process(['npm', 'exec', 'tsc']);
            $process->run(function ($type, $buffer) {
                $this->output->write($buffer);
            });
            $checks['typescript'] = $process->isSuccessful();
        } catch (\Exception $e) {
            $this->error("Failed to run TypeScript check: " . $e->getMessage());
        }

        $this->newLine(2);
        $this->info("========== Running tests:php ==========");
        try {
            $process = new \Symfony\Component\Process\Process(['php', './vendor/bin/pest']);
            $process->setEnv([
                'APP_ENV' => 'testing',
            ]);
            $process->run(function ($type, $buffer) {
                $this->output->write($buffer);
            });
            $checks['tests:php'] = $process->isSuccessful();
        } catch (\Exception $e) {
            $this->error("Failed to run pest: " . $e->getMessage());
        }
        
        $this->newLine(2);
        $this->info("========== Completed pre-commit checks and prep ==========");
        
        // Display summary report
        $this->newLine();
        $this->info("Summary Report:");
        foreach ($checks as $check => $passed) {
            $icon = $passed ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $this->line("$icon $check");
        }
        
        $this->newLine();
        $this->comment("Please check messages above for any errors or warnings");

        // Return 1 (failure) if any check failed, 0 (success) if all passed
        return in_array(false, $checks) ? 1 : 0;
    }
}
