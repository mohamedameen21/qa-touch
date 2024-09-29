<?php

namespace App\Http\Requests;

use App\Models\Module;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewModuleRequest extends FormRequest
{
    private $parent = null;

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
            'name' => ['required', 'string', 'max:255', Rule::unique('modules', 'name')->where(function ($query) {
                return $query->where('parent_id', $this->parent->id);
            })],
        ];
    }

    protected function prepareForValidation()
    {
        $parentId = $this->input('parent_id', null);

        $this->parent = $parentId ? Module::find($parentId) : Module::where('name', auth()->user()->email)->first();

        $this->merge([
            'parent' => $this->parent,
        ]);
    }

}
