<?php
namespace Gecche\Cupparis\App\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Lang;
use Symfony\Component\Console\Input\InputOption;

use Illuminate\Console\Command;

use Illuminate\Filesystem\Filesystem;

class Translations extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creazione del file js di translations';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;


    protected $dirjs = 'js';

    protected $excludedModels = [
        'activityqueue',
    ];

    /**
     * Create a new reminder table command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $langs = Config::get('app.langs', []);

        $this->setDirJs($this->option('dirjs'));

        foreach ($langs as $lang) {
            $this->createFile($lang);
        }


    }

    public function setDirJs($dirjs = null)
    {

        if (!is_null($dirjs)) {
            $this->dirjs = $dirjs;
        }


        $this->dirJs = rtrim($this->dirjs, "/");


    }

    protected function createFile($lang)
    {

        echo "ok\n";
        Auth::loginUsingId(1);


        app()->setLocale($lang);


        $translations = [];
        $translations['app'] = trans('app');
        $translations['model'] = trans('model');
        $translations['pagination'] = trans('pagination');
        $translations['validation'] = trans('validation');


        $foorms = Config::get('foorm.foorms', []);

        $formTypes = [
            'edit',
            'insert',
            'list',
            'search',
        ];

        $formManagerClass = Config::get('foorm.form-manager');


        foreach ($foorms as $foormLabel) {

            foreach ($formTypes as $formType) {


                $this->comment("".$foormLabel);

                $foormManager = new $formManagerClass($foormLabel.".".$formType, request());

                $foorm = $foormManager->getForm();

                $foormTranslations = $this->getFoormTranslations($foorm);

                $keysToMerge = array_keys($foormTranslations);
                foreach ($keysToMerge as $key) {
                    if (!array_key_exists($key, $translations)) {
                        $translations[$key] = [];
                    }
                    $translations[$key] = array_merge($translations[$key], $foormTranslations[$key]);
                }
            }


        }


        $translations = array_dot($translations);

        $filename = public_path($this->dirjs . "/" . $lang . '-translations.js');

        echo $filename;

        $this->files->put($filename, "$.langDefs = " . cupparis_json_encode($translations));

        $this->comment('Traduzioni completate');


    }



    protected function getFoormTranslations($foorm)
    {

        $translations = [];


        echo $foorm->getModelName() . ' --- ' . $foorm->getModelRelativeName() . "\n";

        $formMetadata = $foorm->getFormMetadata();
        $mainModelName = Str::snake($foorm->getModelRelativeName());
        $mainModelFields = array_keys(Arr::get($formMetadata,'fields',[]));
        $relations =  Arr::get($formMetadata,'relations',[]);

        foreach (array_merge($mainModelFields,array_keys($relations)) as $field) {
            $translations[$mainModelName][$field] = [
                'label' =>   Lang::getMFormLabel($field, $mainModelName),
                'msg' => Lang::getMFormMsg($field, $mainModelName),
                'addedLabel' => Lang::getMFormAddedLabel($field, $mainModelName)
            ];
        }
        $translations[$mainModelName]['modelMetadata']['singular'] = trans_choice('model.'.$mainModelName,1);
        $translations[$mainModelName]['modelMetadata']['plural'] = trans_choice('model.'.$mainModelName,2);

        foreach ($relations as $relationName => $relation) {

            $relationModelName = Str::snake(Arr::get($relation,'modelRelativeName',$relationName));

            $relationFields = Arr::get($relation,'fields', []);

            foreach (array_keys($relationFields) as $relationFieldName) {
                $translations[$relationModelName][$relationFieldName] = [
                    'label' =>   Lang::getMFormLabel($relationFieldName, $mainModelName),
                    'msg' => Lang::getMFormMsg($relationFieldName, $mainModelName),
                    'addedLabel' => Lang::getMFormAddedLabel($relationFieldName, $mainModelName)
                ];
            }

            $translations[$relationName]['modelMetadata']['singular'] = trans_choice('model.'.$relationModelName,1);
            $translations[$relationName]['modelMetadata']['plural'] = trans_choice('model.'.$relationModelName,2);

        }

        print_r($translations);
        return $translations;
    }



    protected
    function getArguments()
    {
        return [
//            ["dest_lang", InputArgument::REQUIRED, "Array of dest suffixes lang, example: en,de "],
//            ["source_lang", InputArgument::OPTIONAL, "Source suffix lang to duplicate example default it ", 'it'],
        ];
    }

    protected
    function getOptions()
    {
        return [
            ["dirjs", null, InputOption::VALUE_OPTIONAL, "Dir to make the files into", null],
//            ["nodb", null, InputOption::VALUE_NONE, "No migrations neither seeds", null],
//            ["nomig", null, InputOption::VALUE_NONE, "No migrations", null],
        ];
    }


}
