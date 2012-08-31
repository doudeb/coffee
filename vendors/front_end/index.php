<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport" id="viewport">
    	<meta content="yes" name="apple-mobile-web-app-capable" />
        <meta content="black" name="apple-mobile-web-app-status-bar-style" />
        <meta name=”viewport” content=”width=320,user-scalable=false” />

		<title>Enlightn Coffee Machine</title>
		<link href="static/css/coffee-machine.css" rel="stylesheet" />

        <link href="static/img/favicon.ico" rel="shortcut icon"/>
        <link rel="apple-touch-icon" href="static/img/iphone-icon.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="static/img/ipad-icon.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="/static/img/iphone4-icon.png" />
        <link rel="apple-touch-startup-image" href="static/img/iphone-startup.png">

		<script src="static/js/vendor/json2.js"></script>
		<script src="static/js/vendor/jquery-1.7.2.min.js"></script>
		<script src="static/js/vendor/underscore-min.js"></script>
		<script src="static/js/vendor/ICanHaz.min.js"></script>
		<script src="static/js/vendor/backbone.js"></script>
		<script src="static/js/vendor/jquery.form.js"></script>
	</head>
	<body>
		<div id="container">
            <div class="watermark"><img src="static/img/logo.png" id="watermark" /></div>
		</div>


		<script type="text/html" id="loginTemplate">
			<div id="login">
				<form class="form-horizontal">
					<legend>Login</legend>
					<div class="control-group">
						<label class="control-label" for="inputUsername">Username</label>
						<div class="controls">
							<input type="text" class="input-large" id="inputUsername" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputPassword">Password</label>
						<div class="controls">
							<input type="password" class="input-large" id="inputPassword" />
						</div>
					</div>
					<div class="form-actions">
						<button class="btn btn-primary" id="doLogin">Login</button>
					</div>
				</form>
			</div>
		</script>

		<script type="text/html" id="microbloggingTemplate">
			<div id="content">
				<div id="microblogging" class="content-module">
					<div class="primary-content">
						<div class="update-content">
							<div class="avatar"><img src="{{icon_url}}" width="60" height="60" /></div>
							<textarea class="update-text input-xlarge" placeholder="{{#translate}}coffee:feed:share{{/translate}}"></textarea>
						</div>
						<div class="update-actions">
							<span class="add-media"><a href="#">{{#translate}}coffee:feed:upload{{/translate}}</a></span>
                             <form action="" method="post" enctype="multipart/form-data" class="out" id="uploadForm">
                                <input type="file" name="upload" id="upload">
                            </form>
							<button id="postUpdate" class="btn btn-primary">{{#translate}}coffee:feed:send{{/translate}}</button>
							<img class="loader" src="static/img/loader.gif" width="16" height="16" />
                            {{#isAdmin}}
                            <span class="broadcastMessage">
                                    <a class="" rel="tooltip" title="Broadcast message" href="" data-placement="left"><i class="icon-bullhorn"></i></a>
                            </span>
                            {{/isAdmin}}
                        </div>
					</div>
				</div>
				<div id="feed-items">
				</div>
			</div>
		</script>
		<script type="text/html" id="microbloggingAttachmentTemplate">
			<div class="attachment">
				<img class="thumbnail" width="100" src="{{thumbnail}}" />
				<div class="info">
					<span class="title">{{title}}</span>
					<span class="description">{{description}}</span>
				</div>
				<a class="remove" rel="tooltip" title="Remove" href=""><i class="icon-remove"></i></a>
			</div>
		</script>

		<script type="text/html" id="feedItemTemplate">
			<div class="feed-item" data-guid="{{guid}}">
				<div class="content-module">
                    {{#isBroadCastMessage}}
                    <span class="broadcastMessage"></span>
                    {{/isBroadCastMessage}}
					<div class="primary-content">
						<a class="avatar" href="#profile/{{user.guid}}"><img src="{{user.icon_url}}" title="{{user.name}}" width="40" height="40" /></a>
						<div class="update">
							<a class="name" href="#profile/{{user.guid}}">{{user.name}}</a>
							<p class="text">{{{content.text}}}</p>
                            {{#isLong}}
                                <p class="text-orig">{{{content.textOrig}}}</p>
                                <a class="show-all-text" href="javascript:void(0)">{{#translate}}coffee:feeditem:showalltext{{/translate}}</a>
                                <a class="show-all-text hide" href="javascript:void(0)">{{#translate}}coffee:feeditem:hidetext{{/translate}}</a>
                            {{/isLong}}
							<p class="time"><i class="icon-time"></i> {{content.friendly_time}}</p>
							{{#attachment}}
							<div class="attachment">
								<a class="thumbnail" href="{{url}}" target="_blank"><img width="100" src="{{thumbnail}}" /></a>
								<div class="info">
									<a href="{{url}}" class="title" target="_blank">{{title}}</a>
									<span class="description">{{description}}</span>
								</div>
							</div>
							{{/attachment}}
						</div>
					</div>
					<div class="secondary-content">
						<div class="interactions">
							{{#likes}}
								{{#isOne}}<p class="likes">{{#likes.users}}<a href="#profile/{{owner_guid}}">{{name}}</a> {{/likes.users}}{{#translate}}coffee:feeditem:likesthis{{/translate}}</p>{{/isOne}}
								{{#isTwo}}<p class="likes">{{#likes.users}}<a href="#profile/{{owner_guid}}">{{name}}</a> {{#first}}{{#translate}}coffee:feeditem:and{{/translate}} {{/first}}{{/likes.users}} {{#translate}}coffee:feeditem:likesthis{{/translate}}</p>{{/isTwo}}
								{{#isMore}}<p class="likes">
                                    {{#likes.users}}
                                        {{#first}}<a href="#profile/{{owner_guid}}">{{name}}</a>{{/first}}
                                    {{/likes.users}}
                                    {{#translate}}coffee:feeditem:and{{/translate}} <a href="#" rel="tooltip" title="{{#likes.others}}{{name}}<br /> {{/likes.others}}" data-placement="bottom">{{likes.total}} {{#translate}}coffee:feeditem:others{{/translate}}</a> {{#translate}}coffee:feeditem:likesthis{{/translate}}</p>
                                {{/isMore}}
							{{/likes}}
							<ul class="comments">
								{{#comment.showAllLink}}
                                <li class="show-all"><a class="show-all-link" href="javascript:void(0)">{{#translate}}coffee:feeditem:showall{{/translate}}&nbsp; {{comment.total}} {{#translate}}coffee:feeditem:comments{{/translate}}</a></li>
								{{/comment.showAllLink}}
								{{#comment.comments}}
									<li class="comment">
										<a class="avatar" href="#profile/{{owner_guid}}"><img src="{{icon_url}}" title="{{name}}" width="35" height="35" /></a>
										<div class="comment-content">
											<a href="#profile/{{owner_guid}}" class="name">{{name}}</a>
                                            {{#isCommentOwner}}<span class="pull-right remove-comment" data-id="{{id}}"><a href="javascript:void(0)" rel="tooltip" title="{{#translate}}coffee:feeditem:action:removecomment{{/translate}}" data-placement="right"><i class="icon-trash"></i></a></span>{{/isCommentOwner}}
											<p class="text">{{text}}</p>
											<p class="time">{{friendly_time}}</p>
										</div>
									</li>
								{{/comment.comments}}
								<li class="new-comment">
									<textarea class="new-comment-textarea" placeholder="{{#translate}}coffee:feeditem:action:addcomment{{/translate}}"></textarea>
								</li>
							</ul>
						</div>
					</div>
					<ul class="update-actions">
						{{^hasLiked}}<li class="update-action update-action-like"><a href="{{id}}" rel="tooltip" title="{{#translate}}coffee:feeditem:action:like{{/translate}}" data-action="like" data-placement="right"><i class="icon-thumbs-up"></i></a></li>{{/hasLiked}}
						{{#hasLiked}}<li class="update-action update-action-unlike"><a href="#" rel="tooltip" title="{{#translate}}coffee:feeditem:action:unlike{{/translate}}" data-action="unlike" data-placement="right"><i class="icon-thumbs-up"></i></a></li>{{/hasLiked}}
						<li class="update-action update-action-comment"><a href="#" rel="tooltip" title="{{#translate}}coffee:feeditem:action:addcomment{{/translate}}" data-action="comment" data-placement="right"><i class="icon-reply"></i></a></li>
						{{#isOwner}}<li class="update-action update-action-remove"><a href="#" rel="tooltip" title="{{#translate}}coffee:feeditem:action:removecomment{{/translate}}" data-action="remove" data-placement="right"><i class="icon-trash"></i></a></li>{{/isOwner}}
					</ul>
				</div>
			</div><!-- /.feed-item -->
		</script>

		<script type="text/html" id="menuTemplate">
			<div id="menu">
				<ul id="navigation">
					{{#displayWelcome}}<li><a href="" rel="tooltip" title="{{#translate}}coffee:menu:welcome{{/translate}}" data-action="welcome" data-placement="right"><i class="icon-flag icon-white"></i></a></li>{{/displayWelcome}}
					<li><a href="" rel="tooltip" title="{{#translate}}coffee:menu:feedlist{{/translate}}" data-action="feed" data-placement="right"><i class="icon-home icon-white"></i></a></li>
					<li><a href="" rel="tooltip" title="{{#translate}}coffee:menu:profile{{/translate}}" data-action="profile" data-placement="right"><i class="icon-user icon-white"></i></a></li>
					<li><a href="" rel="tooltip" title="{{#translate}}coffee:menu:tvapp{{/translate}}" data-action="tv" data-placement="right"><i class="icon-tv icon-white"></i></a></li>
					<li><a href="" rel="tooltip" title="{{#translate}}coffee:menu:logout{{/translate}}" data-action="logout" data-placement="right"><i class="icon-off icon-white"></i></a></li>
				</ul>
			</div>
		</script>

		<script type="text/html" id="profileTemplate">
			<div id="content">
				<div id="profile"{{#isOwnProfile}} class="own-profile{{^isProfileComplete}} profile-editing{{/isProfileComplete}}"{{/isOwnProfile}}>
					{{^isProfileComplete}}
					{{#isOwnProfile}}<div class="alert">{{#translate}}coffee:profile:incomplete{{/translate}}</div>{{/isOwnProfile}}
					{{/isProfileComplete}}
					<div class="content-module">
						<div class="primary-content">
                            <ul class="update-actions">
                                <li class="update-action update-action-poke"><a href="mailto:{{email}}?subject={{#translate}}coffee:poke:subject{{/translate}}&body={{#translate}}coffee:poke:body{{/translate}}" rel="tooltip" title="{{#translate}}coffee:poke:action{{/translate}}" data-action="coffeePoke" data-placement="right"><i class="icon-coffeepoke"></i></a></li>
                            </ul>
							<div class="avatar">
								<img src="{{icon_url}}" width="100" height="100" />
                                <form action="" method="post" enctype="multipart/form-data" class="out" id="avatarUpload">
                                    <input type="file" name="avatar" id="avatar">
                                </form>
								{{#isOwnProfile}}<button class="btn btn-mini edit" rel="profile-edit tooltip" title="{{#translate}}coffee:profile:button:changeavatar{{/translate}}" data-edit="avatar"><i class="icon-edit"></i></button>{{/isOwnProfile}}
							</div>
							<div class="info">
								<span class="name">{{name}}</span>
								{{#hasHeadline}}<span class="headline"><span {{#isOwnProfile}}class="editable" data-name="headline"{{/isOwnProfile}}>{{{headline}}}</span></span>{{/hasHeadline}}
								{{^hasHeadline}}{{#isOwnProfile}}<span class="headline"><span class="editable editable-hover" data-name="headline">{{#translate}}coffee:profile:addheadline{{/translate}}</span></span>{{/isOwnProfile}}{{/hasHeadline}}
								<!--{{#hasDepartment}}<span class="department"><span {{#isOwnProfile}}class="editable" data-name="department"{{/isOwnProfile}}>{{department}}</span></span>{{/hasDepartment}}
								{{^hasDepartment}}{{#isOwnProfile}}<span class="department"> <span class="editable editable-hover" data-name="department">[Specify your department]</span></span>{{/isOwnProfile}}{{/hasDepartment}}-->
								{{#hasLocation}}<span class="location"><span {{#isOwnProfile}}class="editable" data-name="location"{{/isOwnProfile}}>{{location}}</span></span>{{/hasLocation}}
								{{^hasLocation}}{{#isOwnProfile}}<span class="location"><span class="editable editable-hover" data-name="location">coffee:profile:addlocation</span></span>{{/isOwnProfile}}{{/hasLocation}}
                                {{#isOwnProfile}}
                                <span class="pull-right"><button class="btn btn-small" rel="profile-edit tooltip" title="{{#translate}}coffee:profile:title:changecoverpic{{/translate}}" data-edit="userCover" id="cover-edit"><i class="icon-picture"></i> {{#translate}}coffee:profile:button:background{{/translate}}</button></span>
                                <form action="" method="post" enctype="multipart/form-data" class="out" id="coverUpload">
                                    <input type="file" name="cover" id="cover">
                                </form>
                                {{/isOwnProfile}}
                            </div>
    						<!--<ul class="sm-links">
								{{#socialmedia}}
								<li><a href="{{link}}" class="sm sm-{{service}}" target="_blank" rel="tooltip" title="{{#isTwitter}}Twitter{{/isTwitter}}{{#isFacebook}}Facebook{{/isFacebook}}{{#isLinkedIn}}LinkedIn{{/isLinkedIn}}{{#isSkype}}Call on Skype{{/isSkype}}" data-placement="right"></a></li>
								{{/socialmedia}}
								{{#isOwnProfile}}
								<li><button class="sm sm-addnew" rel="tooltip" title="Add a social network" data-placement="right"></button></li>
								{{/isOwnProfile}}
							</ul>-->
							<div class="popover right add-socialmedia">
								<div class="arrow"></div>
								<div class="popover-inner">
									<h3 class="popover-title">Add a social network</h3>
									<div class="popover-content">
										<form>
											<div class="control-group">
												<label class="control-label" for="serviceName">Service</label>
												<div class="controls">
													<select id="serviceName">
														<option value="twitter">Twitter</option>
														<option value="facebook">Facebook</option>
														<option value="linkedin">LinkedIn</option>
														<option value="skype">Skype</option>
													</select>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<div class="secondary-content">
                            <div class="other">
                                <div class="introduction">
                                   <h3>{{#translate}}coffee:profile:presentation{{/translate}}</h3>
                                {{#hasIntroduction}}
                                    <span class="introductionCut{{#isOwnProfile}} editable{{/isOwnProfile}}" data-name="introduction">
                                        {{{introduction}}}
                                    </span>
                                {{/hasIntroduction}}
                                {{^hasIntroduction}}
                                    {{#isOwnProfile}}
                                    <span class="editable editable-hover" data-name="introduction">{{#translate}}coffee:profile:add:presentation{{/translate}}</span>
                                    {{/isOwnProfile}}
                                {{/hasIntroduction}}
                                {{#isIntroductionLong}}
                                <!--<a class="show-all-text" href="javascript:void(0)">{{#translate}}coffee:feeditem:showalltext{{/translate}}</a>
                                <a class="show-all-text hide" href="javascript:void(0)">{{#translate}}coffee:feeditem:hidealltext{{/translate}}</a>-->
                                {{/isIntroductionLong}}
                                </div>
                                <div class="info">
                                    <h3>{{#translate}}coffee:profile:information{{/translate}}</h3>
                                    <ul>
                                        <li class="email"><a href="mailto:{{email}}">{{email}}</a></li>
                                        {{#hasPhone}}<li><span {{#isOwnProfile}}class="editable" data-name="phone"{{/isOwnProfile}}>{{phone}}</span> - {{#translate}}coffee:profile:information:workphone{{/translate}}</li>{{/hasPhone}}
                                        {{^hasPhone}}{{#isOwnProfile}}<li><span class="editable editable-hover" data-name="phone">{{#translate}}coffee:profile:add:workphone{{/translate}}</span> - {{#translate}}coffee:profile:information:workphone{{/translate}}</li>{{/isOwnProfile}}{{/hasPhone}}
                                        {{#hasCellphone}}<li><span {{#isOwnProfile}}class="editable" data-name="cellphone"{{/isOwnProfile}}>{{cellphone}}</span> - {{#translate}}coffee:profile:information:mobilephone{{/translate}}</li>{{/hasCellphone}}
                                        {{^hasCellphone}}{{#isOwnProfile}}<li><span class="editable editable-hover" data-name="cellphone">{{#translate}}coffee:profile:add:mobilephone{{/translate}}</span> - {{#translate}}coffee:profile:information:workphone{{/translate}}</li>{{/isOwnProfile}}{{/hasCellphone}}
                                    </ul>
                                </div>
                            </div>
							<div class="other">
								<div class="hobbies-interests">
									<h3>{{#translate}}coffee:profile:hobbiesandinterest{{/translate}}</h3>
									<ul>
										{{#hobbies}}
										<li><span {{#isOwnProfile}}class="editable" data-name="hobbies" data-key="{{key}}"{{/isOwnProfile}}>{{value}}</span></li>
										{{/hobbies}}
									</ul>
									{{#isOwnProfile}}<button class="btn btn-small add-hobby"><i class="icon-plus"></i> {{#translate}}coffee:profile:add:hobbiesandinterest{{/translate}}</button>{{/isOwnProfile}}
								</div>
								<!--<div class="languages">
									<h3>Languages</h3>
									<ul>
										{{#languages}}
										<li rel="tooltip" title="{{#isNative}}Native language{{/isNative}}{{#isBilingual}}Bilingual{{/isBilingual}}{{#isFluent}}Fluent{{/isFluent}}{{#isIntermediate}}Intermediate{{/isIntermediate}}{{#isBeginner}}Beginner{{/isBeginner}}" data-placement="skillbar">{{name}} <span class="level level{{level}}"></span></li>
										{{/languages}}
									</ul>
									{{#isOwnProfile}}<button class="btn btn-small add-language"><i class="icon-plus"></i> Add a language</button>{{/isOwnProfile}}
								</div>-->
							</div>
						</div>
					</div>
				</div>
			</div>
		</script>

        <script type="text/html" id="tvAppTemplate">
            <link href="static/css/style.css" rel="stylesheet" type="text/css">
            {{{scripts}}}
            <div id="cadre">
                <div id="logo"><img src="static/img/logo.png" alt="Logo" width="126" height="34" /></div>
                <div id="fondImg"><img src="" id="fond_icon_url" alt="" /></div>
                <div id="contenu">
                    <div class="postRoue">
                        <div id="img"><img src="" id="icon_url" width="100" height="100" /></div>
                        <div id="usernameBlanc"></div>
                        <p><span id="friendly_time"></span></p>
                    <div class="roue">
                            <span>1</span>
                            <span>2</span>
                            <span>3</span>
                            <span>4</span>
                            <span>5</span>
                            <span>6</span>
                            <span>7</span>
                            <span>8</span>
                            <span>9</span>
                            <span>10</span>
                        </div>
                    </div>

                <div class="post">
                        <div class="text-container"><p id="text"></p></div>
                        <div id="attachment">
                            <div id="marges">
                                <div id="typeAtt" class="piece"></div>
                                <p id="miniatureAtt">&nbsp;</p>
                                <p id="titreAtt">&nbsp;</p>
                                <p id="descAtt">&nbsp;</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="fullscreen"><a href="#" onclick="javascript:pleinEcran()"><img src="static/img/full-screen.png" width="64" height="64" /></a></div>
        </script>

        <script type="text/html" id="welcomeAppTemplate">
            <div id="content">
                <div id="welcome">
                    <div class="alert">
                        {{#translate}}coffee:welcome:headline{{/translate}}
                    </div>
					<div class="content-module">
                        <div class="primary-content">
                             {{#translate}}coffee:welcome:instructions{{/translate}}
                        </div>
                    </div>
                </div>
            </div>
        </script>

		<script src="static/js/coffee.js"></script>
		<script src="static/js/bootstrap-tooltip.js"></script>
		<script>
		$('body').delegate('[rel*=tooltip]', 'mouseenter mouseleave mousedown', function(e) {
			var element = $(this);

			switch (e.handleObj.origType) {
				case 'mouseenter':
					element.tooltip('show');
					break;
				case 'mouseleave': case 'mousedown':
					element.tooltip('hide');
					break;
			}
		});
        $(document).bind('webkitfullscreenchange mozfullscreenchange fullscreenchange',function () {
            $('#menu').toggle();
        });

		</script>
	</body>
</html>