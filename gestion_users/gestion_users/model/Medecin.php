<?php
require_once __DIR__ . '/User.php';

// MODEL : un médecin est un User + spécialité + preuve d'identité
class Medecin extends User
{
    private string $specialite;
    private string $preuveIdentite;

    public function __construct(
        ?int $id, string $nom, string $prenom, string $dateNaissance,
        string $genre, string $email, string $telephone,
        ?string $hebergement, string $specialite, string $preuveIdentite
    ) {
        parent::__construct($id, $nom, $prenom, $dateNaissance, $genre,
                            $email, $telephone, $hebergement, 'medecin');
        $this->specialite = $specialite;
        $this->preuveIdentite = $preuveIdentite;
    }

    public function getSpecialite(): string { return $this->specialite; }
    public function setSpecialite(string $s): void { $this->specialite = $s; }

    public function getPreuveIdentite(): string { return $this->preuveIdentite; }
    public function setPreuveIdentite(string $p): void { $this->preuveIdentite = $p; }
}
