<?php

namespace App\Http\Requests;

use App\Models\Module;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleCreateUpdateRequest extends FormRequest
{
    private $parent = null;
    private $moduleId = null;

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
            'parent_id' => ['sometimes', 'nullable', 'exists:modules,id'],
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $moduleExists = Module::where('parent_id', $this->parent->id)
                    ->where('name', $value)
                    ->when($this->moduleId, function ($query) {
                        return $query->where('id', '!=', $this->moduleId);
                    })->exists();

                if ($moduleExists) {
                    $fail('The ' . $attribute . ' has already been taken.');
                }
            }],
        ];
    }

    protected function prepareForValidation()
    {
        $parentId = $this->input('parent_id', null);
        $this->moduleId = $this->route('moduleId');

        $this->parent = $parentId ? Module::find($parentId) : Module::where('name', auth()->user()->email)->first();

        $this->merge([
            'parent' => $this->parent,
        ]);
    }

}
