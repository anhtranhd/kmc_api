<?php


namespace App\Http\Controllers;


use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Create a new SettingController instance.
     *
     * @return void
     */
    public function __construct() {

    }
    public function getSystemSetting(Request $request)
    {
        try {
            $lang = $request->get('lang') ? $request->get('lang') : 'vi' ;
            $contact = Contact::where('status', 1)->first();
            $contactArr = [];
            if ($contact) {
                $tran = $contact->getTranslation($lang);
                $contactArr = [
                    "id" => $contact->id,
                    "email" => $contact->email,
                    "phoneNumber" => $contact->phone_number,
                    "fax" => $contact->fax,
                    "address" => $tran->address,
                    "website" => $contact->website,
                    "latitude" => (float)$contact->latitude,
                    "longitude" => (float) $contact->longitude
                ];
            }
            $contactTrans = (object) $contactArr;
            return $this->_successResponse($contactTrans, Lang::get('messages.success'));
        } catch (Throwable $t) {
            Log::info($t->getMessage());
            return $this->_errorResponse($t->getMessage());
        }
    }

}
