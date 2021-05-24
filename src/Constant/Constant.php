<?php

namespace App\Constant;

class Constant
{
    // HTTP Code Messages
    const FORBIDDEN        = 'Accès refusé/interdit'; // 403
    const UNAUTHORIZED     = 'Action non autorisée'; // 401
    const NOT_FOUND        = 'La ressource demandée n\'existe pas ou a été déplacée'; // 404
    const NO_RECORDS_FOUND = 'Aucun élément trouvé.';

    const HELP_EMAIL_MESSAGE           = 'L\'adresse email ne sera ni revendue ni communiquée à un tiers.';
    const HELP_FORGOT_PASSWORD_MESSAGE = 'Un nouveau mot de passe vous sera envoyé à cette adresse.';

    const PAGES_LIST_INDEX = 'Liste des pages';
    const BACK_TO_LIST     = 'Retour à la liste';
    const BACK             = 'Retour';

    const SIGN_IN         = 'Connexion';
    const SIGN_OUT        = 'Déconnexion';
    const SIGN_UP         = 'Inscription';
    const FORGOT_PASSWORD = 'Mot de passe oublié';
    const DASHBOARD       = 'Dashboard';
    const DELETE_ACTION   = 'Supprimer';
    const SUBMIT_ACTION   = 'Valider';
    const CANCEL_ACTION   = 'Fermer';
     
    const DASHBOARD_SENTENCE     = 'Tout le contenu de votre page !';
    const SUCCESS_ACTION         = 'Action réalisée avec succès';
    const SUCCESS_SIGN_UP        = 'Inscription validée';
    const ACCOUNT_SETTINGS       = 'Paramètres du compte';
    const LIST_CREATE_FORM_TITLE = 'Création d\'une liste';
    const LIST_UPDATE_FORM_TITLE = 'Modification de la liste : ';
    const ITEM_UPDATE_FORM_TITLE = 'Modification item : ';

    const ITEM_CREATE_FORM_TITLE = 'Ajout d\'un favori';

    const PAGE_DELETE_MODAL_TITLE     = 'Suppression de la page : ';
    const PAGE_DELETE_MODAL_ALERT     = '<b>Attention : </b><br>La suppression d\'une page entraîne la suppression de toutes les listes et de tous les items associés.';
    const LIST_DELETE_MODAL_TITLE     = 'Suppression de la liste : ';
    const ITEM_DELETE_MODAL_TITLE     = 'Suppression item : ';
    const LIST_DELETE_MODAL_ALERT     = '<b>Attention : </b><br>La suppression d\'une liste entraîne la suppression de tous les items associés.';
    const ITEM_DELETE_MODAL_ALERT     = 'Confirmez la suppression de l\'item.';
    const USER_NEW_LABEL              = 'Inscription';
    const USER_UPDATE_LABEL           = 'Modification profil';
    const USER_UPDATE_LABEL_BIS       = 'Options';
    const USER_INDEX_LABEL            = 'Liste des utilisateurs';
    const USER_INDEX_LABEL_BIS        = 'Liste utilisateurs';
    const USER_DELETE_CONFIRMATION    = 'Confirmez la suppression de l\'utilisateur';

    const PAGE_CREATE_TITLE = 'Création Page';
    const PAGE_UPDATE_TITLE = 'Modification Page';


    const DROPDOWN_ITEM_CREATE_LABEL = 'Ajouter lien';
    const DROPDOWN_LIST_CREATE_LABEL = 'Créer liste';
    const DROPDOWN_LIST_UPDATE_LABEL = 'Éditer liste';
    const DROPDOWN_LIST_DELETE_LABEL = 'Supprimer liste';

    const PAGE_CREATE_LABEL = 'Créer une page';
    const LIST_CREATE_LABEL = 'Créer une liste';
    const ITEM_CREATE_LABEL = 'Créer un favori';
    
    const CONSTRAINT_MESSAGE_NOT_BLANK             = 'Ce champ ne doit pas être vide';
    const CONSTRAINT_MESSAGE_INVALID_EMAIL         = 'L\'adresse email saisie n\'est pas valide';
    const CONSTRAINT_MESSAGE_URL_FORMAT            = 'L\'URL renseignée n\'est pas valide';
    const CONSTRAINT_MESSAGE_MIN_LENGTH            = 'Nombre de caractères minimum attendu : ';
    const CONSTRAINT_MESSAGE_MAX_LENGTH            = 'Nombre de caractères maximum attendu : ';
    const CONSTRAINT_MESSAGE_CONFIRMATION_PASSWORD = 'Les champs du mot de passe doivent correspondre';
    const CONSTRAINT_REGEX_PASSWORD                = 'Le mot de passe ne doit pas contenir d\'espace';
    const CAPTCHA_LABEL                            = 'Renseignez les chiffres présents dans l\'image (*)';

    const ERROR_NO_MATCHING_USER = 'Aucun utilisateur correspondant.';

    const EMAIL_SEND_SUCCESS = 'Email envoyé avec succès';
    const EMAIL_SEND_ERROR   = 'Erreur dans l\'envoi de l\'email.';

    // Wallpapers
    const WALLPAPERS_TITLE = 'Fonds d\'écran';
    const WALLPAPERS       = [
        'Années 90'         => '90s.jpg',
        'Autre Monde'       => 'autre-monde.jpg',
        'Plage'             => 'beach.jpg',
        'Échecs 1'          => 'chess-garden.jpg',
        'Échecs 2'          => 'chess-landscape.jpg',
        'DC Comics'         => 'dc-comics.jpg',
        'Mario Geek'        => 'mario.jpg',
        'Marvel'            => 'marvel.jpg',
        'Lac et Montagne'   => 'mountain.jpg',
        'Pacman'            => 'pacman.jpg',
        'Poker'             => 'poker-dog.jpg',
        'Neige'             => 'snow.jpg',
        'Star Wars'         => 'star-wars.jpg',
        'Coucher de soleil' => 'sun.jpg',
        'Tyrannosaure'      => 't-rex.jpg',
        'Business'          => 'zzz.jpg'
    ];

    // FORGOT PASSWORD
    const EMAIL_FORGOT_PASSWORD_SUBJECT = 'Nouveau mot de passe (provisoire)';
    const EMAIL_FORGOT_PASSWORD_TEXT    = 'Bonjour, / Suite à votre demande, voici votre nouveau mot de passe : [NEW_PASSWORD] Important : modifiez le mot de passe dans les paramètres de votre compte dès votre prochaine connexion. / À bientôt.';
    const EMAIL_FORGOT_PASSWORD_HTML    = '<p>Bonjour,<br><br>Suite à votre demande, voici votre nouveau mot de passe : <b>[NEW_PASSWORD]</b><br><b>Important :</b> modifiez le mot de passe dans les paramètres de votre compte dès votre prochaine connexion.<br><br>À bientôt.</p>';
}
