(function($){
	
	var App = {
		models: {},
		collections: {},
		views: {},
		
		baseUrl: "/",
		resourceUrl: '/services/api/rest/json',
		removeAllViews: function (callback) {
			_.each(App.views, function(view){
				view.remove();
			});
			App.views = {};
		}
	};
	
	/* !Model: Session */
	var Session = Backbone.Model.extend({
		defaults: {
			authToken: null,
			userId: null,
			siteName: null,
			logoUrl: null,
			backgroundUrl: null,
			backgroundPos: null
		},
		
		initialize: function () {
			_.bindAll(this);
			
			this.load();
		},
		
		change: function () {
			var changedAttributes = this.changedAttributes();
		},
		
		authenticated: function () {
			return this.get('authToken') == null ? false : true;
		},
		
		save: function () {
			$.cookie('userId', this.get('userId'));
			$.cookie('authToken', this.get('authToken'));
			$.cookie('siteName', this.get('siteName'));
			$.cookie('logoUrl', this.get('logoUrl'));
			$.cookie('backgroundUrl', this.get('backgroundUrl'));
			$.cookie('backgroundPos', this.get('backgroundPos'));
			$.cookie('username', this.get('username'));
			$.cookie('name', this.get('name'));
			$.cookie('iconUrl', this.get('iconUrl'));
			$.cookie('coverUrl', this.get('coverUrl'));
			
			this.trigger('started');
		},
		
		load: function () {
			this.set({
				userId: $.cookie('userId'),
				authToken: $.cookie('authToken'),
				siteName: $.cookie('siteName'),
				logoUrl: $.cookie('logoUrl'),
				backgroundUrl: $.cookie('backgroundUrl'),
				backgroundPos: $.cookie('backgroundPos'),
				username: $.cookie('username'),
				name: $.cookie('name'),
				iconUrl: $.cookie('iconUrl'),
				coverUrl: $.cookie('coverUrl')
			});
		},
		
		start: function () {
			var self = this;
			
			$.ajax({
				type: 'GET',
				url: App.resourceUrl,
				dataType: 'json',
				data: {
					method: 'coffee.getSiteData',
					auth_token: self.get('authToken')
				},
				success: function (response) {
					if (response.status != -1) {
						var result = response.result;
						
						self.set({
							userId: result.user_guid,
							siteName: result.name,
							logoUrl: result.logo_url,
							backgroundUrl: result.background_url,
							backgroundPos: result.background_pos
						});
						
						$.ajax({
							type: 'GET',
							url: App.resourceUrl,
							dataType: 'json',
							data: {
								method: 'coffee.getUserData',
								auth_token: self.get('authToken'),
								guid: self.get('userId')
							},
							success: function (response) {
								if (response.status != -1) {
									var result = response.result;
									
									self.set({
										username: result.username,
										name: result.name,
										iconUrl: result.icon_url,
										coverUrl: result.cover_url
									});
									
									self.save();
								} else {
									/* Error */
								}
							}
						});
						
					} else {
						/* Error */
					}
				}
			});
		},
		
		end: function () {
			this.clear();
			
			$.cookie('userId', null);
			$.cookie('authToken', null);
			$.cookie('siteName', null);
			$.cookie('logoUrl', null);
			$.cookie('backgroundUrl', null);
			$.cookie('backgroundPos', null);
			$.cookie('username', null);
			$.cookie('name', null);
			$.cookie('iconUrl', null);
			$.cookie('coverUrl', null);
			
			Backbone.history.navigate('login', true);
		}
	});
	
	/* !View: LoginView */
	var LoginView = Backbone.View.extend({
		initialize: function () {
			_.bindAll(this);
			
			this.session = App.models.session;
			this.session.on('started', this.redirectToFeed);
			
			this.render();
		},
		
		events: {
			'click #doLogin': 'doLogin'
		},
		
		render: function () {
			var element = ich.loginTemplate();
			this.setElement(element);
			
			this.$el
				.prependTo('#container')
				.hide()
				.fadeIn(500);
			
			return this;
		},
		
		doLogin: function () {
			var self = this;
			
			var username = this.$el.find('#inputUsername').val();
			var password = this.$el.find('#inputPassword').val();
			
			$.ajax({
				type: 'POST',
				data: {
					username: username,
					password: password,
					method: 'auth.gettoken'
				},
				headers: {
					'Accept': 'application/json'
				},
				url: App.baseUrl + 'services/api/rest/json',
				complete: function (xhr, statusText) {
					var response = $.parseJSON(xhr.responseText);
					
					if (response.status != -1) {
						self.session.set({
							authToken: response.result
						});
						
						self.session.start();
					} else {
						/* login failed */
						alert('Invalid user or password');
					}
				}
			});
			
			return false;
		},
		
		redirectToFeed: function () {
			Backbone.history.navigate('feed', true);
		}
	});
	
	/* !Model: FeedItem */
	var FeedItem = Backbone.Model.extend({
		initialize: function () {
			_.bindAll(this);
		},
		
		set: function (attributes, options) {
			attributes.isOwner = (attributes.user.guid == App.models.session.get('userId')) ? true : false;
			attributes.hasLiked = false;
			
			attributes.likes.isOne = (attributes.likes.total == 1) ? true : false;
			attributes.likes.isTwo = (attributes.likes.total == 2) ? true : false;
			attributes.likes.isMore = (attributes.likes.total > 2) ? true : false;
			
			if (attributes.likes.users.length > 0) {
				attributes.likes.users[0].first = true;
				
				_.each(attributes.likes.users, function(like){
					if (like.owner_guid == App.models.session.get('userId')) attributes.hasLiked = true;
				});
			}
			
			if (attributes.comment.comments.length > 0) {
				attributes.comment.comments.reverse();
				attributes.comment.comments[0].showAllLink = (attributes.comment.total > attributes.comment.comments.length) ? true : false;
			}
			
			console.log(attributes);
			
			Backbone.Model.prototype.set.call(this, attributes, options);
		}
	});
	
	/* !Collection: FeedItemList */
	var FeedItemList = Backbone.Collection.extend({
		model: FeedItem,
		
		initialize: function () {
			_.bindAll(this);
			var self = this;
			
			$.ajax({
				type: 'GET',
				url: App.resourceUrl,
				dataType: 'json',
				data: {
					method: 'coffee.getPosts',
					auth_token: self.get('authToken')
				},
				success: function (response) {
					if (response.status != -1) {
						var result = response.result;
						self.add(result);
						
					} else {
						/* Error */
					}
				}
			});
		},
		
		loadNew: function (postGuid) {
			var self = this;
			
			$.ajax({
				type: 'GET',
				url: App.resourceUrl,
				dataType: 'json',
				data: {
					method: 'coffee.getPost',
					auth_token: self.get('authToken'),
					guid: postGuid
				},
				success: function (response) {
					if (response.status != -1) {
						var result = response.result;
						self.unshift(result[0]);
					} else {
						/* Error */
					}
				}
			});
		}
	});
	
	/* !View: FeedItemView */
	var FeedItemView = Backbone.View.extend({
		initialize: function () {
			_.bindAll(this);
		},
		
		events: {
			'click .show-all-link': 'showAllComments',
			'keyup .new-comment-textarea': 'textareaKeyup',
			'keypress .new-comment-textarea': 'textareaKeypress',
			'click .update-action a': 'updateAction'
		},
		
		render: function () {
			var element = ich.feedItemTemplate(this.model.toJSON());
			
			$(this.el).replaceWith(element);
			this.setElement(element);
			
			return this;
		},
		
		showAllComments: function () {
			var self = this;
			
			$.ajax({
				type: 'GET',
				headers: {
					'Accept': 'application/json'
				},
				url: 'api/feed/comments.json',
				complete: function (xhr, statusText) {
					var response = $.parseJSON(xhr.responseText);
					
					var comments = self.model.get('comments');
					comments.items = response.comments;
					
					self.model.set({'comments': comments});
					self.render();
				}
			});
		},
		
		textareaKeyup: function (e) {
			if (e.keyCode == 13) { // Enter key
				var theComment = $(e.currentTarget).val();
				this.comment(theComment);
			}
		},
		
		textareaKeypress: function (e) {
			if (e.keyCode == 13) { // Enter key
				return false;
			}
		},
		
		updateAction: function (e) {
			var self = this;
			var action = $(e.currentTarget).attr('data-action');
			
			if (action == 'like') {
				self.like();
			} else if(action == 'unlike') {
				self.unlike();
			}
			
			return false;
		},
		
		like: function () {
			var self = this;
			var postGuid = self.model.get('guid');
			
			$.ajax({
				type: 'GET',
				url: App.resourceUrl,
				dataType: 'json',
				data: {
					method: 'coffee.setRelationship',
					auth_token: App.models.session.get('authToken'),
					guid_parent: App.models.session.get('userId'),
					guid_children: postGuid,
					type: 'coffee_like'
				},
				success: function (response) {
					if (response.status != -1) {
						self.refresh();
					} else {
						/* Error */
					}
					
				}
			});
		},
		
		unlike: function () {
			alert('unlike!')
		},
		
		comment: function (theComment) {
			var self = this;
			var postGuid = this.model.get('guid')
			
			$.ajax({
				type: 'GET',
				url: App.resourceUrl,
				dataType: 'json',
				data: {
					method: 'coffee.comment',
					auth_token: App.models.session.get('authToken'),
					guid: postGuid,
					comment: theComment
				},
				success: function (response) {
					console.log(response);
					if (response.status != -1) {
						self.refresh();
					} else {
						// Error
					}
				}
			});
		},
		
		refresh: function () {
			var self = this;
			
			$.ajax({
				type: 'GET',
				url: App.resourceUrl,
				dataType: 'json',
				data: {
					method: 'coffee.getPost',
					auth_token: App.models.session.get('authToken'),
					guid: self.model.get('guid')
				},
				success: function (response) {
					if (response.status != -1) {
						var result = response.result;
						self.model.set(result[0]);
						self.render();
					} else {
						/* Error */
					}
				}
			});
		}
	});
	
	/* !View: FeedItemsView */
	var FeedItemsView = Backbone.View.extend({
		tagName: 'div',
		
		initialize: function () {
			_.bindAll(this);
			
			this.$el.attr('id', 'feed-items');
			
			this.collection = new FeedItemList();
			this.collection.bind('add', this.addItem);
			
			this.render();
		},
		
		render: function () {
			var self = this;
			
			_(this.collection.models).each(function(feedItem) {
				self.appendItem(feedItem);
			}, this);
		},
		
		addItem: function (item) {
			var self = this;
			
			var feedItemView = new FeedItemView({
				model: item
			});
			
			var element = $(feedItemView.render().el);
			
			if (self.collection.indexOf(item) == 0) {
				element
					.prependTo(self.$el)
					.hide()
					.fadeIn(500);
			} else {
				self.$el.append(feedItemView.render().el);
			}
		},
		addNew: function (postGuid) {
			this.collection.loadNew(postGuid);
		}
	});
	
	/* !View: Microblogging */
	var MicrobloggingView = Backbone.View.extend({
		initialize: function () {
			_.bindAll(this);
			
			this.isSending = false;
			this.updateLength = 0;
			this.attachmentGuid = false;
			this.isAttaching = false;
			
			this.render();
		},
		
		events: {
			'click #postUpdate': 'postUpdate',
			'keyup .update-text': 'listenForLink',
			'click .attachment .remove': 'removeAttachment'
		},
		
		render: function () {
			var element = ich.microbloggingTemplate();
			this.setElement(element);
			
			this.$el.prependTo('#container');
			
			this.feedItemsView = new FeedItemsView();
			this.$el.append(this.feedItemsView.el);
			
			return this;
		},
		
		disable: function () {
			this.$el.find('.update-text').eq(0).attr('disabled', 'disabled');
			this.$el.find('#postUpdate').addClass('disabled');
		},
		
		enable: function () {
			this.$el.find('.update-text').eq(0).removeAttr('disabled');
			this.$el.find('#postUpdate').removeClass('disabled');
		},
		
		postUpdate: function () {
			var self = this;
			
			if (! this.isSending) {
				var updateText = self.$el.find('.update-text').eq(0).val();
				
				if (! self.attachmentGuid && updateText.length == 0) {
					alert('No update!');
				} else {
					self.disable();
					self.isSending = true;
					self.$el.addClass('microblogging-loading');
					
					var data = {
						method: 'coffee.createNewPost',
						auth_token: App.models.session.get('authToken'),
						post: updateText
					};
					
					if (self.attachmentGuid != false) data.attachment = [self.attachmentGuid];
					
					$.ajax({
						type: 'GET',
						url: App.resourceUrl,
						dataType: 'json',
						data: data,
						success: function (response) {
							self.enable();
							self.isSending = false;
							self.$el.removeClass('microblogging-loading');
							
							if (response.status != -1) {
								var postGuid = response.result.guid;
								self.clear();
								self.feedItemsView.addNew(postGuid);
							} else {
								alert('There was an error posting the update.');
							}
						}
					});
				}
			}
			
			return false;
		},
		
		listenForLink: function (e) {
			var self = this;
			var textarea = $(e.currentTarget);
			var value = textarea.val();
			
			if (value.length > (self.updateLength + 7)) {
				if (self.isUrl(value)) {
					var theUrl = value.substr(self.updateLength);
					self.attachLink(theUrl);
				}
			} else {
				if (e.keyCode == 32) {
					if (self.isUrl(value)) {
						var splitValue = value.split(' ');
						var theUrl = splitValue[splitValue.length - 2];
						self.attachLink(theUrl);
					}
				}
			}
			
			self.updateLength = value.length;
		},
		
		isUrl: function (s) {
			var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
			return regexp.test(s);
		},
		
		attachLink: function (url) {
			var self = this;
			
			if (! self.attachmentGuid && ! self.isAttaching) {
				self.$el.addClass('microblogging-loading');
				self.isAttaching = true;
				
				$.ajax({
					type: 'GET',
					url: App.resourceUrl,
					dataType: 'json',
					data: {
						method: 'coffee.getUrlData',
						auth_token: App.models.session.get('authToken'),
						url: url
					},
					success: function (response) {
						if (response.status != -1) {
							var result = response.result;
							self.attachmentGuid = result.guid;
							console.log(self.attachmentGuid);
							self.attachmentElement = ich.microbloggingAttachmentTemplate(result);
							self.attachmentElement
								.insertBefore(self.$el.find('.update-actions').eq(0));
						} else {
							/* Error */
						}
						self.isAttaching = false;
						self.$el.removeClass('microblogging-loading');
					}
				});
			}
		},
		
		removeAttachment: function () {
			var self = this;
			
			if (self.attachmentGuid != false) {
				self.attachmentElement.remove();
				self.attachmentGuid = false;
			}
			
			return false;
		},
		
		clear: function () {
			this.removeAttachment();
			this.$el.find('.update-text').eq(0).val('');
		}
	});
	
	/* !View: Menu */
	var MenuView = Backbone.View.extend({
		initialize: function () {
			_.bindAll(this);
			this.render();
		},
		
		events: {
			'click a': 'handleClick'
		},
		
		render: function () {
			var element = ich.menuTemplate();
			this.setElement(element);
			
			this.$el.prependTo('#container');
			
			return this;
		},
		
		handleClick: function (e) {
			var target = $(e.currentTarget);
			var action = target.attr('data-action');
			
			if (action == 'logout') {
				App.models.session.end();
			}
			
			return false;
		}
	});
	
	
	/* !Router: WorkspaceRouter */
	var WorkspaceRouter = Backbone.Router.extend({
		routes: {
			"login":				"login",
			"feed":					"feed",
			"profile":				"myProfile",
			"profile/:user_id":		"profile"
		},
		
		login: function () {
			App.removeAllViews();
			if (! App.models.session.authenticated()) {
				App.views.loginView = new LoginView();
			} else {
				Backbone.history.navigate('feed', true);
			}
		},
		
		feed: function () {
			App.removeAllViews();
			if (App.models.session.authenticated()) {
				App.views.microbloggingView = new MicrobloggingView();
				App.views.menuView = new MenuView();
			} else {
				Backbone.history.navigate('login', true);
			}
		},
		
		myProfile: function () {
			console.log('my profile');
		},
		
		profile: function (userId) {
			App.removeAllViews();
			if (App.models.session.authenticated()) {
				
				App.views.menuView = new MenuView();
			} else {
				Backbone.history.navigate('login', true);
			}
		} 
	});
	
	
	$(document).ready(function(){
		App.models.session = new Session();
		
		new WorkspaceRouter();
		Backbone.history.start();
		
		if (window.location.hash == "") {
			Backbone.history.navigate('feed', true);
		}
		
		//var feedItemsView = new FeedItemsView();
		//var menuView = new App.Views.Menu();
	});

})(jQuery);