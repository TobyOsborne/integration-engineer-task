<?php

namespace App\Http\Requests;

use App\Connectors\MailerLite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use App\Models\Setting;

class UpdateSettingsRequest extends FormRequest
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
        return ['per_page'=>'sometimes|required|int|max:100|min:10'];
    }


    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // Maybe validate the api_key.
        // if we don't have a key yet then always validate if submitted. If we have a key only validate if empty value.
        $validator->sometimes('api_key', 'sometimes|required', function ($input, $item) {
            // if we dont have a saved key, or the values not empty we need to validate it.
            return in_array('api_key', array_keys($input->toArray())) && (!Setting::hasAPIKey() || !empty($item));
        });

        // maybe validate the key against mailerlite.
        $validator->after(function ($validator) {
            // if the api_key is required, and hasn't already failed, then validate via account lookup.
            if (in_array('api_key', array_keys($validator->getRules())) &&
                !in_array('api_key', array_keys($validator->failed()))) {
                try {
                    $mailerLite = new MailerLite(Arr::get($validator->getData(), 'api_key'));
                    $account = $mailerLite->getAccount();
                } catch (\Exception $e) {
                    $validator->errors()->add('api_key', $e->getMessage());
                }
            }
        });
    }

     /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'api_key' => __('forms.api_key'),
        ];
    }
}
