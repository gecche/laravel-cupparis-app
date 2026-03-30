<?php namespace Gecche\Cupparis\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;

use App\Http\Controllers\JsonControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class DownloadController extends BaseController
{
    use JsonControllerTrait;


    public function viewMediableFile($mediableModelName, $mediablePk, $template = null)
    {

        $this->redirectIfNotAuthorizedFile($mediableModelName, $mediablePk);

        $mediableModel = $this->getMediableModel($mediableModelName, $mediablePk);

        if (!$mediableModel->fileExists()) {
            return redirect('/')->withErrors('file_not_found');
        }

        $template = $template ?: Config::get('imagecache.default_template', 'small');

        $imagecacheRoute = 'imagecache/' . $template . '/' . $mediableModel->full_filename;

        $request = Request::create($imagecacheRoute, 'GET', array());

        return Route::dispatch($request);
    }

    public function viewUploadableFile($filename, $template = null)
    {

        $template = $template ?: Config::get('imagecache.default_template', 'small');

        $imagecacheRoute = 'imagecache/' . $template . '/uploads';

        $request = Request::create($imagecacheRoute, 'GET', array());

        return Route::dispatch($request);
    }

    public function openMediableFile(Request $request, $mediableModelName, $mediablePk)
    {

        $this->redirectIfNotAuthorizedFile($mediableModelName, $mediablePk);

        return $this->getResponseUploadableModelFile($request, $mediableModelName, $mediablePk, 'inline');
    }

    public function downloadMediableFile(Request $request, $mediableModelName, $mediablePk)
    {

        $this->redirectIfNotAuthorizedFile($mediableModelName, $mediablePk);

        return $this->getResponseUploadableModelFile($request, $mediableModelName, $mediablePk, 'attachment');
    }

    protected function redirectIfNotAuthorizedFile($mediableModelName, $mediablePk)
    {

        if (!is_string($mediableModelName) || !is_numeric($mediablePk)) {
            return redirect('/')->withErrors('file_not_found');
        }

        //TODO: CHECK IF THE USER CAN VIEW THAT RESOURCE (CHECK THE RESOURCE OR THE MODEL ATTACHED)
        //
    }

    protected function getResponseUploadableModelFile(Request $request, $mediableModelName, $mediablePk, $disposition)
    {
        $mediableModel = $this->getMediableModel($mediableModelName, $mediablePk);

        if (!$mediableModel->fileExists()) {
            return $this->_errorAndExit("File not found");
        }

        if ($this->checkIfIsApi($request)) {
            $data = $mediableModel->storageResponseApi(null, null);
            return $this->_result($data,null,true);
        }
        return $mediableModel->storageResponse(null, null, [], $disposition);
    }

    public function downloadtemp($nome)
    {

        $filename = storage_temp_path($nome);
        if (File::exists($filename)) {

            if (Str::endsWith($filename, 'pdf')) {
                return Response::make(file_get_contents($filename), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $nome . '"'
                ]);
            }
            return Response::download($filename, $nome);
        }
    }


    protected function getMediableModel($mediableModelName, $mediablePk, $errorMsg = 'file_not_found')
    {
        $modelsNamespace = rtrim(Config::get('breeze.namespace'), "\\");

        $fullUploadableModelName = $modelsNamespace . '\\' . Str::studly($mediableModelName);
        $mediableModel = $fullUploadableModelName::find($mediablePk);

        if (!($mediableModel->getKey() == $mediablePk)) {
            return redirect('/')->withErrors($errorMsg);
        }

        return $mediableModel;
    }

    //    public function downloadfileonce($filename) {
//        $path_key = Input::get('path_key',null);
//        $path_key = $path_key?$path_key:'default';
//        $path = Config::get('filesystems.upload_path_keys')[$path_key];
//        $ext = pathinfo($filename, PATHINFO_EXTENSION);
//        return Response::download(storage_path($path."/".$filename), basename($path).".".$ext);
//    }


    public function downloadUploadableFile($filename, $disposition = 'attachment')
    {
        $diskDriver = property_exists($this, 'disk_driver') ? $this->disk_driver : 'local';
        $filename = 'files/uploads/' . $filename;
        return Storage::disk($diskDriver)->response($filename, $filename, [], $disposition);
    }

    public function checkIfIsApi(Request $request)
    {

        if ($request->wantsJson()
            || $request->is('*api/*')
            || $request->routeIs('api.*')
        ) {
            return true;
        }

        return false;
    }

}
