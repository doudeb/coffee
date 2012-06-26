(function($){
	
	/* !Model: FeedItem */
	var FeedItem = Backbone.Model.extend({
		initialize: function () {
			_.bindAll(this);
		},
		
		set: function (attributes, options) {
			
			if (attributes.hasOwnProperty('likes')) {
				attributes.likes.isOne = (attributes.likes.total == 1) ? true : false;
				attributes.likes.isTwo = (attributes.likes.total == 2) ? true : false;
				attributes.likes.isMore = (attributes.likes.total > 2) ? true : false;
				attributes.likes.users[0].first = true;
			}
			
			if (attributes.hasOwnProperty('comments')) {
				attributes.comments.items[0].showAllLink = (attributes.comments.total > attributes.comments.items.length) ? true : false;
			}
			
			Backbone.Model.prototype.set.call(this, attributes, options);
		}
	});
	
	/* !Collection: FeedItemList */
	var FeedItemList = Backbone.Collection.extend({
		model: FeedItem,
		
		initialize: function () {
			_.bindAll(this);
			var self = this;
			
			// Load the feed items
			$.ajax({
				type: 'GET',
				headers: {
					'Accept': 'application/json'
				},
				url: 'api/feed/updates.json',
				complete: function (xhr, statusText) {
					if (xhr.status == 200) {
						var response = $.parseJSON(xhr.responseText);
						self.add(response.updates);
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
			'click .show-all-link': 'showAllComments'
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
		
		testing: function () {
			console.log('hi');
		}
	});
	
	/* !View: FeedItemsView */
	var FeedItemsView = Backbone.View.extend({
		el: $('#feed-items'),
		
		initialize: function () {
			_.bindAll(this);
			
			this.collection = new FeedItemList();
			this.collection.bind('add', this.appendItem);
			
			this.render();
		},
		
		render: function () {
			var self = this;
			
			_(this.collection.models).each(function(feedItem) {
				self.appendItem(feedItem);
			}, this);
		},
		
		appendItem: function (item) {
			var feedItemView = new FeedItemView({
				model: item
			});
			
			this.$el.append(feedItemView.render().el);
		}
	});
	
	
	$(document).ready(function(){
		var session = new App.Models.Session();
		
		if (! session.authenticated()) {
			console.log('not authed');
			window.location.href = 'index.html';
		}
		
		var feedItemsView = new FeedItemsView();
		var menuView = new App.Views.Menu();
	});

})(jQuery);