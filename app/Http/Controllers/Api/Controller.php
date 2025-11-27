<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function dashboard() {
        return view('dashboard');
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return [
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
            'index' => 'listing',
        ];
    }



    function getAppMenu(Request $request) {
        $user = $request->user();
        $nome =  $user &&  $user->name?$user->name:'noname';
        $appMenu = config('cup-gui-vue.menu');
        $menu = [];
        $roleName = $user->getRolename() ;
        foreach ($appMenu as $item) {
            //print_r($item);
            if ($roleName != 'Superutente'  && Arr::exists($item,'visible') && ! in_array($roleName,$item['visible'])) {
                continue;
            }
            $voce = $item;
            if (Arr::exists($item,'items')) {  // sotto menu di secondo livello
                $voce['items'] = [];
                foreach ($item['items'] as $submenu) {
                    if ($roleName != 'Superutente' && Arr::exists($item,'visible') && ! in_array($roleName,$item['visible'])) {
                        continue;
                    }
                    $voce['items'][] = $submenu;
                }
            }
            $menu[] = $voce;
        }

//        print_r($menu);
//        die();


//        $menu = [
//            [
//                "label" =>'Dashboard', "icon"=>'fa fa-tachometer-alt', "to"=>'/'
//            ],
//            [
//                "label" =>'Admin',
//                'icon' => 'fa-solid fa-toolbox',
//                "items" =>[
////                    [
////                        "label" =>'Dashboard', "icon"=>'fa fa-tachometer-alt', "to"=>'/'
////                    ],
//                    [
//                        "label"  =>'User', "icon" =>'pi pi-fw pi-shopping-cart', "to"=>'/manage/ModelUser'
//                    ],
//                    [
//                        "label"  =>'Pagina Linkata', "icon" =>'pi pi-fw pi-shopping-cart', "to"=>'/pagina-linkata'
//                    ],
//                ]
//            ],
//        ];
        if (\config('cupparis-vue-client.profile_items_in_menu')) {
            $menu[] = [
                "label"  =>'Profilo',
                'icon' => 'fa fa-user',
                "items"  =>[
                    [
                        "label" => $nome,
                        "icon"=>'pi pi-fw pi-user',
                        "to"=>'/profilo'
                    ],
                    [
                        "label" =>'Logout',
                        "icon"=>'fa-solid fa-right-from-bracket',
                        "to"=> '/logout'
                    ],
                ]
            ];
        };
        // [
        //     "label"  =>'Gestione',
        //     "items"  =>[
        //         [
        //             "label" =>'Italia', "icon"=>'pi pi-fw pi-home', "to"=>'/geoitalia'
        //         ],
        //         [
        //             "label" =>'Mondo', "icon"=>'pi pi-fw pi-globe', "to"=>'/geomondo'
        //         ],
        //         [
        //             "label" =>'Anag. Tipologie', "icon"=>'pi pi-fw pi-home', "to"=>'/anagtipologie'
        //         ],
        //         [
        //             "label" =>'Anag. Miscellanous', "icon"=>'pi pi-fw pi-home', "to"=>'/anagmisc'
        //         ],
        //         [
        //             "label" =>'Cont Misc', "icon"=>'pi pi-fw pi-home', "to"=>'/contmisc'
        //         ],
        //     ]
        // ],

        return response()->json($menu);
    }


}
