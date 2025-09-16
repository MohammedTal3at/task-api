<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:5', Rule::unique('tasks')->ignore($this->route('task'))],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(array_column(TaskStatus::cases(), 'value'))],
            'priority' => ['sometimes', Rule::in(array_column(TaskPriority::cases(), 'value'))],
            'due_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    $status = $this->input('status', TaskStatus::PENDING->value);
                    $dueDate = Carbon::parse($value);

                    if (in_array($status, [TaskStatus::PENDING->value, TaskStatus::IN_PROGRESS->value]) && $dueDate->isPast()) {
                        $fail('The due date cannot be in the past for tasks with pending or in-progress status.');
                    }
                },
            ], 'assigned_to' => ['nullable', 'exists:users,id'],
            'metadata' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }
}
