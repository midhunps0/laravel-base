<?php

namespace App\Http\Requests;

use App\Models\Script;
use Illuminate\Foundation\Http\FormRequest;

class ScriptUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth()->user();
        $client = Script::find($this->route('script'));
        return $user->hasPermissionTo('script.edit_any');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'isin_code' => 'sometimes|string',
            'symbol' => 'sometimes|string',
            'tracked' => 'sometimes|boolean',
            'company_name' => 'sometimes|string',
            'industry' => 'sometimes|string',
            'series' => 'sometimes|string',
            'fno' => 'sometimes|boolean',
            'nifty' => 'sometimes|boolean',
            'nse_code' => 'sometimes|integer',
            'bse_code' => 'sometimes|integer',
            'yahoo_code' => 'sometimes|string',
            'doc' => 'sometimes|string',
            'bbg_ticker' => 'nullable|string',
            'bse_security_id' => 'sometimes|string',
            'capitaline_code' => 'sometimes|integer',
            'mvg_sector' => 'nullable|string',
            'agio_indutry' => 'sometimes|string',
            'remarks' => 'nullable|string',
        ];
    }
}
