<?php

namespace App\Http\Requests\Task;

use App\Dtos\ListTasksDto;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTasksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->has('status') ? explode(',', $this->input('status')) : null,
            'priority' => $this->has('priority') ? explode(',', $this->input('priority')) : null,
            'assigned_to' => $this->has('assigned_to') ? explode(',', $this->input('assigned_to')) : null,
            'tags' => $this->has('tags') ? explode(',', $this->input('tags')) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'array'],
            'status.*' => [Rule::in(array_column(TaskStatus::cases(), 'value'))],
            'priority' => ['nullable', 'array'],
            'priority.*' => [Rule::in(array_column(TaskPriority::cases(), 'value'))],
            'assigned_to' => ['nullable', 'array'],
            'assigned_to.*' => ['integer', 'exists:users,id'],
            'due_date_start' => ['nullable', 'date'],
            'due_date_end' => ['nullable', 'date', 'after_or_equal:due_date_start'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'keyword' => ['nullable', 'string', 'min:3'],
            'sort_by' => ['nullable', Rule::in(['created_at', 'due_date', 'priority', 'title'])],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'cursor' => ['nullable', 'string'],
        ];
    }
}
