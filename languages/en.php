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
        , 'coffee:poke:action' => 'Coffee Poke'
        , 'coffee:poke:body' => 'Let\'s have a coffee'
        , 'coffee:poke:subject' => 'Coffee Break ?'
        , 'coffee:profile:incomplete' => '<strong>This is your profile.</strong><br />It is visible to your coworkers so be sure to complete it and keep it up to date!'
        /*feed*/
        , 'coffee:feed:share' => 'Share something with your colleagues...'
        , 'coffee:feed:upload' => 'Upload something...'
        , 'coffee:feed:send' => 'Send'
        , 'coffee:feed:cancel' => 'Cancel'
        , 'coffee:feed:search' => 'Search'
        , 'coffee:feed:corporatetags' => 'Sponsored Tags'
        , 'coffee:feed:mostused' => 'Most used'
        , 'coffee:feeditem:showalltext' => 'Show all text'
        , 'coffee:feeditem:hidetext' => 'Hide text'
        , 'coffee:feeditem:likethis' => 'like this'
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
        , 'coffee:feeditem:action:openlinkconfirm' => 'This will open a new window'
        /*menu*/
        , 'coffee:menu:welcome' => 'Welcome'
        , 'coffee:menu:feedlist' => 'Feed'
        , 'coffee:menu:profile' => 'Profile'
        , 'coffee:menu:tvapp' => 'CoffeeTV'
        , 'coffee:menu:logout' => 'Log out'
        , 'coffee:menu:admin' => 'Administration'
        , 'coffee:menu:settings' => 'Settings'
        /*admin*/
        , 'coffee:admin:corporatehashtagshelp' => 'As an administrator, you are able to highlight some hashtags (keywords that begin with a # to make them clickable). Users will see this list if they click on the arrow at the right of the search field (on the feed).'
        /*welcome*/
        , 'coffee:welcome:headline' => '<h3>Hello,</h3>
                        <p>Welcome to CoffeePoke. This message will only display for your 3 first log ins, so that you get used with the application.</p>
                        <p><a href="/static/doc/CoffeePoke_en.pdf">Click here </a> to understand how CoffeePoke will make you even more efficient. (this will open a .pdf file).</p>
                        <p>Enjoy and have a nice day with CoffeePoke.</p>'
        , 'coffee:welcome:instructions' => '
                            <h3>Taste the 2.0 Coffee Break</h3>
                            <div class="steps">
                                <!--<span class="stepN">1.</span>-->
                                <span class="instruction">
                                    <a href="#feed">The private feed of your coworkers</a>
                                    <br />
                                    Share and comment with your colleagues. Use the # symbol before a keywords to make it clickable and find information more easily.
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#profile">Profile</a>
                                    <br />
                                    Meet your colleagues and show them the real you. Share your hobbies, choose a background that represent what you like, and of course add your avatar!
                                    <br />
                                </span>
                            </div>
                            <div class="steps">
                                <span class="instruction">
                                    <a href="#tv">CoffeeTV</a>
                                    <br />
                                    No time to check CoffeePoke every day? Don\'t worry, the TV app is here for that. Have a look at the TV while you\'re at the watercooler, having a break. The TVs will show some of the public messages from your colleagues in a nice and fun interface. There is no TV in your office? Ask your boss to contact us, we\'ll take care of everything ;-)
                                    <br />
                                </span>
                            </div>'

/**
 * User add
 */

	,'useradd:subject' => '%1$s, welcome to CoffeePoke'
	,'useradd:body' => '
Hello %1$s,

CoffeePoke is the new private social network for %2$s.
CoffeePoke is here to let you share easily with your coworkers informal and free messages in a nice, simple and colored interface.
This is not a new productivity tool, this is just a new simple way to make your company smaller than it looks like and strenghten the bonds within the company.

%2$s has just created a user account for you.

To log-in, visit:
http://%2$s.coffeepoke.com/
And log in with these user credentials:

Username: %4$s
Password: %5$s

You can also log-in from your smartphone ;-)
See you soon,

%2$s',

);

add_translation("en",$english);