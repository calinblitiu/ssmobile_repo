<tr>
  <td colspan="20" width="100%" class="multiple_streams" id="userstreams_{{$stream->username}}" style="background: #fff; padding-top: 0; border: none; border-bottom: 1px solid #c0c0c0; margin-top: 5px; border-radius: 0;">
  @if( !isset( $groupInfo[ $stream->username ]['displayed'] ))
    <div class="streamUser" style="background: #f0f0f0; padding: 0px 10px; border-top-left-radius: 5px; border-top-right-radius: 5px; margin-bottom: 5px !important;">
        <div class="voting-area">
            @if( \Illuminate\Support\Facades\Auth::guest() )
                <div class="vot">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                        <i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="rate color-gold">
                    @if( $stream->vote ) {{ $stream->vote }}
                    @else 0
                    @endif
                </div>
                <div class="vot">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                        <i class="fa fa-thumbs-down fa-2x"aria-hidden="true"></i>
                    </a>
                </div>
            @elseif( \Illuminate\Support\Facades\Auth::user()->ban == 1 )
                <div class="vot">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Banned user haven\'t the ability to vote streams!', 'error');">
                        <i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="rate color-gold">
                    @if( $stream->vote ) {{ $stream->vote }}
                    @else 0
                    @endif
                </div>
                <div class="vot">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Banned user haven\'t the ability to vote streams!', 'error');">
                        <i class="fa fa-thumbs-down fa-2x"aria-hidden="true"></i>
                    </a>
                </div>
            @elseif( $stream->user_id == \Illuminate\Support\Facades\Auth::user()->id )
                <div class="vot">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'You can\'t vote your stream.', 'error');">
                        <i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="rate color-gold">
                    @if( $stream->vote ) {{ $stream->vote }}
                    @else 0
                    @endif
                </div>
                <div class="vot">
                    <a href="javascript:void(0);" nclick="sweetAlert('Oops...', 'You can\'t vote your stream.', 'error');">
                        <i class="fa fa-thumbs-down fa-2x"aria-hidden="true"></i>
                    </a>
                </div>
            @else
                <div class="vot @if( !is_null($stream->is_voted) && is_null($stream->is_downvoted) ) disabled @endif">
                    <a href="javascript:void(0);" onclick="voteUp(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')">
                        <i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="rate color-gold">
                    @if( $stream->vote ) {{ $stream->vote }}
                    @else 0
                    @endif
                </div>
                <div class="vot @if( is_null($stream->is_voted) && !is_null($stream->is_downvoted) ) disabled @endif">
                    <a href="javascript:void(0);" onclick="voteDown(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')">
                        <i class="fa fa-thumbs-down fa-2x"aria-hidden="true"></i>
                    </a>
                </div>
            @endif
        </div>
    <a href="#" style="background: #00222e; height: 22px!important; margin-top: 5px!important; padding: 2px 7px!important;" onclick="event.preventDefault()" class="btn btn-sm btn-rss leaveReply" data-toggle="collapse" data-target="#comments_{{$stream->user_id}}" title="Reply to streamer">
          <!--<i class="fa fa-reply" style="padding-top: 3px;"></i>&nbsp;--> Reply to streamer @php $count = count($groupInfo[$stream->username]['usc']); @endphp {{ $count != 0 ? " &nbsp;(".$count.")" : ' ' }}
        </a>
	<div class="user-image user-image--center">
        <span id="heart">
          <a href="#" class="likeUser @if(in_array($stream->user_id,$liked_streammers)) active @endif" data-uid="{{$stream->user_id}}" data-toggle="tooltip" rel="{{$groupInfo[ $stream->username ]['fans']}}" data-placement="top" data-original-title="Casting heart votes will improve streamer's ranks and may help them to get promoted to Approve/Verifed status" style="margin-left: 5px">
            <i class="fa fa-heart-o" style="color:red"></i>
          </a>
        </span>
        {{--<span class="likeCounter">{{$groupInfo[ $stream->username ]['fans']}}</span>--}}
        <a href="/publicProfile/{{$stream->user_id}}">
            @if (file_exists('images/avatar' . '/' . $stream->user_id . '.jpg'))
                <img src="{{ secure_url('images/avatar') . '/' . $stream->user_id . '.jpg' }}">
            @else
                <img src="{{ secure_url('images/noimage/no-image.png') }}">
            @endif
            @if($stream->verified_user==1)
              <span class="streamer streamer_verified" data-toggle="tooltip" data-placement="top" data-original-title="Verified Streamer">{{$stream->username}}</span>
              <span class="streamer streamer_verified stream--label" data-toggle="tooltip" data-placement="top" data-original-title="Verified Streamers are handpicked and represent the highest quality and/or most stable streams on Soccer Streams">VERIFIED STREAMER</span>
            @elseif( $stream->approved == 1 )
              <span class="streamer streamer_approved" data-toggle="tooltip" data-placement="top" data-original-title="Approved Streamer">{{$stream->username}}</span>
              <span class="streamer streamer_approved stream--label" data-toggle="tooltip" data-placement="top" data-original-title="APPROVED STREAMER">APPROVED STREAMER</span>
            @else
              <span class="streamer">{{$stream->username}}</span>
            @endif
        </a>
      </div>

      <div class="streams-actions">
        <div class="streams-block-share">
			<a style="background: #B3994C;" class="btn clipboard" data-clipboard-text="{{ url()->current() }}#userstreams_{{$stream->username}}" title="Copy Stream permalink">
              <fa class="fa fa fa-share-square-o"></fa>
            </a>

          <a style="background: #3b5999" target="_blank" data-toggle="" class="btn fb customer share" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}#userstreams_{{$stream->username}}">
              <span class="socicon socicon-facebook"></span>
            </a>
            <a style="background: #55acee" target="_blank" data-toggle="" class="btn tw customer share" href="https://twitter.com/home?status={{ url()->current() }}#userstreams_{{$stream->username}}">
              <span class="socicon socicon-twitter"></span>
            </a>
            <a style="background: #dd4b39" target="_blank" data-toggle="" class="btn gp customer share" href="https://plus.google.com/share?url={{ url()->current() }}#userstreams_{{$stream->username}}">
              <span class="socicon socicon-googleplus"></span>
            </a>

        </div>

      </div>
    </div>
    @php
      $groupInfo[ $stream->username ]['displayed'] = true;
    @endphp
  @endif
  @foreach( $groupInfo[ $stream->username ]['data'] as $key=>$stream )
    @if( isset( $stream->displayed ))
      @php continue; @endphp
    @endif
    @if(empty($stream->language_flag))
      @php $flag = 'unknown'; @endphp
    @else
      @php $flag = $stream->language_flag; @endphp
    @endif
    @if( $stream->mod_recommended && !$stream->sponsor)
      @php $highlight = "highlight"; $tooltip = "data-toggle=tooltip data-placement=right data-original-title=Recommended"; @endphp
    @elseif($stream->sponsor && $stream->verified_user)
      @php $highlight = "sponsor"; $tooltip = "data-toggle=tooltip data-placement=left data-original-title=Sponsored";$isSponsor=true; @endphp
    @else
      @php $highlight = ""; $tooltip = ""; @endphp
    @endif

    @if( $stream->geoLock )
      @php $geolock = "geoLock"; @endphp
    @else
      @php $geolock = ""; @endphp
    @endif
    @php $stream->displayed = 1; @endphp
    <div class="stream_block" style="display: inline-block !important;">
      <button data-clipboard-text="{{ $stream->url }}" class="clipboard click_{{ $stream->stream_id }}" style="display: none;">
      </button>
      <div class="clickable-tab {{ $geolock }} {{ $highlight }}
                  @if(strtolower($stream->compatibility)=='no')
                    hidden-xs
                  @endif
                  @if($highlight=="sponsor" && $geolock == "")
                    isExpandable
                  @endif"
                  @if($highlight=="sponsor")
                    style="background-color: #faf3b3 !important;"
                  @endif
            data-href="{{ $stream->url }}"
            data-stream-id="{{ $stream->stream_id }}"
            data-type="{{ strtoupper($stream->stream_type) }}"
            data-quality="{{ strtoupper($stream->quality) }}"
            data-language="{{ strtoupper($stream->language_name) }}"
            data-mobile="{{ $stream->compatibility }}" {{ $tooltip }}
      >
        <div class="stream_body"
              @if(!is_null($stream->other_info) && !empty($stream->other_info))
                data-toggle="tooltip"
                data-placement="top"
                data-original-title="{{ $stream->other_info }}"
                @endif
        >
          <div class="stream_info_list">
            <div class="stream_tab_el big">
            @if(strtolower($stream->stream_type)=='acestream' || strtolower($stream->stream_type)=='vlc' || strtolower($stream->stream_type)=='sopcast')
              <button data-clipboard-text="{{ $stream->url }}" class="btn btn-rss btn-copy exception" style="width: 35px; border-radius: 0 !important;" onclick="$('.click_{{ $stream->stream_id }}').click();">
                <i class="fa fa-clipboard" aria-hidden="true"></i>
              </button>
            @else
              <a href="{{ $stream->url }}" target="_blank" class="btn btn-rss btnWatch_{{ $stream->stream_id }} exception" style="  width: 35px; border-radius: 0 !important;">
                <span class="tag whatchbtn_tooltip tooltip_{{ $stream->stream_id }}" style="display: none;">
                  Click here to watch the stream
                </span>
                <i class="fa fa-play-circle-o" aria-hidden="true"></i>
              </a>
            @endif
            </div>
            {{--<div class="stream_tab_el small">
              @if( $stream->other_info )
              <a class="btn-copy"  style="padding:1px">
                <i class="fa fa-info-circle" style="margin: 0px 0px 1px;font-size: 18px;"></i>
              </a>
              @endif
            </div>--}}
            <div class="stream_tab_el small">
              <img src="{{ secure_asset('images/languages/'.$flag.'.png') }}" alt="{{ $stream->language_flag }}">
              <p class="hidden languageValue">{{ $stream->language_name }}</p>
            </div>
            <div class="stream_tab_el small">
              @if(strtolower($stream->stream_type)=='vlc')
              <span class="tag stream-type-tag">VLC</span>
              @elseif(strtolower($stream->stream_type)=='acestream')
              <span class="tag stream-type-tag">ACE</span>
              @elseif(strtolower($stream->stream_type)=='sopcast')
              <span class="tag stream-type-tag">SOP</span>
              @elseif(strtolower($stream->stream_type)=='http')
              <span class="tag stream-type-tag">HTTP</span>
              @else
              <span class="tag stream-type-tag">Other</span>
              @endif
            </div>
            <div class="stream_tab_el small">
              <p class="hidden">{{ $stream->stream_type }}</p>
              @if(strtolower($stream->quality)=='hd' || strtolower($stream->quality)=='sd')
                <span class="tag stream-type-tag qualityValue">{{ $stream->quality }}</span>
              @elseif(strtolower($stream->quality)=='520p')
                <span class="tag quality-tag qualityValue">520</span>
              @else
                <span class="tag unknown quality-tag"></span>
              @endif
            </div>
            @if(strtolower($stream->compatibility)!='no')
              <div class="stream_tab_el small">
                  <img class="small_icon" src="{{ secure_asset('icons/streaminfo/mobilecompat.png') }}" alt="compatible" title="Mobile Compatible">
              </div>
            @endif
            @if ($stream->ad_number>0)
              <div class="stream_tab_el">
              @if($stream->nsfw==1)
                <span class="tag newtag-nsfw" data-toggle="tooltip" data-placement="bottom" data-original-title="This Stream has {{$stream->ad_number}} ad overs and nsfw">{{ $stream->ad_number>0?$stream->ad_number.' clicks':'no over' }}</span>
              @else
                <span class="tag newtag" data-toggle="tooltip" data-placement="bottom" data-original-title="This Stream has {{$stream->ad_number}} ad over">{{ $stream->ad_number>0?$stream->ad_number.' clicks':'no over' }}</span>
              @endif
              </div>
            @endif
            <div class="stream_tab_el icons">
            @if(\Illuminate\Support\Facades\Auth::guest())
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to report streams!', 'error');">
                <i class="fa fa-exclamation-triangle red" aria-hidden="true"></i>
              </a>
            @else
              @if(is_null($stream->is_reported))
                <a href="javascript:void(0);" onclick="report(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')" title="Report stream">
                  <i class="fa fa-exclamation-triangle red" aria-hidden="true"></i>
                </a>
              @else
                <span><i class="fa fa-check" aria-hidden="true"></i></span>
              @endif
              <span style="display: none"><i class="fa fa-check" aria-hidden="true"></i></span>
            @endif
            </div>
          @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
            <div class="dropdown stream_tab_el icons exception">
              <i class="fa fa-cog dropbtn"></i>
              <div class="dropdown-content">
                <a href="javascript:void(0);" onclick="banDomainAction(this,'{{ $stream->stream_id }}',event)" title="Ban this Domain" data-href="{{ secure_url('moderator/stream/banDomain/'.$stream->stream_id) }}">
                  <i class="fa fa-ban red" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0);" onclick="banUserAction(this,'{{ $stream->stream_id }}',event)" title="Ban this user"
                   data-href="{{ secure_url('moderator/user/ban/'.$stream->user_id.'/1') }}">
                  <i class="fa fa-user red" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0);" onclick="sendMessage(this,'{{ $stream->user_id }}',event, '{{ Request::url() }}#{{ $stream->username }}_{{ $stream->stream_id }}')" title="Send Message">
                  <i class="fa fa-send-o red" aria-hidden="true"></i>
                </a>

              @if( $stream->mod_recommended )
                <span><a href="javascript:void(0);" onclick="recommend(this,'{{ $stream->stream_id }}', '{{ $stream->event_id }}', 0 )" title="Undo Recommend">
                  <i class="fa fa-hand-o-down red" aria-hidden="true"></i>
                </a></span>
              @else
                <span><a href="javascript:void(0);" onclick="recommend(this,'{{ $stream->stream_id }}', '{{ $stream->event_id }}', 1)" title="Recommend">
                  <i class="fa fa-hand-o-up red" aria-hidden="true"></i>
                </a></span>
              @endif
                <a href="javascript:void(0);" data-toggle="modal" title="Edit" data-target="#edit_form" onclick="edit_form( {{$stream->stream_id}} )">
                  <i class="fa fa-edit red" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0);" onclick="streamAction(this,'{{ $stream->stream_id }}')" title="Delete"
                   data-href="{{ secure_url('moderator/stream/delete/'.$stream->stream_id) }}">
                  <i class="fa fa-trash red" aria-hidden="true"></i>
                </a>
              </div>
            </div>
          @elseif(\Illuminate\Support\Facades\Auth::check() && $stream->user_id == \Illuminate\Support\Facades\Auth::user()->id )
            <div class="dropdown icons">
              <i class="fa fa-cog dropbtn" style="padding: 5px;"></i>
              <div class="dropdown-content">
              <a href="javascript:void(0);" data-toggle="modal" title="Edit" data-target="#edit_form" onclick="edit_form( {{$stream->stream_id}} )">
                <i class="fa fa-edit red" aria-hidden="true"></i>
              </a>
              <a href="javascript:void(0);" onclick="streamAction(this,'{{ $stream->stream_id }}')" title="Delete"
                 data-href="{{ secure_url('deleteStream/'.$stream->stream_id) }}">
                <i class="fa fa-trash red" aria-hidden="true"></i>
              </a>
              </div>
            </div>
          @endif

          </div>
        </div>
      </div>
    </div>
  @endforeach
      <div class="comments collapse" id="comments_{{$stream->user_id}}">
        <div class="event-comments">

          {{-- end of registered user comment box --}}

          {{-- nested comments --}}
          <div class="stream-comments-div" id="stream-comments-div">
              @if(count($groupInfo[$stream->username]['usc']))
                @foreach($groupInfo[$stream->username]['usc'] as $comm)
                  @include('userStreamComments', ['comment'=>$comm, 'user_comment_count'=>count($groupInfo[$stream->username]['usc']), 'event_id'=> $stream->event_id])
                @endforeach
              @endif
              @if(\Illuminate\Support\Facades\Auth::check())
                {{-- registered user comment box --}}

                {{-- <a class="btn btn-sm btn-rss btn-primary" onclick="toggleAddStreamCommentForm($(this));" style="margin-top: 10px; left: 45%;"> Post a reply </a> --}}
                {{-- <form onsubmit="event.preventDefault();addStreamComment($(this), '{{ $stream->stream_id }}');$(this).hide();" class="streamCommentAdd" method="post"> --}}
				<form action="#" class="streamCommentAdd" method="post">
                  <input type="hidden" class='stream_id_val' id="streamId" value="{{ $stream->stream_id }}">
                  <input type="hidden" class='event_id_val' name="event_id" value="{{ $stream->event_id }}">
                  <div class="form-group">
                    <textarea name="comment" class="form-control" placeholder="Your Comment on stream" rows="1" required="required"></textarea>
                  </div>
                  <button type="submit" class="btn btn-default">Add Comment</button>
                </form>
              @else
                {{-- <p>Please <a href="{{ secure_url('register') }}">register</a> to add your comment or <a href="{{ secure_url('redditLogin') }}">login with Reddit.</a></p> --}}
                @if(Auth::guest())
                <div class="col-md-12" style="background-image: url(/images/bg.png); color: white; text-align: center;">
                  <h2>Join the conversation!</h2>
                  <h3 style="padding-top: 5px; padding-bottom: 10px;"> <a href="{{ secure_url('/login') }}"><button style="background: #2c3e50; border: none; padding: 5px 20px 5px 20px;">Login</button></a><small> or </small> <a href="{{ secure_url('register') }}""><button style="background: #2c3e50; border: none; padding: 5px 20px 5px 20px;">Sign Up</button></a></h3>
                  <h4>To post a comment</h4>
                </div>
                @endif
              @endif
          </div>
          {{-- end of nested comments --}}
        </div>
      </div>
    </div>
  </td>
