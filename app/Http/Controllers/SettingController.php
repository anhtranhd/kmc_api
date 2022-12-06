<?php


namespace App\Http\Controllers;


use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *   schema="ContactSchema",
 *   title="Contact Model",
 *   description="Contact model",
 *   @OA\Property(
 *     property="id", description="ID of the Contact",
 *     @OA\Schema(type="number", example=1)
 *  ),
 *   @OA\Property(
 *     property="name", description="name of the Contact",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="description", description="description of the Contact",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="origin", description="origin of the Contact",
 *     @OA\Schema(type="string", example="")
 *  ),
 *  @OA\Property(
 *     property="status", description="status of the Contact",
 *     @OA\Schema(type="string", example="")
 *  )
 * )
 */
class SettingController extends Controller
{
    /**
     * Create a new SettingController instance.
     *
     * @return void
     */
    public function __construct() {
    
    }
    /**
     * @OA\Get(
     *     path="/api/system-settings",
     *     operationId="/api/system-settings",
     *     tags={"SystemSettings"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         description="The lang parameter in query, if not fill this param lang = vi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                      @OA\Property(
     *                         property="success",
     *                         type="boolean",
     *                         description="The response error status"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="The response message"
     *                     ),
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         description="The response result",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "success": true,
     *                         "message": "success",
     *                         "data": {
     *                              "id": 1,
     *                              "email": "baotang@yopmail.com",
     *                              "address": "Ha Noi, Viet Nam",
     *                              "website": "baotangvietnam.com",
     *                              "latitude": "1.222212",
     *                              "longitude": "10.21322"
     *                         }
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
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
