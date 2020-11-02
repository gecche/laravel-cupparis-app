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

//        echo "ok\n";
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

                if (!array_key_exists($foormEntity, $translations)) {
                    $translations[$foormEntity] = $foormTranslations[$foormEntity];
                } else {
                    $translations[$foormEntity] = array_replace_recursive($translations[$foormEntity], $foormTranslations[$foormEntity]);
//                    $keysToMerge = array_keys(Arr::get($foormTranslations, $foormEntity, []));
//                    foreach ($keysToMerge as $key) {
//                        if (!array_key_exists($key, $translations[$foormEntity])) {
//                            $translations[$foormEntity][$key] = $foormTranslations[$foormEntity][$key];
//                            continue;
//                        }
//                        if (is_array($foormTranslations[$foormEntity][$key])) {
//                            $translations[$foormEntity][$key] = array_merge($translations[$foormEntity][$key], $foormTranslations[$foormEntity][$key]);
//                        }
//                    }
                }
            }
        }


        $translations = Arr::dot($translations);

        $filename = public_path($this->dirjs . "/" . $lang . '-translations.js');

        $this->files->put($filename, "crud.lang = " . cupparis_json_encode($translations));

        $this->comment('Traduzioni completate');


    }


    protected function getSearchKeysGroup($field, $foormEntity, $formType = null)
    {
        if (is_null($formType)) {
            return [
                'foorms/' . $foormEntity . '.fields.' . $field,
                'fields.' . $field,
            ];
        }
        return [
            'foorms/' . $foormEntity . '.' . $formType . '.fields.' . $field,
        ];
    }

    protected function getSearchKeysRelationGroup($field, $foormEntity, $relationName, $formType = null)
    {
        if (is_null($formType)) {
            return [
                'foorms/' . $foormEntity . '.fields.' . $relationName . '.' . $field,
                'foorms/' . $foormEntity . '.fields.' . $field,
                'fields.' . $field,
            ];
        }
        return [
            'foorms/' . $foormEntity . '.' . $formType . '.fields.' . $relationName . '.' . $field,
        ];
    }

    protected function getSearchKeysGroupFoorname($foormEntity, $foormType = null)
    {
        if (is_null($foormType)) {
            return [
                'foorms/' . $foormEntity . '.name',
                'model.' . $foormEntity,
            ];
        }
        return [
            'foorms/' . $foormEntity . '.' . $foormType . '.name',
        ];
    }

    protected function getSearchKeysGroupRelationname($foormEntity, $relationName, $foormType = null)
    {
        if (is_null($foormType)) {
            return [
                'foorms/' . $foormEntity . '.relations.' . $relationName,
                'model.' . $relationName,
            ];
        }
        return [
            'foorms/' . $foormEntity . '.' . $foormType . '.relations.' . $relationName,
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
        $field, $foormEntity, $formType = null, $relationName = null, array $replace = [], $locale = null, $capitals = 'ucfirst'
    )
    {
        $params = [
            'capitals' => $capitals,
            'nullable' => true,
        ];
        $keys = $relationName
            ? $this->getSearchKeysRelationGroup($field, $foormEntity, $relationName, $formType)
            : $this->getSearchKeysGroup($field, $foormEntity, $formType);
        $result = $this->getTranslated($keys, $replace, $locale, $params);
        if ($result === null) {
            $fieldAdjusted = $this->adjustField($field);
            if ($field !== $fieldAdjusted) {
                $keys = $this->getSearchKeysGroup($fieldAdjusted, $foormEntity, $formType);
                $result = $this->getTranslated($keys, $replace, $locale, $params);
            }
        }
        if ($result === null && $formType === null) {
            $keys = [$field];
            $params['nullable'] = false;
            $result = $this->getTranslated($keys, $replace, $locale, $params);
        }

        return $result;
    }

    protected function getFoormFieldFurtherLabel(
        $furtherLabel, $field, $foormEntity, $formType = null, $relationName = null, array $replace = [], $locale = null, $capitals = 'ucfirst'
    )
    {
        $params = [
            'capitals' => $capitals,
            'nullable' => true,
        ];
        $keys = $relationName
            ? $this->getSearchKeysRelationGroup($field . $furtherLabel, $foormEntity, $relationName, $formType)
            : $this->getSearchKeysGroup($field . $furtherLabel, $foormEntity, $formType);
        $result = $this->getTranslated($keys, $replace, $locale, $params);
        return $result;
    }

    protected function getFoormMetadata($foormEntity, $formType = null, array $replace = [], $locale = null, $capitals = false)
    {
        $foormNameKeys = $this->getSearchKeysGroupFoorname($foormEntity, $formType);

        if (is_null($formType)) {
            $result = Lang::capitalizations($this->getTranslated($foormNameKeys, $replace, $locale, []), $capitals);
        } else {
            $result = $this->getTranslated($foormNameKeys, $replace, $locale, ['nullable' => true]);
            if (!is_null($result)) {
                $result = Lang::capitalizations($result, $capitals);

            }
        }
//        $result['singular'] = Lang::capitalizations($this->getTranslatedChoice($foormNameKeys, 1, $replace, $locale, []), $capitals);
//        $result['plural'] = Lang::capitalizations($this->getTranslatedChoice($foormNameKeys, 2, $replace, $locale, []), $capitals);

        return $result;

    }

    protected function getFoormRelationMetadata($foormEntity, $relationName, $formType = null, array $replace = [], $locale = null, $capitals = false)
    {
        $foormNameKeys = $this->getSearchKeysGroupRelationname($foormEntity, $relationName, $formType);

        if (is_null($formType)) {
            $result = Lang::capitalizations($this->getTranslated($foormNameKeys, $replace, $locale, []), $capitals);
        } else {
            $result = $this->getTranslated($foormNameKeys, $replace, $locale, ['nullable' => true]);
            if (!is_null($result)) {
                $result = Lang::capitalizations($result, $capitals);

            }
        }

//        $result['singular'] = Lang::capitalizations($this->getTranslatedChoice($foormNameKeys, 1, $replace, $locale, []), $capitals);
//        $result['plural'] = Lang::capitalizations($this->getTranslatedChoice($foormNameKeys, 2, $replace, $locale, []), $capitals);

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
        foreach ($foormFields as $field) {
            $translations[$foormEntity]['fields'][$field] = [
                'label' => $this->getFoormFieldLabel($field, $foormEntity, null),
            ];
            $line = $this->getFoormFieldLabel($field, $foormEntity, $formType);
            if (!is_null($line)) {
                $translations[$foormEntity][$formType]['fields'][$field]['label'] = $line;
            }
            foreach ($furtherLabels as $furtherLabel) {
                $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $field, $foormEntity, null);
                if (!is_null($line)) {
                    $translations[$foormEntity]['fields'][$field][$furtherLabel] = $line;
                }
                $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $field, $foormEntity, $formType);
                if (!is_null($line)) {
                    $translations[$foormEntity][$formType]['fields'][$field][$furtherLabel] = $line;
                }
            }
        }
        if (!array_key_exists('label', $translations[$foormEntity])) {
            $translations[$foormEntity]['label'] = $this->getFoormMetadata($foormEntity);
        }
        $line = $this->getFoormMetadata($foormEntity,$formType);
        if (!is_null($line)) {
            $translations[$foormEntity][$formType]['label'] = $line;
        }


        foreach ($relations as $relationName => $relation) {

            $translations[$foormEntity]['relations'][$relationName]['label'] = $this->getFoormRelationMetadata($foormEntity, $relationName);
            $line = $this->getFoormRelationMetadata($foormEntity,$relationName,$formType);
            if (!is_null($line)) {
                $translations[$foormEntity][$formType]['relations'][$relationName]['label'] = $line;
            }

            $relationFields = Arr::get($relation, 'fields', []);

            foreach (array_keys($relationFields) as $relationFieldName) {
//                if (!array_key_exists($relationFieldName, Arr::get($translations, $relationModelName, []))) {
//
//                    $translations[$relationModelName][$relationFieldName] = [
//                        'label' => $this->getFoormFieldLabel($relationFieldName, $foormEntity, null, $relationModelName),
//                    ];
//                    $line = $this->getFoormFieldLabel($relationFieldName, $foormEntity, $formType, $relationModelName);
//                    if (!is_null($line)) {
//                        $translations[$relationModelName][$formType][$relationFieldName]['label'] = $line;
//                    }
//                    foreach ($furtherLabels as $furtherLabel) {
//                        $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $relationFieldName, $relationModelName, null);
//                        if (!is_null($line)) {
//                            $translations[$relationModelName][$relationFieldName][$furtherLabel] = $line;
//                        }
//                        $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $relationFieldName, $relationModelName, $formType);
//                        if (!is_null($line)) {
//                            $translations[$relationModelName][$formType][$relationFieldName][$furtherLabel] = $line;
//                        }
//                    }
////                    $translations[$relationModelName]['modelMetadata']['singular'] = trans_choice('model.' . $relationModelName, 1);
////                    $translations[$relationModelName]['modelMetadata']['plural'] = trans_choice('model.' . $relationModelName, 2);
//                }
                $prefix = $relationName . '.' . $relationFieldName;
                $translations[$foormEntity]['fields'][$relationName][$relationFieldName] = [

                    'label' => $this->getFoormFieldLabel($relationFieldName, $foormEntity, null, $relationName),
                ];
                $line = $this->getFoormFieldLabel($relationFieldName, $foormEntity, $formType, $relationName);
                if (!is_null($line)) {
                    $translations[$foormEntity][$formType]['fields'][$relationName][$relationFieldName]['label'] = $line;
                }

                foreach ($furtherLabels as $furtherLabel) {
                    $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $relationFieldName, $prefix, null);
                    if (!is_null($line)) {
                        $translations[$foormEntity]['fields'][$relationName][$relationFieldName][$furtherLabel] = $line;
                    }
                    $line = $this->getFoormFieldFurtherLabel('_' . $furtherLabel, $relationFieldName, $prefix, $formType);
                    if (!is_null($line)) {
                        $translations[$foormEntity][$formType]['fields'][$relationName][$relationFieldName][$furtherLabel] = $line;
                    }
                }
//                $translations[$relationName]['modelMetadata']['singular'] = trans_choice('model.' . $relationName, 1);
//                $translations[$relationName]['modelMetadata']['plural'] = trans_choice('model.' . $relationName, 2);


            }


        }

//        print_r($translations);
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

        return $translations;
    }


}
