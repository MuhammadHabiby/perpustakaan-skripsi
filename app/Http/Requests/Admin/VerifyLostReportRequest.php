<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\LostReport;
use App\Enum\LostReportStatus;

class VerifyLostReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!Auth::guard('admin')->check()) {
            return false;
        }
        $lostReport = $this->route('lost_report');
        return $lostReport instanceof LostReport && $lostReport->status === LostReportStatus::Reported;
    }

    public function rules(): array
    {
        return [];
    }
}
