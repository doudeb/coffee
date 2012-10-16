<?php
/**
 * Core English Language
 *
 * @package Elgg.Core
 * @subpackage Languages.English
 */

$translation = array(
       /*profile */
        'coffee:profile:add:hobbiesandinterest' => 'Ajouter un centre d\'intéret'
        , 'coffee:profile:hobbiesandinterest' => 'Centres d\'intérets'
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
        , 'coffee:profile:incomplete' => '<strong>Ceci est votre profil.</strong><br />Tous vos collègues y ont accès et peuvent le consulter. Faites donc bien attention à bien le personnaliser et à le mettre à jour!'
        /*feed*/
        , 'coffee:feed:share' => 'Partagez une information avec mess collègues'
        , 'coffee:feed:upload' => 'Ajouter une pièce jointe...'
        , 'coffee:feed:send' => 'Envoyer'
        , 'coffee:feed:cancel' => 'Annuler'
        , 'coffee:feeditem:showalltext' => 'Voir plus'
        , 'coffee:feeditem:hidetext' => 'Cacher'
        , 'coffee:feeditem:likesthis' => 'aiment ça'
        , 'coffee:feeditem:and' => 'et'
        , 'coffee:feeditem:others' => 'autres'
        , 'coffee:feeditem:showall' => 'Tout voir'
        , 'coffee:feeditem:comments' => 'commentaires'
        , 'coffee:feeditem:action:removecomment' => 'Supprimer le commentaire'
        , 'coffee:feeditem:action:addcomment' => 'Ajouter un commentaire...'
        , 'coffee:feeditem:action:like' => 'J\'aime'
        , 'coffee:feeditem:action:unlike' => 'Je n\'aime plus'
        , 'coffee:feeditem:action:removecomment' => 'Supprimer le commentaire'
        , 'coffee:feeditem:action:addcomment' => 'Ajouter un commentaire...'
        , 'coffee:feeditem:action:addcomment' => 'Ajouter un commentaire...'
        /*menu*/
        , 'coffee:menu:welcome' => 'Bienvenue!'
        , 'coffee:menu:feedlist' => 'Coffee Wall'
        , 'coffee:menu:profile' => 'Mon profil'
        , 'coffee:menu:tvapp' => 'Voir l\'application Coffee TV'
        , 'coffee:menu:logout' => 'Déconnexion'
        , 'coffee:menu:admin' => 'Administration'
        , 'coffee:menu:settings' => 'Préférences'
        /*welcome*/
        , 'coffee:welcome:headline' => '<h3>Bienvenue !</h3>
                        <p>Dans le monde professionnel, les bonnes idées arrivent souvent par accident, lors d\'une discussion informelle dans un couloir ou à la machine à café.</p>
                        <p>C\'est pour cette raison que nous avons développé Coffee Poke : un réseau social d\'entreprise innovant et convivial permettant d\'avoir échanges informels avec l\'ensemble de ses collaborateurs.</p>
                        <p>Où que vous soyez,</p>
                        <ul>
                            <li>Tenez vous informé(e) de l\'actualité de votre entreprise</li>
                            <li>Partagez et commentez infos, réflexions ou bonnes idées</li>
                            <li>Et n\'oubliez pas d\'inviter vos collaborateurs à partager un "vrai" café grâce à la fonctionnalité Coffee Poke</li>
                        </ul>
                        '
        , 'coffee:welcome:instructions' => '
                            <h3>Quelques étapes à suivre pour bien démarrer…</h3>
                            <div class="steps">
                                <!--<span class="stepN">1.</span>-->
                                <span class="instruction">
                                    <a href="#profile">Personnalisez votre profil</a>
                                    <br />
                                    Renseignez vos hobbies et centres d\'intérêt, ajoutez votre photo et choisissez un fond d\'écran qui vous ressemble.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">Publiez un message</a>
                                    <br />
                                    Dites sur quoi vous travaillez, posez une question, demandez un conseil, partagez articles, photos et vidéos.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">Soyez réactif !</a>
                                    <br />
                                    Réagissez au contenu publié et enrichissez le de vos expériences personnelles.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">Accessibilité</a>
                                    <br />
                                    Coffee Poke est accessible partout et tout le temps, sur votre ordinateur, votre smartphone et dans les lieux de vie de votre entreprise grâce à l\'application Coffee TV
                                    <br />
                                </span>
                            </div>

                            <p><h3><a href="/static/doc/CoffeePoke_fr.pdf">Plus d\'infos pour obtenir le meilleur de Coffe Poke.</h3></p>'

/**
 * User add
 */

	, 'useradd:subject' => 'User account created'
	, 'useradd:body' => '
%1$s,

A user account has been created for you at %2$s. To log in, visit:

http://%2$s.coffeepoke.com/

And log in with these user credentials:

Username: %4$s
Password: %5$s

Enjoy coffeePoke!!'
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
