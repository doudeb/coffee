(function($){
	
	/* !View: LoginView */
	var LoginView = Backbone.View.extend({
		initialize: function () {
			_.bindAll(this);
			
			this.session = this.options.session;
			this.session.on('started', this.redirectToFeed);
			
			this.render();
		},
		
		events: {
			'click #doLogin': 'doLogin'
		},
		
		render: function () {
			var element = ich.loginScreen();
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
			window.location.href = 'feed.html';
		}
	});
	
	
	$(document).ready(function(){
		var session = new App.Models.Session();
		
		if (session.authenticated()) {
			window.location.href = 'feed.html'
		} else {
			var loginView = new LoginView({
				session: session
			});
		}
	});

})(jQuery);