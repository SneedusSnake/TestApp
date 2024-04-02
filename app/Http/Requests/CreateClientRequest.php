<?php

namespace App\Http\Requests;

use App\Rules\Domain;
use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
{
    public function __construct(private Domain $domainRule)
    {
        parent::__construct();
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|unique:client_emails|email',
            'emails'     => 'array',
            'emails.*'   => 'unique:client_emails,email|email',
            'websites'   => 'required|array',
            'websites.*' => ['unique:client_websites,website', 'url', $this->domainRule],
            'country_id' => 'required|exists:countries,id',
        ];
    }
}
