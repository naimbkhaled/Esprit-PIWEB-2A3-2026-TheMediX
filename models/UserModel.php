<?php
class UserModel
{
// ------------------------------------------------------------
// ATTRIBUTES
// ------------------------------------------------------------
private int $id = 0;
private string $username = '';
private string $email = '';
private string $password = '';
private string $role = 'user';
private string $createdAt = '';
private bool $isActive = true;
private string $twoFactorSecret = '';
private bool $twoFactorEnabled = false;
// ------------------------------------------------------------
// CONSTRUCTOR
// ------------------------------------------------------------
public function __construct()
{
}
// ------------------------------------------------------------
// GETTERS
// ------------------------------------------------------------
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
public function getCreatedAt(): string
{
return $this->createdAt;
}
public function isActive(): bool
{
    return $this->isActive;
}
// ------------------------------------------------------------
// SETTERS
// ------------------------------------------------------------
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
public function setCreatedAt(string $createdAt): void
{
$this->createdAt = $createdAt;
}
public function setIsActive(bool $isActive): void
{
    $this->isActive = $isActive;
}
public function getTwoFactorSecret(): string { return $this->twoFactorSecret; }
public function setTwoFactorSecret(string $s): void { $this->twoFactorSecret = $s; }
public function isTwoFactorEnabled(): bool { return $this->twoFactorEnabled; }
public function setTwoFactorEnabled(bool $e): void { $this->twoFactorEnabled = $e; }
// ------------------------------------------------------------
// CONVERT ARRAY TO OBJECT
// ------------------------------------------------------------
public static function fromArray(array $row): self
{
$user = new self();
$user->setId((int) ($row['id'] ?? 0));
$user->setUsername(
(string) ($row['username'] ?? '')
);
$user->setEmail(
(string) ($row['email'] ?? '')
);
$user->setPassword(
(string) ($row['password'] ?? '')
);
$user->setRole(
(string) ($row['role'] ?? 'user')
);
$user->setCreatedAt(
(string) ($row['created_at'] ?? '')
);
$user->setIsActive((bool) ($row['is_active'] ?? true));
$user->setTwoFactorSecret((string) ($row['two_factor_secret'] ?? ''));
$user->setTwoFactorEnabled((bool) ($row['two_factor_enabled'] ?? false));
return $user;
}
}
