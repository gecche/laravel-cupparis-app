<?php namespace Gecche\Cupparis\App\Http\Controllers;

use App\Http\Controllers\Controller as AppController;
use App\Models\NewsletterEmail;
use App\Models\User;
use Gecche\Foorm\Facades\Foorm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\ValidationException;

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
        } catch (ValidationException $e) {
            $errors = $e->errors();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($errors);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }

        return view("user.profile",['user' => $userFoorm->getModel()]);
    }

}
