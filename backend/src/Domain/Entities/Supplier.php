<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Supplier
{
    private ?int $id;
    private string $companyName;
    private ?string $contactName;
    private ?string $email;
    private ?string $phone;
    private ?string $document;
    private ?string $address;
    private ?string $city;
    private ?string $state;
    private ?string $zipcode;
    private ?string $website;
    private ?string $notes;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        string $companyName = '',
        ?string $contactName = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $document = null,
        ?string $address = null,
        ?string $city = null,
        ?string $state = null,
        ?string $zipcode = null,
        ?string $website = null,
        ?string $notes = null,
        string $status = 'active',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->companyName = $companyName;
        $this->contactName = $contactName;
        $this->email = $email;
        $this->phone = $phone;
        $this->document = $document;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->zipcode = $zipcode;
        $this->website = $website;
        $this->notes = $notes;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getCompanyName(): string { return $this->companyName; }
    public function getContactName(): ?string { return $this->contactName; }
    public function getEmail(): ?string { return $this->email; }
    public function getPhone(): ?string { return $this->phone; }
    public function getDocument(): ?string { return $this->document; }
    public function getAddress(): ?string { return $this->address; }
    public function getCity(): ?string { return $this->city; }
    public function getState(): ?string { return $this->state; }
    public function getZipcode(): ?string { return $this->zipcode; }
    public function getWebsite(): ?string { return $this->website; }
    public function getNotes(): ?string { return $this->notes; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setCompanyName(string $companyName): void { $this->companyName = $companyName; }
    public function setContactName(?string $contactName): void { $this->contactName = $contactName; }
    public function setEmail(?string $email): void { $this->email = $email; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function setDocument(?string $document): void { $this->document = $document; }
    public function setAddress(?string $address): void { $this->address = $address; }
    public function setCity(?string $city): void { $this->city = $city; }
    public function setState(?string $state): void { $this->state = $state; }
    public function setZipcode(?string $zipcode): void { $this->zipcode = $zipcode; }
    public function setWebsite(?string $website): void { $this->website = $website; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function isActive(): bool { return $this->status === 'active'; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->companyName,
            'contact_name' => $this->contactName,
            'email' => $this->email,
            'phone' => $this->phone,
            'document' => $this->document,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zipcode' => $this->zipcode,
            'website' => $this->website,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
