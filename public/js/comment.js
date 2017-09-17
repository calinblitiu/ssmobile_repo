$('[data-toggle="collapse"]').on('click', function () {
  var $this = $(this),
    $parent = $this.data('parent');
  if ($this.data('parent') === undefined) { /* Just toggle my  */
    $this.find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
    return true;
  }

  /* Open element will be close if parent !== undefined */
  var currentIcon = $this.find('.glyphicon');
  currentIcon.toggleClass('glyphicon-plus glyphicon-minus');
  $parent.find('.glyphicon').not(currentIcon).removeClass('glyphicon-minus').addClass('glyphicon-plus');

});

function setCharAt(str,index,chr) {
  // console.log(chr);
    if(index > str.length-1) return chr;
    return str.substr(0,index) + chr + str.substr(index+1);
}

function isStringReg(term){
  var return_term ='';
  var return_firstchar;
  var firstchar = term.charAt(0);
  return_firstchar = firstchar.indexOf(firstchar.toUpperCase()) === 0 ? firstchar.toLowerCase():firstchar.toUpperCase();
  return_term = setCharAt(term,0,return_firstchar);
  return return_term;
}

function operator(){
  // console.log("dddddd");
  var current_time = Date.now();
  $('span.comment-post-time').each(function(){
        var post_time = $(this).attr('data-created');
        var time_stamp = parseInt(current_time/1000 - post_time);
        var days = parseInt(time_stamp/60/60/24);
        var hours = parseInt(time_stamp/60/60) - 24*days;
        var minutes = parseInt(time_stamp/60) - 60*hours - 60*24*days;
        var display_time = "";
        if(minutes != 0 ){
          if(days != 0){
            display_time += days+" days ";
          }
          if(hours != 0){
            display_time += hours+" hours ";
          }
          display_time += minutes+" minutes ago";
        }
        if(minutes == 0){
          display_time += "Just now";
        }
        $(this).text(display_time);
    }
  )
}

