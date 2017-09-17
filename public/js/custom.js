$(document).ready(function() {
  // $('div.comment-sort').css('top',$('.panel-heading#panel-comment').outerHeight()/2+1-$('div.comment-sort').outerHeight()/2)
  $(".floating-button-toggle,.background-overlay").click(function(){
		if($(".background-overlay").is(":visible")){
			$(".background-overlay").fadeOut(125);
      $('.floating-button>a').css('display','none');
		}else{
			$(".background-overlay").fadeIn(125);
      $('.floating-button>a').css('display','block');
		}
	});

  $(".background-overlay").click(function(){
      $('.plus').prop( "checked", false );
	});

  if($(window).height() > $('.main-content').outerHeight()+$('.footer').outerHeight()){
      $('footer.footer').css('position','fixed');
  }
  if($(window).height() < $('.main-content').outerHeight()+$('.footer').outerHeight()){
      $('footer.footer').css('position','static');
  }
  $(".main-content").change('height', function() {
    if($(window).height() > $('.main-content').outerHeight()+$('.footer').outerHeight()){
        $('footer.footer').css('position','fixed');
    }
    else{
      $('footer.footer').css('position','static');
    }
  });
    var usercommentcount=$('.comment-count').text();
    usercommentcount=parseInt(usercommentcount);

    var star1 = '<i class="fa glyphicon glyphicon-star"></i>'
    var star2 = '<i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i>'
    var star2_5 = '<i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star half"></i>'
    var star3 = '<i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i>'
    var star3_5 = '<i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star half"></i>'
    var star4 = '<i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i>'
    var star4_5 = '<i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star half"></i>'
    var star5 = '<i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i><i class="fa glyphicon glyphicon-star"></i>'

    if ((usercommentcount >= 0) && (usercommentcount < 50)) {
        $('.comment-star').append('');
    } else if ((usercommentcount >= 50 ) && (usercommentcount < 100 )) {
        $('.comment-star').append(star1);
    } else if ((usercommentcount >= 100 ) && (usercommentcount < 125 )) {
        $('.comment-star').append(star2);
    } else if ((usercommentcount >= 125 ) && (usercommentcount < 150 )) {
        $('.comment-star').append(star2_5);
    } else if ((usercommentcount >= 150 ) && (usercommentcount < 175 )) {
        $('.comment-star').append(star3);
    } else if ((usercommentcount >= 175 ) && (usercommentcount < 200 )) {
        $('.comment-star').append(star3_5);
    } else if ((usercommentcount >= 200 ) && (usercommentcount < 225 )) {
        $('.comment-star').append(star4);
    } else if ((usercommentcount >= 225 ) && (usercommentcount < 250 )) {
        $('.comment-star').append(star4_5);
    } else {
        $('.comment-star').append(star5);
    }


    $('#side-menubar,.background-overlay-sidebar').click(function(){
      $('.mobile-show').toggleClass(' open-side-menu');
      $('.move-left-delay').toggleClass('move-left-content');
      $('#mobile-view-content').toggleClass(' move-left-content');
      $('.footer').toggleClass(' move-left-footer');
      if($(".background-overlay-sidebar").css('display') == 'none'){
        $(".background-overlay-sidebar").css('display','block');
        $(".background-overlay-sidebar").animate({opacity: 0.7}, 250);
      }
      else{
        $(".background-overlay-sidebar").css('display','none');
        $(".background-overlay-sidebar").animate({opacity: 0}, 250);
      }
    });

    $('.mobile-show-acound-dropdown1').click(function(){
      $('.dropdown1').toggleClass(" open");
      $('.dropdown2').removeClass(" open");
    });
    $('.mobile-show-acound-dropdown2').click(function(){
      $('.dropdown2').toggleClass(" open");
      $('.dropdown1').removeClass(" open");
    });

    var swipe = new Hammer(document);
    // detect swipe and call to a function
    swipe.on('swiperight swipeleft', function(e) {
      e.preventDefault();
      if (e.type == 'swipeleft') {
        $('.background-overlay-sidebar').css('display','block')
        $('.mobile-show').addClass(' open-side-menu');
        $('#mobile-view-content').addClass(' move-left-content');
        $('.move-left-delay').addClass(' move-left-content');
        $('.footer').addClass(' move-left-footer');
        $(".background-overlay-sidebar").animate({opacity: 0.7}, 250);
      } else {
        // close/hide menu
        $('.background-overlay-sidebar').css('display','none')
        $('.mobile-show').removeClass(' open-side-menu');
        $('#mobile-view-content').removeClass(' move-left-content');
        $('.move-left-delay').removeClass(' move-left-content');
        $('.footer').removeClass(' move-left-footer');
        $(".background-overlay-sidebar").animate({opacity: 0}, 250);
      }

    });


    var countryName = $("a[data-option = " + $('.bfh-countries input').val() + "]").text();
    var countryFullName = $('.country-type span').text(countryName);
    $('.country-type span').prepend(countryFullName);

    var $comment = $('.comment-content');
    if ($comment.length >0 ) {
        setInterval(function() {
            // updateCommentTime();
        }, 1000);
    }
});

function updateCommentTime() {
    $created_data = $("[data-created]");

    $created_data.each(function(i, v) {
        var mid = moment($(v).data('created'));
        var now = moment();

        var duration = moment.duration(now.diff(mid));

        var html = '';

        if (duration.days()) {
            html = duration.days() + ' days ago';
        } else {
            if (duration.hours()) {
                html = duration.hours() + ' hours ago';
            } else {
                if (duration.minutes()) {
                    html = duration.minutes() + ' minutes ago';
                } else {
                    if (duration.seconds()) {
                        html = duration.seconds() + ' seconds ago';
                    }
                }
            }

        }
        // var html = duration.days() + ' days ' + duration.hours() + ' hours ' + duration.minutes() + ' mins ' + duration.seconds() + ' secs ago';
        $(v).text(html);
    });

  // if($('div.stream_body').length){
  //   $('div.table-responsive').css('overflow','hidden');
  //   var origin_length = $('div.table-responsive').outerWidth();
  //   var value_length = origin_length-2;
  //   $('div.stream_body').css('width',value_length+"px");
  // }
}

$( window ).resize(function() {
  if($(window).height() > $('.main-content').outerHeight()+$('.footer').outerHeight()){
    $('footer.footer').css('position','fixed');
  }
  else if($(window).height() < $('.main-content').outerHeight()+$('.footer').outerHeight()){
    $('footer.footer').css('position','static');
  }
  // if($('div.stream_body').length){
  //   $('div.table-responsive').css('overflow','hidden');
  //   var origin_length = $('div.table-responsive').outerWidth();
  //   var value_length = origin_length-2;
  //   $('div.stream_body').css('width',value_length+"px");
  // }
});
function assassin_close(){
  $("div.assassin-insert-div").remove();
  $('.background-overlay').css('display','none');
}
