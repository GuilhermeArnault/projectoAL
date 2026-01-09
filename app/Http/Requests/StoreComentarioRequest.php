<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComentarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'alojamento_id' => 'required|exists:alojamentos,id',
            'titulo' => 'required|string|max:255',
            'texto' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'aprovado' => 'sometimes|boolean',
            'resposta_admin' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'alojamento_id.required' => 'O alojamento é obrigatório.',
            'alojamento_id.exists' => 'O alojamento indicado não existe.',
            'titulo.required' => 'O título é obrigatório.',
            'texto.required' => 'O comentário não pode estar vazio.',
            'texto.max' => 'O comentário não pode ultrapassar 1000 caracteres.',
            'rating.required' => 'A avaliação é obrigatória.',
            'rating.min' => 'A avaliação mínima é 1.',
            'rating.max' => 'A avaliação máxima é 5.',
        ];
    }
}
