<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function authorize()
    {
        // Using policy for Authorization
        return true;
    }
}
