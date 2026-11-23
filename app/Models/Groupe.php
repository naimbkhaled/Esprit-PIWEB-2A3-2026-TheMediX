<?php
// MODÈLE Groupe : attributs, constructeur, getters et setters uniquement (aucune logique métier, aucun SQL)
class Groupe
{
    private $id;
    private $nom;
    private $description;
    private $regles;
    private $statut;
    private $motifRejet;
    private $dateCreation;
    private $idCreateur;
    private $nomCreateur;
    private $nbMembres;
    private $estMembre;

    public function __construct($id = null, $nom = null, $description = null, $regles = null, $statut = null, $motifRejet = null, $dateCreation = null, $idCreateur = null, $nomCreateur = null, $nbMembres = null, $estMembre = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->regles = $regles;
        $this->statut = $statut;
        $this->motifRejet = $motifRejet;
        $this->dateCreation = $dateCreation;
        $this->idCreateur = $idCreateur;
        $this->nomCreateur = $nomCreateur;
        $this->nbMembres = $nbMembres;
        $this->estMembre = $estMembre;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNom() { return $this->nom; }
    public function setNom($nom) { $this->nom = $nom; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getRegles() { return $this->regles; }
    public function setRegles($regles) { $this->regles = $regles; }

    public function getStatut() { return $this->statut; }
    public function setStatut($statut) { $this->statut = $statut; }

    public function getMotifRejet() { return $this->motifRejet; }
    public function setMotifRejet($motifRejet) { $this->motifRejet = $motifRejet; }

    public function getDateCreation() { return $this->dateCreation; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; }

    public function getIdCreateur() { return $this->idCreateur; }
    public function setIdCreateur($idCreateur) { $this->idCreateur = $idCreateur; }

    public function getNomCreateur() { return $this->nomCreateur; }
    public function setNomCreateur($nomCreateur) { $this->nomCreateur = $nomCreateur; }

    public function getNbMembres() { return $this->nbMembres; }
    public function setNbMembres($nbMembres) { $this->nbMembres = $nbMembres; }

    public function getEstMembre() { return $this->estMembre; }
    public function setEstMembre($estMembre) { $this->estMembre = $estMembre; }
}