</tr>


<script>

;(function($){

  /**
   * jQuery function to prevent default anchor event and take the href * and the title to make a share popup
   *
   * @param  {[object]} e           [Mouse event]
   * @param  {[integer]} intWidth   [Popup width defalut 500]
   * @param  {[integer]} intHeight  [Popup height defalut 400]
   * @param  {[boolean]} blnResize  [Is popup resizeabel default true]
   */
  $.fn.customerPopup = function (e, intWidth, intHeight, blnResize) {

    // Prevent default anchor event
    e.preventDefault();

    // Set values for window
    intWidth = intWidth || '500';
    intHeight = intHeight || '400';
    strResize = (blnResize ? 'yes' : 'no');

    // Set title and open popup with focus on it
    var strTitle = ((typeof this.attr('title') !== 'undefined') ? this.attr('title') : 'Social Share'),
        strParam = 'width=' + intWidth + ',height=' + intHeight + ',resizable=' + strResize,
        objWindow = window.open(this.attr('href'), strTitle, strParam).focus();
  }

  /* ================================================== */

  $(document).ready(function ($) {
    $('.customer.share').on("click", function(e) {
      $(this).customerPopup(e);
    });
  });

}(jQuery));

// $('.clickable-tab').click(function(){
//   window.open($(this).data('href'), '_blank');
// });
$(".clickable-tab").click(function(){
    window.open($(this).data('href'), '_blank');
    return false;
});

