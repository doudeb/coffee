(function($){
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i) ? true : false;
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i) ? true : false;
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPod/i) ? true : false;
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i) ? true : false;
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());
        }
    };

    /* Vars */
    var translations = null;

    /* Tools */
    var stripslashes = function (str) {
        str=str.replace(/\\'/g,'\'');
        str=str.replace(/\\"/g,'"');
        str=str.replace(/\\0/g,'\0');
        str=str.replace(/\\\\/g,'\\');
        return str;
    };

    var capitaliseFirstLetter = function (str)	{
        return str.charAt(0).toUpperCase() + str.slice(1);
    };

    var nl2br = function (str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
    }

    var setBackground = function (backgroundUrl) {
        if (backgroundUrl && backgroundUrl.length > 0) {
            $('body')
            .css('background','url(' + backgroundUrl + ')')
            .css('background-repeat','no-repeat')
            .css('background-attachment','fixed')
            .css('-moz-background-size','100%')
            .css('background-size','100%');
        } else {
            $('body').css('background','url(userpics/client_bg.jpeg)');
        }
    }

    var setLogo = function (logoUrl) {
        $('.watermark').hide();
        if (logoUrl && logoUrl.length > 0) {
            $('#watermark').attr('src',logoUrl);
        } else {
            $('#watermark').attr('src','url(userpics/logo.png)');
        }
        $('.watermark').show();
    }

    var t = function (key) {
        if (translations === null) translations = $.parseJSON(localStorage.getItem('translations'));
        if (translations[key]) {
            return translations[key];
        }
        return key;
    }

    $.print = function( message, insertType ) {
        insertType = insertType || "append";
        if ( typeof(message) == "object" ) {
          var string = "{<br>",
              values = [],
              counter = 0;
          $.each( message, function( key, value ) {
            if ( value && value.nodeName ) {
              var domnode = "&lt;" + value.nodeName.toLowerCase();
              domnode += value.className ? " class='" + value.className + "'" : "";
              domnode += value.id ? " id='" + value.id + "'" : "";
              domnode += "&gt;";
              value = domnode;
            }
            values[counter++] = key + ": " + value;
          });
          string += values.join( ",<br>" );
          string += "<br>}";
          message = string;
        }

        var $output = $( "#print-output" );

        if ( !$output.length ) {
          $output = $( "<div id='print-output' />" ).prependTo( "body" );
        }

        var newMsg = $('<div />', {
          "class": "print-output-line",
          html: message
        });

        $output[insertType]( newMsg );
      };

    var App = {
        models: {},
        collections: {},
        views: {},

        baseUrl: "/",
        resourceUrl: "/services/api/rest/json",

        isComposing: false,

        removeAllViews: function () {
            _.each(App.views, function(view){
                view.remove();
            });
            App.views = {};
        },

        initMention : function (elm) {
            $(elm).mentionsInput({
                elastic : true
                , onDataRequest:function (mode, query, callback) {
                    $.ajax({
                        type: 'GET',
                        url: App.resourceUrl,
                        dataType: 'json',
                        data: {
                            method: 'coffee.getUserList'
                            , auth_token: App.models.session.get('authToken')
                            , username: query
                        },
                        success: function (response) {
                            if (response.status != -1) {
                                responseData = response.result;
                                //responseData = _.filter(responseData, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });
                                callback.call(this, responseData);
                            }
                        }
                    });
                }
            });
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
            customCss: null,
            translations: null,
            isAdmin: null,
            accountTime: null,
            loginCount: null
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
            localStorage.setItem('userId', this.get('userId'));
            localStorage.setItem('authToken', this.get('authToken'));
            localStorage.setItem('siteName', this.get('siteName'));
            localStorage.setItem('logoUrl', this.get('logoUrl'));
            localStorage.setItem('backgroundUrl', this.get('backgroundUrl'));
            localStorage.setItem('customCss', this.get('customCss'));
            localStorage.setItem('name', this.get('name'));
            localStorage.setItem('iconUrl', this.get('iconUrl'));
            localStorage.setItem('coverUrl', this.get('coverUrl'));
            localStorage.setItem('translations', this.get('translations'));
            localStorage.setItem('isAdmin', this.get('isAdmin'));
            localStorage.setItem('accountTime', this.get('accountTime'));
            localStorage.setItem('loginCount', this.get('loginCount'));

            this.trigger('started');
        },

        load: function () {
            this.set({
                userId: localStorage.getItem('userId'),
                authToken: localStorage.getItem('authToken'),
                siteName: localStorage.getItem('siteName'),
                logoUrl: localStorage.getItem('logoUrl'),
                backgroundUrl: localStorage.getItem('backgroundUrl'),
                customCss: localStorage.getItem('custom_css'),
                name: localStorage.getItem('name'),
                iconUrl: localStorage.getItem('iconUrl'),
                coverUrl: localStorage.getItem('coverUrl'),
                translations: localStorage.getItem('translations'),
                isAdmin: localStorage.getItem('isAdmin'),
                accountTime: localStorage.getItem('accountTime'),
                loginCount: localStorage.getItem('loginCount')
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
                            customCss: result.custom_css,
                            translations: result.translations,
                            isAdmin: result.is_admin==='true'?true:''
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
                                        name: result.name,
                                        iconUrl: result.icon_url,
                                        coverUrl: result.cover_url,
                                        accountTime: result.created,
                                        loginCount: result.login_count
                                    });

                                    self.save();
                                } else if (response.message == 'pam_auth_userpass:failed') {
                                    localStorage.clear();
                                    App.models.session.end();
                                    Backbone.history.navigate('login', true);
                                } else {
                                    Backbone.history.navigate('feed', true);
                                }
                            }
                        });

                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }
                }
            });
        },

        end: function () {
            this.clear();

            localStorage.removeItem('userId');
            localStorage.removeItem('authToken');
            localStorage.removeItem('siteName');
            localStorage.removeItem('logoUrl');
            localStorage.removeItem('backgroundUrl');
            localStorage.removeItem('backgroundPos');
            localStorage.removeItem('name');
            localStorage.removeItem('iconUrl');
            localStorage.removeItem('coverUrl');
            localStorage.removeItem('isAdmin');
            localStorage.removeItem('translations');
            localStorage.removeItem('accountTime');
            localStorage.removeItem('loginCount');

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
            if(isMobile.any()){
                var element = ich.mobileLoginTemplate();
            } else {
                var element = ich.loginTemplate();
            }
            //      var element = ich.mobileLoginTemplate();
            this.setElement(element);

            this.$el
            .prependTo('#container')
            .hide()
            .fadeIn(500);

            return this;
        },

        doLogin: function () {
            var self = this;

            var email = this.$el.find('#inputEmail').val();
            var password = this.$el.find('#inputPassword').val();

            $.ajax({
                type: 'POST',
                data: {
                    email: email,
                    password: password,
                    method: 'coffee.getTokenByEmail'
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
            loginCount = parseInt(this.session.get('loginCount'));
            if (loginCount <= 5) {
                Backbone.history.navigate('welcome', true);
            } else {
                Backbone.history.navigate('feed', true);
            }
        }
    });

    /* !Model: UserItem */
    var UserItem = Backbone.Model.extend({
        initialize: function () {
            _.bindAll(this);
        }
    });

     /* !View: UserItemView */
    var UserItemView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);
            this.model.bind('change', this.render);
            this.model.bind('remove', this.remove);
            //this.render();
        },

        render: function () {
            data = this.model.toJSON();
            console.log(data);

            element = ich.userVcardTemplate(data);
            $(this.el).replaceWith(element);
            this.setElement(element);
            return this;
        },

        events: {
            'click .update-action a': 'updateAction'
        },

        updateAction: function (e) {
            var self = this;
            var action = $(e.currentTarget).attr('data-action');

            switch (action) {
                case 'remove' :
                    self.removeUser();
                    break;
                default:
                    return false;
                    break;
            }
            return false;
        },

        removeUser: function () {
            var self = this;
            var guid = self.model.get('id');
            $.ajax({
                type: 'POST',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.banUser',
                    auth_token: App.models.session.get('authToken'),
                    guid: guid
                },
                success: function (response) {
                    if (response.status != -1) {
                        self.remove();
                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }

                }
            });
        }
    });

   /* !Collection: UserItemList */
    var UserItemList = Backbone.Collection.extend({
        model: UserItem,

        initialize: function () {
            _.bindAll(this);
            this.loadUser();

        },

        loadUser: function (username,offset,limit) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.getUserList'
                    , auth_token: App.models.session.get('authToken')
                    , username: username?username:''
                    , offset: offset?offset:0
                    , limit: limit?limit:10
                },
                success: function (response) {
                    if (response.status != -1) {
                        var result = response.result;
                        self.add(result);
                        self.trigger('userReady');
                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    }
                }
            });
        },

        removeList: function () {
            var self = this;
            _.each(this.models, function (item, key) {
                console.log(item);
                //item.remove();
                self.remove();
            });
        }
    });

    /* !View: UserListView */
    var UserListView = Backbone.View.extend({
        tagName: 'div',
        initialize: function () {
            _.bindAll(this);
            this.$el
                .attr('id', 'user-list')
                .attr('class', 'user-list');

            this.collection = new UserItemList();
            this.collection.bind('add', this.addUser);
            this.collection.bind('userReady', this.render)
        },

        render: function () {
            var self = this;
            _(this.collection.models).each(function(userItem) {
                //self.appendItem(userItem);
            }, this);
        },

        addUser: function (item) {
            var self = this;
            var userItemView = new UserItemView ({
                model: item
            });

            var element = $(userItemView.render().el);
            if (self.collection.indexOf(item) == 0) {
                element
                .prependTo(self.$el)
                .hide()
                .fadeIn(500);
            } else {
                self.$el.append(element);
            }
            self.$el.appendTo('#manageUser');
        }
    });

    /* !Model: FeedItem */
    var FeedItem = Backbone.Model.extend({
        initialize: function () {
            _.bindAll(this);
            isComposing = false;
        },

        set: function (attributes, options) {
            attributes.isOwner = (attributes.user.guid == App.models.session.get('userId')) || (App.models.session.get('isAdmin')) ? true : false;

            attributes.isLong =  (attributes.content.text.length > 500) ? true : false;
            if (attributes.isLong) {
                attributes.content.textOrig = attributes.content.text;
                attributes.content.text = attributes.content.text.substr(0, 500);
            }

            attributes.hasLiked = false;

            attributes.likes.isOne = (attributes.likes.total == 1) ? true : false;
            attributes.likes.isTwo = (attributes.likes.total == 2) ? true : false;
            if (attributes.likes.total > 2) {
                attributes.likes.isMore = true;
                attributes.likes.totaltoDisplay = attributes.likes.total - 1;
            }

            if (attributes.likes.users != false) {
                attributes.likes.users[0].isFirst = true;
                attributes.likes.others = [];
                _.each(attributes.likes.users, function(like,key) {
                    if (like.owner_guid == App.models.session.get('userId')) attributes.hasLiked = true;
                    if (key >= 1 && attributes.likes.isMore) {
                        attributes.likes.others[key] = like;
                        attributes.likes.users[key].isFirst = false;
                    }
                });
            }
            if (attributes.comment.total > 0) {
                attributes.comment.comments.reverse();
                attributes.comment.showAllLink = (attributes.comment.total > attributes.comment.comments.length) ? true : false;
                for (i=0; i < attributes.comment.total; i++) {
                    if (typeof attributes.comment.comments[i] != 'undefined') {
                        if (typeof attributes.comment.comments[i].mentioned != 'undefined' && attributes.comment.comments[i].mentioned.length>0) {
                            attributes.comment.comments[i].text = this.replaceMentionedUsers(attributes.comment.comments[i].text,attributes.comment.comments[i].mentioned);
                        }
                        if (attributes.comment.comments[i].owner_guid == App.models.session.get('userId') || (App.models.session.get('isAdmin') == 'true')) attributes.comment.comments[i].isCommentOwner = true;
                    }
                }
            }

            if (attributes.mentioned.length>0) {
                attributes.content.text = this.replaceMentionedUsers(attributes.content.text,attributes.mentioned);
            }

            attributes.isBroadCastMessage = (attributes.content.type === 'coffee_broadcast_message') ? true : false;
            Backbone.Model.prototype.set.call(this, attributes, options);
        },

        replaceMentionedUsers : function (text,mentionedUsers) {
            _.each(mentionedUsers, function (user){
                replacement = ich.mentionUserTemplate(user,true);
                text = text.replace(user.name,replacement);
            });
            return text;
        }
    });

    /* !Collection: FeedItemList */
    var FeedItemList = Backbone.Collection.extend({
        model: FeedItem,

        initialize: function () {
            _.bindAll(this);
            this.loadFeed();

        },

        loadFeed: function (offset,limit) {
            var self = this;
            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.getPosts'
                    , auth_token: App.models.session.get('authToken')
                    , offset: offset?offset:0
                    , limit: limit?limit:10
                },
                success: function (response) {
                    if (response.status != -1) {
                        var result = response.result;
                        self.add(result);
                        self.startCheckingForNewPosts();
                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
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
                    auth_token: App.models.session.get('authToken'),
                    guid: postGuid
                },
                success: function (response) {
                    if (response.status != -1) {
                        var result = response.result;
                        self.unshift(result[0]);
                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }
                }
            });
        },

        startCheckingForNewPosts: function () {
            setTimeout(this.checkForNewPosts, 5000);
        },

        checkForNewPosts: function () {
            var self = this;
            var latestTimestamp = _.max(self.models, function(latest){
                return latest.attributes.content.time_updated;
            }).attributes.content.time_updated;
            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.getPosts',
                    auth_token: App.models.session.get('authToken'),
                    newer_than: latestTimestamp
                },
                success: function (response) {
                    if (response.status != -1) {
                        var result = response.result;
                        if (result.length > 0) {
                            for (i = 0; i < result.length; i++ ) {
                                exist = self.where({
                                    guid: result[i].guid
                                });
                                if (exist.length > 0) {
                                    if (!exist[0].get('isComposing')) {
                                        exist[0].set(result[i]);
                                    }
                                } else {
                                    self.loadNew(result[i].guid);
                                }
                            }
                        }
                        if (Backbone.history.fragment === 'feed') {
                            self.startCheckingForNewPosts();
                        }
                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }
                }
            });
        }
    });

    /* !View: FeedItemView */
    var FeedItemView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);
            this.model.bind('change', this.render);
        },

        events: {
            'click .show-all-link': 'showAllComments',
            'keydown .new-comment-textarea': 'textareaKeydown',
            'keypress .new-comment-textarea': 'textareaKeypress',
            'click .update-action a': 'updateAction',
            'focus .new-comment-textarea': 'changeComposingFlag',
            'blur .new-comment-textarea': 'changeComposingFlag',
            'click .remove-comment': 'removeComment',
            'click .show-all-text': 'toggleAllText',
            'click .update-action-comment-mobile a': 'showMobileCommentForm'
        },

        render: function () {
            data = this.model.toJSON();
            data.translate = function() {
                return function(text) {
                    return t(text);
                }
            };

            if(isMobile.any()){
                var element = ich.mobileFeedItemTemplate(data);
            } else {
                var element = ich.feedItemTemplate(data);
            }
            //      var element = ich.mobileFeedItemTemplate(data);

            $(this.el).replaceWith(element);
            this.setElement(element);
            App.initMention(element.find('textarea.mention'));
            return this;
        },

        showAllComments: function () {
            var self = this;

            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.getComments',
                    auth_token: App.models.session.get('authToken'),
                    guid: self.model.get('guid'),
                    offset: 0,
                    limit: self.model.get('comment').total
                },
                success: function (response) {
                    if (response.status != -1) {
                        var update = self.model.toJSON();

                        update.comment = response.result;
                        self.model.set(update);
                    }
                    if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }
                }
            });
        },

        textareaKeydown: function (e) {
            if (e.keyCode == 13) { // Enter key
                var comment = $(e.currentTarget).val();
                this.comment(comment);
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

            switch (action) {
                case 'like' :
                    self.like();
                    break;
                case 'unlike' :
                    self.unlike();
                    break;
                case 'remove' :
                    self.removeFeed();
                    break;
                case 'comment':
                    elm = $(e.currentTarget).parent().parent().parent().find('.new-comment-textarea');
                    elm.focus();
                    break;
                default:
                    return false;
                    break;
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
                    }
                    if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }

                }
            });
        },

        unlike: function () {
            var self = this;
            var postGuid = self.model.get('guid');

            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.removeRelationship',
                    auth_token: App.models.session.get('authToken'),
                    guid_parent: App.models.session.get('userId'),
                    guid_children: postGuid,
                    type: 'coffee_like'
                },
                success: function (response) {
                    if (response.status != -1) {
                        self.refresh();
                    }
                    if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }

                }
            });
        },

        removeFeed: function () {
            var self = this;
            var postGuid = self.model.get('guid');
            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.disableObject',
                    auth_token: App.models.session.get('authToken'),
                    guid: postGuid
                },
                success: function (response) {
                    if (response.status != -1) {
                        self.remove();
                    }
                    if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }

                }
            });
        },

        removeComment: function (e) {
            var self = this
            , elm = $(e.currentTarget);
            id = elm.data('id');
            $.ajax({
                type: 'POST',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.disableAnnotation',
                    auth_token: App.models.session.get('authToken'),
                    id: id
                },
                success: function (response) {
                    if (response.status != -1) {
                        self.refresh();
                    }
                    if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }

                }
            });
        },

        comment: function (comment) {
            var self = this;
            var postGuid = self.model.get('guid');
            var mentionedUser = self.getMentions();

            $.ajax({
                type: 'POST',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.comment'
                    , auth_token: App.models.session.get('authToken')
                    , guid: postGuid
                    , comment: comment
                    , mentionedUser : mentionedUser
                },
                success: function (response) {
                    if (response.status != -1) {
                        var update = self.model.toJSON();

                        if (!update.comment.comments) update.comment.comments = [];
                        update.comment.comments.reverse();

                        update.comment.comments.unshift({
                            friendly_time: "Just now",
                            icon_url: App.models.session.get('iconUrl'),
                            name: App.models.session.get('name'),
                            owner_guid: App.models.session.get('userId'),
                            text: comment,
                            time_created: new Date().getTime()
                        });

                        self.model.set(update);
                        self.model.attributes.isComposing = false;
                        self.render();
                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
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
                        if (result.length > 0) {
                            self.model.set(result[0]);
                            self.render();
                        } else {
                            self.remove();
                        }
                    } else {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    }
                }
            });
        },

        changeComposingFlag: function (e) {
            var self = this;
            self.model.attributes.isComposing = e.type==='focusin'?true:false;
        },

        toggleAllText: function (e) {
            var elm = $(e.currentTarget);
            elm.parent().find('p.text').toggle()
            .parent().find('p.text-orig').fadeToggle()
            .parent().find('a.hide').toggle();
            elm.addClass('hide');
            return false;
        },

        showMobileCommentForm: function(e) {
            localStorage.setItem('postId', $(e.currentTarget).parents('.feed-item').attr('data-guid'));
            Backbone.history.navigate('mobileComment', true);
        },

        getMentions: function () {
            var self = this
                , mentionedUsers = new Array()
                , elm = $(self.el).find('textarea.mention');
            elm
                .mentionsInput('getMentions', function(data) {
                    _.each(data, function(user, key){
                        mentionedUsers[key] = user.id;
                    });
                })
                .mentionsInput('reset')
                .css('height','35px');
            return mentionedUsers;
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
        refreshItem: function (item) {

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
            this.offset = 0;
            this.attachmentGuid = false;
            this.isAttaching = false;
            this.isBroadCastMessage = false;

            this.render();
        },

        events: {
            'click #postUpdate': 'postUpdate',
            'keyup .update-text': 'listenForLink',
            'click .attachment .remove': 'removeAttachment',
            'click .add-media': 'uploadMedia',
            'click .broadcastMessage': 'toggleBroadcastMessage',
            'click #cancelPostUpdate': 'hideMobileCommentForm'
        },

        render: function () {
            data = {
                icon_url: App.models.session.get('iconUrl')
                , isAdmin: App.models.session.get('isAdmin')
                , translate: function() {return function(text) {return t(text);}}
            };

            if(isMobile.any()){
                var element = ich.mobileMicrobloggingTemplate(data);
            } else {
                var element = ich.microbloggingTemplate(data);
            }
            this.setElement(element);

            this.$el.prependTo('#container');

            this.feedItemsView = new FeedItemsView();
            this.$el.append(this.feedItemsView.el);
            App.initMention(element.find('textarea.update-text'));
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
            if (! self.isSending && ! self.isAttaching) {
                var updateText = self.$el.find('.update-text').eq(0).val()
                    , mentionedUser = self.getMentions();
                if (! self.attachmentGuid && updateText.length == 0) {
                    alert('No update!');
                } else {
                    self.disable();
                    self.isSending = true;
                    self.$el.addClass('microblogging-loading');

                    var data = {
                        method: 'coffee.createNewPost',
                        auth_token: App.models.session.get('authToken'),
                        post: updateText,
                        type: this.isBroadCastMessage?'coffee_broadcast_message':'',
                        mentionedUser: mentionedUser
                    };
                    if (self.attachmentGuid != false) data.attachment = [self.attachmentGuid];

                    $.ajax({
                        type: 'POST',
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

                                if(isMobile.any()){
                                    self.hideMobileCommentForm();
                                }
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
                    textarea.val(value.replace(theUrl, ''));
                }
            } else {
                if (e.keyCode == 32) {
                    if (self.isUrl(value)) {
                        var splitValue = value.split(' ');
                        var theUrl = splitValue[splitValue.length - 2];
                        self.attachLink(theUrl);
                        textarea.val(value.replace(theUrl, ''));
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
        },

        uploadMedia: function () {
            var self = this
            , upload = $('#upload')
            , uploadForm = $('#uploadForm')
            , percent = $('.percent');

            upload
            .trigger('click')
            .change(function (){
                //$('#uploadSpinner').modal('show');
                self.$el.addClass('microblogging-loading');
                self.isAttaching = true;
                elm = $(this);
                var options = {
                    success:       self.updateAttachement
                    /*, uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        percent.html(percentVal);
                    }*/
                    , url: App.resourceUrl
                    , dataType: 'json'
                    , data: {
                        method: 'coffee.uploadData'
                        , auth_token: App.models.session.get('authToken')
                    }
                };
                uploadForm.ajaxSubmit(options);
            });
        },

        updateAttachement: function (response) {
            var self = this
            ,percent = $('.percent');
            if (response.status != -1) {
                var result = response.result;
                self.attachmentGuid = result.guid;
                self.attachmentElement = ich.microbloggingAttachmentTemplate(result);
                self.attachmentElement
                .insertBefore(self.$el.find('.update-actions').eq(0));
                self.$el.removeClass('microblogging-loading');
                self.isAttaching = false;
                $('#uploadSpinner').modal('hide');
                percent.html('0%');
            } else {
            /* Error */
            }
        },

        toggleBroadcastMessage : function (e) {
            var self = this,
            elm = $(e.currentTarget);
            elm.toggleClass('on');
            this.isBroadCastMessage = elm.hasClass('on');
            return false;
        },

        hideMobileCommentForm : function (e) {
            $('#profile').hide();
            $('#feed-items').show();
            $('#microblogging').hide();
            $('#microblogging .update-text').val('');
        },

        initMention : function () {
            alert("pouet");
            $('textarea.mention').mentionsInput({
                onDataRequest:function (mode, query, callback) {
                    $.ajax({
                        type: 'GET',
                        url: App.resourceUrl,
                        dataType: 'json',
                        data: {
                            method: 'coffee.getUserList',
                            auth_token: App.models.session.get('authToken')
                        },
                        success: function (response) {
                            if (response.status != -1) {
                                responseData = response.result;
                                //responseData = _.filter(responseData, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });
                                callback.call(this, responseData);
                            }
                        }
                    });
                }
            });
        },

        getMentions: function () {
            var mentionedUsers = new Array();
            $('#microblogging .update-text')
                .mentionsInput('getMentions', function(data) {
                    _.each(data, function(user, key){
                        mentionedUsers[key] = user.id;
                    });
                })
                .mentionsInput('reset')
                .css('height','50px');
            return mentionedUsers;
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
            data = {
                translate : function() {
                    return function(text) {
                        return t(text);
                    }
                }
                , displayWelcome : parseInt(App.models.session.get('accountTime')) + (60 * 60 * 24 * 30) > Math.round(new Date().getTime()) / 1000
                , isAdmin : App.models.session.get('isAdmin')
            };

            if(isMobile.any()){
                var element = ich.mobileMenuTemplate(data);
            } else {
                var element = ich.menuTemplate(data);
            }
            //      var element = ich.mobileMenuTemplate(data);
            this.setElement(element);

            this.$el.prependTo('#container');
            var navigationItems = $('#navigation').find('li');
            $.each(navigationItems, function (key,item) {
                var current = $(item).find('a').attr('data-action');
                if (current == Backbone.history.fragment) {
                    $(item).addClass('active');
                } else {
                    $(item).removeClass('active');
                }
            });
            return this;
        },

        handleClick: function (e) {
            var target = $(e.currentTarget);
            var action = target.attr('data-action');

            if (action == 'logout') {
                App.models.session.end();
            } else if (action == 'feed') {
                Backbone.history.navigate('feed', true);
            } else if (action == 'profile') {
                Backbone.history.navigate('profile', true);
            } else if (action == 'tv') {
                Backbone.history.navigate('tv', true);
            } else if (action == 'welcome') {
                Backbone.history.navigate('welcome', true);
            } else if (action == 'mobilePost') {
                Backbone.history.navigate('mobilePost', true);
            } else if (action == 'admin') {
                Backbone.history.navigate('admin', true);
            }

            return false;
        },

        showMobileCommentForm: function (e) {
            $('#profile').hide();
            $('#feed-items').hide();
            $('#microblogging').show();
        }
    });

    /* !Model: Profile */
    var Profile = Backbone.Model.extend({
        initialize: function () {
            _.bindAll(this);
            var self = this;
            self.optionalInfo = ['hobbies', 'languages', 'socialmedia', 'headline', 'department', 'location', 'introduction', 'phone', 'cellphone'];
            self.optionalInfo = ['hobbies', 'headline', 'department', 'location', 'introduction', 'phone', 'cellphone'];

            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.getUserData',
                    auth_token: App.models.session.get('authToken'),
                    guid: self.get('guid')
                },
                success: function (response) {
                    if (response.status != -1) {
                        var result = response.result;
                        self.set(result);

                        if (result.id == App.models.session.get('userId')) self.set('isOwnProfile', true);

                        $.ajax({
                            type: 'GET',
                            url: App.resourceUrl,
                            dataType: 'json',
                            data: {
                                method: 'coffee.getUserExtraInfo',
                                auth_token: App.models.session.get('authToken'),
                                guid: self.get('guid'),
                                names: self.optionalInfo
                            },
                            success: function (response) {
                                if (response.status != -1) {
                                    var result = response.result;
                                    var attributes = self.processExtraInfo(result);

                                    self.set(attributes);
                                    self.trigger('ready');
                                    console.log('prodile say ready');
                                }
                            }
                        });
                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }
                }
            });
        },

        processExtraInfo: function (object) {
            var self = this;
            var extraInfo = object;

            extraInfo.isProfileComplete = true; // Is all optional info present?
            extraInfo.isIntroductionLong = false;

            for (i in self.optionalInfo) {
                var varName = self.optionalInfo[i];
                var dynamicHasKey = 'has'+capitaliseFirstLetter(varName);

                if (object.hasOwnProperty(varName)) {
                    extraInfo[dynamicHasKey] = true;
                } else {
                    extraInfo[dynamicHasKey] = false;
                    extraInfo.isProfileComplete = false;
                }
            }

            extraInfo.hobbies = (extraInfo.hasHobbies) ? JSON.parse(stripslashes(object.hobbies)) : [];
            extraInfo.languages = (extraInfo.hasLanguages) ? JSON.parse(stripslashes(object.languages)): [];
            extraInfo.socialmedia = (extraInfo.hasSocialmedia) ? JSON.parse(stripslashes(object.socialmedia)) : [];
            extraInfo.introduction = (extraInfo.introduction) ? stripslashes(object.introduction) : [];
            if (extraInfo.introduction.length > 190) {
                extraInfo.isIntroductionLong = true;
            }

            _.each(extraInfo.socialmedia, function(item){
                var type = item.service;

                item.isTwitter = (type == 'twitter') ? true : false;
                item.isFacebook = (type == 'facebook') ? true : false;
                item.isSkype = (type == 'skype') ? true : false;
                item.isLinkedIn = (type == 'linkedin') ? true : false;
            });

            _.each(extraInfo.languages, function(language){
                var level = language.level;

                language.isNative = (level == 5) ? true : false;
                language.isBilingual = (level == 4) ? true : false;
                language.isFluent = (level == 3) ? true : false;
                language.isIntermediate = (level == 2) ? true : false;
                language.isBeginner = (level == 1) ? true : false;
            });

            return extraInfo;
        }
    });

    /* !View: ProfileView */
    var ProfileView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);

            this.model = new Profile({
                guid: this.options.guid
            });
            this.model.bind('ready', this.firstRender);
        },

        events: {
            'mouseenter .editable': 'toggleEditable',
            'mouseleave .editable': 'toggleEditable',
            'focusout .editing-introduction': 'updateField',
            'click .editable': 'startInlineEdit',
            'focusout .editing' : 'updateField',
            'keyup .editing': 'textareaKeyup',
            'keypress .editing': 'textareaKeypress',
            'click .avatar .btn': 'avatarEdit',
            'click #cover-edit': 'coverEdit',
            'click .sm-addnew': 'newSocialmedia',
            'click .add-hobby': 'addHobby',
            'click #profile .btn-danger': 'logout',
            'click #cancelPostUpdate': 'hideMobileCommentForm',
            'click .show-all-text': 'toggleIntroduction'/*,
			'click .update-actions a': 'updateAction'*/
        },

        firstRender: function () {
            this.render().$el.appendTo('#container');
        },

        logout: function () {
            App.models.session.end();
        },

        render: function () {
            data = this.model.toJSON();
            data.translate = function() {
                return function(text) {
                    return t(text);
                }
            };
            if(isMobile.any()){
                var element = ich.mobileProfileTemplate(data);
            } else {
                var element = ich.profileTemplate(data);
            }
            $(this.el).replaceWith(element);
            this.setElement(element);

            if(isMobile.any()){
                var element = ich.mobileMicrobloggingTemplate(data).find('#microblogging');
                $('#content').prepend(element);
            }

            setBackground (this.model.get('cover_url'));

            return this;
        },

        toggleEditable: function (e) {
            var element = $(e.currentTarget);
            var hoverClassName = 'editable-hover';

            if (! element.data('keepsHoverState')) {
                if (e.handleObj.origType == 'mouseenter') {
                    if (element.hasClass(hoverClassName)) {
                        element.data('keepsHoverState', true);
                    } else {
                        element.addClass(hoverClassName);
                    }
                } else {
                    element.removeClass(hoverClassName);
                }
            }
        },

        startInlineEdit: function (e) {

            var self = this
            , element = $(e.currentTarget)
            , name = element.attr('data-name')
            , key = element.attr('data-key')
            , prevValue = typeof eval('self.model.attributes.' + name) == 'undefined' ? '':element.html().replace('<br>', '');
            var editingTextarea = $('<textarea class="editing editing-'+name+'" data-name="'+name+'" data-key="'+key+'">' + prevValue + '</textarea>')
            .bind('blur', function(){
                editingTextarea.replaceWith(element);
            });
            element
            .removeClass('editable-hover')
            .replaceWith(editingTextarea);
            editingTextarea.focus();
        },

        textareaKeyup: function (e) {
            if (e.keyCode == 13) { // Enter key
                var element = $(e.currentTarget);

                if (element.data('name') != 'introduction') {
                    var value = element.val();
                    var name = element.data('name');
                    var key = element.data('key');
                    this.finishedEditing(value, name, key);
                }
            }
        },

        textareaKeypress: function (e) {
            if (e.keyCode == 13) { // Enter key
                if ($(e.currentTarget).data('name') != 'introduction') {
                    return false;
                }
            }
        },

        finishedEditing: function (value, name, key) {
            var self = this,
            processedValue = value;
            if (key >= 0) {
                prevValue = self.model.get(name);
                if (value === '') {
                    prevValue.splice(key,1);
                } else {
                    prevValue[key] = {
                        key:key,
                        value:value
                    };
                }
                processedValue = prevValue;
                value = JSON.stringify(prevValue);
            }
            $.ajax({
                type: 'POST',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.setUserExtraInfo',
                    auth_token: App.models.session.get('authToken'),
                    name: name,
                    value: value
                },
                success: function (response) {
                    if (response.status != -1) {
                        self.model.set(name, processedValue);
                        self.model.set('has' + capitaliseFirstLetter(name), true);
                        self.render();
                    }
                }
            });
        },

        newSocialmedia: function () {
            var self = this,
            elm = $('.add-socialmedia');
            elm.toggle();
        },

        updateField: function (e) {
            var element = $(e.currentTarget)
            , value = element.val()
            , name = element.data('name')
            , key = element.data('key');
            this.finishedEditing(value, name, key);
        },

        avatarEdit: function () {
            var self = this
            , avatar = $('#avatar')
            , avatarForm = $('#avatarUpload')
            , avatarCrop = $('#avatarCrop');
            avatar
            .trigger('click')
            .change(function (){
                elm = $(this);
                if (self.isPicture(elm.val())) {
                    var options = {
                        success: self.updateAvatar
                        , url: App.resourceUrl
                        , dataType: 'json'
                        , data: {
                            method: 'coffee.uploadUserAvatar',
                            auth_token: App.models.session.get('authToken')
                        }
                    };
                    avatarForm.ajaxSubmit(options);
                }
            });

        },

        coverEdit: function () {
            var self = this
            , cover = $('#cover')
            , coverForm = $('#coverUpload')
            , coverCrop = $('#coverCrop');
            cover
            .trigger('click')
            .change(function (){
                elm = $(this);
                if (self.isPicture(elm.val())) {
                    var options = {
                        success: self.updateCover
                        , url: App.resourceUrl
                        , dataType: 'json'
                        , data: {
                            method: 'coffee.uploadUserCover',
                            auth_token: App.models.session.get('authToken')
                        }
                    };
                    coverForm.ajaxSubmit(options);
                }
            });

        },

        isPicture: function (s) {
            var regexp = /jpg|jpeg|gif|png/i
            return regexp.test(s);
        },

        updateAvatar: function (response) {
            var self = this;
            if (response.status != -1) {
                self.model.set('icon_url', response.result);
                self.render();
            }
        },

        updateCover: function (response) {
            var self = this;
            if (response.status != -1) {
                self.model.set('cover_url', response.result);
                self.render();
            }
        },

        addHobby : function (e) {
            var self = this;
            element = $(e.currentTarget);
            var editingTextarea = $('<textarea class="editing editing-hobbies"></textarea>')
            .insertAfter(element)
            .focus()
            .bind('blur', function(){
                editingTextarea.remove();
                element.removeAttr('style');
            })
            .data('name', 'hobbies')
            .data('key', self.model.get('hobbies').length);

        },

        updateAction: function (e) {
            var self = this;
            var action = $(e.currentTarget).attr('data-action');
            if (action == "coffeePoke") {

            }
        },

        toggleIntroduction : function () {
            elm = $('.introductionCut');
            elm.toggleClass('full');
            return true;
        },

        hideMobileCommentForm : function (e) {
            $('#profile').show();
            $('#microblogging').hide();
            $('#microblogging .update-text').val('');
        }

    });

    /* !View: TvAppView */
    var TvAppView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);
            /*this.collection = new FeedItemList();
			this.collection.bind('add', this.addItem);
            console.log(this.collection);*/
            this.render();
        },

        render: function () {
            data =  {
                scripts : '<script src="static/js/jquery.color.js"></script><script src="static/js/animation.js"></script>'
            };
            var element = ich.tvAppTemplate(data);
            this.setElement(element);
            this.$el.prependTo('#container');
            return this;
        }

    });

    /* !View: adminView */
    var adminView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);
            this.userList = new UserListView();
            this.render();
            this.offset = 0;
            this.limit = 0;
            this.username = '';
        },

        events: {
            'click #addNewUser' : 'addNewUser'
            , 'click #siteSettingsUpdate' : 'siteSettingsUpdate'
            , 'click #manageUser .nav li' : 'manageUserNav'
            , 'keyup #manageUser #username' : 'manageUserNav'
        },

        render: function () {
            data = {
                translate : function() {
                    return function(text) {
                        return t(text);
                    }
                }
            };

            var element = ich.adminTemplate(data);
            this.setElement(element);

            this.$el.prependTo('#container');
            $('#adminMenu a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            return this;
        },

        addNewUser: function () {
            var self = this;
            $.ajax({
                type: 'POST',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.registerUser'
                    , auth_token: App.models.session.get('authToken')
                    , display_name : this.$el.find('#addnewuser #displayName').val()
                    , email : this.$el.find('#addnewuser #email').val()
                    , password : this.$el.find('#addnewuser #password').val()
                    , password2 : this.$el.find('#addnewuser #password2').val()
                    , language : this.$el.find('#addnewuser #language').val()
                },
                success: function (response) {
                    if (response.status == '-1') {
                        self.$el.find('#addnewuser #registrationResult')
                            .html(response.message)
                            .show();
                    } else if (response.status == '0') {
                          self.$el.find('#addnewuser #registrationResult')
                            .html('Registration success, new user id : ' + response.result.guid)
                            .show();
                            self.resetUserList();
                            self.userList.collection.loadUser();
                    }
                }
            });
            return false;
        },

        siteSettingsUpdate: function() {
            var options = {
                success: this.refreshSitePicture
                , url: App.resourceUrl
                , dataType: 'json'
                , data: {
                    method: 'coffee.editSiteSettings'
                    , auth_token: App.models.session.get('authToken')
                    , language : this.$el.find('#siteSettingForm #language').val()
                }
            };
            $("#siteSettingForm").ajaxSubmit(options);
            return false;
        },

        refreshSitePicture: function (response) {
            _.each(response.result, function (item,key) {
                if (typeof item.background != 'undefined') {
                    App.models.session.set('backgroundUrl',item.background);
                    setBackground (item.background);
                }
                if (typeof item.logo != 'undefined') {
                    App.models.session.set('logoUrl',item.logo);
                    setLogo (item.logo);
                }
            });
        },

        manageUserNav: function(e) {
            elm = $(e.currentTarget);
            action = $(e.currentTarget).attr('id');
            this.username = $(e.currentTarget).parent().parent().find('#username').val();
            this.resetUserList();
            switch (action) {
                case 'next' :
                    this.offset = this.offset + 10;
                    this.userList.collection.loadUser(this.username,this.offset);
                    break;
                 case 'prev' :
                    this.offset = this.offset - 10;
                    this.userList.collection.loadUser(this.username,this.offset);
                    break;
                 case 'username' :
                    this.userList.collection.loadUser(this.username,this.offset);
                    break;
            }
            return false;
        },

        resetUserList: function () {
            this.userList.collection.remove(this.userList.collection.models);
        }

    });

    /* !View: WelcomeAppView */
    var WelcomeAppView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);
            this.render();
        },

        render: function () {
            data = {
                translate : function() {
                    return function(text) {
                        return t(text);
                    }
                }
            };
            var element = ich.welcomeAppTemplate(data);
            this.setElement(element);

            this.$el.prependTo('#container');

            return this;
        }

    });

    /* !View: MobileCommentView */
    var MobileCommentView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);

            this.render();
        },

        render: function () {
            var self = this;
            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.getPost',
                    auth_token: App.models.session.get('authToken'),
                    guid: localStorage.getItem('postId')
                },
                success: function (response) {
                    if (response.status != -1) {
                        data = response.result[0];
                        data.translate = function() {
                            return function(text) {
                                return t(text);
                            }
                        };

                        var element = ich.mobileCommentTemplate(data);
                        self.setElement(element);

                        self.$el.prependTo('#container');
                    } else if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }
                }
            });
        },

        events: {
            "click #commentUpdate": "comment",
            "click #cancelCommentUpdate": "back"
        },

        back: function() {
            Backbone.history.navigate('feed', true);
        },

        comment: function(e) {
            var postGuid = localStorage.getItem('postId');
            var theComment = $('.new-comment-textarea').val();

            $.ajax({
                type: 'POST',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.comment',
                    auth_token: App.models.session.get('authToken'),
                    guid: postGuid,
                    comment: theComment
                },
                success: function (response) {
                    if (response.message == 'pam_auth_userpass:failed') {
                        localStorage.clear();
                        App.models.session.end();
                        Backbone.history.navigate('login', true);
                    } else {
                        Backbone.history.navigate('feed', true);
                    }
                }
            });
        }
    });

    /* !View: MobilePostView */
    var MobilePostView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);

            this.render();
        },

        render: function () {
            var self = this;
            data = {
                'translate': function() {
                    return function(text) {
                        return t(text);
                    }
                }
            };

            var element = ich.mobileMicrobloggingTemplate(data);
            self.setElement(element);

            self.$el.prependTo('#container');
            $('#microblogging').show();
        },

        events: {
            "click #postUpdate": "post",
            "click #cancelPostUpdate": "back"
        },

        back: function() {
            Backbone.history.navigate('feed', true);
        },

        post: function(e) {
            var updateText = $('.update-text').val();

            var data = {
                method: 'coffee.createNewPost',
                auth_token: App.models.session.get('authToken'),
                post: updateText,
                type: ''
            };

            $.ajax({
                type: 'POST',
                url: App.resourceUrl,
                dataType: 'json',
                data: data,
                success: function (response) {
                    if (response.status != -1) {
                        Backbone.history.navigate('feed', true);
                    } else {
                        alert('There was an error posting the update.');
                    }
                }
            });
        }
    });

   /* !View: userSettings */
    var userSettingsView = Backbone.View.extend({
        initialize: function () {
            _.bindAll(this);

            this.render();
        },

        render: function () {
            var self = this;
            data = {
                'translate': function() {
                    return function(text) {
                        return t(text);
                    }
                }
                , 'userSetting' : App.models.session.toJSON()
            };

            var element = ich.userSettingsTemplate(data);
            self.setElement(element);

            self.$el.prependTo('#container');
        },

        events: {
            "click #saveSettings" : "post"
        },

        post: function(e) {

            var data = {
                method: 'coffee.editUserDetail'
                , auth_token: App.models.session.get('authToken')
                , name: $('#inputName').val()
                , current_password: $('#inputCurrentPassword').val()
                , password: $('#inputNewPassword').val()
                , language: $('#inputLanguage').val()
            };
            $.ajax({
                type: 'POST',
                url: App.resourceUrl,
                dataType: 'json',
                data: data,
                success: function (response) {
                    console.log(response);
                    if (response.status != -1) {
                        $('#settingUpdateSuccess').fadeIn();
                    } else {
                        alert('There was an error posting the update.\n\
                                Message : ' + response.message);
                    }
                }
            });
            return false;
        }
    });

    /* !Router: WorkspaceRouter */
    var WorkspaceRouter = Backbone.Router.extend({
        routes: {
            "login":                    "login"
            , "feed":					"feed"
            , "profile":				"myProfile"
            , "profile/:user_id":		"profile"
            , "tv":                     "tv"
            , "welcome":                "welcome"
            , "mobileComment":          "mobileComment"
            , "mobilePost":             "mobilePost"
            , "userSettings":           "userSettings"
            , "admin":                  "admin"
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
                setBackground (App.models.session.get('backgroundUrl'));
                setLogo (App.models.session.get('logoUrl'));
            } else {
                Backbone.history.navigate('login', true);
            }
        },

        myProfile: function () {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                App.views.profileView = new ProfileView({
                    guid: App.models.session.get('userId')
                });
                App.views.menuView = new MenuView();
                setLogo (App.models.session.get('logoUrl'));
            } else {
                Backbone.history.navigate('login', true);
            }
        },

        profile: function (userId) {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                App.views.profileView = new ProfileView({
                    guid: userId
                });
                App.views.menuView = new MenuView();
                setLogo (App.models.session.get('logoUrl'));
            } else {
                Backbone.history.navigate('login', true);
            }
        },

        tv: function () {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                App.views.tvAppView = new TvAppView();
                App.views.menuView = new MenuView();
                $('.watermark').hide();
            } else {
                Backbone.history.navigate('login', true);
            }
        },

        welcome: function () {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                App.views.welcomeAppView = new WelcomeAppView();
                App.views.menuView = new MenuView();
                setBackground (App.models.session.get('backgroundUrl'));
                setLogo (App.models.session.get('logoUrl'));
            } else {
                Backbone.history.navigate('login', true);
            }
        },

        userSettings: function () {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                App.views.userSettingsView = new userSettingsView();
                App.views.menuView = new MenuView();
                setBackground (App.models.session.get('backgroundUrl'));
                setLogo (App.models.session.get('logoUrl'));
            } else {
                Backbone.history.navigate('login', true);
            }
        },

        admin: function () {
            App.removeAllViews();
            if (App.models.session.authenticated() && App.models.session.get('isAdmin')) {
                App.views.adminView = new adminView();
                App.views.menuView = new MenuView();
                setBackground (App.models.session.get('backgroundUrl'));
                setLogo (App.models.session.get('logoUrl'));
            } else {
                Backbone.history.navigate('login', true);
            }
        },

        mobileComment: function () {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                App.views.mobileCommentView = new MobileCommentView();
                App.views.menuView = new MenuView();
                setBackground (App.models.session.get('backgroundUrl'));
                setLogo (App.models.session.get('logoUrl'));
            } else {
                Backbone.history.navigate('login', true);
            }
        },

        mobilePost: function () {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                App.views.mobilePostView = new MobilePostView();
                App.views.menuView = new MenuView();
                setBackground (App.models.session.get('backgroundUrl'));
                setLogo (App.models.session.get('logoUrl'));
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

        if(isMobile.any()){
            $(document.body).addClass('mobile');
        }

        $(window).scroll(function() {
            if($(window).scrollTop() == $(document).height() - $(window).height()
                && Backbone.history.fragment === 'feed') {
                App.views.microbloggingView.offset = App.views.microbloggingView.offset + 10;
                App.views.microbloggingView.feedItemsView.collection.loadFeed(App.views.microbloggingView.offset);
            }
        });
    });

})(jQuery);