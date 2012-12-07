<?php
/**
 * Core English Language
 *
 * @package Elgg.Core
 * @subpackage Languages.French
 */

$translation = array(
       /*profile */
        'coffee:profile:add:hobbiesandinterest' => 'Ajouter un centre d\'intérêt'
        , 'coffee:profile:hobbiesandinterest' => 'Centres d\'intérêts'
        , 'coffee:profile:information:mobilephone' => 'portable'
        , 'coffee:profile:information:workphone' => 'professionnel'
        , 'coffee:profile:information' => 'Coordonnés'
        , 'coffee:profile:presentation' => 'Présentation'
        , 'coffee:profile:button:background' => 'Fond d\'écran'
        , 'coffee:profile:title:changecoverpic' => 'Personnaliser le fond d\'écran de mon profil'
        , 'coffee:profile:button:changeavatar' => 'Changer ma photo'
        , 'coffee:profile:add:presentation' => 'Présentez vous en quelques lignes'
        , 'coffee:profile:add:workphone' => 'Ajouter mon téléphone professionnel'
        , 'coffee:profile:add:mobilephone' => 'Ajouter mon numéro de portable'
        , 'coffee:profile:addheadline' => 'Ajouter votre fonction'
        , 'coffee:profile:addlocation' => 'Situation géographique'
        , 'coffee:poke:action' => 'Coffee Poke'
        , 'coffee:poke:body' => 'Je t\'invite à prendre un café'
        , 'coffee:poke:subject' => 'Une pause café ?'
        , 'coffee:profile:incomplete' => '<strong>Ceci est votre profil.</strong><br />Tous vos collègues y ont accès et peuvent le consulter. Faites en sorte de bien le personnaliser et à le mettre à jour!'
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
        , 'coffee:feeditem:likethis' => 'aiment ça'
        , 'coffee:feeditem:likesthis' => 'aime ça'
        , 'coffee:feeditem:and' => 'et'
        , 'coffee:feeditem:others' => 'autres'
        , 'coffee:feeditem:showall' => 'Voir les'
        , 'coffee:feeditem:comments' => 'commentaires'
        , 'coffee:feeditem:action:removecomment' => 'Supprimer le commentaire'
        , 'coffee:feeditem:action:addcomment' => 'Ajouter un commentaire...'
        , 'coffee:feeditem:action:like' => 'J\'aime'
        , 'coffee:feeditem:action:unlike' => 'Je n\'aime plus'
        , 'coffee:feeditem:action:removecomment' => 'Supprimer le commentaire'
        , 'coffee:feeditem:action:openlinkconfirm' => 'Cela va ouvrir une nouvelle page.'
        /*menu*/
        , 'coffee:menu:welcome' => 'Bienvenue!'
        , 'coffee:menu:feedlist' => 'L\'espace Café '
        , 'coffee:menu:profile' => 'Mon profil'
        , 'coffee:menu:tvapp' => 'CoffeeTV'
        , 'coffee:menu:logout' => 'Déconnexion'
        , 'coffee:menu:admin' => 'Administration'
        , 'coffee:menu:settings' => 'Préférences'
        /*admin*/
        , 'coffee:admin:corporatehashtagshelp' => 'En tant qu\'administrateur, vous avez la possibilité de mettre en avant certains hashtags (mot-clés commençant par # pour les rendre cliquables). Vos utilisateurs retrouveront cette liste en cliquant sur la flche ˆ droite du moteur de recherche, sur le mur.'
        /*welcome*/
        , 'coffee:welcome:headline' => '<h3>Bonjour,</h3>
                        <p>Bienvenue sur CoffeePoke. Ce message d\'accueil s\'affichera seulement lors de vos trois premières connexions pour vous familiariser avec l\'application.</p>
                        <p><a href="/static/doc/CoffeePoke_fr.pdf">Cliquez ici</a> pour comprendre comment CoffeePoke vous permet d\'être encore plus efficace. (vous allez télécharger un .pdf)</p>
                        <p>Bonne découverte, et très bonne journée avec CoffeePoke.</p>'
        , 'coffee:welcome:instructions' => '
                            <h3>Goûtez à la Pause-Café 2.0</h3>
                            <div class="steps">
                                <!--<span class="stepN">1.</span>-->
                                <span class="instruction">
                                    <a href="#feed">L\'espace Café de votre entreprise.</a>
                                    <br />
                                    Partagez et commentez avec vos collègues. Utilisez le sigle # devant un mot-clé pour le rendre cliquable et retrouver plus facilement l\'information.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#profile">Profil</a>
                                    <br />
                                    (re)découvrez vos collègues et présentez vous sous un autre jour. Partagez vos passions, choisissez un fond d\'écran qui vous ressemble, personnalisez votre message d\'accueil et bien sur ajoutez votre photo!
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#tv">CoffeeTV</a>
                                    <br />
                                    Pas de temps pour consulter l\'application? Profitez de votre pause-café pour rester informé! Conviviale, colorée, l\'interface CoffeeTV facilite la transmission des messages grÃ¢ce aux écrans situés dans les lieux de vie de l\'entreprise. Il n\'y a pas encore d\'écrans TV dans votre entreprise? Parlez-en à votre hiérarchie, CoffeePoke s\'occupe de tout!
                                    <br />
                                </span>
                            </div>'

/**
 * User add
 */

	, 'useradd:subject' => '%1$s, bienvenue sur CoffeePoke.'
	, 'useradd:body' => '

Bonjour %1$s,

CoffeePoke est le nouveau réseau social privé de %2$s.
CoffeePoke est le prolongement de la pause-café.
Il est là pour vous permettre d\'échanger librement, simplement et de manière totalement informelle entre vous.
Le tout dans un esprit convivial, sur une interface simple, colorée et très accessible.
Ce n\'est pas un outil de travail, c\'est simplement une nouvelle manière sympa de rendre l\'entreprise plus petite qu\'elle n\'y parait, de créer du lien social, une cohésion d\'équipe...

%2$s vient de créer un compte pour vous.

Pour vous connecter, rendez-vous sur:
http://%2$s.coffeepoke.com/
et connectez-vous avec vos identifiants personnels:

Utilisateur: %4$s
Mot de passe: %5$s

Vous pouvez aussi accéder à CoffeePoke sur votre mobile :-)
A très vite,

%2$s'
			//DATES
			,'friendlytime:justnow' => "à l'instant"
			,'friendlytime:minutes' => "il y a %s minutes"
			,'friendlytime:minutes:singular' => "il y a une minute"
			,'friendlytime:hours' => "il y a %s heures"
			,'friendlytime:hours:singular' => "il y a une heure"
			,'friendlytime:days' => "il y a %s jours"
			,'friendlytime:days:singular' => "hier"
			,'friendlytime:date_format' => 'j F Y à H:i'


);
add_translation("fr",$translation);