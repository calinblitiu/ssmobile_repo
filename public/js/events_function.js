function myFunction(){
    var matcher = $("#country_selector").val();
    $(".tv_row_channel").each(function(i, val){
        var dump = $(val).find("#country_name").text();
        if(matcher==dump || matcher=="All")
        {
            $(val).css({"display":"inline"});
        }
        else
        {
            $(val).css("display","none");
        }
    });

}
$('textarea').sceditor({
    toolbar: 'bold,italic,underline,color,emoticon',
    style: '/css/jquery.sceditor.default.css',
    height: 150,
    width: '95%'
});

setInterval(function(){
    $('.sceditor-container iframe').each(function(){
        $(this).width('95%');
    });
}, 1000);


function CommentVoteUp(el, comment_id) {
    el = $(el);
    var url = "{{ secure_url('comment_vote') }}";
    $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "comment_id": comment_id},
        cache: false,
        success: function (data) {
            if (data.msg) {
                swal({title: data.msg, type: "error"});
                return;
            }
            el.parent().parent().find('.done').show();
            var votes = parseInt(el.parent().parent().find('.votes_count').html())+1;
            if(el.parent().parent().hasClass('stream_comment')){
                el.closest('.stream_comments').attr('data-votes', votes);
            }else{
                el.parent().parent().attr('data-votes', votes);
            }
            el.parent().parent().find('.votes_count').text(votes);
            el.parent().parent().attr('data-votes', votes);
            el.hide();
            swal({title: "Thank you!", type: "success"});
            refresh_comment_votes();
        },
        error: function (data) {
            sweetAlert('Oops...', 'Only registered user have the ability to vote!', 'error');
        }
    });
}

function CommentVoteDown(el, comment_id) {
    el = $(el);
    var url = "{{ secure_url('comment_vote_down') }}";
    $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "comment_id": comment_id},
        cache: false,
        success: function (data) {
            if (data.msg) {
                swal({title: data.msg, type: "error"});
                return;
            }
            el.parent().parent().find('.vot').show();
            var votes = parseInt(el.parent().parent().find('.votes_count').html())-1;
            if(el.parent().parent().hasClass('stream_comment')){
                el.closest('.stream_comments').attr('data-votes', votes);
            }else{
                el.parent().parent().attr('data-votes', votes);
            }
            el.parent().parent().find('.votes_count').text(votes);
            el.hide();
            swal({title: "Thank you!", type: "success"});
            refresh_comment_votes();
        },
        error: function (data) {
            sweetAlert('Oops...', 'Only registered user have the ability to vote!', 'error');
        }
    });
}

function refresh_comment_votes() {
    var sortedDivs = $(".parent_comment:not(.stream_comment)").toArray().sort(sorter);
    $.each(sortedDivs, function (index, value) {
        $("#comments-div").append(value);
    });
}

function sorter(a, b) {
    var contentA =parseInt( $(a).attr('data-votes'));
    var contentB =parseInt( $(b).attr('data-votes'));
    contentB = (contentB)?contentB:0;
    contentA = (contentA)?contentA:0;
    return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0
}

(function () {
    refresh_comment_votes();
    // $('.parent_comment .replies').first().css('margin-left', '10px');
    $('[data-toggle="tooltip"]').tooltip();
    var filters = {
        type: null,
        quality: null,
        language: null,
        mobile: null
    };

    function updateFilters() {
        $('.clickable-row').hide().filter(function () {
            var self = $(this), result = true;

            Object.keys(filters).forEach(function (filter) {
                if (filters[filter] && (filters[filter] != 'None') && (filters[filter] != 'Any')) {
                    result = result && filters[filter] === self.data(filter);
                }
            });

            return result;
        }).show();
    }

    function bindDropdownFilters() {
        Object.keys(filters).forEach(function (filterName) {
            $('#' + filterName + '-filter').on('change', function () {
                if (filterName == 'mobile') {
                    filters[filterName] = $(this).prop('checked') ? '' : 'Yes';
                    console.log(filters[filterName]);
                } else {
                    filters[filterName] = this.value;
                    console.log(filters[filterName]);
                }

                updateFilters();
            });
        });
    }

    bindDropdownFilters();
})();

