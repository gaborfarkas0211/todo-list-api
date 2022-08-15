<?php

namespace App\Http\Requests\Api;

use App\Traits\HasJsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;


class FormRequest extends BaseFormRequest
{
    use HasJsonResponse;

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
