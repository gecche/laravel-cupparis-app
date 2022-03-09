<?php namespace Gecche\Cupparis\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
//use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Request as RequestFacade;
#use igaster\laravelTheme\Facades\Theme;
use Igaster\LaravelTheme\Facades\Theme;
use Illuminate\Support\Facades\Route;


use App\Models\Foto;
use App\Models\Attachment;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Response as IlluminateResponse;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Mews\Captcha\Facades\Captcha;


/*
 * QUESTO CONTROLLER E' DA RIVEDERE TUTTO SE SERVE QUALCOSA O NO
 */
class MiscController extends BaseController {



    public function __construct(Request $request) {
        $this->result = array(
            "msg" 	=> "",
            "error"	=> 0,
            "result"	=> []
        );
    }


    public function crudPage($page) {
        try {
            $realPage = 'pages/' . str_replace('.','/',$page) . '.html';
            $file = Theme::url($realPage);
            $fileString = File::get(public_path($file));
            return response()->make($fileString);
//            return Response::file(public_path($file));
        } catch(\Exception $e) {
            abort(404);
        }

    }
    public function captchajs() {

        return response(captcha_img(),200);

    }

    public function captchajs6() {

        return response(Captcha::create('default',true),200);

    }

    public function faIcons() {
        /*
         * FONT-AWESOME PANEL
         */

        $faFileName = Theme::url('css/font-awesome/css/font-awesome.min.css');
        $files = new Filesystem();
        $faFile = $files->get(public_path() . $faFileName);

        /*
        $icons = [];
        $hits = preg_match_all("#(.+):before(.+)#",$faFile,$icons,PREG_SET_ORDER);
        */

        $icons = preg_split("#:before\{#", $faFile);
        $faIcons = array_map(function ($item) {
            $dotPos = strrpos($item, '.');
            if ($dotPos === false) {
                return false;
            }
            return substr($item, $dotPos + 4);
        }, $icons);

        $faIcons = array_filter($faIcons);

        return view('fa-icons',compact('faIcons'));
    }

    public function privacy() {
        return view("privacy",array());
    }

    public function setlocale($lang) {
        $cookie = null;
        if ( in_array( $lang , \Illuminate\Support\Facades\Config::get( 'app.langs' ) ) )
        {
            \Illuminate\Support\Facades\App::setLocale( $lang );
            \Illuminate\Support\Facades\Session::put( 'locale', $lang );
            //$_COOKIE['lang'] = $mLocale;
            $cookie = \Illuminate\Support\Facades\Cookie::forever( 'lang', $lang );
        }
        return redirect()->back()->withCookie($cookie);
    }

    public function captchajs_img() {

        return view('captcha_img');

    }

    public function viewimage($nome,$location,$template = null) {
        if (!is_string($nome)) {
            return redirect('/')->withErrors('image not found'); //->withErrors();

        }

        /*
         if ($location == 'imagecache' && !is_numeric($nome)) {
        Notify::error(Lang::get('image_not_found'));
        return redirect('/'); //->withErrors();
        }
        *
        */

        switch ($location) {

            case 'asset':
                return Response::file(asset('images/' . $nome));
            case 'imagecacheanteprima':
                if (is_numeric($nome)) {
                    $foto = \App\Models\Anteprima::find($nome);
                    if ($foto) {
                        $fotofile = $foto->getFullFilenameAttribute();
                    } else {
                        $fotofile = 'default-anteprima.png';
                    }
                } else {
                    $fotofile = $nome;
                }
                $request = Request::create('imagecache/' . $template . '/' . $fotofile, 'GET', array());
                return Route::dispatch($request);
            case 'imagecache':
                if (is_numeric($nome)) {
                    $foto = Foto::find($nome);
                    if ($foto) {
                        $fotofile = $foto->getFullFilenameAttribute();
                    } else {
                        if ($template == 'anteprima') {
                            $fotofile = 'default300.png';
                        } else {
                            $fotofile = 'default.png';
                        }
                    }
                } else {
                    $fotofile = $nome;
                }
                $request = Request::create('imagecache/' . $template . '/' . $fotofile, 'GET', array());

                return Route::dispatch($request);
            //return redirect(URL::route('imagecache', array($template, $foto)));
            case 'temp':
                return Response::file(storage_temp_path($nome));
            case 'foto':
                return Response::file(storage_path('foto/' . $nome));
                break;
            case 'icona':
                return Response::file(asset('images/icone/' . $nome));
            case 'icona-attachment':

                $mimes_to_icon = Config::get('filesystems.mimes_icone');
                /*
                if (is_array($mimetype))
                    $mimetype = current($mimetype);
                */
                $iconaName = Arr::get($mimes_to_icon,$nome,false);
                if (!$iconaName) {
                    $iconaName = File::get_icona_mime($nome,true);
                }
                return '/viewimage/'.$iconaName.'/imagecache/attachments-icon';
            case 'flag':
                return Response::file(asset('images/flags/' . $nome));
            case 'upload':

                $paths  =  Config::get('filesystems.upload_path_keys');
                $path_key = RequestFacade::get('path_key','default');
                $path_key = $path_key?$path_key:'default';
                $path =$paths[$path_key];

                if (File::exists(storage_path("$path/$nome"))) {
                    $content = File::get(storage_path("$path/$nome"));
                    $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content);
                } else {
                    $request = Request::create('imagecache/' . $template . '/default.png' , 'GET', array());

                    return Route::dispatch($request);
                }

                // return http response
                return new IlluminateResponse($content, 200, array(
                    'Content-Type' => $mime,
                    'Cache-Control' => 'max-age='.(config('imagecache.lifetime')*60).', public',
                    'Etag' => md5($content)
                ));




                response()->file(storage_path("$path/$nome"));
                //return Response::file(storage_path("$path/$nome"));
            default:
                break;
        }
    }


    public function downloadfileonce($filename) {
        $path_key = RequestFacade::get('path_key',null);
        $path_key = $path_key?$path_key:'default';
        $path = Config::get('filesystems.upload_path_keys')[$path_key];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return Response::download(storage_path($path."/".$filename), basename($path).".".$ext);
    }


    public function getDownloadDatafileTemplate($modelName) {

        $datafilemodels_namespace = Config::get('app.datafilemodels_namespace') . "\\";
        $datafileproviders_namespace = Config::get('app.datafileproviders_namespace') . "\\";
        $datafileProviderName = $datafileproviders_namespace . Str::studly($modelName);
        $datafileProvider = new $datafileProviderName;

        $path = storage_temp_path();
        $templateFile = $datafileProvider->getTemplateFile($path);

        return Response::download($templateFile);

    }
}
