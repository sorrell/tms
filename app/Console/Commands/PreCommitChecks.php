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
            'ide-helper' => true,  // Assume ide-helper succeeds if it runs
            'phpstan' => false,
            'npm-lint' => false
        ];

        $this->info("Running pre-commit checks and prep");

        $this->newLine(2);
        $this->info("========== Running ide-helper:actions ==========");
        $this->call('ide-helper:actions');

        $this->newLine(2);
        $this->info("========== Running phpstan ==========");
        // run php or sail php ./vendor/bin/phpstan analyse from command line
        try {
            $process = new \Symfony\Component\Process\Process(['./vendor/bin/sail', 'php', './vendor/bin/phpstan', 'analyse']);
            $output = '';
            $process->run(function ($type, $buffer) use (&$output) {
                $output .= $buffer;
                if (\Symfony\Component\Process\Process::ERR === $type) {
                    $this->output->write($buffer);
                    throw new \Exception("Failed to run phpstan via sail");
                } else {
                    $this->output->write($buffer);
                }
            });
            $checks['phpstan'] = str_contains($output, '[OK] No errors');
        } catch (\Exception $e) {
            $this->comment("Trying to run phpstan directly with php");

            try {
                $process = new \Symfony\Component\Process\Process(['php', './vendor/bin/phpstan', 'analyse']);
                $output = '';
                $process->run(function ($type, $buffer) use (&$output) {
                    $output .= $buffer;
                    if (\Symfony\Component\Process\Process::ERR === $type) {
                        $this->output->write($buffer);
                    } else {
                        $this->output->write($buffer);
                    }
                });
                $checks['phpstan'] = str_contains($output, '[OK] No errors');
            } catch (\Exception $e) {
                $this->error("Failed to run phpstan: " . $e->getMessage());
            }
        }

        $this->newLine(2);
        $this->info("========== Running npm lint ==========");
        try {
            $process = new \Symfony\Component\Process\Process(['npm', 'run', 'lint']);
            $process->run(function ($type, $buffer) {
                if (\Symfony\Component\Process\Process::ERR === $type) {
                    $this->output->write($buffer);
                } else {
                    $this->output->write($buffer);
                }
            });
            $checks['npm-lint'] = $process->isSuccessful();
        } catch (\Exception $e) {
            $this->error("Failed to run npm lint: " . $e->getMessage());
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
    }
}
