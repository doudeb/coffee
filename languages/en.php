<?php
/**
 * Core English Language
 *
 * @package Elgg.Core
 * @subpackage Languages.English
 * @versiondate 171212
 */

$translation = array(
/*menu*/
         'coffee:menu:welcome' => 'Welcome'
        , 'coffee:menu:feedlist' => 'CoffeeWall'
        , 'coffee:menu:profile' => 'Profile'
        , 'coffee:menu:tvapp' => 'CoffeeTV'
        , 'coffee:menu:admin' => 'Administration'
        , 'coffee:menu:settings' => 'Settings'
        , 'coffee:menu:logout' => 'Log out'
        , 'coffee:menu:people' => 'People'
        , 'coffee:menu:notifications' => 'My notifications'

/*welcome*/
        , 'coffee:welcome:headline' => '<h3>Hi,</h3>
                        <p>Welcome to CoffeePoke. This message will only display for your 3 first log ins, so that you get used with the application.</p>
                        <p><a href="/static/doc/CoffeePoke_en.pdf" target="_blank">Click here </a> to understand how CoffeePoke will make you even more efficient (this will open a .pdf file).</p>
                        <p>Enjoy and have a nice day with CoffeePoke.</p>'
        , 'coffee:welcome:instructions' => '
                            <h3>Taste the 2.0 Coffee Break</h3>
                            <div class="steps">
                                <!--<span class="stepN">1.</span>-->
                                <span class="instruction">
                                    <a href="#feed">The private feed of your company</a>
                                    <br />
                                    Share and comment with your colleagues. Use the # symbol before a keyword to make it clickable and find information more easily.
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
                                    No time to check CoffeePoke every day? Don\'t worry, the CoffeeTV app is here for that. Have a look at the TV while you\'re at the watercooler, having a break. The TVs will show some of the public messages from your colleagues in a nice and fun interface. There is no TV in your office? Ask your Community Manager to contact us, we\'ll take care of everything. ;-)
                                    <br />
                                </span>
                            </div>'

/*feed*/
        , 'coffee:feed:share' => 'Share something with your colleagues...'
        , 'coffee:feed:upload' => 'Upload something...'
        , 'coffee:feed:send' => 'Send'
        , 'coffee:feed:cancel' => 'Cancel'
        , 'coffee:feed:search' => 'Search'
        , 'coffee:feed:corporatetags' => 'Suggestions'
        , 'coffee:feed:mostused' => 'Most used'
        , 'coffee:feeditem:showalltext' => 'Show all text'
        , 'coffee:feeditem:hidetext' => 'Hide text'
        , 'coffee:feeditem:likesthis' => 'likes this'
        , 'coffee:feeditem:likethis' => 'like this'
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
       	, 'coffee:feed:broadcastmessageunactive' => 'Highlight this message'
		, 'coffee:feed:broadcastmessage' => 'Don\'t highlight this message'
		, 'coffee:feed:search' => 'Search'
		, 'coffee:feed:corporatetags' => 'Suggestions'
		, 'coffee:feed:mostused' => 'Most used'

/*profile */
        , 'coffee:profile:add:hobbiesandinterest' => 'Add a hobby or interest'
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
        , 'coffee:poke:action' => 'CoffeePoke'
        , 'coffee:poke:body' => 'Let\'s have a coffee break together?'
        , 'coffee:poke:subject' => 'CoffeeBreak?'
        , 'coffee:profile:incomplete' => '<strong>This is your profile.</strong><br />It is visible to your coworkers so be sure to complete it and keep it up to date!'

/*TVapp*/
		, 'coffee:tvapp:title' => 'Hi,'
		, 'coffee:tvapp:message' => 'What would you like to see on the screen'
		, 'coffee:tvapp:button' => 'Reply'
		, 'coffee:tvapp:answer1' => 'I\'d love to see the 10 last posts'
		, 'coffee:tvapp:fromusers' => 'from'
		, 'coffee:tvapp:fromusersall' => 'all users'
		, 'coffee:tvapp:fromuserselect' => 'the following users:'
		, 'coffee:tvapp:fromuserselectusername' => 'Name'
		, 'coffee:tvapp:tagselect' => 'Only those with the following hashtags'
		, 'coffee:tvapp:addhashtag' => 'hashtag'
		, 'coffee:tvapp:thanks' => 'Thanks'
		, 'coffee:tvapp:cancel' => 'Cancel'

/*admin*/
		, 'coffee:admin:message' => 'This is your CoffeePoke admin tool. <br/ > <a href="#userSettings">Go to your settings</a>'
		, 'coffee:admin:users' => 'Users'
		, 'coffee:admin:search' => 'Search'
		, 'coffee:admin:addnewuser' => 'Add a new user'
		, 'coffee:admin:addnewusertitle' => 'Add a new user'
		, 'coffee:admin:displayname' => 'Name'
		, 'coffee:admin:email' => 'Email'
		, 'coffee:admin:password' => 'Pick a password'
		, 'coffee:admin:confirmpassword' => 'Confirm password'
		, 'coffee:admin:admin' => 'Tick the box if you want this user to have admin rights:'
		, 'coffee:admin:sendemail' => 'Tick the box if you want this user to receive a confirmation email with all his/her details:'
		, 'coffee:admin:language' => 'Language'
		, 'coffee:admin:languageES' => 'Spanish'
		, 'coffee:admin:languageFR' => 'French'
		, 'coffee:admin:languageEN' => 'English'
		, 'coffee:admin:addnewusersave' => 'Create profile'
		, 'coffee:admin:manageuser' => 'Manage users'
		, 'coffee:admin:popupdelete' => 'Do you really want to delete this user?<br />Warning, his/her profile and all his/her messages will be deleted.'
		, 'coffee:admin:site' => 'Site'
		, 'coffee:admin:sitesettings' => 'Site Settings'
		, 'coffee:admin:sitesettingstitle' => 'Site Settings'
		, 'coffee:admin:logo' => '<strong>Logo</strong><br/>Max. recommended size: width: 300px - height: 100px.'
		, 'coffee:admin:background' => '<strong>Background</strong><br/>Min. recommended size: 1920*1080. Max. recommended weight: 300mo.'
		, 'coffee:admin:defaultlanguage' => 'Default language'
		, 'coffee:admin:defaultlanguageEN' => 'English'
		, 'coffee:admin:defaultlanguageES' => 'Spanish'
		, 'coffee:admin:defaultlanguageFR' => 'French'
		, 'coffee:admin:sitesettingssave' => 'Save'
		, 'coffee:admin:corporatehashtags' => 'Hashtags suggestions'
		, 'coffee:admin:corporatehashtagstitle' => 'Hashtags suggestions'
		, 'coffee:admin:corporatehashtagshelp' => 'As an admin, you can push forward some hashtags. The # symbol, called a hashtag, is used to mark keywords or topics in a post. It\'s a way to categorize messages. Users will find hashtags list by clicking on the arrow on the right-hand side of the search engine on the CoffeeWall.'
		, 'coffee:admin:addhashtag' => 'hashtag'
		, 'coffee:admin:corporatehashtagssave' => 'Save'

/*usersettings*/
		, 'coffee:usersettings:message' => 'Welcome to your settings'
		, 'coffee:usersettings:usersettings' => 'User settings'
		, 'coffee:usersettings:name' => 'Name'
		, 'coffee:usersettings:currentpassword' => 'Current password'
		, 'coffee:usersettings:newpassword' => 'New password'
		, 'coffee:usersettings:confirmnewpassword' => 'New password confirmation'
		, 'coffee:usersettings:language' => 'Language'
		, 'coffee:usersettings:languageES' => 'Spanish'
		, 'coffee:usersettings:languageFR' => 'French'
		, 'coffee:usersettings:languageEN' => 'English'
		, 'coffee:usersettings:save' => 'Save'

/*User add*/

        ,'useradd:subject' => '%1$s, welcome to CoffeePoke'
        ,'useradd:body' => '
Hi %1$s,

I invite you to join %2$s\'s private community on CoffeePoke.

CoffeePoke is here to let you share easily with your coworkers in a nice, simple and fun interface.
Our goal: to strenghten the bonds, promote informal exchanges and reinforce conviviality within our company.

I\'ve just created a user account for you. To log-in, visit:
http://%2$s.coffeepoke.com/
And log in with these user credentials:
Username: %4$s
Password: %5$s

Log in now to see what your colleagues are talking about, and tell them what you\'re working on.

Oh by the way, you can also log in from the web browser of your smartphone while your out of the office.
See you soon on CoffeePoke,

%6$s'
        , 'email:resetreq:body' => '
Hi %1$s,

You have requested a new password for your account.

Click on the link below to reset your password. Otherwise ignore this email.

%3$s

Thanks

Team Coffeepoke'
    , 'user:password:resetreq:success' => ' Your password request has been successfully sent by email.'
    , 'user:password:resetreq:fail' => 'Ooops… Password request has failed.'
    , 'user:resetpassword:yes' => 'Your password has now been updated, you will received an email shortly.'
    , 'user:resetpassword:no' => 'Oooops… Password reset failed, please try again.'
    #Notifications
    , 'notification::like' => 'has liked your post'
    , 'notification::like::alsoliked' => 'has also liked this post'
    , 'notification::mentioned::liked' => 'has liked this post you were quoted in'
    , 'hoho' => 'has liked the post you commented'
    , 'notification::comment' => 'has commented your post'
    , 'notification::comment::alsocommented' => 'has also commented this post'
    , 'notification::comment::mentioned' => 'has commented this post you were quoted in'
    , 'notification::post::mentioned' => 'You have been quoted in a post from'
    , 'notification::comment::mentioned' => 'You have been quoted in a comment from'
    , 'notification::post::mention::comment' => 'has commented this post you were quoted in '
    #New Label
    , 'notification::like' => 'likes this post '
    , 'notification::comment' => 'has commented this post '
    , 'notification::mention' => 'has quoted your name in '
    #People
    , 'coffee:people:legend' => 'Directory. Find your colleagues\' profiles'
);

add_translation('en', $translation);