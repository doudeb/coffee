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
		<script src="static/js/session.js"></script>
	</head>
	<body style="background: url(userpics/client_bg.jpeg) fixed repeat 0 0;">
		<div id="container">
			<img src="static/img/0_dot.gif" id="watermark" width="90" height="40" />
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
							<div class="avatar"><img src="userpics/user_avatar.png" width="60" height="60" /></div>
							<textarea class="update-text input-xlarge" placeholder="Share something with your colleagues…"></textarea>
						</div>
						<div class="update-actions">
							<span class="add-media"><a href="#">Upload picture</a></span>
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
							<p class="text">{{content.text}}</p>
							<p class="time"><i class="icon-time"></i> {{content.friendly_time}}</p>
							{{#attachment}}
							<div class="attachment">
								<a class="thumbnail" href=""><img width="100" src="{{thumbnail}}" /></a>
								<div class="info">
									<a class="title">{{title}}</a>
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
								{{#isMore}}<p class="likes">{{#likes.users}}<a href="#profile/{{owner_guid}}">{{name}}</a>{{#first}}, {{/first}}{{^first}} and <a href="#">{{likes.total}} others</a> {{/first}} {{/likes.users}} like this</p>{{/isMore}}
							{{/likes}}
							<ul class="comments">
								{{#comment.comments}}
									<li class="comment">
										<a class="avatar" href="#profile/{{owner_guid}}"><img src="userpics/avatar/{{user.avatar}}" title="{{name}}" width="35" height="35" /></a>
										<div class="comment-content">
											<a href="#profile/{{owner_guid}}" class="name">{{name}}</a>
											<p class="text">{{text}}</p>
											<p class="time">{{friendly_time}}</p>
										</div>
									</li>
									{{#showAllLink}}
										<li class="show-all"><a class="show-all-link" href="javascript:void(0)">Show all comments</a></li>
									{{/showAllLink}}
								{{/comment.comments}}
								<li class="new-comment">
									<textarea class="new-comment-textarea" placeholder="Write a comment..."></textarea>
								</li>
							</ul>
						</div>
					</div>
					<ul class="update-actions">
						{{^hasLiked}}<li class="update-action update-action-like"><a href="{{id}}" rel="tooltip" title="Like" data-action="like"><i class="icon-thumbs-up"></i></a></li>{{/hasLiked}}
						{{#hasLiked}}<li class="update-action update-action-unlike"><a href="#" rel="tooltip" title="Unlike" data-action="unlike"><i class="icon-thumbs-up"></i></a></li>{{/hasLiked}}
						<li class="update-action update-action-comment"><a href="#" rel="tooltip" title="Add a comment" data-action="like"><i class="icon-reply"></i></a></li>
						
						{{#isOwner}}<li class="update-action update-action-remove"><a href="#" rel="tooltip" title="Remove update" data-action="remove"><i class="icon-trash"></i></a></li>{{/isOwner}}
					</ul>
				</div>
			</div><!-- /.feed-item -->
		</script>
		
		<script type="text/html" id="menuTemplate">
			<div id="menu">
				<ul id="navigation">
					<li class="active"><a href="" rel="tooltip" title="News Feed" data-action="feed"><i class="icon-home icon-white"></i></a></li>
					<li><a href="" rel="tooltip" title="Profile" data-action="profile"><i class="icon-user icon-white" data-page="profile"></i></a></li>
					<li><a href="" rel="tooltip" title="Log out" data-action="logout"><i class="icon-off icon-white"></i></a></li>
				</ul>
			</div>
		</script>
		
		<script src="static/js/coffee.js"></script>
	</body>
</html>