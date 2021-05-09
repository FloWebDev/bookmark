<?php

namespace App\Constant;

class Constant
{
    // HTTP Code Messages
    const FORBIDDEN        = 'Accès refusé/interdit'; // 403
    const NOT_FOUND        = 'Ressource introuvable'; // 404
    const NO_RECORDS_FOUND = 'Aucun élément trouvé.';

    const PAGES_LIST_INDEX = 'Liste des pages';
    const BACK_TO_LIST     = 'Retour à la liste';

    const SIGN_IN = 'Connexion';
    const SIGN_OUT = 'Déconnexion';
    const DELETE_ACTION = 'Supprimer';
    const SUBMIT_ACTION = 'Valider';
    const CANCEL_ACTION = 'Fermer';
     
    const DASHBOARD_SENTENCE     = 'Tout le contenu de votre page !';
    const SUCCESS_ACTION         = 'Action réalisée avec succès';
    const LIST_CREATE_FORM_TITLE = 'Création d\'une liste';
    const LIST_UPDATE_FORM_TITLE = 'Modification de la liste : ';
    const ITEM_UPDATE_FORM_TITLE = 'Modification du favori : ';

    const ITEM_CREATE_FORM_TITLE = 'Ajout d\'un favori';

    const PAGE_DELETE_MODAL_TITLE = 'Suppression de la page : ';
    const PAGE_DELETE_MODAL_ALERT = '<b>Attention : </b><br>La suppression d\'une page entraîne la suppression de toutes les listes et de tous les items associés.';
    const LIST_DELETE_MODAL_TITLE = 'Suppression de la liste : ';
    const ITEM_DELETE_MODAL_TITLE = 'Suppression du favori : ';
    const LIST_DELETE_MODAL_ALERT = '<b>Attention : </b><br>La suppression d\'une liste entraîne la suppression de tous les items associés.';
    const ITEM_DELETE_MODAL_ALERT = 'Confirmez la suppression du favori.';

    const PAGE_CREATE_TITLE = 'Création Page';
    const PAGE_UPDATE_TITLE = 'Modification Page';


    const DROPDOWN_ITEM_CREATE_LABEL = 'Ajouter lien';
    const DROPDOWN_LIST_CREATE_LABEL = 'Créer liste';
    const DROPDOWN_LIST_UPDATE_LABEL = 'Éditer liste';
    const DROPDOWN_LIST_DELETE_LABEL = 'Supprimer liste';

    const LIST_CREATE_LABEL = 'Créer une liste';
    const ITEM_CREATE_LABEL = 'Créer un favori';
    
}
