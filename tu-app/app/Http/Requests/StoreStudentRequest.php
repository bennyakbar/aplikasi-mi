<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['System Admin', 'Admin Master Data']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nis' => [
                'required',
                'string',
                'min:5',
                'max:20',
                'regex:/^[A-Za-z0-9\-]+$/',  // Only alphanumeric and dash
                'unique:students,nis',
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[A-Za-z\s\.\'\-]+$/',  // Only letters, spaces, dots, apostrophes
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:student_categories,id',
            ],
            'grade' => [
                'required',
                'integer',
                'min:1',
                'max:6',
            ],
            'academic_year' => [
                'required',
                'string',
                'regex:/^\d{4}\/\d{4}$/',  // Format: 2024/2025
            ],
            'status' => [
                'required',
                'in:active,graduated,transferred',
            ],
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'nis' => 'NIS',
            'name' => 'Nama Siswa',
            'category_id' => 'Kategori',
            'grade' => 'Kelas',
            'academic_year' => 'Tahun Ajaran',
            'status' => 'Status',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'nis.regex' => 'NIS hanya boleh mengandung huruf, angka, dan tanda hubung.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'name.regex' => 'Nama siswa hanya boleh mengandung huruf, spasi, titik, dan apostrof.',
            'academic_year.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2024/2025).',
        ];
    }
}
