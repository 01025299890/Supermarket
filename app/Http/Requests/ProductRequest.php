<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'image' => 'nullable||image|max:2048',
            'type' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'unit_quantity' => 'required|numeric|min:1',
            'unit' => 'required|max:50|in:kg,g,l,ml,pcs',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'available_quantity' => 'required|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'user_id' => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'The image must be a valid image file.',
            'image.max' => 'The image size must not exceed 2MB.',
            'type.required' => 'The product type is required.',
            'type.string' => 'The product type must be a string.',
            'type.max' => 'The product type must not exceed 255 characters.',
            'brand.string' => 'The brand must be a string.',
            'brand.max' => 'The brand must not exceed 255 characters.',
            'name.required' => 'The product name is required.',
            'name.string' => 'The product name must be a string.',
            'name.max' => 'The product name must not exceed 255 characters.',
            'unit_quantity.required' => 'The unit quantity is required.',
            'unit_quantity.numeric' => 'The unit quantity must be a number.',
            'unit_quantity.min' => 'The unit quantity must be at least 1.',
            'unit.required' => 'The unit is required.',
            'unit.in' => 'The unit must be one of the following: kg, g, l, ml, pcs.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 2000 characters.',
            'available_quantity.required' => 'The available quantity is required.',
            'available_quantity.integer' => 'The available quantity must be an integer.',
            'available_quantity.min' => 'The available quantity must be at least 0.',
            'rating.numeric' => 'The rating must be a number.',
            'rating.min' => 'The rating must be at least 0.',
            'rating.max' => 'The rating must not exceed 5.',
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The specified user ID does not exist.',
        ];
    }
}
