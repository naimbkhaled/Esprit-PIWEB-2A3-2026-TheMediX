<?php
// MODEL : attributs + constructeur + getters/setters uniquement (client)
class User
{
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $dateNaissance;
    private string $genre;
    private string $email;
    private string $telephone;
    private ?string $hebergement;
    private string $role;

    public function __construct(
        ?int $id, string $nom, string $prenom, string $dateNaissance,
        string $genre, string $email, string $telephone,
        ?string $hebergement = null, string $role = 'client'
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->genre = $genre;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->hebergement = $hebergement;
        $this->role = $role;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): void { $this->nom = $nom; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }

    public function getDateNaissance(): string { return $this->dateNaissance; }
    public function setDateNaissance(string $d): void { $this->dateNaissance = $d; }

    public function getGenre(): string { return $this->genre; }
    public function setGenre(string $genre): void { $this->genre = $genre; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getTelephone(): string { return $this->telephone; }
    public function setTelephone(string $t): void { $this->telephone = $t; }

    public function getHebergement(): ?string { return $this->hebergement; }
    public function setHebergement(?string $h): void { $this->hebergement = $h; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }
}
