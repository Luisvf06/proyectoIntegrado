<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AusenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fecha'=> 'required','date','after_or_equal:today',
            function($attribute, $value, $fail) {
                $year =Carbon::createFromFormat('Y-m-d', $value)->year;
                if ($year != now()->year) {
                    $fail('El año de la fecha debe ser el actual');
                }
            },
            'hora'=> 'required','date_format:H:i',
            function($attribute, $value, $fail) {
            $hour = Carbon::createFromFormat('H:i', $value)->hour;
            $init = Carbon::createFromTime(8,15);
            $end = Carbon::createFromTime(21,15);
            if (!$hour->between($init, $end,true)) {
                $fail('La hora debe estar entre las 8:15 y las 21:15');
                }
            },
        ];
    }
}
