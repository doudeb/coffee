App.Views.Menu = Backbone.View.extend({
	el: '#menu',
	
	initialize: function () {
		_.bindAll(this);
		
		console.log('hello');
	},
	
	events: {
		'click a': 'handleClick'
	},
	
	handleClick: function () {
		console.log('hi');
		return false;
	}
});