$(".exception").click(function (e) {
    e.stopPropagation();
});
</script>

<style>
.user-image .streamer {
    border-width: 1px;
}
.streams-block-share {
    margin-top: 4px;
}
.stream_info_list{
  min-height: 27px !important;
  padding: 0 !important;
    height: 27px !important;
}
.stream_block{
  padding: 0 !important;
}
.stream_tab_el{
  padding-left: 0 !important;
}
.stream_tab_el > img{
  margin: 1px 5px 5px 5px !important;
  height: 18px !important;
  object-fit: contain;
}
.stream_tab_el .tag{
  margin: 2px 5px 5px 0px !important;
  height: 18px !important;
}
.stream_tab_el.icons{
  margin: 4px 5px 5px 0px !important;
}
.stream_tab_el.icons i{
  font-size: 17px !important;
  margin-top: -10px;
}
.btn-rss{
  height: 100% !important;
  margin: 0 !important;
  padding: 6px 7px !important;
}
.stream_tab_el {
    padding: 3px 3px !important;
}

.stream_body .stream_tab_el.big {
    /*width: 10%;*/
    padding: 0px !important;
}

.stream_tab_el .btn-rss .fa.fa-play-circle-o {
    display: inline-block !important;
}

.stream_tab_el .btn-rss{
    margin-top: 0px!important;
    height: 27px !important;
}

