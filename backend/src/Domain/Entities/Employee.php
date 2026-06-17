<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Employee
{
    private ?int $id;
    private ?int $userId;
    private string $firstName;
    private string $lastName;
    private string $email;
    private ?string $phone;
    private ?string $document;
    private ?string $address;
    private ?string $city;
    private ?string $state;
    private ?string $zipcode;
    private ?string $birthDate;
    private ?string $department;
    private ?string $position;
    private float $salary;
    private string $hireDate;
    private ?string $terminationDate;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        ?int $userId = null,
        string $firstName = '',
        string $lastName = '',
        string $email = '',
        ?string $phone = null,
        ?string $document = null,
        ?string $address = null,
        ?string $city = null,
        ?string $state = null,
        ?string $zipcode = null,
        ?string $birthDate = null,
        ?string $department = null,
        ?string $position = null,
        float $salary = 0.0,
        string $hireDate = '',
        ?string $terminationDate = null,
        string $status = 'active',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->document = $document;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->zipcode = $zipcode;
        $this->birthDate = $birthDate;
        $this->department = $department;
        $this->position = $position;
        $this->salary = $salary;
        $this->hireDate = $hireDate;
        $this->terminationDate = $terminationDate;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getUserId(): ?int { return $this->userId; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getFullName(): string { return $this->firstName . ' ' . $this->lastName; }
    public function getEmail(): string { return $this->email; }
    public function getPhone(): ?string { return $this->phone; }
    public function getDocument(): ?string { return $this->document; }
    public function getAddress(): ?string { return $this->address; }
    public function getCity(): ?string { return $this->city; }
    public function getState(): ?string { return $this->state; }
    public function getZipcode(): ?string { return $this->zipcode; }
    public function getBirthDate(): ?string { return $this->birthDate; }
    public function getDepartment(): ?string { return $this->department; }
    public function getPosition(): ?string { return $this->position; }
    public function getSalary(): float { return $this->salary; }
    public function getHireDate(): string { return $this->hireDate; }
    public function getTerminationDate(): ?string { return $this->terminationDate; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setUserId(?int $userId): void { $this->userId = $userId; }
    public function setFirstName(string $firstName): void { $this->firstName = $firstName; }
    public function setLastName(string $lastName): void { $this->lastName = $lastName; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function setDocument(?string $document): void { $this->document = $document; }
    public function setAddress(?string $address): void { $this->address = $address; }
    public function setCity(?string $city): void { $this->city = $city; }
    public function setState(?string $state): void { $this->state = $state; }
    public function setZipcode(?string $zipcode): void { $this->zipcode = $zipcode; }
    public function setBirthDate(?string $birthDate): void { $this->birthDate = $birthDate; }
    public function setDepartment(?string $department): void { $this->department = $department; }
    public function setPosition(?string $position): void { $this->position = $position; }
    public function setSalary(float $salary): void { $this->salary = $salary; }
    public function setHireDate(string $hireDate): void { $this->hireDate = $hireDate; }
    public function setTerminationDate(?string $terminationDate): void { $this->terminationDate = $terminationDate; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'full_name' => $this->getFullName(),
            'email' => $this->email,
            'phone' => $this->phone,
            'document' => $this->document,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zipcode' => $this->zipcode,
            'birth_date' => $this->birthDate,
            'department' => $this->department,
            'position' => $this->position,
            'salary' => $this->salary,
            'hire_date' => $this->hireDate,
            'termination_date' => $this->terminationDate,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
