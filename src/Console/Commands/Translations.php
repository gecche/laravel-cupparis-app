<?php
namespace Gecche\Cupparis\App\Console\Commands;

use Gecche\Foorm\Facades\Foorm;
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

    protected function transUc($translations = [])
    {
        foreach ($translations as $translationKey => $translationValue) {
            if (is_array($translationValue)) {
                $translations[$translationKey] = $this->transUc($translationValue);
            } else {
                $translations[$translationKey] = ucfirst($translationValue);
            }
        }
        return $translations;
    }

    protected function createFile($lang)
    {

        echo "ok\n";
        Auth::loginUsingId(1);


        app()->setLocale($lang);


        $translations = $this->getAppTranslations();

        $translations = $this->transUc($translations);

        $foormsToTranslate = $this->getFoormsToTranslate();

        foreach ($foormsToTranslate as $foormEntity => $formTypes) {


            foreach ($formTypes as $formType) {

                $foormToTranslate = $foormEntity . '.' . $formType;
                $this->comment($foormToTranslate);

                $params = [];

                try {
                    $foorm = Foorm::getFoorm($foormToTranslate, request(), $params);
                } catch (\Exception $e) {
                    if (!Str::startsWith($e->getMessage(), 'Configuration of foorm')) {
                        throw $e;
                    }
                }

                $foormTranslations = $this->getFoormTranslations($foorm, $foormEntity, $formType);

                $keysToMerge = array_keys($foormTranslations);
                foreach ($keysToMerge as $key) {
                    if (!array_key_exists($key, $translations)) {
                        $translations[$key] = [];
                    }
                    $translations[$key] = array_merge($translations[$key], $foormTranslations[$key]);
                }
            }
        }


        $translations = Arr::dot($translations);

        $filename = public_path($this->dirjs . "/" . $lang . '-translations.js');

        echo $filename;

        $this->files->put($filename, "crud.lang = " . cupparis_json_encode($translations));

        $this->comment('Traduzioni completate');


    }


    protected function getSearchKeysGroup($field, $foormEntity, $formType)
    {
        return [
            'foorms.' . $foormEntity . '.' . $formType . '.' . $field,
            'foorms.' . $foormEntity . '.' . $field,
            'fields.' . $field,
        ];
    }

    protected function getSearchKeysRelationGroup($field, $foormEntity, $relationName, $formType)
    {
        return [
            'foorms.' . $foormEntity . '.' . $formType . '.' . $relationName . '.' . $field,
            'foorms.' . $foormEntity . '.' . $relationName . '.' . $field,
            'fields.' . $field,
        ];
    }

    protected function getSearchKeysGroupFoorname($foormEntity)
    {
        return [
            'foorms.' . $foormEntity . '.metadata.name',
            'model.' . $foormEntity,
        ];
    }

    protected function getSearchKeysGroupRelationname($foormEntity, $relationName)
    {
        return [
            'foorms.' . $foormEntity . '.metadata.' . $relationName . '.name',
            'model.' . $relationName,
        ];
    }

    protected function getTranslated($keys, $replace, $locale, $params)
    {

        foreach ($keys as $key) {

            $line = Lang::getRaw($key, $replace, $locale);
            if ($line !== null) {
                break;
            }
        }


        if (is_null($line) && Arr::get($params, 'nullable', false)) {
            return null;
        }

        if (is_null($line)) {
            $line = $key;
            if (Arr::get($params, 'humanize', true)) {
                $line = Lang::humanize($line);
            }
        }

        $capitals = Arr::get($params, 'capitals', false);
        return Lang::capitalizations($line, $capitals);
    }

    protected function adjustField($field, $separator = '_')
    {
        $suffixes = $this->getSuffixesToRemove();
        foreach ($suffixes as $suffix) {
            if (Str::endsWith($field, 'id')) {
                return substr($field, 0, -strlen($suffix));
            }
        }
        return $field;
    }

    protected function getSuffixesToRemove()
    {

        $suffixes = ['id'];

        foreach (config('app.langs', []) as $langSuffix) {
            $suffixes[] = $langSuffix;
        }

        return $suffixes;

    }


    protected function getFoormFieldLabel(
        $field, $foormEntity, $formType, $relationName = null, array $replace = [], $locale = null, $capitals = 'ucfirst'
    )
    {
        $params = [
            'capitals' => $capitals,
            'nullable' => true,
        ];
        $keys = $relationName
            ? $this->getSearchKeysGroup($field, $foormEntity, $formType)
            : $this->getSearchKeysRelationGroup($field, $foormEntity, $relationName, $formType);
        $result = $this->getTranslated($keys, $replace, $locale, $params);
        if ($result === null) {
            $fieldAdjusted = $this->adjustField($field);
            if ($field !== $fieldAdjusted) {
                $keys = $this->getSearchKeysGroup($fieldAdjusted, $foormEntity, $formType);
                $result = $this->getTranslated($keys, $replace, $locale, $params);
            }
        }
        if ($result === null) {
            $keys = [$field];
            $params['nullable'] = false;
            $result = $this->getTranslated($keys, $replace, $locale, $params);
        }

        return $result;
    }

    protected function getFoormFieldFurtherLabel(
        $furtherLabel, $field, $foormEntity, $formType, $relationName = null, array $replace = [], $locale = null, $capitals = 'ucfirst'
    )
    {
        $params = [
            'capitals' => $capitals,
            'nullable' => true,
        ];
        $keys = $relationName
            ? $this->getSearchKeysGroup($field . $furtherLabel, $foormEntity, $formType)
            : $this->getSearchKeysRelationGroup($field . $furtherLabel, $foormEntity, $relationName, $formType);
        $result = $this->getTranslated($keys, $replace, $locale, $params);
        return $result;
    }

    protected function getFoormMetadata($foormEntity, array $replace = [], $locale = null, $capitals = false)
    {
        $result = [];
        $foormNameKeys = $this->getSearchKeysGroupFoorname($foormEntity);
        $result['singular'] = Lang::capitalizations($this->getTranslatedChoice($foormNameKeys, 1, $replace, $locale, []), $capitals);
        $result['plural'] = Lang::capitalizations($this->getTranslatedChoice($foormNameKeys, 2, $replace, $locale, []), $capitals);

        return $result;

    }

    protected function getFoormRelationMetadata($foormEntity, $relationName, array $replace = [], $locale = null, $capitals = false)
    {
        $result = [];
        $foormNameKeys = $this->getSearchKeysGroupRelationname($foormEntity, $relationName);
        $result['singular'] = Lang::capitalizations($this->getTranslatedChoice($foormNameKeys, 1, $replace, $locale, []), $capitals);
        $result['plural'] = Lang::capitalizations($this->getTranslatedChoice($foormNameKeys, 2, $replace, $locale, []), $capitals);

        return $result;

    }


    protected function getTranslatedChoice($keys, $number, $replace, $locale, $params)
    {

        $line = null;
        foreach ($keys as $key) {
            $line = Lang::choiceRaw($key, $number, $replace, $locale);
            if ($line !== null) {
                break;
            }
        }

        $capitals = Arr::get($params, 'capitals', false);
        return Lang::capitalizations($line, $capitals);
    }

    protected function getFoormTranslations($foorm, $foormEntity, $formType)
    {

        $translations = [];

//        $mainModelName = Str::snake($foorm->getModelRelativeName());

        //echo $foorm->getModelName() . ' --- ' . $foorm->getModelRelativeName() . "\n";

        $formMetadata = $foorm->getFormMetadata();
        $foormFields = array_keys(Arr::get($formMetadata, 'fields', []));
        $relations = Arr::get($formMetadata, 'relations', []);

        $furtherLabels = ['msg', 'addedLabel'];
        foreach (array_merge($foormFields, array_keys($relations)) as $field) {
            $translations[$foormEntity][$field] = [
                'label' => $this->getFoormFieldLabel($field, $foormEntity, $formType),
            ];
            foreach ($furtherLabels as $furtherLabel) {
                $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $field, $foormEntity, $formType);
                if (!is_null($line)) {
                    $translations[$foormEntity][$field][$furtherLabel] = $line;
                }
            }
        }
        $translations[$foormEntity]['modelMetadata']['singular'] = trans_choice('model.' . $foormEntity, 1);
        $translations[$foormEntity]['modelMetadata']['plural'] = trans_choice('model.' . $foormEntity, 2);
        if (!array_key_exists('metadata', $translations[$foormEntity])) {
            $translations[$foormEntity]['metadata'] = $this->getFoormMetadata($foormEntity);
        }


        foreach ($relations as $relationName => $relation) {

            $relationModelName = Str::snake(Arr::get($relation, 'modelRelativeName', $relationName));

            $relationFields = Arr::get($relation, 'fields', []);

            foreach (array_keys($relationFields) as $relationFieldName) {
                if (!array_key_exists($relationFieldName, Arr::get($translations, $relationModelName, []))) {

                    $translations[$relationModelName][$relationFieldName] = [
                        'label' => $this->getFoormFieldLabel($relationFieldName, $foormEntity, $formType, $relationModelName),
                    ];
                    foreach ($furtherLabels as $furtherLabel) {
                        $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $relationFieldName, $relationModelName, $formType);
                        if (!is_null($line)) {
                            $translations[$relationModelName][$relationFieldName][$furtherLabel] = $line;
                        }
                    }
                    $translations[$relationModelName]['modelMetadata']['singular'] = trans_choice('model.' . $relationModelName, 1);
                    $translations[$relationModelName]['modelMetadata']['plural'] = trans_choice('model.' . $relationModelName, 2);
                }
                $prefix = $relationName . '.' . $relationFieldName;
                $translations[$foormEntity][$relationName][$relationFieldName] = [

                    'label' => $this->getFoormFieldLabel($relationFieldName, $foormEntity, $formType, $relationName),
                ];
                foreach ($furtherLabels as $furtherLabel) {
                    $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $relationFieldName, $prefix, $formType);
                    if (!is_null($line)) {
                        $translations[$foormEntity][$relationName][$relationFieldName][$furtherLabel] = $line;
                    }
                }
                $translations[$relationName]['modelMetadata']['singular'] = trans_choice('model.' . $relationName, 1);
                $translations[$relationName]['modelMetadata']['plural'] = trans_choice('model.' . $relationName, 2);
                if (!array_key_exists('metadata', $translations[$foormEntity][$relationName])) {
                    $translations[$foormEntity][$relationName]['metadata'] = $this->getFoormRelationMetadata($foormEntity, $relationName);
                }


            }


        }

        print_r($translations);
        return $translations;
    }

    protected function getFoorms()
    {
        return Config::get('foorm.foorms', []);
    }

    protected function getFormTypes()
    {
        return [
            'edit',
            'insert',
            'list',
            'search',
            'view',
        ];
    }

    protected function getFoormsToTranslate()
    {
        $foorms = $this->getFoorms();
        $formTypes = $this->getFormTypes();

        $foormsToTranslate = [];
        foreach ($foorms as $foormLabel) {
            $foormsToTranslate[$foormLabel] = $formTypes;
        }

        return $foormsToTranslate;
    }


    protected function getAppTranslations()
    {


        $appTranslations = [];
        $appTranslationsGroups = $this->getAppTranslationsGroups();
        foreach ($appTranslationsGroups as $targetGroup => $langGroup) {
            $appTranslations[$targetGroup] = trans($langGroup);
        }
        return $appTranslations;
    }

    protected function getAppTranslationsGroups()
    {
        return [
            'app' => 'app',
            'model' => 'model',
            'pagination' => 'pagination',
            'validation' => 'validation',
        ];
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

    protected function getFoormTranslationsOld($foorm, $foormEntyt, $formType)
    {

        $translations = [];


        echo $foorm->getModelName() . ' --- ' . $foorm->getModelRelativeName() . "\n";

        $formMetadata = $foorm->getFormMetadata();
        $mainModelName = Str::snake($foorm->getModelRelativeName());
        $mainModelFields = array_keys(Arr::get($formMetadata, 'fields', []));
        $relations = Arr::get($formMetadata, 'relations', []);

        foreach (array_merge($mainModelFields, array_keys($relations)) as $field) {
            $translations[$mainModelName][$field] = [
                'label' => Lang::getMFormLabel($field, $mainModelName),
                'msg' => Lang::getMFormMsg($field, $mainModelName),
                'addedLabel' => Lang::getMFormAddedLabel($field, $mainModelName)
            ];
        }
        $translations[$mainModelName]['modelMetadata']['singular'] = trans_choice('model.' . $mainModelName, 1);
        $translations[$mainModelName]['modelMetadata']['plural'] = trans_choice('model.' . $mainModelName, 2);

        foreach ($relations as $relationName => $relation) {

            $relationModelName = Str::snake(Arr::get($relation, 'modelRelativeName', $relationName));

            $relationFields = Arr::get($relation, 'fields', []);

            foreach (array_keys($relationFields) as $relationFieldName) {
                $translations[$relationModelName][$relationFieldName] = [
                    'label' => Lang::getMFormLabel($relationFieldName, $mainModelName),
                    'msg' => Lang::getMFormMsg($relationFieldName, $mainModelName),
                    'addedLabel' => Lang::getMFormAddedLabel($relationFieldName, $mainModelName)
                ];
            }

            $translations[$relationName]['modelMetadata']['singular'] = trans_choice('model.' . $relationModelName, 1);
            $translations[$relationName]['modelMetadata']['plural'] = trans_choice('model.' . $relationModelName, 2);

        }

        print_r($translations);
        return $translations;
    }


}
