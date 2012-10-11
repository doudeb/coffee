<?php
/**
 * Core English Language
 *
 * @package Elgg.Core
 * @subpackage Languages.English
 */

$english = array(
       /*profile */
        'coffee:profile:add:hobbiesandinterest' => 'Add a hobby or interest'
        , 'coffee:profile:hobbiesandinterest' => 'Hobbies & Interests'
        , 'coffee:profile:information:mobilephone' => 'mobile'
        , 'coffee:profile:information:workphone' => 'work'
        , 'coffee:profile:information' => 'Contact Information'
        , 'coffee:profile:presentation' => 'Presentation'
        , 'coffee:profile:button:background' => 'Background'
        , 'coffee:profile:title:changecoverpic' => 'Change your background picture'
        , 'coffee:profile:button:changeavatar' => 'Change your avatar'
        , 'coffee:profile:add:presentation' => 'Add your presentation'
        , 'coffee:profile:add:workphone' => 'Add your phone number'
        , 'coffee:profile:add:mobilephone' => 'Add your mobile number'
        , 'coffee:profile:addheadline' => 'Add your headline'
        , 'coffee:profile:addlocation' => 'Add your location'
        , 'coffee:profile:button:changeavatar' => 'Change your avatar'
        , 'coffee:poke:action' => 'Coffee Poke !!'
        , 'coffee:poke:body' => 'Let\s have a coffee'
        , 'coffee:poke:subject' => 'Coffee ??'
        , 'coffee:profile:incomplete' => '<strong>This is your profile.</strong><br />It is visible to your coworkers so be sure to complete it and keep it up to date!'
        /*feed*/
        , 'coffee:feed:share' => 'Share something with your colleagues…'
        , 'coffee:feed:upload' => 'Upload something...'
        , 'coffee:feed:send' => 'Send'
        , 'coffee:feed:cancel' => 'Cancel'
        , 'coffee:feeditem:showalltext' => 'Show all text'
        , 'coffee:feeditem:hidetext' => 'Hide text'
        , 'coffee:feeditem:likesthis' => 'likes this'
        , 'coffee:feeditem:and' => 'and'
        , 'coffee:feeditem:others' => 'others'
        , 'coffee:feeditem:showall' => 'Show all'
        , 'coffee:feeditem:comments' => 'comments'
        , 'coffee:feeditem:action:removecomment' => 'Remove comment'
        , 'coffee:feeditem:action:addcomment' => 'Write a comment...'
        , 'coffee:feeditem:action:like' => 'Like'
        , 'coffee:feeditem:action:unlike' => 'Unlike'
        , 'coffee:feeditem:action:removecomment' => 'Remove comment'
        /*menu*/
        , 'coffee:menu:welcome' => 'Welcome !!'
        , 'coffee:menu:feedlist' => 'News Feed'
        , 'coffee:menu:profile' => 'Profile'
        , 'coffee:menu:tvapp' => 'Launch TV App'
        , 'coffee:menu:logout' => 'Log out'
        , 'coffee:menu:admin' => 'Administration'
        , 'coffee:menu:settings' => 'Settings'
        /*welcome*/
        , 'coffee:welcome:headline' => '<h3>Welcome on CoffeePoke!</h3>
                        <p>In a professional environment, good ideas often rise up during informal discussions in a corridor or at the coffee machine.</p>
                        <p>That\'s the reason why we have decided to develop Coffee Poke application: an innovative and friendly enterprise social network which allows informal exchanges across your whole company.</p>
                        <p>Wherever you are:</p>
                        <ul>
                            <li>Keep yourself up-to-date with your company’s news</li>
                            <li>Share and comment on information and best practices</li>
                            <li>And don\'t forget to invite your colleagues for a real coffee thanks to coffee poke</li>
                        </ul>
                        '
        , 'coffee:welcome:instructions' => '
                            <h3>Follow those easy steps to get started</h3>
                            <div class="steps">
                                <!--<span class="stepN">1.</span>-->
                                <span class="instruction">
                                    <a href="#profile">Customise your profile:</a>
                                    <br />
                                    Share your hobbies, add your picture and choose a background
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">Edit a message:</a>
                                    <br />
                                    Let people know what you are working on, ask a question, seek advises, share articles, photos and videos
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">Be pro-active:</a>
                                    <br />
                                    Like and comment posts based on your personal or professional experience
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#feed">Accessibility:</a>
                                    <br />
                                    Coffee poke is accessible from any computer or smartphone at any time. Also, you can enjoy our TV app in your office. A real tool that creates connections within your company. With the TV app, you can follow what is going on on CoffeePoke.
                                    <br />
                                </span>
                            </div>

                            <p><h3><a href="/static/doc/CoffeePoke_en.pdf">More information</a> to get the best from Coffee Poke</h3></p>'

/**
 * User add
 */

	,'useradd:subject' => 'User account created'
	,'useradd:body' => '
%1$s,

A user account has been created for you at %2$s. To log in, visit:

http://%2$s.coffeepoke.com/

And log in with these user credentials:

Username: %4$s
Password: %5$s

Enjoy coffeePoke!!',

);

add_translation("en",$english);
