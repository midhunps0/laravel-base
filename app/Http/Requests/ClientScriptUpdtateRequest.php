<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class ClientScriptUpdtateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth()->user();
        $client = Client::find($this->route('client'));
        return $user->hasPermissionTo('client.edit_any') ||
            ($client->rm_id == $user->id && $user->hasPermissionTo('client.edit_own'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'script_id' => 'required|numeric',
            'qty' => 'required|integer',
            'buy_avg_price' => 'required|numeric'
        ];
    }
}