$(function ($) {
    $("#getting-started")
        .countdown( new Date().getTime() + {{ $offset_start }}, {elapse: true})
    .on('update.countdown', function (event) {
        var $this = $(this);
        if (event.elapsed) {
            $('#fake_count').hide();
            $('#real_count').show();
            $this.html('');
            $this.countdown('stop');
        } else {
            $('#fake_count').show();
            $('#real_count').hide();
            $this.html(
                event.strftime('<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><span>%D</span>Days</div>' +
                    '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><span>%H</span>Hours</div>' +
                    '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><span>%M</span>Minutes</div>' +
                    '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><span>%S</span>Seconds</div><div class="clearfix"></div>')
            );
        }
    });
$("#getting-finished")
    .countdown( new Date().getTime() + {{ $offset_end }}, {elapse: true})
.on('update.countdown', function (event) {
    var $this = $(this);
    if (event.elapsed) {
        $('#fake_count').remove();
        $('#real_count').remove();
        $('#match_end').show();
        $this.html('');
        $this.countdown('stop');
        $('#streams-table tbody').html('<tr><td colspan="11" align=center>This match is ended!</td></tr>');
    }
});

$('#offset').change(function(){
    console.log($(this).val());
    var oldTime = $('#eventTime').attr('data-eventtime');
    console.log(oldTime);
    var utcStart = moment.utc(oldTime).utcOffset('UTC');
    console.log(utcStart);
    var startDate = utcStart.utcOffset($(this).val() * 60).format('HH:mm');
    console.log(startDate);
    $('#eventTime').html(startDate);
});
});

function voteUp(el, stream,eventId) {
    if( $(el.parentElement).hasClass('disabled') )
        return;
    var url = "{{ secure_url('vote') }}";
    $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "stream": stream,"eventId":eventId},
        cache: false,
        success: function (data) {
            var rate = el.parentElement.nextElementSibling.innerText;
            if( data == 1 )
                $(el.parentElement).toggleClass('disabled');

            $(el.parentElement.nextElementSibling.nextElementSibling).removeClass('disabled');
            el.parentElement.nextElementSibling.innerHTML = parseInt(rate) + 1;
            swal({title: "Thank you!", type: "success"});
        }
    });
}

function voteDown(el, stream, eventId) {
    if( $(el.parentElement).hasClass('disabled') )
        return;
    var url = "{{ secure_url('voteDown') }}";
    $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "stream": stream,"eventId":eventId},
        cache: false,
        success: function (data) {
            var rate = el.parentElement.previousElementSibling.innerText;
            if( data == 1 )
                $(el.parentElement).toggleClass('disabled');

            $(el.parentElement.previousElementSibling.previousElementSibling).removeClass('disabled');
            el.parentElement.previousElementSibling.innerHTML = parseInt(rate) - 1;
            swal({title: "Thank you!", type: "success"});
        }
    });
}

function getEventComments() {
    var eventId = "{{ $event->event_id }}";
    var url = "{{ secure_url('getEventComments') }}";
    var orderType = $('#commentSort').val();
    $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "eventId":eventId, "orderType": orderType},
        cache: false,
        success: function (data) {
            $('.panel-group.panel-comments').html(data);

            $('textarea').sceditor({
                toolbar: 'bold,italic,underline,color,emoticon',
                style: '/css/jquery.sceditor.default.css',
                height: 150,
                width: '95%'
            });

            /*
             tinymce.init({
             selector: 'textarea',
             height: 100,
             theme: 'modern',
             plugins: [
             'advlist autolink lists link image charmap print preview hr anchor pagebreak',
             'searchreplace wordcount visualblocks visualchars code fullscreen',
             'insertdatetime media nonbreaking save table contextmenu directionality',
             'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
             ],
             menubar: false,
             toolbar: 'bold italic forecolor backcolor emoticons',
             image_advtab: true,
             templates: [
             { title: 'Test template 1', content: 'Test 1' },
             { title: 'Test template 2', content: 'Test 2' }
             ],
             content_css: [
             '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
             '//www.tinymce.com/css/codepen.min.css'
             ],
             setup: function (editor) {
             editor.on('change', function () {
             editor.save();
             });
             }
             });
             */
        }
    });
}

