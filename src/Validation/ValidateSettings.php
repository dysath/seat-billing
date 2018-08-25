<?php

namespace Denngarr\Seat\Billing\Validation;

use Illuminate\Foundation\Http\FormRequest;

class ValidateSettings extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'oremodifier'    => 'min:0|max:200',
            'oretaxrate'     => 'min:0|max:200',
            'bountytaxrate'  => 'min:0|max:200',
            'ioremodifier'   => 'min:0|max:200',
            'ioretaxrate'    => 'min:0|max:200',
            'ibountytaxrate' => 'min:0|max:200',
            'irate'          => 'min:0|max:200',
        ];
    }
}
