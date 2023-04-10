<?php

namespace App\Http\Requests;

use App\Connectors\MailerLite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use App\Models\Setting;

class UpdateSubscriberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        $rules['name'] = 'required';

        // if post method, then we need an email.
        if ($this->method() === 'POST') {
            $rules['email'] = 'required|email';
        }

        $rules['fields.country'] = 'required';
        
        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'fields.country' => __('forms.country'),
            'email' => __('forms.email'),
            'name' => __('forms.name'),
        ];
    }
}
