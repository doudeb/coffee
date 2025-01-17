/*
 * Mentions Input
 * Version 1.0
 * Written by: Kenneth Auchenberg (Podio)
 *
 * Using underscore.js
 *
 * License: MIT License - http://www.opensource.org/licenses/mit-license.php
 */

(function ($, _, undefined) {

  // Settings
  var KEY = { BACKSPACE : 8, TAB : 9, RETURN : 13, ESC : 27, LEFT : 37, UP : 38, RIGHT : 39, DOWN : 40, COMMA : 188, SPACE : 32, HOME : 36, END : 35 }; // Keys "enum"
  var defaultSettings = {
    triggerChar   : '@',
    onDataRequest : $.noop,
    minChars      : 2,
    showAvatars   : true,
    elastic       : true,
	display		  : 'name',
    classes       : {
      autoCompleteItemActive : "active"
    },
    templates     : {
      wrapper                    : _.template('<div class="mentions-input-box"></div>'),
      autocompleteList           : _.template('<div class="mentions-autocomplete-list"></div>'),
      autocompleteListItem       : _.template('<li data-ref-id="<%= id %>" data-ref-type="<%= type %>" data-display="<%= display %>"><%= content %></li>'),
      autocompleteListItemAvatar : _.template('<img  src="<%= avatar %>" />'),
      autocompleteListItemIcon   : _.template('<div class="icon <%= icon %>"></div>'),
      mentionsOverlay            : _.template('<div class="mentions"><div></div></div>'),
      mentionItemSyntax          : _.template('<%= triggerChar %>[<%= value %>](<%= type %>:<%= id %>)'),
      mentionItemHighlight       : _.template('<strong><span><%= value %></span></strong>')
    }
  };

  var utils = {
    htmlEncode       : function (str) {
      return _.escape(str);
    },
    highlightTerm    : function (value, term) {
      if (!term && !term.length) {
        return value;
      }
      return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<b>$1</b>");
    },
    setCaratPosition : function (domNode, caretPos) {
      if (domNode.createTextRange) {
        var range = domNode.createTextRange();
        range.move('character', caretPos);
        range.select();
      } else {
        if (domNode.selectionStart) {
          domNode.focus();
          domNode.setSelectionRange(caretPos, caretPos);
        } else {
          domNode.focus();
        }
      }
    }
  };

  var MentionsInput = function (input) {
    var settings;
    var elmInputBox, elmInputWrapper, elmAutocompleteList, elmWrapperBox, elmMentionsOverlay, elmActiveAutoCompleteItem;
    var mentionsCollection = [];
    var inputBuffer = [];
    var currentDataQuery;

    function initTextarea() {
      elmInputBox = $(input);

      if (elmInputBox.attr('data-mentions-input') == 'true') {
        return;
      }

      elmInputWrapper = elmInputBox.parent();
      elmWrapperBox = $(settings.templates.wrapper());
      elmInputBox.wrapAll(elmWrapperBox);
      elmWrapperBox = elmInputWrapper.find('> div');

      elmInputBox.attr('data-mentions-input', 'true');
      elmInputBox.bind('keydown', onInputBoxKeyDown);
      elmInputBox.bind('keypress', onInputBoxKeyPress);
      elmInputBox.bind('input', onInputBoxInput);
      elmInputBox.bind('click', onInputBoxClick);

      if (settings.elastic) {
        elmInputBox.elastic();
      }
    }

    function initAutocomplete() {
      elmAutocompleteList = $(settings.templates.autocompleteList());
      elmAutocompleteList.appendTo(elmWrapperBox);
      elmAutocompleteList.delegate('li', 'click', onAutoCompleteItemClick);
    }

    function initMentionsOverlay() {
      elmMentionsOverlay = $(settings.templates.mentionsOverlay());
      elmMentionsOverlay.prependTo(elmWrapperBox);
    }

    function updateValues() {
      var syntaxMessage = getInputBoxValue();

      _.each(mentionsCollection, function (mention) {
        var textSyntax = settings.templates.mentionItemSyntax({ value : mention.value, type : mention.type, id : mention.id, triggerChar: mention.trigger });
        syntaxMessage = syntaxMessage.replace(mention.value, textSyntax);
      });

      var mentionText = utils.htmlEncode(syntaxMessage);

      _.each(mentionsCollection, function (mention) {
        var textSyntax = settings.templates.mentionItemSyntax({ value : utils.htmlEncode(mention.value), type : mention.type, id : mention.id, triggerChar: mention.trigger  });
        var textHighlight = settings.templates.mentionItemHighlight({ value : utils.htmlEncode(mention.value) });

        mentionText = mentionText.replace(textSyntax, textHighlight);
      });

      mentionText = mentionText.replace(/\n/g, '<br />');
      mentionText = mentionText.replace(/ {2}/g, '&nbsp; ');

      elmInputBox.data('messageText', syntaxMessage);
      elmMentionsOverlay.find('div').html(mentionText);
    }

    function resetBuffer() {
      inputBuffer = [];
    }

    function updateMentionsCollection() {
      var inputText = getInputBoxValue();

      mentionsCollection = _.reject(mentionsCollection, function (mention, index) {
        return !mention.value || inputText.indexOf(mention.value) == -1;
      });
      mentionsCollection = _.compact(mentionsCollection);
    }

    function addMention(value, id, type) {
      var currentMessage = getInputBoxValue();
      var currentTriggerChar = elmInputBox.data('triggerChar');

      // Using a regex to figure out positions
      var regex = new RegExp("\\" + currentTriggerChar + currentDataQuery, "gi");
      regex.exec(currentMessage);

      var startCaretPosition = regex.lastIndex - currentDataQuery.length - 1;
      var currentCaretPosition = regex.lastIndex;

      var start = currentMessage.substr(0, startCaretPosition);
      var end = currentMessage.substr(currentCaretPosition, currentMessage.length);
      var startEndIndex = (start + value).length;

      var updatedMessageText = start + value + end;

      mentionsCollection.push({
        id    : id,
        type  : type,
        value : value,
        trigger : currentTriggerChar
      });

      // Cleaning before inserting the value, otherwise auto-complete would be triggered with "old" inputbuffer
      resetBuffer();
      currentDataQuery = '';
      hideAutoComplete();

      // Mentions & syntax message
      elmInputBox.val(updatedMessageText);
      updateValues();

      // Set correct focus and selection
      elmInputBox.focus();
      utils.setCaratPosition(elmInputBox[0], startEndIndex);
    }

    function getInputBoxValue() {
      return $.trim(elmInputBox.val());
    }

    function onAutoCompleteItemClick(e) {
      var elmTarget = $(this);

      addMention(elmTarget.attr('data-display'), elmTarget.attr('data-ref-id'), elmTarget.attr('data-ref-type'));

      return false;
    }

    function onInputBoxClick(e) {
      resetBuffer();
    }

    function checkTriggerChar(inputBuffer, triggerChar) {
      var triggerCharIndex = _.lastIndexOf(inputBuffer, triggerChar);
      if (triggerCharIndex > -1) {
        currentDataQuery = inputBuffer.slice(triggerCharIndex + 1).join('');
        _.defer(_.bind(doSearch, this, currentDataQuery, triggerChar));
      }
    }
    function onInputBoxInput(e) {
      updateValues();
      updateMentionsCollection();
      hideAutoComplete();

	  if (_.isArray(settings.triggerChar)) {
		_.each(settings.triggerChar, function (triggerChar) {
			checkTriggerChar(inputBuffer, triggerChar);
		});
	  } else {
		checkTriggerChar(inputBuffer, settings.triggerChar);
	  }

    }

    function onInputBoxKeyPress(e) {
      var typedValue = String.fromCharCode(e.which || e.keyCode);
      inputBuffer.push(typedValue);
    }

    function onInputBoxKeyDown(e) {

      // This also matches HOME/END on OSX which is CMD+LEFT, CMD+RIGHT
      if (e.keyCode == KEY.LEFT || e.keyCode == KEY.RIGHT || e.keyCode == KEY.HOME || e.keyCode == KEY.END) {
        // Defer execution to ensure carat pos has changed after HOME/END keys
        _.defer(resetBuffer);
        return;
      }

      if (e.keyCode == KEY.BACKSPACE) {
        inputBuffer = inputBuffer.slice(0, -1 + inputBuffer.length); // Can't use splice, not available in IE
        return;
      }

      if (!elmAutocompleteList.is(':visible')) {
        return true;
      }

      switch (e.keyCode) {
        case KEY.UP:
        case KEY.DOWN:
          var elmCurrentAutoCompleteItem = null;
          if (e.keyCode == KEY.DOWN) {
            if (elmActiveAutoCompleteItem && elmActiveAutoCompleteItem.length) {
              elmCurrentAutoCompleteItem = elmActiveAutoCompleteItem.next();
            } else {
              elmCurrentAutoCompleteItem = elmAutocompleteList.find('li').first();
            }
          } else {
            elmCurrentAutoCompleteItem = $(elmActiveAutoCompleteItem).prev();
          }

          if (elmCurrentAutoCompleteItem.length) {
            selectAutoCompleteItem(elmCurrentAutoCompleteItem);
          }

          return false;

        case KEY.RETURN:
        case KEY.TAB:
          if (elmActiveAutoCompleteItem && elmActiveAutoCompleteItem.length) {
            elmActiveAutoCompleteItem.click();
            return false;
          }

          break;
      }

      return true;
    }

    function hideAutoComplete() {
      elmActiveAutoCompleteItem = null;
      elmAutocompleteList.empty().hide();
    }

    function selectAutoCompleteItem(elmItem) {
      elmItem.addClass(settings.classes.autoCompleteItemActive);
      elmItem.siblings().removeClass(settings.classes.autoCompleteItemActive);

      elmActiveAutoCompleteItem = elmItem;
    }

    function populateDropdown(query, results) {
      elmAutocompleteList.show();

      // Filter items that has already been mentioned
      var mentionValues = _.pluck(mentionsCollection, 'value');
      results = _.reject(results, function (item) {
        return _.include(mentionValues, item.name);
      });

      if (!results.length) {
        hideAutoComplete();
        return;
      }

      elmAutocompleteList.empty();
      var elmDropDownList = $("<ul>").appendTo(elmAutocompleteList).hide();

      _.each(results, function (item) {
        var elmListItem = $(settings.templates.autocompleteListItem({
          'id'      : utils.htmlEncode(item.id),
          'display' : utils.htmlEncode(item[settings.display]),
          'type'    : utils.htmlEncode(item.type),
          'content' : utils.highlightTerm(utils.htmlEncode((item.name)), query)
        }));

        if (settings.showAvatars) {
          var elmIcon;

          if (item.avatar) {
            elmIcon = $(settings.templates.autocompleteListItemAvatar({ avatar : item.avatar }));
          } else {
            elmIcon = $(settings.templates.autocompleteListItemIcon({ icon : item.icon }));
          }
          elmIcon.prependTo(elmListItem);
        }
        elmListItem = elmListItem.appendTo(elmDropDownList);
      });

      elmAutocompleteList.show();
      elmDropDownList.show();
    }

    function doSearch(query, triggerChar) {
      if (query && query.length && query.length >= settings.minChars) {
        settings.onDataRequest.call(this, 'search', query, function (responseData) {
          populateDropdown(query, responseData);
          elmInputBox.data('triggerChar', triggerChar);
        }, triggerChar);
      }
    }

    // Public methods
    return {
      init : function (options) {
        settings = options;

        initTextarea();
        initAutocomplete();
        initMentionsOverlay();
      },

      val : function (callback) {
        if (!_.isFunction(callback)) {
          return;
        }
        var value = mentionsCollection.length ? elmInputBox.data('messageText') : getInputBoxValue();
        callback.call(this, value);
      },

      reset : function () {
        elmInputBox.val('');
        mentionsCollection = [];
        updateValues();
      },

      getMentions : function (callback) {
        if (!_.isFunction(callback)) {
          return;
        }

        callback.call(this, mentionsCollection);
      }
    };
  };

  $.fn.mentionsInput = function (method, settings) {

    if (typeof method === 'object' || !method) {
      settings = $.extend(true, {}, defaultSettings, method);
    }

    var outerArguments = arguments;

    return this.each(function () {
      var instance = $.data(this, 'mentionsInput') || $.data(this, 'mentionsInput', new MentionsInput(this));

      if (_.isFunction(instance[method])) {
        return instance[method].apply(this, Array.prototype.slice.call(outerArguments, 1));

      } else if (typeof method === 'object' || !method) {
        return instance.init.call(this, settings);

      } else {
        $.error('Method ' + method + ' does not exist');
      }

    });
  };

})(jQuery, _);