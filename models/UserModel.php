<?php

class UserModel
{
    private int $id = 0;
    private string $username = '';
    private string $email = '';
    private string $password = '';
    private string $role = 'user';
    private string $provider = '';
    private string $providerId = '';
    private string $createdAt = '';
    private bool $isActive = true;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getProviderId(): string
    {
        return $this->providerId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function setProviderId(string $providerId): void
    {
        $this->providerId = $providerId;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public static function fromArray(array $row): self
    {
        $user = new self();
        $user->setId((int) ($row['id'] ?? 0));
        $user->setUsername((string) ($row['username'] ?? ''));
        $user->setEmail((string) ($row['email'] ?? ''));
        $user->setPassword((string) ($row['password'] ?? ''));
        $user->setRole((string) ($row['role'] ?? 'user'));
        $user->setProvider((string) ($row['provider'] ?? ''));
        $user->setProviderId((string) ($row['provider_id'] ?? ''));
        $user->setCreatedAt((string) ($row['created_at'] ?? ''));
        $user->setIsActive((bool) ($row['is_active'] ?? true));
        return $user;
    }
}
