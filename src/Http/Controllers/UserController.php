<?php namespace Gecche\Cupparis\App\Http\Controllers;

use App\Http\Controllers\Controller as AppController;
use App\Models\NewsletterEmail;
use App\Models\User;
use Gecche\Foorm\Facades\Foorm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class UserController extends AppController {


    public function getProfile() {

        $userId = Auth::id();
        $user = User::find($userId);
        return view("user.profile", ['user' => $user]);
    }

    public function postProfile() {

        $userFoorm = Foorm::getFoorm('user.edit',request(),['id' => Auth::id()]);

        try {
            $userFoorm->save();
        } catch (\Exception $e) {
            $errors = json_decode($e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($errors);
        }

        return view("user.profile",['user' => $userFoorm->getModel()]);
    }

}
