<?php
namespace App\Service;

class Summary
{

    /*
        TODO

        Chargé le résumé à partir du choice
        - Constructeur : 
            - Via service prices charger les prix + 
            - via param le choice pour initialiser
        - Variable privé pour les infos du choice 
            - faire aussi les getter/setter
        - Tableau de ticket pour les ticket avec prix
            -Faire l'objet ticket avec infos de prix
            -constructure d'un ticket
                - Charger les prix depuis le service
                - Prendre en paramétre
                - calculer le prix en fin de chargement
            -faire les accesseurs (dont le prix)
            - Appliqué les prix des billets par tickets (et pouvoir les afficher via twig)
        - Fin du chargement des tickets
            - Stocker le total dans une variable pour affichage via le twig et stockage en bdd
        - Après validation
            - Avant envoie vers payement : faire une table de trace des paiment avec ID du choice, montant, statut paiment
        - Apres validation paiement
            - Mettre a jour le statut de paiement
            - Envoyer le mail
    */

    private $toto;

    public function __construct()
    {
        $this->toto = "toto";
    }

    public function loadChoice()
    {

    }

    public function gettoto()
    {
        return $this->toto;
    }
}