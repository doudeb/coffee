var App = {
	Models: {},
	Collections: {},
	Views: {},
	
	baseUrl: 'http://localhost:8888/'
};

App.Models.Session = Backbone.Model.extend({
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
		
		this.trigger('started');
	},
	
	load: function () {
		this.set({
			userId: $.cookie('userId'),
			authToken: $.cookie('authToken'),
			siteName: $.cookie('siteName'),
			logoUrl: $.cookie('logoUrl'),
			backgroundUrl: $.cookie('backgroundUrl'),
			backgroundPos: $.cookie('backgroundPos')
		});
	},
	
	start: function () {
		var self = this;
		
		$.ajax({
			type: 'GET',
			url: App.baseUrl + 'services/api/rest/json',
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
					
					self.save();
				} else {
					/* Error */
				}
			}
		});
	}
});