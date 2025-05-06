<?php namespace Gecche\Cupparis\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class Init extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init
                            {--force : it forces initialization without prompting (default: no)}
                            {--stopOnException=0 : stop execution at the first exception (default: no)}
                            {--type=standard : the initialization type (default: standard)}
                            {--composer=0 : runs shell commands labeled as "composer" (default: no)}
                            {--mig=1 : it runs shell commands labeled either as "mig" or "seed" (default: yes)}
                            {--seed=1 : it runs shell commands labeled as "seed" (default: yes)}
                            {--storage=1 : it runs shell commands labeled as "storage" (default: yes)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurazione iniziale del progetto';


    protected $storageFoldersToInit = [
        'allegati',
        'foto',
        //'anteprime',
        'queues',
        'queues-failed',
        'temp',
        'datafile',
    ];

    protected $optionsToCheckForExecution = [
      'composer',
      'mig',
      'seed',
      'storage',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    protected function handleAfterConfirmation()
    {

        $cmdsArray = $this->prepareCommandsArray();

        $path = base_path();

        $cmdArrayProcessed = [];
        foreach ($cmdsArray as $cmdArray) {

            foreach ($cmdArray as $group => $cmd) {

                if (!$this->checkExecutionFromOptions($group, $cmd)) {
                    continue;
                };

                $cmdArrayProcessed[] = implode(' ', $cmd)."\n";

                $inputFile = null;
                $input = null;
                if (array_key_exists('input',$cmd)) {
                    $inputFile = $cmd['input'];
                    unset($cmd['input']);
                }

                //SE SERVONO FEATURES DI SHELL (NCHE SOLO *) BISOGNA PASSARE OLD SCHOOL LA STRINGA DEL COMANDO
                if (array_key_exists('shell',$cmd)) {
                    $process = Process::fromShellCommandline($cmd['shell'], $path);
                    unset($cmd['shell']);
                } else {

                    $process = new Process($cmd, $path);
                }

                $process->setTimeout(null);
                if (File::exists($inputFile)) {
                    $input = new InputStream();
                    $stream = File::get($inputFile);
//                    print_r($stream);
                    $input->write($stream);
                    $process->setInput($input);
                }
                $process->start();

                if (!is_null($input)) {
                    $input->close();
                }

                $process->wait(function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        echo 'ERR > '.$buffer;
                    }
//                    else {
//                        echo 'OUT > '.$buffer;
//                    }
                });


                // executes after the command finishes
                if (!$process->isSuccessful()) {

                    $e = new ProcessFailedException($process);
                    if ($this->option('stopOnException')) {
                        throw $e;
                    } else {
                        $this->comment($e->getMessage());
                    }

                }


                echo $process->getOutput();
            }
        }

        $finalCommand = implode(' ', $cmdArrayProcessed);


        $this->comment('Comandi eseguiti: ');

        $this->comment($finalCommand);

        $this->comment('Inizializzazione completata');


    }


    protected function checkExecutionFromOptions($group,$cmd) {

        foreach ($this->optionsToCheckForExecution as $optionToCheck) {
            if (!$this->option($optionToCheck) && in_array($group, [$optionToCheck])) {
                return false;
            }
        }
        return true;

    }

    protected function prepareCommandsArray() {

        switch ($this->option('type')) {
            case 'standard':

                $cmdArray = [
                    ['composer' => [env('COMPOSER_PATH','composer'),'update']],
                    ['composer' => [env('COMPOSER_PATH','composer'),'dump-autoload']],
                ];

                foreach ($this->storageFoldersToInit as $folder) {
                    $cmdArray[] = ['storage' => ['shell' => 'rm -rf '. storage_path() . '/files/'.$folder.'/*']];
                }

                $postStorageCmdArray = [
                    ['mig' => [env('PHP_PATH','php'),'artisan','migrate:reset']],
                    ['mig' => [env('PHP_PATH','php'),'artisan','migrate']],
                    ['seed' => [env('PHP_PATH','php'),'artisan','db:seed']],
                    ['seed' => [env('PHP_PATH','php'),'artisan','permissions']],
                    ['seed' => [env('PHP_PATH','php'),'artisan','cache:clear']],
                ];

                $cmdArray = array_merge($cmdArray,$postStorageCmdArray);

            break;
            default:
                $cmdArray = [];
            break;
        }
        return $cmdArray;

    }


    public function handle() {
        if ($this->option('force')) {
            return $this->handleAfterConfirmation();
        }

        $this->comment("\nDominio ". app()->domain());
        $confirmMessage1 = "Continuare inizializzazione ?";

        if ($this->confirm($confirmMessage1)) {
            $this->comment("DATABASE ". env('DB_DATABASE','NO DB'));
            $this->comment("\nCARTELLA STORAGE ". storage_path());
            $confirmMessage2 = "Il comando inizializzerà il database e la cartella di storage indicate, continuo?";
            if ($this->confirm($confirmMessage2)) {
                $this->handleAfterConfirmation();
            }
        }

    }
}
