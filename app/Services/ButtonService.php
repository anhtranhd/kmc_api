<?php

namespace App\Services;

use App\Models\Button;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Validation\Validator as ReturnedValidator;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Throwable;
use URL;

/**
 * Class ButtonService
 *
 * @package App\Services
 */
class ButtonService extends BaseService
{
    /**
     * Get Store builder.
     *
     * @return Store|Builder
     */
    public function getButtonBuilder()
    {

        $buttons = Button::query();
        return $buttons;
    }

    /**
     * Get artifact builder.
     *
     * @return Store|Builder
     */
    public function checkButtonById($id)
    {

        $button = Button::query()->where('id', $id)->first();

        return $button;
    }

    public function getButtonByCode($code) {
        $buttons = Button::all();
        $buttonTrans = [];
        foreach($buttons as &$button) {
            $tran = $button->getTranslation($code);
            if ($tran) {
                $btn = [
                    "id" => $button->id,
                    "dom_id" => $button->dom_id,
                    "dom_html" => $tran->dom_html
                ];
                $buttonTrans[] = (object) $btn;
            }
        }
        return $buttonTrans;
    }
}
