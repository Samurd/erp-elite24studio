<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeetingRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'date' => ['sometimes', 'date'],
            'start_time' => ['sometimes', 'date_format:H:i:s'],
            'end_time' => ['sometimes', 'date_format:H:i:s', 'after:start_time'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'status_id' => ['nullable', 'exists:tags,id'],
            'notes' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'goal' => ['boolean'],
            'url' => ['nullable', 'url'],
            'bookingId' => ['nullable', 'string'],
            'responsibles' => ['nullable', 'array'],
            'responsibles.*' => ['exists:users,id'],
        ];
    }
}
