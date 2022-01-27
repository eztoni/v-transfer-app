<?php
/**
 * Created by PhpStorm
 * User: Tin Modrić
 * Date: 9/8/2021
 */

namespace App\Http\Controllers;

class AddNewServiceWizardController extends Controller
{
    public function show()
    {
        return view('add-new-service-wizard-page');
    }
}
