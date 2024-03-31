<?php

namespace App\Commands;

class CreateClientCommand
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private int $countryId,
        private string $email,
        private array $websites,
        private array $emails = []
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['country_id'],
            $data['email'],
            $data['websites'],
            $data['emails'] ?? []
        );
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getWebsites(): array
    {
        return $this->websites;
    }

    /**
     * @return array
     */
    public function getEmails(): array
    {
        return $this->emails;
    }

}
