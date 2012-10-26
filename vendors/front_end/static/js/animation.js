var decalage = 0;
var nb = -1;
var objJson;
var couleur = new Array('#ff9c00','#ffd700','#3bd322','#419ac2','#f80077','#ff9c00','#ffd700','#3bd322','#419ac2','#f80077');

var getSearchCriteria = function(name) {
    searchCriteria = JSON.parse($.cookie('searchCriteria'));
    try {
        value = eval('searchCriteria.' + name);
        if (typeof value != 'undefined') return value;
    } catch (Exception) {
        return null;
    }
    return null;
}

$(document).ready(function() {
    loadPost(false);
    document.addEventListener("keydown", function(e) {
      if (e.keyCode == 122) {
        pleinEcran();
      }
    }, false);
});

function loadPost (isReload) {
    $('.roue').find('span').html('').attr('data-guid',null).attr('data-user',null);
    $.ajax({
        type: 'GET'
        , url: '/services/api/rest/json'
        , dataType: 'json'
        , data: {
            method: 'coffee.getPosts'
            , auth_token: $.cookie('authToken')
            , offset: 0
            , limit: 10
            , tags: getSearchCriteria('tags')
            , owner_guids: getSearchCriteria('users')
        },
        success: function (response) {
            objJson = [];
            decalage = 0;
            objJson = response.result;
            if (response.status != -1) {
                nb = objJson.length;
                $.each(objJson, function(i,item) {
                    $('.roue span:eq('+i+')').html(item.user.name).attr('data-guid',item.guid).attr('data-user',item.user.name);
                });
                if (!isReload)
                    startRoue();
            } else {
                /* Error */
            }
        }
    });
}


// Premier demarrage

function startRoue()
{
	$('.roue').animate({ opacity:1 },1000, function() {
		$('.roue').addClass('tournerRoue');
	});
	setTimeout("animationTxt(0)",6000);
	setTimeout("flip()",5000);
}

// Rotation du logo

function flip() {
    return;
	$('#logo').toggleClass('flip');
	setTimeout("flip()",5000);
}

// Animation de l'username selectionné

function animationTxt(id) {
	$('#usernameBlanc').html($('.roue span:first').attr('data-user'));

	$('#usernameBlanc').animate({ opacity:1 },1000, function() {});
	$('.roue').animate({ opacity:0 },1000, function() {
		$('#usernameBlanc').animate({ top:'8px', left:0 },1000, function() {$('#usernameBlanc').addClass('animate');});
	});
	setTimeout('animerPost('+id+')',2000);
}

// Animation du post

function animerPost(id) {
    if (typeof objJson[id] == 'undefined') arreterRoue ();
	var post = objJson[id];
	$('#logo').animate({ opacity:1},1000, function() {
		$('#icon_url').attr('src',post.user.icon_url);
		$('#fond_icon_url').attr('src',post.user.cover_url);
		$('#img, #usernameBlanc').addClass("decalageTop");
		$('#logo').animate({ opacity:1},1100, function() {
			var seconds = 6500;
			$('#usernameBlanc').addClass('transitionTxt'); // post.user.icon_url
			//$('#likes').html(post.likes.total).show('blind');
			if(post.content.text.length > 140)
				$('#text').html(post.content.text.replace(/<br \/>/g," ").substr(0,140) + ' ...').show('blind');
			else
				$('#text').html(post.content.text.replace(/<br \/>/g," ")).show('blind');
			$('#fond_icon_url').fadeIn();
			$('#friendly_time').html(post.content.friendly_time).show('blind');
			if(post.attachment != false) {
				$('#marges').removeClass('link image video');
				if(post.attachment[0].type == "image") {
					$('#marges').addClass('image');
					$('#miniatureAtt').html('<img src="' + post.attachment[0].thumbnail + '" class="gloss" />');
					setTimeout("arreterRoue()",seconds);
				} else {
                    $('#miniatureAtt').html('<a href="' + post.attachment[0].url + '" target="_blank"><img src="' + post.attachment[0].thumbnail + '" class="gloss" /></a>');
					// Video ?
					var idVideo = post.attachment[0].url.replace(/http:\/\/www.youtube.com\/watch\?v=/gi,"");
					if(idVideo != post.attachment[0].url) {
						// Video
						$('#marges').addClass('video');
					} else {
						$('#marges').addClass('link');
						$('#titreAtt').html(post.attachment[0].title);
                        if(post.attachment[0].description.length > 140)
                            $('#descAtt').html(post.attachment[0].description.substr(0,140) + ' ...').show('blind');
                        else
                            $('#descAtt').html(post.attachment[0].description.replace(/<br \/>/g," ")).show('blind');
					}
                    setTimeout("arreterRoue()",seconds);
				}
				$('#attachment').fadeIn();
			} else {
				$('#attachment').hide('blind');
				setTimeout("arreterRoue()",seconds);
			}
            if (id==9) {
                loadPost(true);
            }
			});
		});
}

// Reinitialisation
function arreterRoue() {
	$('.roue').removeClass('tournerRoue');
	$('#img, #usernameBlanc').removeClass("decalageTop");
	$('#usernameBlanc').hide('blind');
	setTimeout("decalerSpan()",1500);
	$('#likes, #text, #friendly_time, #attachment, #fond_icon_url').hide('blind');
	$('#usernameBlanc').animate({ opacity:0},1500,null, function() {
		$('#usernameBlanc').removeClass('animate');
		$('#usernameBlanc').css('top','auto').css('left','auto');
		$('#usernameBlanc').show('slow');
	});
	$('#miniatureAtt, #titreAtt, #descAtt, #typeAtt').html('');
	$('#cadre').animate({ backgroundColor: couleur[decalage] },1000);
}

// Decalage de la roue (+1)

function decalerSpan() {
	$('.roue').animate({ opacity:1 },300, function() { $('#usernameBlanc').html(''); $('.roue').addClass('tournerRoue');});
	decalage++;
	$('.roue span:first').appendTo('.roue');
	if(decalage >= nb)
	{
		decalage = 0;
	}
	setTimeout('animationTxt('+decalage+')',6000);
}

// Plein écran
function pleinEcran() {

if ((document.fullScreenElement && document.fullScreenElement !== null) ||    // alternative standard method
      (!document.mozFullScreenElement && !document.webkitFullScreenElement)) {  // current working methods
    if (document.documentElement.requestFullScreen) {
      document.documentElement.requestFullScreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullScreen) {
      document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.cancelFullScreen) {
      document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
      document.webkitCancelFullScreen();
    }
  }
}