.tag{
  padding-top: 3px;
  min-width: 48px !important;
}
.streams-block-share > a.btn{
  padding: 2px 10px !important;
}
.avatar-image img{
  height: 45px;
  width: 45px;
}
.user-image .voting-area,
.streamUser .voting-area {
    float: left;
    margin: 6px 10px 0 0;
    display: flex;
    flex-direction: row;
    text-align: center;
}

.newtag {
    background: #333;
    display: inline-block !important;
    color: #fff;
    width: 40px;
}
::content .newTag{
    display: block!important;
}
.newtag-nsfw {
    background: #FF0000;
    display: inline-block !important;
    color: #fff;
    width: 40px;
}

.user-image .streamer{
    color: #07212B;
    background: transparent;
    border-color: #07212B;
    border: solid;
    min-width: 110px;
    border-radius: 0;
    border-width: 1px;
}
.user-image .streamer_approved{
    border-color: #07212B;
    color: #07212B;
    background: #CCCCCC;
}
.user-image .streamer_verified.streamer--label {
    border-color: #07212B;
    color: #07212B;
    background: #B59B48;
    border-radius: 20px;
}

.user-image .streamer_approved.streamer--label {
    color: rgb(7, 33, 43);
    border-color: rgb(7, 33, 43);
    background: rgb(204, 204, 204);
    border-radius: 20px;
}

.user-image .streamer_verified{
    border-color: #07212B;
    color: #07212B;
    background: #B59B48;
}

</style>