<?php namespace Gecche\Cupparis\App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Init extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init 
                            {--composer : runs shell commands labeled as "composer" (default: no)} 
                            {--nodb : does not runs shell commands labeled either as "mig" or "seed" (default: yes)} 
                            {--nomig : does not runs shell commands labeled as "mig" (default: yes)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurazione iniziale del progetto';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $cmdArray = [
            env('COMPOSER_PATH','composer').' update' => 'composer',
            env('COMPOSER_PATH','composer').' dump-autoload' => 'composer',
            'php artisan migrate:reset' => 'mig',
            'php artisan migrate' => 'mig',
            'php artisan db:seed' => 'seed',
        ];

        $phpPath = env('PHP_PATH', 'php');
        $path = base_path();

        $cmdArrayProcessed = [];
        foreach ($cmdArray as $cmd => $group) {
            if (!$this->option('composer') && in_array($group, ['composer'])) {
                continue;
            }

            if ($this->option('nodb') && in_array($group, ['mig', 'seed'])) {
                continue;
            }
            if ($this->option('nomig') && in_array($group, ['mig'])) {
                continue;
            }

            if (starts_with($cmd, 'php')) {
                $cmd = $phpPath . substr($cmd, 3);
            }

            $cmdArrayProcessed[] = $cmd;

            $process = new Process($cmd, $path);
            $process->setTimeout(null);
            $process->run();

// executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            echo $process->getOutput();
        }

        $finalCommand = implode(';', $cmdArrayProcessed);


        echo $finalCommand . "\n";
        $this->comment('Inizializzazione completata');


    }


}
