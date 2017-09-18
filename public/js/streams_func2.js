<script>
function recommend(el, stream, eventID, action ) {
    var url = "{{ secure_url('recommend') }}";
    $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "stream": stream, "event": eventID, "action": action },
        cache: false,
        success: function (data) {
            var tr = el.closest('.stream_block');
            if( action == 1 ){
                tr.className += " highlight";

                $html = '<a href="javascript:void(0);" onclick="recommend(this,\''+stream+'\', \''+eventID+'\', 0 )" title="Undo Recommend"><i class="fa fa-hand-o-down" style="color: red" aria-hidden="true"></i></a>';

                $(el).parent().html( $html );
                $(tr).attr("data-toggle", "tooltip");
                $(tr).attr("data-placement", "right");
                $(tr).attr("data-original-title", "Recommended");
                $(tr).tooltip('show');
                swal({title: "Successfully recommended.", type: "success"});
            }
            else{
                tr.classList.remove('highlight');

                $html = '<a href="javascript:void(0);" onclick="recommend(this,\''+stream+'\', \''+eventID+'\', 1 )" title="Recommend"><i class="fa fa-hand-o-up" style="color: red" aria-hidden="true"></i></a>';

                $(el).parent().html( $html );
                $(tr).removeAttr('data-toggle');
                $(tr).removeAttr('data-original-title');

                $(tr).tooltip('hide');
                swal({title: "Unrecommended.", type: "success"});
            }
        }
    });
}

function banUserAction(el, streamId,e) {
    e.stopPropagation();
    swal({
            title: "Are you sure?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Ban this user!",
            closeOnConfirm: false
        },
        function () {
            $.get(
                $(el).attr('data-href'),
                {"_token": "{{ csrf_token() }}"},
                function (data, status) {
                    console.log(data)
                });
            $(el).closest('tr').slideUp('slow');
            swal("Done!", "Successfully.", "success");
        });
}

function deleteComment(commentId) {
    var $this = $('#comment_'+commentId);
    swal({
            title: "Are you sure?",
            text: "You will not be able to recover this comment!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        },
        function () {
            var deleteURL = '{{ secure_url('deleteComment') }}';
            axios.post(deleteURL, {
                id: commentId,
            }).then(function (response) {
                if ($this.parent().parent().hasClass('stream_comments') && $this.parent().parent().find('.media').length == 1) {
                    $this.parent().parent().addClass('hidden');
                }
                if(!$this.parent().hasClass('replies') && $this.parent().parent().parent().find('.stream_comment_row').length){
                    var comments_count_div = $('tr[data-stream-id='+ $this.parent().parent().find('.stream_comment_row').data('stream-id') +'] .comments_count');
                    var comments_count = parseInt(comments_count_div.text())-1;
                    comments_count_div.text(comments_count);
                }
                $('#comment_' + commentId).remove();
                swal("Deleted!", "Comment has been deleted.", "success");
            });
        });
}

function banDomainAction(el, streamId,e) {
    e.stopPropagation();
    swal({
            title: "Are you sure?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Ban it!",
            closeOnConfirm: false
        },
        function () {
            console.log( $(el).attr('data-href') );
            $.get(
                $(el).attr('data-href'),
                {"_token": "{{ csrf_token() }}"},
                function (data, status) {
                    console.log(data);
                    location.reload();
                });
            swal("Banned!", "Successfully", "success");
        });
}

function sendMessage( el, userId, e, url )
{
    e.stopPropagation();
    alertify.prompt('Send Message', 'You can send message to this user.', ''
        , function (evt, value) {
            if( !value ){
                alertify.error('Empty message.');
                return;
            }

            console.log( value );
            $.post(
                '{{ secure_url('/profile/messages/send') }}',
            {"_token": "{{ csrf_token() }}", "userId": userId, "body": value, "permalink": url},
                function (data, status) {
                    console.log(data)
                    alertify.success('Message sent')
                });
        }
        , function () {
            alertify.error('Cancel')
        });
}
</script>