$(function ($) {

  setInterval( function(){
    operator();
  }, 60000);

  $.fn.editable.defaults.params = function (params) {
    params._token = $("#_token").data("token");
    return params;
  };
  $.fn.editable.defaults.mode = 'inline';
  $('.editable').editable();



  $('body').delegate('#postComment', 'submit', function (e) {
    e.preventDefault();

	var comment_text = $('<div>'+$(this).find('textarea[name=comment]').val()+'</div>').text();

    // if ($(this).context[3].value != '') {
    if (comment_text != '') {
      var form = $(this);
      var formAction = $(this).attr("action");
      var data = $(this).serialize();
      axios.post(formAction, data)
      .then(function (response) {
        response.data = response.data.replace(/&lt;/g,'<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
        var html = $.parseHTML(response.data);

        $('#postComment').hide();
        $('#comments-div').prepend(html);
        if ($('.no-comments').length) {
          $('.no-comments').remove();
        }
        var newCount = $(html).find(' [data-comment-count]').data('commentCount');
        $('.comment-count span').text(newCount);

        form[0].reset();

		$('textarea').sceditor({
						toolbar: 'bold,italic,underline,color,emoticon',
						style: '/css/jquery.sceditor.default.css',
						height: 150,
						width: '95%'
					});

      })
      .catch(function (error) {
        console.log(error);
      });
    }

  });

  $('body').delegate('.comment-reply-form', 'submit', function (e) {
    e.preventDefault();
    // if ($(this).context[3].value != '') {
    if ($(this).find('textarea[name=comment]').val() != '') {
      var replyForm = $(this);
      var formAction = $(this).attr("action");
      var parent = replyForm.context[2].value;
      var data = $(this).serialize();
      axios.post(formAction, data)
      .then(function (response) {
        response.data = response.data.replace(/&lt;/g,'<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
        var html = $.parseHTML(response.data);

        if ($("#replies_" + parent).length) {
          $('#replies_' + parent).prepend(html);
        }else{
          $("#comment_" + parent).append($.parseHTML('<div class="replies" id="replies_'+ parent +'" data-comment-id = "'+ parent +'">'+response.data+'</div>'));
        }
        replyForm[0].reset();
        replyForm.parent().parent().find('.reply_button').click();

        // Update Posts count
        var newCount = $(html).find('[data-comment-count]').data('commentCount');
        $('.comment-count span').text(newCount);

		$('textarea').sceditor({
						toolbar: 'bold,italic,underline,color,emoticon',
						style: '/css/jquery.sceditor.default.css',
						height: 150,
						width: '95%'
					});

		/*
        tinymce.init({
        selector: 'textarea',
        height: 200,
        theme: 'modern',
        plugins: [
          'advlist autolink lists link image charmap print preview hr anchor pagebreak mention',
          'searchreplace wordcount visualblocks visualchars code fullscreen',
          'insertdatetime media nonbreaking save table contextmenu directionality',
          'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help',
          ],
          menubar: false,
          toolbar: 'bold italic forecolor Blockquote emoticons',
          image_advtab: true,
          templates: [
            { title: 'Test template 1', content: 'Test 1' },
            { title: 'Test template 2', content: 'Test 2' }
          ],
          content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tinymce.com/css/codepen.min.css'
          ],
          mentions: {
            source: function (query, process, delimiter) {
                if (delimiter === '@') {
                   process(elements);
                }
            },
            insert: function(item) {
                return '&nbsp;@' + item.name + ' &nbsp;';
            }
          },
          setup: function (editor) {
              editor.on('change', function () {
                  editor.save();
              });
            }
         });
		 */
      })
      .catch(function (error) {
        console.log(error);
      });
    }
  });

  $('body').delegate('.comment-quota-form', 'submit', function (e) {
    e.preventDefault();
    // if ($(this).context[3].value != '') {
    if ($(this).find('textarea[name=comment]').value != '') {
      var quotaForm = $(this);
      var formAction = $(this).attr("action");
      var parent = quotaForm.context[2].value;
      var data = $(this).serialize();
      axios.post(formAction, data)
      .then(function (response) {
        response.data = response.data.replace(/&lt;/g,'<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
        var html = $.parseHTML(response.data);

        if ($("#replies_" + parent).length) {
          $('#replies_' + parent).prepend(html);
        }else{
          $("#comment_" + parent).append($.parseHTML('<div class="replies" id="replies_'+ parent +'" data-comment-id = "'+ parent +'">'+response.data+'</div>'));
        }
        quotaForm[0].reset();
        quotaForm.parent().parent().find('.quota_button').click();

        // Update Posts count
        var newCount = $(html).find('[data-comment-count]').data('commentCount');
        $('.comment-count span').text(newCount);

		$('textarea').sceditor({
						toolbar: 'bold,italic,underline,color,emoticon',
						style: '/css/jquery.sceditor.default.css',
						height: 150,
						width: '95%'
					});

		/*
        tinymce.init({
        selector: 'textarea',
        height: 200,
        theme: 'modern',
        plugins: [
          'advlist autolink lists link image charmap print preview hr anchor pagebreak mention',
          'searchreplace wordcount visualblocks visualchars code fullscreen',
          'insertdatetime media nonbreaking save table contextmenu directionality',
          'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help',
          ],
          menubar: false,
          toolbar: 'bold italic forecolor Blockquote emoticons',
          image_advtab: true,
          templates: [
            { title: 'Test template 1', content: 'Test 1' },
            { title: 'Test template 2', content: 'Test 2' }
          ],
          content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tinymce.com/css/codepen.min.css'
          ],
        mentions: {
            source: function (query, process, delimiter) {
                if (delimiter === '@') {
                   process(elements);
                }
            },
            insert: function(item) {
                return '&nbsp;@' + item.name + ' &nbsp;';
            }
          },
          setup: function (editor) {
              editor.on('change', function () {
                  editor.save();
              });
            }
         });
		*/
      })
      .catch(function (error) {
        console.log(error);
      });
    }
  });
  $('body').delegate('.comment-edit-form', 'submit', function (e) {
      e.preventDefault();
      if ($(this).find('textarea[name=comment]').val() != '') {
        // console.log($(this));
        var editForm = $(this);
        var formAction = $(this).attr("action");
        var parent = editForm.context[2].value;
        var data = $(this).serialize();
        // console.log(data);
        axios.post(formAction, data)
        .then(function (response) {
          console.log(response.data);
          if(response.statusText == 'OK'){
            editForm.parent().parent().find('.edit_button').click();
            var setResultContent = editForm.parent().parent().find('div.comment_content');
            setResultContent.html(response.data);
          //   // console.log(editForm.find('textarea[name=comment]').val());
          }
        })
        .catch(function (error) {
          console.log(error);
        })
      }
  });
});

function editComment(e,commentId,itself) {
  var editContents = $(itself).parent().parent().parent().parent().find('div#commentContent_'+commentId).html();
  var editTextarea = $(itself).parent().parent().parent().parent().find('textarea#texteditor_'+commentId);
  $(editTextarea).val(editContents);
  //tinymce.get("texteditor_"+commentId).setContent(editContents);
}

function validateEdit(value){
  alert('hi');
  if($.trim(value) == '') {
    return 'This field is required';
  }
}

function deleteComment(id){
  axios.post("/deleteComment", {commentId:id})
  .then(function (response) {
    if (response.data.deleted) {
      $("#comment_"+ id).remove();
    }
  })
  .catch(function (error) {
    console.log(error);
  });
}
