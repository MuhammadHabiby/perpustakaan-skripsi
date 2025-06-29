<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\LostReport;
use App\Enum\LostReportStatus;

class ResolveLostReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!Auth::guard('admin')->check()) {
            return false;
        }
        $lostReport = $this->route('lost_report');
        return $lostReport instanceof LostReport &&
            in_array($lostReport->status, [LostReportStatus::Reported, LostReportStatus::Verified]);
    }

    public function rules(): array
    {
        return [
            'resolution_notes' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'resolution_notes.required' => 'Catatan penyelesaian wajib diisi.',
            'resolution_notes.string' => 'Catatan penyelesaian harus berupa teks.',
            'resolution_notes.max' => 'Catatan penyelesaian terlalu panjang (maksimal 1000 karakter).',
        ];
    }
}
