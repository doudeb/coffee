<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Enlightn Coffee Machine</title>
		<link href="static/css/coffee-machine.css" rel="stylesheet" />
		<script src="static/js/vendor/json2.js"></script>
		<script src="static/js/vendor/jquery-1.7.2.min.js"></script>
		<script src="static/js/vendor/underscore-min.js"></script>
		<script src="static/js/vendor/ICanHaz.min.js"></script>
		<script src="static/js/vendor/backbone.js"></script>
		<script src="static/js/vendor/jquery.cookie.js"></script>
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
							<textarea class="update-text input-xlarge" placeholder="Share something with your colleagues…"></textarea>
						</div>
						<div class="update-actions">
							<span class="add-media"><a href="#">Upload something...</a></span>
                             <form action="" method="post" enctype="multipart/form-data" class="out" id="uploadForm">
                                <input type="file" name="upload" id="upload">
                            </form>
							<button id="postUpdate" class="btn btn-primary">Send</button>
							<img class="loader" src="static/img/loader.gif" width="16" height="16" />
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
					<div class="primary-content">
						<a class="avatar" href="#profile/{{user.guid}}"><img src="{{user.icon_url}}" title="{{user.name}}" width="40" height="40" /></a>
						<div class="update">
							<a class="name" href="#profile/{{user.guid}}">{{user.name}}</a>
							<p class="text">{{{content.text}}}</p>
                            {{#isLong}}
                                <p class="text-orig">{{{content.textOrig}}}</p>
                                <a class="show-all-text" href="javascript:void(0)">Show all text</a>
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
								{{#isOne}}<p class="likes">{{#likes.users}}<a href="#profile/{{owner_guid}}">{{name}}</a> {{/likes.users}}likes this</p>{{/isOne}}
								{{#isTwo}}<p class="likes">{{#likes.users}}<a href="#profile/{{owner_guid}}">{{name}}</a> {{#first}}and {{/first}}{{/likes.users}} like this</p>{{/isTwo}}
								{{#isMore}}<p class="likes">
                                    {{#likes.users}}
                                        {{#first}}<a href="#profile/{{owner_guid}}">{{name}}</a>{{/first}}
                                    {{/likes.users}}
                                    and <a href="#">{{likes.total}} others</a> like this</p>
                                {{/isMore}}
							{{/likes}}
							<ul class="comments">
								{{#comment.showAllLink}}
									<li class="show-all"><a class="show-all-link" href="javascript:void(0)">Show all {{comment.total}} comments</a></li>
								{{/comment.showAllLink}}
								{{#comment.comments}}
									<li class="comment">
										<a class="avatar" href="#profile/{{owner_guid}}"><img src="{{icon_url}}" title="{{name}}" width="35" height="35" /></a>
										<div class="comment-content">
											<a href="#profile/{{owner_guid}}" class="name">{{name}}</a>
                                            {{#isCommentOwner}}<span class="pull-right remove-comment" data-id="{{id}}"><a href="javascript:void(0)" rel="tooltip" title="Remove comment" data-placement="right"><i class="icon-trash"></i></a></span>{{/isCommentOwner}}
											<p class="text">{{text}}</p>
											<p class="time">{{friendly_time}}</p>
										</div>
									</li>
								{{/comment.comments}}
								<li class="new-comment">
									<textarea class="new-comment-textarea" placeholder="Write a comment..."></textarea>
								</li>
							</ul>
						</div>
					</div>
					<ul class="update-actions">
						{{^hasLiked}}<li class="update-action update-action-like"><a href="{{id}}" rel="tooltip" title="Like" data-action="like" data-placement="right"><i class="icon-thumbs-up"></i></a></li>{{/hasLiked}}
						{{#hasLiked}}<li class="update-action update-action-unlike"><a href="#" rel="tooltip" title="Unlike" data-action="unlike" data-placement="right"><i class="icon-thumbs-up"></i></a></li>{{/hasLiked}}
						<li class="update-action update-action-comment"><a href="#" rel="tooltip" title="Add a comment" data-action="comment" data-placement="right"><i class="icon-reply"></i></a></li>
						{{#isOwner}}<li class="update-action update-action-remove"><a href="#" rel="tooltip" title="Remove update" data-action="remove" data-placement="right"><i class="icon-trash"></i></a></li>{{/isOwner}}
					</ul>
				</div>
			</div><!-- /.feed-item -->
		</script>

		<script type="text/html" id="menuTemplate">
			<div id="menu">
				<ul id="navigation">
					<li><a href="" rel="tooltip" title="News Feed" data-action="feed" data-placement="right"><i class="icon-home icon-white"></i></a></li>
					<li><a href="" rel="tooltip" title="Profile" data-action="profile" data-placement="right"><i class="icon-user icon-white"></i></a></li>
					<li><a href="" rel="tooltip" title="Log out" data-action="logout" data-placement="right"><i class="icon-off icon-white"></i></a></li>
				</ul>
			</div>
		</script>

		<script type="text/html" id="profileTemplate">
			<div id="content">
				<div id="profile"{{#isOwnProfile}} class="own-profile{{^isProfileComplete}} profile-editing{{/isProfileComplete}}"{{/isOwnProfile}}>
					{{^isProfileComplete}}
					{{#isOwnProfile}}<div class="alert"><strong>This is your profile.</strong><br />It is visible to your coworkers so be sure to complete it and keep it up to date!</div>{{/isOwnProfile}}
					{{/isProfileComplete}}
					<div class="content-module">
						<div class="primary-content">
                            <ul class="update-actions">
                                <li class="update-action update-action-poke"><a href="mailto:{{email}}&subject=Let's have a coffee !!!&body=In your hass ?" rel="tooltip" title="Coffee Poke!" data-action="coffeePoke" data-placement="right"><i class="icon-coffeepoke"></i></a></li>
                            </ul>
							<div class="avatar">
								<img src="{{icon_url}}" width="100" height="100" />
                                <form action="" method="post" enctype="multipart/form-data" class="out" id="avatarUpload">
                                    <input type="file" name="avatar" id="avatar">
                                </form>
								{{#isOwnProfile}}<button class="btn btn-mini edit" rel="profile-edit tooltip" title="Change avatar" data-edit="avatar"><i class="icon-edit"></i></button>{{/isOwnProfile}}
							</div>
							<div class="info">
								<span class="name">{{name}}</span>
                                {{#isOwnProfile}}
                                Change cover picture
                                <button class="btn btn-mini edit" rel="profile-edit tooltip" title="Change your cover pic" data-edit="userCover" id="cover-edit"><i class="icon-edit"></i></button>
                                <form action="" method="post" enctype="multipart/form-data" class="out" id="coverUpload">
                                    <input type="file" name="cover" id="cover">
                                </form>
                                {{/isOwnProfile}}
								{{#hasHeadline}}<span class="headline"><span {{#isOwnProfile}}class="editable" data-name="headline"{{/isOwnProfile}}>{{{headline}}}</span></span>{{/hasHeadline}}
								{{^hasHeadline}}{{#isOwnProfile}}<span class="headline"><span class="editable editable-hover" data-name="headline">[Add a headline]</span></span>{{/isOwnProfile}}{{/hasHeadline}}
								{{#hasDepartment}}<span class="department"><span {{#isOwnProfile}}class="editable" data-name="department"{{/isOwnProfile}}>{{department}}</span></span>{{/hasDepartment}}
								{{^hasDepartment}}{{#isOwnProfile}}<span class="department"> <span class="editable editable-hover" data-name="department">[Specify your department]</span></span>{{/isOwnProfile}}{{/hasDepartment}}
								{{#hasLocation}}<span class="location"><span {{#isOwnProfile}}class="editable" data-name="location"{{/isOwnProfile}}>{{location}}</span></span>{{/hasLocation}}
								{{^hasLocation}}{{#isOwnProfile}}<span class="location"><span class="editable editable-hover" data-name="location">[Choose your location]</span></span>{{/isOwnProfile}}{{/hasLocation}}
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
                                    <h3>Introduction</h3>
                                {{#hasIntroduction}}
                                    <span {{#isOwnProfile}}class="editable"{{/isOwnProfile}} data-name="introduction">{{{introduction}}}</span>
                                {{/hasIntroduction}}
                                {{^hasIntroduction}}{{#isOwnProfile}}
                                    <span class="editable editable-hover" data-name="introduction">[Write a short introduction]</span>
                                {{/isOwnProfile}}{{/hasIntroduction}}
                                </div>
                                <div class="info">
                                    <h3>Contact Information</h3>
                                    <ul>
                                        <li class="email">{{email}}</li>
                                        {{#hasPhone}}<li><span {{#isOwnProfile}}class="editable" data-name="phone"{{/isOwnProfile}}>{{phone}}</span></li>{{/hasPhone}}
                                        {{^hasPhone}}{{#isOwnProfile}}<li><span class="editable editable-hover" data-name="phone">[Choose your phone]</span></li>{{/isOwnProfile}}{{/hasPhone}}
                                        {{#hasCellphone}}<li><span {{#isOwnProfile}}class="editable" data-name="cellphone"{{/isOwnProfile}}>{{cellphone}}</span></li>{{/hasCellphone}}
                                        {{^hasCellphone}}{{#isOwnProfile}}<li><span class="editable editable-hover" data-name="cellphone">[Choose your cellphone]</span></li>{{/isOwnProfile}}{{/hasCellphone}}
                                    </ul>
                                </div>
                            </div>
							<div class="other">
								<div class="hobbies-interests">
									<h3>Hobbies &amp; Interests</h3>
									<ul>
										{{#hobbies}}
										<li><span {{#isOwnProfile}}class="editable" data-name="hobbies" data-key="{{key}}"{{/isOwnProfile}}>{{value}}</span></li>
										{{/hobbies}}
									</ul>
									{{#isOwnProfile}}<button class="btn btn-small add-hobby"><i class="icon-plus"></i> Add a hobby or interest</button>{{/isOwnProfile}}
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
		</script>
	</body>
</html>