function report(el, stream,eventId) {
    var url = "{{ secure_url('report') }}";
    swal({
            title: "Report a stream!",
            text: "Please add a comment before submit your report:",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Write something"
        },
        function (inputValue) {
            if (inputValue === false) return false;

            if (inputValue === "") {
                swal.showInputError("You need to write something!");
                return false
            } else {
                $.ajax({
                    url: url,
                    type: "post",
                    data: {"_token": "{{ csrf_token() }}", "stream": stream,"eventId":eventId, "comment": inputValue},
                    cache: false,
                    success: function (data) {
                        $(el).hide();
                        $(el.parentElement.lastElementChild).show();
                    }
                });
            }

            swal("Thank you!");
        });

}

function comments(stream) {
    var url = "{{ secure_url('getComments') }}";
    axios.get(url + '/' + stream)
        .then(function (response) {
            $('#commentsModal').modal();
            $('#modal-body').html(response);
            $('#commentsModal').on('shown.bs.modal', function () {
                $('#commentsModal .modal-body').html(response);
                $('#modal-body').html(response);
            });
            $('#commentsModal').on('hidden.bs.modal', function () {
                $('#commentsModal .modal-body').data('');
            });
            console.log(response);
        })
        .catch(function (error) {
            console.log(error);
        });
}

function addComment(el, stream_id) {
    var comment = el.find('textarea').val();
    if (comment == '' || stream_id == '') {
        sweetAlert("Oops...", "Can't leave an empty comment!", "error");
        return false;
    }
    axios.post("{{ secure_url('saveComment') }}", {
        stream_id: stream_id,
        comment: comment,
        event_id: "{{ $event->event_id }}"
    })
        .then(function (response) {
            $('.comment').val('');
            $('#commentsModal').modal('hide');
            $('#collapseComments').addClass('in');
            if(stream_id){
                $('.stream_'+stream_id).parent().find('.comments').prepend(response.data);
                $('.stream_'+stream_id).parent().removeClass('hidden');
                $('.stream_'+stream_id+":not(.media-body .stream_"+ stream_id +")").addClass('hidden');
                var comments_count_div = $('tr[data-stream-id="'+ stream_id +'"]').find('.comments_count');
                var comments_count = parseInt(comments_count_div.text())+1;
                comments_count_div.text(comments_count);
            }
            $('.parent_comment .replies').first().css('margin-left', '10px');
        })
        .catch(function (error) {
            console.log(error);
        });
}

function stream_comment_init(stream_id) {
    if($('.stream_'+stream_id).parent().hasClass('media-body')){
        $('.stream_'+stream_id).parent().find('.reply_button').first().click();
        $('hmtl, body').scrollTop($('.stream_'+stream_id).parent().find('.reply_button').offset().top);
    }else{
        $('.stream_'+stream_id).parent().removeClass('hidden');
        $('.stream_'+stream_id).removeClass('hidden');
        $('.stream_comment_row.stream_'+stream_id).parent().find('.streamCommentAdd').show();
        $('hmtl, body').animate({
            scrollTop: $('.stream_'+stream_id).parent().find('.streamCommentAdd').offset().top
        }, 1000);
    }
}

function streamAction(el, streamId) {
    swal({
            title: "Are you sure?",
            text: "You will not be able to recover this stream!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        },
        function () {
            $.post(
                $(el).attr('data-href'),
                {"_token": "{{ csrf_token() }}"},
                function (data, status) {
                    console.log(data)
                });
            if( $(el).closest('.stream_block').length > 0 )
                $(el).closest('.stream_block').hide();
            else
                $(el).closest('tr').slideUp('slow');
            swal("Deleted!", "Stream has been deleted.", "success");
        });
}