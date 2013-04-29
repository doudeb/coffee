<?php
/**
 * Core English Language
 *
 * @package Elgg.Core
 * @subpackage Languages.French
 * @versiondate 171212
 */

$translation = array(
/*menu*/
         'coffee:menu:welcome' => 'Bienvenue!'
        , 'coffee:menu:feedlist' => 'CoffeeWall '
        , 'coffee:menu:profile' => 'Mon profil'
        , 'coffee:menu:tvapp' => 'CoffeeTV'
        , 'coffee:menu:admin' => 'Administration'
        , 'coffee:menu:settings' => 'Préférences'
        , 'coffee:menu:logout' => 'Déconnexion'
        , 'coffee:menu:people' => 'Trombinoscope'
        , 'coffee:menu:notifications' => 'Mes notifications'

/*welcome*/
        , 'coffee:welcome:headline' => '<h3>Bonjour,</h3>
                        <p>Bienvenue sur CoffeePoke.
                        Ce message d\'accueil s\'affichera seulement lors de vos trois premières connexions pour vous familiariser avec l\'application.</p>
                        <p><a href="/static/doc/CoffeePoke_fr.pdf" target="_blank">Cliquez ici</a> pour comprendre comment CoffeePoke vous permet d\'être encore plus efficace (vous allez télécharger un .pdf).</p>
                        <p>Bonne découverte, et très bonne journée avec CoffeePoke.</p>'
        , 'coffee:welcome:instructions' => '
                            <h3>Goûtez à la Pause-Café 2.0</h3>
                            <div class="steps">
                                <!--<span class="stepN">1.</span>-->
                                <span class="instruction">
                                    <a href="#feed">CoffeeWall, l\'espace Café de votre entreprise.</a>
                                    <br />
                                    Partagez et commentez messages publics et documents avec vos collègues. Accolez le signe # devant un mot-clé pour le rendre cliquable et rendre l\'information plus facilement accessible.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#profile">Profil</a>
                                    <br />
                                    (Re)découvrez vos collègues et présentez vous sous un autre jour. Partagez vos passions, choisissez un fond d\'écran qui vous ressemble, personnalisez votre message d\'accueil et bien sûr ajoutez votre photo !
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#tv">CoffeeTV</a>
                                    <br />
                                    Pas de temps pour consulter l\'application ? Profitez de votre pause-café pour rester informé ! Conviviale, colorée, l\'interface CoffeeTV facilite la diffusion des messages grâce aux écrans situés dans les lieux de vie de l\'entreprise.
                                    <br />
                                    Il n\'y a pas encore d\'écrans TV dans votre entreprise ? Parlez-en à votre Community Manager, CoffeePoke s\'occupe de tout !
                                    <br />
                                </span>
                            </div>'

/*feed*/
        , 'coffee:feed:share' => 'Partager une information avec mes collègues'
        , 'coffee:feed:upload' => 'Ajouter une pièce jointe...'
        , 'coffee:feed:send' => 'Envoyer'
        , 'coffee:feed:cancel' => 'Annuler'
        , 'coffee:feed:search' => 'Rechercher'
        , 'coffee:feed:corporatetags' => 'Mots-clés mis en avant'
        , 'coffee:feed:mostused' => 'Plus populaires'
        , 'coffee:feeditem:showalltext' => 'Voir plus'
        , 'coffee:feeditem:hidetext' => 'Cacher'
        , 'coffee:feeditem:likesthis' => 'aime ça'
        , 'coffee:feeditem:likethis' => 'aiment ça'
        , 'coffee:feeditem:and' => 'et'
        , 'coffee:feeditem:others' => 'autres'
        , 'coffee:feeditem:showall' => 'Voir les'
        , 'coffee:feeditem:comments' => 'commentaires'
        , 'coffee:feeditem:action:removecomment' => 'Supprimer le commentaire'
        , 'coffee:feeditem:action:addcomment' => 'Ajouter un commentaire...'
        , 'coffee:feeditem:action:like' => 'J\'aime'
        , 'coffee:feeditem:action:unlike' => 'Je n\'aime plus'
        , 'coffee:feeditem:action:removecomment' => 'Supprimer le commentaire'
        , 'coffee:feeditem:action:openlinkconfirm' => 'Cette action va ouvrir une nouvelle page.'
       	, 'coffee:feed:broadcastmessageunactive' => 'Activer la mise en avant'
		, 'coffee:feed:broadcastmessage' => 'Désactiver la mise en avant'
		, 'coffee:feed:search' => 'Recherche'
		, 'coffee:feed:corporatetags' => 'Suggestions'
		, 'coffee:feed:mostused' => 'Plus populaires'

/*profile */
        , 'coffee:profile:add:hobbiesandinterest' => 'Ajouter un centre d\'intérêt'
        , 'coffee:profile:hobbiesandinterest' => 'Centres d\'intérêt'
        , 'coffee:profile:information:mobilephone' => 'portable'
        , 'coffee:profile:information:workphone' => 'pro'
        , 'coffee:profile:information' => 'Coordonnées'
        , 'coffee:profile:presentation' => 'Présentation'
        , 'coffee:profile:button:background' => 'Fond d\'écran'
        , 'coffee:profile:title:changecoverpic' => 'Personnaliser le fond d\'écran de mon profil'
        , 'coffee:profile:button:changeavatar' => 'Changer ma photo'
        , 'coffee:profile:add:presentation' => 'Présentez-vous en quelques lignes'
        , 'coffee:profile:add:workphone' => 'Numéro de tél pro'
        , 'coffee:profile:add:mobilephone' => 'Numéro de tél portable'
        , 'coffee:profile:addheadline' => 'Ajouter votre fonction'
        , 'coffee:profile:addlocation' => 'Situation géographique'
        , 'coffee:poke:action' => 'CoffeePoke'
        , 'coffee:poke:body' => 'Veux-tu partager une petite pause-café avec moi?'
        , 'coffee:poke:subject' => 'Une pause café ?'
        , 'coffee:profile:incomplete' => '<strong>Ceci est votre profil.</strong><br />Tous vos collègues y ont accès et peuvent le consulter. Faites en sorte de bien le personnaliser et à le mettre à jour!'

/*TVapp*/
		, 'coffee:tvapp:title' => 'Bonjour,'
		, 'coffee:tvapp:message' => 'Que voulez-vous afficher sur l\'écran'
		, 'coffee:tvapp:button' => 'Répondre'
		, 'coffee:tvapp:answer1' => 'J\'aimerais voir les 10 derniers messages'
		, 'coffee:tvapp:fromusers' => 'de'
		, 'coffee:tvapp:fromusersall' => 'tous les utilisateurs'
		, 'coffee:tvapp:fromuserselect' => 'les utilisateurs suivants :'
		, 'coffee:tvapp:fromuserselectusername' => 'Nom'
		, 'coffee:tvapp:tagselect' => 'Uniquement ceux qui contiennent le(s) hashtag(s) suivant(s)'
		, 'coffee:tvapp:addhashtag' => 'hashtag'
		, 'coffee:tvapp:thanks' => 'Merci'
		, 'coffee:tvapp:cancel' => 'Annuler'

/*admin*/
		, 'coffee:admin:message' => 'Ceci est votre outil d\'administration. <br/ > <a href="#userSettings">Accédez à vos préférences.</a> '
		, 'coffee:admin:users' => 'Utilisateurs'
		, 'coffee:admin:search' => 'Recherche'
		, 'coffee:admin:addnewuser' => 'Ajouter un nouvel utilisateur'
		, 'coffee:admin:addnewusertitle' => 'Ajouter un nouvel utilisateur'
		, 'coffee:admin:displayname' => 'Prénom Nom'
		, 'coffee:admin:email' => 'Email'
		, 'coffee:admin:password' => 'Choisir un mot de passe'
		, 'coffee:admin:confirmpassword' => 'Confirmer le mot de passe'
		, 'coffee:admin:admin' => 'Cochez ci-dessous si vous souhaitez qu\'il ait les droits administrateur :'
		, 'coffee:admin:sendemail' => 'Cochez ci-dessous si vous souhaitez que CoffeePoke lui envoie un email de bienvenue avec ses identifiants de connexion :'
		, 'coffee:admin:language' => 'Langue de navigation'
		, 'coffee:admin:languageES' => 'Espagnol'
		, 'coffee:admin:languageFR' => 'Français'
		, 'coffee:admin:languageEN' => 'Anglais'
		, 'coffee:admin:addnewusersave' => 'Créer le compte'
		, 'coffee:admin:manageuser' => 'Gérer les utilisateurs'
		, 'coffee:admin:popupdelete' => 'Êtes-vous sûr de vouloir supprimer cet utilisateur? <br /> Attention, cette opération est irréversible. Son profil et tous ses messages seront supprimés.'
		, 'coffee:admin:site' => 'Site'
		, 'coffee:admin:sitesettings' => 'Personnalisation'
		, 'coffee:admin:sitesettingstitle' => 'Personnalisation de votre plateforme'
		, 'coffee:admin:logo' => '<strong>Logo</strong><br/>Taille max. recommandée: width: 300px - height: 100px.'
		, 'coffee:admin:background' => '<strong>Fond d\'écran</strong><br/>Taille min. recommandée: 1920*1080 min. Poids max. recommandé: 300mo.'
		, 'coffee:admin:defaultlanguage' => 'Langue par défaut'
		, 'coffee:admin:defaultlanguageEN' => 'Anglais'
		, 'coffee:admin:defaultlanguageES' => 'Espagnol'
		, 'coffee:admin:defaultlanguageFR' => 'Français'
		, 'coffee:admin:sitesettingssave' => 'Sauvegarder'
		, 'coffee:admin:corporatehashtags' => 'Suggestions de hashtags'
		, 'coffee:admin:corporatehashtagstitle' => 'Suggestions de hashtags'
		, 'coffee:admin:corporatehashtagshelp' => 'En tant qu\'administrateur, vous avez la possibilité de mettre en avant certains hashtags. Les #hashtags sont des mots-clés rendus cliquables par l\'accolade du signe # en début de mot. L\'utilisation de hashtags facilite l\'accessibilité de telle ou telle information. Vos utilisateurs retrouveront cette liste en cliquant sur la flèche située à droite du moteur de recherche, sur votre CoffeeWall.'
		, 'coffee:admin:addhashtag' => 'hashtag'
		, 'coffee:admin:corporatehashtagssave' => 'Sauvegarder'

/*usersettings*/
		, 'coffee:usersettings:message' => 'Bienvenue dans vos préférences.'
		, 'coffee:usersettings:usersettings' => 'Préférences'
		, 'coffee:usersettings:name' => 'Prénom Nom'
		, 'coffee:usersettings:currentpassword' => 'Mot de passe actuel'
		, 'coffee:usersettings:newpassword' => 'Choisissez un nouveau mot de passe'
		, 'coffee:usersettings:confirmnewpassword' => 'Confirmez votre nouveau mot de passe'
		, 'coffee:usersettings:language' => 'Langue de navigation'
		, 'coffee:usersettings:languageES' => 'Espagnol'
		, 'coffee:usersettings:languageFR' => 'Français'
		, 'coffee:usersettings:languageEN' => 'Anglais'
		, 'coffee:usersettings:save' => 'Sauvegarder'

/*User add*/

	, 'useradd:subject' => '%1$s, bienvenue sur CoffeePoke.'
	, 'useradd:body' => '

Bonjour %1$s,

Je vous invite à rejoindre notre communauté %2$s sur CoffeePoke.

CoffeePoke, notre réseau social privé, nous permet d\'échanger des messages publics sur notre "CoffeeWall" dans un esprit convivial.
Notre objectif : développer les échanges informels pour mieux nous connaitre et favoriser la cohésion d\'équipe.

Je viens de vous créer un compte. Pour y accéder, cliquez sur:
http://%2$s.coffeepoke.com/
et renseignez vos identifiants :
Utilisateur: %4$s
Mot de passe: %5$s

Connectez-vous sans plus attendre pour partager ce sur quoi vous travailler, vous tenir informé de l\'actualité de %2$s ou poser une question.

Toujours en déplacement? Aucun problème, connectez-vous depuis le navigateur de votre mobile.
A tout de suite sur CoffeePoke,

%6$s'
			//DATES
			,'friendlytime:justnow' => "à l'instant"
			,'friendlytime:minutes' => "il y a %s minutes"
			,'friendlytime:minutes:singular' => "il y a une minute"
			,'friendlytime:hours' => "il y a %s heures"
			,'friendlytime:hours:singular' => "il y a une heure"
			,'friendlytime:days' => "il y a %s jours"
			,'friendlytime:days:singular' => "hier"
			,'friendlytime:date_format' => 'j F Y à H:i'
//Lost Password
            , 'user:password:resetreq:success' => "Merci. Nous venons de vous envoyer un email vous expliquant comment réinitialiser votre mot de passe."
            , 'user:password:resetreq:fail' => "Désolé, mais cette adresse email n'existe pas."
            , 'admin:user:resetpassword:yes' => "Votre nouveau mot de passe vous a été envoyé par email."
            , 'admin:user:resetpassword:no' => "Oups une erreur est survenue. Merci de réessayer plus tard. Si le problème persiste, merci de contacter votre administrateur."
    #Notifications
    , 'notification::like' => 'a aimé votre publication'
    , 'notification::like::alsoliked' => 'a aussi aimé votre publication'
    , 'notification::mentioned::liked' => 'has liked this post you were quoted in'
    , 'hoho' => 'has liked the post you commented'
    , 'notification::comment' => 'a commenté votre publication'
    , 'notification::comment::alsocommented' => 'has also commented this post'
    , 'notification::comment::mentioned' => 'has commented this post you were quoted in'
    , 'notification::post::mentioned' => 'You have been quoted in a post from'
    , 'notification::comment::mentioned' => 'You have been quoted in a comment from'
    , 'notification::post::mention::comment' => 'has commented this post you were quoted in '
    #New Label
    , 'notification::like' => 'aime le post '
    , 'notification::comment' => 'a commenté le post '
    , 'notification::mention' => 'a mentionné votre nom dans le post '

);

add_translation('fr', $translation);