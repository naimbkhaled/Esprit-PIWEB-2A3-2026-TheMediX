<?php
// MODÈLE Invitation : attributs, constructeur, getters et setters uniquement (aucune logique métier, aucun SQL)
class Invitation
{
    private $id;
    private $idGroupe;
    private $idInvite;
    private $idInviteur;
    private $statut;
    private $dateInvitation;

    public function __construct($id = null, $idGroupe = null, $idInvite = null, $idInviteur = null, $statut = null, $dateInvitation = null)
    {
        $this->id = $id;
        $this->idGroupe = $idGroupe;
        $this->idInvite = $idInvite;
        $this->idInviteur = $idInviteur;
        $this->statut = $statut;
        $this->dateInvitation = $dateInvitation;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getIdGroupe() { return $this->idGroupe; }
    public function setIdGroupe($idGroupe) { $this->idGroupe = $idGroupe; }

    public function getIdInvite() { return $this->idInvite; }
    public function setIdInvite($idInvite) { $this->idInvite = $idInvite; }

    public function getIdInviteur() { return $this->idInviteur; }
    public function setIdInviteur($idInviteur) { $this->idInviteur = $idInviteur; }

    public function getStatut() { return $this->statut; }
    public function setStatut($statut) { $this->statut = $statut; }

    public function getDateInvitation() { return $this->dateInvitation; }
    public function setDateInvitation($dateInvitation) { $this->dateInvitation = $dateInvitation; }
}
