(function($) {
    /* Vars */
    var translations = null;
    /* Tools */
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
    },

    stripslashes = function (str) {
        str=str.replace(/\\'/g,'\'');
        str=str.replace(/\\"/g,'"');
        str=str.replace(/\\0/g,'\0');
        str=str.replace(/\\\\/g,'\\');
        return str;
    },

    capitaliseFirstLetter = function (str)	{
        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    nl2br = function (str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
    },

    setBackground = function (backgroundUrl) {
        if (backgroundUrl && backgroundUrl.length > 0) {
            $('body')
            .css('background','url(' + backgroundUrl +')')
            .css('background-repeat','no-repeat')
            .css('background-attachment','fixed')
            .css('-moz-background-size','cover')
            .css('background-size','cover');
        } else {
            $('body').css('background','url(userpics/client_bg.jpeg)');
        }
    },

    setLogo = function (logoUrl) {
        $('.watermark').hide();
        if (logoUrl && logoUrl.length > 0) {
            $('#watermark').attr('src',logoUrl);
            if (isMobile.any()) {
                return;
                $('.watermark')
                    .html('<h3>' + App.models.session.get('siteName') + '</h3>')
                    .css('color','#FFF');
            }
        } else {
            $('#watermark').attr('src','url(userpics/logo.png)');
        }
        $('.watermark').show();
    },

    t = function (key) {
        if (translations === null) {
            $.ajax({
                type: 'GET'
                , url: location.host?"/ajax/view/js/languages":"http://api.coffeepoke.com/ajax/view/js/languages"
                , cache: true
                , async: false
                , data: {
                    language : App.models.session.get('language')?App.models.session.get('language'):'en'
                }
                , success: function (response) {
                    translations = $.parseJSON(response);
                }
            });
        }

        if (translations != null && translations[key]) {
            return translations[key];
        }
        return key;
    },

    toggleUploadSpinner = function  () {
        spinner = $('#uploadProgress');
        spinner.toggle()
                .find('.bar').css('0%');
    },

    uploadProgress = function(percentComplete) {
            var percentVal = percentComplete + '%'
                , bar = $('#uploadProgress div.bar');
            bar.css('width', percentVal);
    },

    replaceUrl = function(inputText) {
       var replaceText, replacePattern1, replacePattern2, replacePattern3;
       //URLs starting with http://, https://, or ftp://
       replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
       replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

       //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
       replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
       replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

       //Change email addresses to mailto:: links.
       replacePattern3 = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
       replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

       return replacedText;
    };

    var App = {
        models: {},
        collections: {},
        views: {},
        resourceUrl: location.host?"/services/api/rest/json":"http://api.coffeepoke.com/services/api/rest/json",

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
                , triggerChar:['@','#']
                , onDataRequest:function (mode, query, triggerChar,callback) {
                    $.ajax({
                        type: 'GET'
                        , url: App.resourceUrl
                        , dataType: 'json'
                        , data: {
                            method: triggerChar=='@'?'coffee.getUserList':'coffee.getTagList'
                            , auth_token: App.models.session.get('authToken')
                            , query: query
                            , mode : triggerChar
                        }
                        , success: function (response) {
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

        getSearchCriteria: function(name) {
            try {
                searchCriteria = JSON.parse(App.models.session.get('searchCriteria'));
                value = eval('searchCriteria.' + name);
                if (typeof value != 'undefined') return value;
            } catch (Exception) {
                return null;
            }
            return null;
        }
    };

    /* !Model: Session */
    var Session = Backbone.Model.extend({
        defaults: {
            authToken: null
            , userId: null
            , siteName: null
            , logoUrl: null
            , backgroundUrl: null
            , customCss: null
            , isAdmin: null
            , language: null
            , accountTime: null
            , loginCount: null
            , searchCriteria: null
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
            $.cookie('userId', this.get('userId'), { expires: 365 });
            $.cookie('authToken', this.get('authToken'), { expires: 365 });
            $.cookie('siteName', this.get('siteName'), { expires: 365 });
            $.cookie('logoUrl', this.get('logoUrl'), { expires: 365 });
            $.cookie('backgroundUrl', this.get('backgroundUrl'), { expires: 365 });
            $.cookie('customCss', this.get('customCss'), { expires: 365 });
            $.cookie('name', this.get('name'), { expires: 365 });
            $.cookie('iconUrl', this.get('iconUrl'), { expires: 365 });
            $.cookie('coverUrl', this.get('coverUrl'), { expires: 365 });
            $.cookie('isAdmin', this.get('isAdmin'), { expires: 365 });
            $.cookie('language', this.get('language'), { expires: 365 });
            $.cookie('accountTime', this.get('accountTime'), { expires: 365 });
            $.cookie('loginCount', this.get('loginCount'), { expires: 365 });
            $.cookie('searchCriteria', this.get('searchCriteria'), { expires: 365 });

            this.trigger('started');
        },

        /*set: function (attributes, options) {
            if (typeof attributes === 'object') {
                _.each(attributes, function(value,item) {
                });
            } else {
                $.cookie(attributes, options);
            }
            Backbone.Model.prototype.set.call(this, attributes, options);
        },*/

        load: function () {
            this.set({
                userId: $.cookie('userId'),
                authToken: $.cookie('authToken'),
                siteName: $.cookie('siteName'),
                logoUrl: $.cookie('logoUrl'),
                backgroundUrl: $.cookie('backgroundUrl'),
                customCss: $.cookie('custom_css'),
                name: $.cookie('name'),
                language: $.cookie('language'),
                iconUrl: $.cookie('iconUrl'),
                coverUrl: $.cookie('coverUrl'),
                isAdmin: $.cookie('isAdmin'),
                accountTime: $.cookie('accountTime'),
                loginCount: $.cookie('loginCount'),
                searchCriteria: $.cookie('searchCriteria')
            });
        },

        set: function (attributes, options) {
            $.cookie(attributes, options);
            Backbone.Model.prototype.set.call(this, attributes, options);
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
                                        loginCount: result.login_count,
                                        language: result.language
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

            $.cookie('userId',null);
            $.cookie('authToken',null);
            $.cookie('siteName',null);
            $.cookie('logoUrl',null);
            $.cookie('backgroundUrl',null);
            $.cookie('backgroundPos',null);
            $.cookie('name',null);
            $.cookie('iconUrl',null);
            $.cookie('coverUrl',null);
            $.cookie('isAdmin',null);
            $.cookie('accountTime',null);
            $.cookie('loginCount',null);

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
            self.$el.addClass('loading');
            var email = self.$el.find('#inputEmail').val();
            var password = self.$el.find('#inputPassword').val();

            $.ajax({
                type: 'POST'
                , url: App.resourceUrl
                , dataType: 'json'
                , data: {
                    email: email
                    , password: password
                    , method: 'coffee.getTokenByEmail'
                }
                , success: function (response) {
                    if (response.status != -1) {
                        self.session.set({ authToken: response.result });
                        self.session.start();
                    } else {
                        self.$el.removeClass('loading');
                        /* login failed */
                        alert(response.message);
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
        },

        getUserById: function (guid) {
            var self = this;
            self.guid = guid;
            $.ajax({
                type: 'GET'
                , url: App.resourceUrl
                , dataType: 'json'
                , async: false
                , data: {
                    method: 'coffee.getUserData',
                    auth_token: App.models.session.get('authToken'),
                    guid: self.guid
                },
                success: function (response) {
                    if (response.status != -1) {
                        var result = response.result;
                        self.set(result);
                    }
                }
            });
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
            if (!confirm("Are you sure ?")) return false;
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

            if (attributes.mentioned && attributes.mentioned.length>0) {
                attributes.content.text = this.replaceMentions(attributes.content.text,attributes.mentioned, 'user');
            }

            if (attributes.attachment && attributes.attachment.length>0) {
                for (i=0;i<attributes.attachment.length;i++) {
                    attributes.attachment[i].noModal = false;
                    if (attributes.attachment[i].type == 'url') {
                        if ((attributes.attachment[i].html.indexOf('edouard[at]coffeepoke.com') > -1 || attributes.attachment[i].html == "")) {
                            attributes.attachment[i].noModal = true;
                        }
                        if (attributes.attachment[i].title == '') {
                            attributes.attachment[i].title = attributes.attachment[i].url;
                        }
                    }
                }
            }

            if (attributes.tags && attributes.tags.length>0) {
                var tagReplacement = new Array();
                if (_.isArray(attributes.tags)) {
                    _.each(attributes.tags, function (tag) {
                        tagReplacement.push({name:'#' + tag
                                                , tag:tag});
                    });
                } else {
                    tagReplacement.push({name:'#' + attributes.tags
                                                , tag:attributes.tags});
                }
                attributes.content.text = this.replaceMentions(attributes.content.text,tagReplacement, 'tag');
            }

            if (attributes.comment.total > 0) {
                attributes.comment.comments.reverse();
                attributes.comment.showAllLink = (attributes.comment.total > attributes.comment.comments.length) ? true : false;
                for (i=0; i < attributes.comment.total; i++) {
                    if (typeof attributes.comment.comments[i] != 'undefined') {
                        if (attributes.comment.comments[i].mentioned && attributes.comment.comments[i].mentioned.length>0) {
                            attributes.comment.comments[i].text = this.replaceMentions(attributes.comment.comments[i].text,attributes.comment.comments[i].mentioned,'user');
                        }
                        if (tagReplacement) {
                            attributes.comment.comments[i].text = this.replaceMentions(attributes.comment.comments[i].text,tagReplacement,'tag');
                        }
                        if (attributes.comment.comments[i].owner_guid == App.models.session.get('userId') || (App.models.session.get('isAdmin') == 'true')) attributes.comment.comments[i].isCommentOwner = true;
                    }
                }
            }

            attributes.content.text = replaceUrl(attributes.content.text);

            attributes.isBroadCastMessage = (attributes.content.type === 'coffee_broadcast_message') ? true : false;
            Backbone.Model.prototype.set.call(this, attributes, options);
        },

        replaceMentions : function (text,mentions,type) {
            var template;
            switch (type) {
                case 'user':
                default:
                    template = 'mentionUserTemplate';
                    pattern = /mentioned user/;
                    break;
                case 'tag':
                    template = 'mentionTagTemplate';
                    pattern = /mentioned tag/;
                    break;
            }
            if (pattern.test(text)) return text;
            _.each(mentions, function (mention) {
                replacement = eval('ich.'+ template+'(mention,true)');
                patern = '/(^|\s)' + mention.name + '($|\s|[^\w])/';
                text = text.replace(mention.name,replacement);
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
                    , tags: App.getSearchCriteria('tags')
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
            if (App.views.microbloggingView.isAttaching) {
                self.startCheckingForNewPosts();
                return false;
            }
            var latestTimestamp = _.max(self.models, function(latest){
                return latest.attributes.content.time_updated;
            }).attributes.content.time_updated;
            $.ajax({
                type: 'GET'
                , url: App.resourceUrl
                , dataType: 'json'
                , data: {
                    method: 'coffee.getPosts'
                    , auth_token: App.models.session.get('authToken')
                    , newer_than: latestTimestamp
                    , tags: App.getSearchCriteria('tags')
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
                        if (Backbone.history.fragment.indexOf('feed') != -1) {
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
            'click .show-all-link': 'showAllComments'
           , 'keydown .new-comment-textarea': 'textareaKeydown'
           , 'keypress .new-comment-textarea': 'textareaKeypress'
           , 'click .update-action a': 'updateAction'
           , 'focus .new-comment-textarea': 'changeComposingFlag'
           , 'blur .new-comment-textarea': 'changeComposingFlag'
           , 'click .remove-comment': 'removeComment'
           , 'click .show-all-text': 'toggleAllText'
           , 'click .update-action-comment-mobile a': 'showMobileCommentForm'
           , 'click .thumbnail': 'openMobileLink'
           , 'click .title': 'openMobileLink'
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
                        if(isMobile.any()){
                            Backbone.history.navigate('feed', true);
                        } else {
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
            guid = $(e.currentTarget).parents('.feed-item').attr('data-guid');
            Backbone.history.navigate('mobileComment/' + guid, true);
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
        },

        openMobileLink: function (e) {
            openLink = true;
            if(isMobile.any()){
                openLink = confirm(t('coffee:feeditem:action:openlinkconfirm'));
            }
            return openLink;
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
            'paste .update-text': 'detectPaste',
            'click .attachment .remove': 'removeAttachment',
            'click .add-media': 'uploadMedia',
            'click .broadcastMessage': 'toggleBroadcastMessage',
            'click #cancelPostUpdate': 'hideMobileCommentForm'
        },

        render: function () {
            data = {
                icon_url: App.models.session.get('iconUrl')
                , isAdmin: App.models.session.get('isAdmin')
                , siteName: App.models.session.get('siteName')
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
                                if(isMobile.any()){
                                    Backbone.history.navigate('feed', true);
                                } else {
                                    self.clear();
                                    self.feedItemsView.addNew(postGuid);
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
            if (e.ctrlKey && _.contains([86,16], e.keyCode)) {
                textarea.val(textarea.val() + ' ');
            }
            if(self.isUrl(value)) {
                var splitValue = value.split(/(\s|\n|\r)/);
                splitValue[splitValue.length-1] = "";
                _.each(splitValue, function(item, key) {
                    if(self.isUrl(item)) {
                        self.attachLink(item, textarea);
                    }
                });
            }
            /*
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

            self.updateLength = value.length;*/
        },

        isUrl: function (s) {
            var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
            return regexp.test(s);
        },

        detectPaste : function (e) {
            alert(paste);
        },

        attachLink: function (url, textarea) {
            var self = this;

            if (! self.attachmentGuid && ! self.isAttaching) {
                self.$el.addClass('microblogging-loading');
                self.isAttaching = true;

                $.ajax({
                    type: 'GET'
                    , url: App.resourceUrl
                    , dataType: 'json'
                    , timeout: '25000'
                    , data: {
                        method: 'coffee.getUrlData'
                        , auth_token: App.models.session.get('authToken')
                        , url: url
                    }, success: function (response) {
                        if (response.status != -1) {
                            result = response.result;
                            self.attachmentGuid = result.guid;
                            self.attachmentElement = ich.microbloggingAttachmentTemplate(result);
                            self.attachmentElement
                            .insertBefore(self.$el.find('.update-actions').eq(0));
                            textarea.val(textarea.val().replace(url, ''));
                        }
                        self.isAttaching = false;
                        self.$el.removeClass('microblogging-loading');
                    }, error: function () {
                        self.attachmentGuid = self.isAttaching = false;
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
                self.$el.find('.attachment').remove();
                self.$el.find('.add-media').show();
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

            upload
            .trigger('click')
            .change(function (){
                toggleUploadSpinner();
                self.isAttaching = true;
                elm = $(this);
                var options = {
                    success: self.updateAttachement
                    , error: self.updateAttachement
                    , url: App.resourceUrl
                    , dataType: 'json'
                    , uploadProgress : function(event, position, total, percentComplete) {uploadProgress(percentComplete)}
                    , data: {
                        method: 'coffee.uploadData'
                        , auth_token: App.models.session.get('authToken')
                    }
                };
                uploadForm.ajaxSubmit(options);
                uploadForm.resetForm();
            });
            return false;
        },

        updateAttachement: function (response) {
            var self = this;
            if (response.status != -1) {
                result = response.result;
                self.attachmentGuid = result.guid;
                self.attachmentElement = ich.microbloggingAttachmentTemplate(result);
                self.attachmentElement
                .insertBefore(self.$el.find('.update-actions').eq(0));
                self.$el.find('.add-media').hide();
            } else {
            /* Error */
            }
            self.isAttaching = false;
            toggleUploadSpinner();
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

        getMentions: function () {
            var mentionedUsers = new Array();
            try {
                $('#microblogging .update-text')
                    .mentionsInput('getMentions', function(data) {
                        _.each(data, function(user, key){
                            mentionedUsers[key] = user.id;
                        });
                    })
                    .mentionsInput('reset')
                    .css('height','50px');
                return mentionedUsers;
            } catch (e) {
                return [];
            }
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
                , siteName : App.models.session.get('siteName')
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

            switch (action) {
                case 'logout':
                    App.models.session.end();
                    break;
                case 'feed':
                default:
                    App.models.session.set('searchCriteria',null);
                    Backbone.history.navigate('feed', true);
                    break;
                case 'profile':
                    Backbone.history.navigate('profile', true);
                    break;
                case 'tv':
                    Backbone.history.navigate('tv', true);
                    break;
                case 'welcome':
                    Backbone.history.navigate('welcome', true);
                    break;
                case 'mobilePost':
                    Backbone.history.navigate('mobilePost', true);
                    break;
                case 'admin':
                    Backbone.history.navigate('admin', true);
                    break;
                case 'settings':
                    Backbone.history.navigate('userSettings', true);
                    break;
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
            self.optionalInfo = ['hobbies', 'headline', 'location', 'introduction', 'phone', 'cellphone'];

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
                    toggleUploadSpinner();
                    var options = {
                        success: self.updateAvatar
                        , url: App.resourceUrl
                        , dataType: 'json'
                        , uploadProgress : function(event, position, total, percentComplete) {uploadProgress(percentComplete)}
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
                    toggleUploadSpinner();
                    var options = {
                        success: self.updateCover
                        , url: App.resourceUrl
                        , dataType: 'json'
                        , uploadProgress : function(event, position, total, percentComplete) {uploadProgress(percentComplete)}
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
                toggleUploadSpinner();
            }
        },

        updateCover: function (response) {
            var self = this;
            if (response.status != -1) {
                self.model.set('cover_url', response.result);
                self.render();
                toggleUploadSpinner();
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
            this.tags = this.prepareTags(App.getSearchCriteria('tags'));
            this.users = this.prepareUsers(App.getSearchCriteria('users'));
            //console.log(this.tags,this.users);
            this.render();
        },

        events: {
            'click span.del' : 'removeTag'
            , 'click #doTvAppConfig' : 'doTvAppConfig'
            , 'change #fromUsersSelect' : 'showUserInput'
            , 'click a.cancel' : 'doTvAppConfig'
            , 'click #saveConfig' : 'saveConfig'
        },

        render: function () {
             $(this.el).remove();
            data =  {
                scripts : '<script src="static/js/jquery.color.js"></script><script src="static/js/animation.js"></script>'
                , iconUrl : App.models.session.get('iconUrl')
                , name : App.models.session.get('name')
                , users : this.users
                , tags : this.tags
            };
            var self = this
                , element = ich.tvAppTemplate(data);
            self.setElement(element);
            self.$el.prependTo('#container');
            $('#users').typeahead({
                minLength: 2
                , source: function (query, process) {
                    var results = []
                        users = [];
                    return $.getJSON(App.resourceUrl, {method:'coffee.getUserList',auth_token: App.models.session.get('authToken'), query: query}, function (response) {
                         if (response.status != '-1') {
                             _.each(response.result, function(item) {
                                 results.push(item.name);
                                 users[item.name] = item.id;
                             });
                             return process(results);
                         }
                    });
                }
                , updater: function (item) {
                    data = {name:item
                            , id:users[item]
                            , css:'label-info user'
                            , del:true};
                    elm = ich.tagTemplate(data);
                    self.$el.find('#usersSelected').append(elm);
                }

            });
            $('#tags').typeahead({
                minLength: 2
                , source: function (query, process) {
                    var results = []
                        users = [];
                    return $.getJSON(App.resourceUrl, {method:'coffee.getTagList',auth_token: App.models.session.get('authToken'), query: query}, function (response) {
                         if (response.status != '-1') {
                             _.each(response.result, function(item) {
                                 results.push(item.name);
                                 users[item.name] = item.id;
                             });
                             return process(results);
                         }
                    });
                }
                , updater: function (item) {
                    data = {name:item
                            , id:users[item]
                            , css:'tag'
                            , del:true};
                    elm = ich.tagTemplate(data);
                    self.$el.find('#hashtagsSelected').append(elm);
                }

            });
            return this;
        },

        removeTag: function (e) {
            elm = $(e.currentTarget);
            id = elm.attr('id');
            elm.parent().remove();
        },

        doTvAppConfig: function (e) {
            var self = this
            , elm = $(e.currentTarget);
            self.$el.find('.secondary-content').fadeToggle();
            self.$el.find('.update-action').fadeToggle();
            self.$el.find('#doTvAppConfig').fadeToggle();
            //_.each(self.$el.find('span.label.user'), function (item, key) {$(item).remove();});
            //_.each(self.$el.find('span.label.tag'), function (item, key) {$(item).remove();});
            return false;
        },

        showUserInput: function (e) {
            var self = this
            , elm = $(e.currentTarget);
            _.each(self.$el.find('span.label.user'), function (item, key) {$(item).remove();});
            self.$el.find('.users').fadeToggle();
        },

        saveConfig: function (e) {
            self = this
                , users = []
                , tags = []
                , criteria = [];
            _.each(self.$el.find('span.label.user'), function (item, key) {
                users[key] = $(item).attr('data-id');
            });

            _.each(self.$el.find('span.label.tag'), function (item, key) {
                tags[key] = $(item).attr('data-name').replace('#','');
            });

            criteria = {tags:tags,users:users};
            criteria = JSON.stringify(criteria);

            App.models.session.set('searchCriteria',criteria);
            this.doTvAppConfig(e);
            loadPost(true);
        },

        prepareTags: function (tagsToFormat) {
            var tags = new Array();
            _.each(tagsToFormat, function (item, key) {
                tags.push({name:'#' + item
                            , css: 'tag'
                            , del:true});
            });
            return tags;
        },

        prepareUsers: function (usersToFormat) {
            var users = new Array();
            _.each(usersToFormat, function (item, key) {
                user = new UserItem();
                user.getUserById(item);
                users.push({id:item
                            , name:user.attributes.name
                            , css:'label-info user'
                            , del:true});
            });
            return users;
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
            , 'click #userSettings' : 'userSettings'
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
                    , make_admin : this.$el.find('#addnewuser #makeAdmin').attr('checked')?1:0
                    , send_email : this.$el.find('#addnewuser #sendEmail').attr('checked')?1:0
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
            toggleUploadSpinner();
            var options = {
                success: this.refreshSitePicture
                , url: App.resourceUrl
                , dataType: 'json'
                , uploadProgress : function(event, position, total, percentComplete) {uploadProgress(percentComplete)}
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
            toggleUploadSpinner();
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
        },

        userSettings: function () {
            alert('pouet');
            Backbone.history.navigate('userSettings', true);
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
    var MobileCommentView = FeedItemView.extend({
        initialize: function () {
            _.bindAll(this);
            this.guid = this.options.guid;
            this.render();
        },

        render: function () {
            var self = this;
            $.ajax({
                type: 'GET',
                url: App.resourceUrl,
                dataType: 'json',
                data: {
                    method: 'coffee.getPost'
                    , auth_token: App.models.session.get('authToken')
                    , guid: self.guid
                },
                success: function (response) {
                    if (response.status != -1) {
                        self.model = new FeedItem (response.result[0]);
                        data = self.model.toJSON();
                        data.translate = function() {
                            return function(text) {
                                return t(text);
                            }
                        };
                        var element = ich.mobileCommentTemplate(data);
                        self.setElement(element);
                        self.$el.prependTo('#container');
                        App.initMention(element.find('textarea.mention'));
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
            "click #commentUpdate": "mobileComment"
            , "click #cancelCommentUpdate": "back"
        },

        back: function() {
            Backbone.history.navigate('feed', true);
        },

        mobileComment: function(e) {
            var comment = $('.new-comment-textarea').val();
            this.comment(comment);
        }
    });

    /* !View: MobilePostView */
    var MobilePostView = MicrobloggingView.extend({
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
            App.initMention(element.find('textarea.update-text'));
            self.$el.prependTo('#container');
            $('#microblogging').show();
            element.find('textarea.update-text').focus();

        },

        events: {
            "click #postUpdate": "post"
            , "click #cancelPostUpdate": "back"
            , "keyup .update-text": "listenForLink"
        },

        back: function() {
            Backbone.history.navigate(-1, true);
        },

        post: function(e) {
            this.postUpdate(e);
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
                type: 'POST'
                , url: App.resourceUrl
                , dataType: 'json'
                , data: data
                , success: function (response) {
                    if (response.status != -1) {
                        $('#settingUpdateResult')
                        .html('Settings successfuly updated!')
                        .fadeIn();
                        try {
                            App.models.session.start();
                        } catch (e) {
                            alert(e);
                        }
                        setTimeout("window.location.reload(true)",1000);
                    } else {
                        $('#settingUpdateResult')
                        .html(response.message)
                        .fadeIn();
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
            , "feed":                   "feed"
            , "feed/:tag":              "feed"
            , "profile":				"myProfile"
            , "profile/:user_id":		"profile"
            , "tv":                     "tv"
            , "tv/:authToken":          "tv"
            , "welcome":                "welcome"
            , "mobileComment/:guid":    "mobileComment"
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

        feed: function (tag) {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                if (typeof tag != 'undefined') {
                    tags = JSON.stringify({tags:new Array(tag)});
                    App.models.session.set('searchCriteria',tags);
                }
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

        tv: function (authToken) {
            App.removeAllViews();
            if (authToken) {
                App.models.session.set('authToken', authToken);
                App.models.session.start();
            }
            if (App.models.session.authenticated()) {
                App.views.tvAppView = new TvAppView();
                $('.watermark').hide();
                setBackground (App.models.session.get('backgroundUrl'));
                if (authToken) {
                    $('#tvAppConfig').toggle();
                    $('#fullscreen').toggle();
                } else {
                    App.views.menuView = new MenuView();
                }
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

        mobileComment: function (guid) {
            App.removeAllViews();
            if (App.models.session.authenticated()) {
                App.views.mobileCommentView = new MobileCommentView({guid:guid});
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