<?php
// MODÈLE User : attributs, constructeur, getters et setters uniquement (aucune logique métier, aucun SQL)
class User
{
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $identifiant;
    private $motDePasse;
    private $role;

    public function __construct($id = null, $nom = null, $prenom = null, $email = null, $identifiant = null, $motDePasse = null, $role = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->identifiant = $identifiant;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }

    public function getPrenom() { return $this->prenom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getIdentifiant() { return $this->identifiant; }
    public function setIdentifiant($identifiant) { $this->identifiant = $identifiant; }

    public function getMotDePasse() { return $this->motDePasse; }
    public function setMotDePasse($motDePasse) { $this->motDePasse = $motDePasse; }

    public function getRole() { return $this->role; }
    public function setRole($role) { $this->role = $role; }
}
