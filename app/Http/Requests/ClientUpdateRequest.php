<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateRequest extends FormRequest
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
            'rm_id' => 'required|integer',
            'client_code' => 'required|string',
            'unique_code' => 'required|string',
            'name' => 'required|string',
            'fresh_fund' => 'sometimes|numeric',
            're_invest' => 'sometimes|numeric',
            'withdrawal' => 'sometimes|numeric',
            'payout' => 'sometimes|numeric',
            'total_aum' => 'required|numeric',
            'other_funds' => 'sometimes|numeric',
            'brokerage' => 'sometimes|numeric',
            'realised_pnl' => 'required|numeric',
            'ledger_balance' => 'sometimes|numeric',
            'pfo_type' =>'required|string',
            'category' =>'required|string',
            'type' =>'required|string',
            'fno' =>'required|integer',
            'pan_number' => 'sometimes|string',
            'email' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'whatsapp' => 'sometimes|string',
        ];
    }
}
