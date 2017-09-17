<style>
    .whatchbtn_tooltip{
        position: absolute;
        top: -30px;
        left: -75px;
        width: 208px;
        padding: 2px 6px;
        background: #333;
        color: #fff;
        font-size: 14px;
        font-weight: 100;
    }
    .whatchbtn_tooltip:after {
        position: absolute;
        margin-top: 20px;
        margin-left: -118px;
        width: 0;
        height: 8px;
        border: 8px solid transparent;
        border-top: 8px solid #2F373F;
        content: "";
    }

    .not-active {
        pointer-events: none;
        cursor: default;
    }

    .sponsor{
        background-color: #faf3b3 !important;
    }

</style>
@if(!isset($comment_show))
  @php $displayed = 0; $index = 0; @endphp
  @foreach($allStreams as $stream)
    @if(empty($stream->language_flag))
      @php $flag = 'unknown'; @endphp
    @else
      @php $flag = $stream->language_flag; @endphp
    @endif

    @if( $stream->mod_recommended && !$stream->sponsor)
      @php $highlight = "highlight"; $tooltip = "data-toggle=tooltip data-placement=left data-original-title=Recommended"; @endphp
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

    <!-- multiple stream -->
    @if( $groupInfo[ $stream->username ]['count'] > 1 )
      @if( !$displayed )
        <tr>
          <td colspan="20" width="100%" class="multiple_streams">
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
              @php $highlight = "highlight"; $tooltip = "data-toggle=tooltip data-placement=left data-original-title=Recommended"; @endphp
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
            <div class="stream_block clickable-row {{ $geolock }} {{ $highlight }} @if(strtolower($stream->compatibility)=='no')hidden-xs @endif @if($highlight=="sponsor" && $geolock == "")isExpandable @endif" @if($highlight=="sponsor") style="background-color: #faf3b3 !important;" @endif data-href="{{ $stream->url }}" data-stream-id="{{ $stream->stream_id }}" data-type="{{ strtoupper($stream->stream_type) }}" data-quality="{{ strtoupper($stream->quality) }}" data-language="{{ strtoupper($stream->language_name) }}" data-mobile="{{ $stream->compatibility }}" {{ $tooltip }}>
              <div class="stream_body">
                <div style="float: left;  margin:-10px 0 0; text-align: center;">
                @if(\Illuminate\Support\Facades\Auth::guest())
                  <div class="vot disabled">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                      <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
                    </a>
                  </div>
                  <div class="rate color-gold">
                    @if( $stream->vote ) {{ $stream->vote }}
                    @else 0
                    @endif
                  </div>
                  <div class="vot disabled">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                      <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
                    </a>
                  </div>
                @elseif( \Illuminate\Support\Facades\Auth::user()->ban == 1 )
                  <div class="vot disabled">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Banned user haven\'t the ability to vote streams!', 'error');">
                      <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
                    </a>
                  </div>
                  <div class="rate color-gold">
                    @if( $stream->vote ) {{ $stream->vote }}
                    @else 0
                    @endif
                  </div>
                  <div class="vot disabled">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Banned user haven\'t the ability to vote streams!', 'error');">
                      <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
                    </a>
                  </div>
                @elseif( $stream->user_id == \Illuminate\Support\Facades\Auth::user()->id )
                  <div class="vot disabled">
                    <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'You can\'t vote your stream.', 'error');">
                      <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
                    </a>
                  </div>
                  <div class="rate color-gold">
                    @if( $stream->vote ) {{ $stream->vote }}
                    @else 0
                    @endif
                  </div>
                  <div class="vot disabled">
                    <a href="javascript:void(0);" nclick="sweetAlert('Oops...', 'You can\'t vote your stream.', 'error');">
                      <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
                    </a>
                  </div>
                @else
                  <div class="vot @if( !is_null($stream->is_voted) && is_null($stream->is_downvoted) ) disabled @endif">
                    <a href="javascript:void(0);" onclick="voteUp(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')">
                      <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
                    </a>
                  </div>
                  <div class="rate color-gold">
                    @if( $stream->vote ) {{ $stream->vote }}
                    @else 0
                    @endif
                  </div>
                  <div class="vot @if( is_null($stream->is_voted) && !is_null($stream->is_downvoted) ) disabled @endif">
                    <a href="javascript:void(0);" onclick="voteDown(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')">
                      <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
                    </a>
                  </div>
                @endif
                </div>
                <div class="stream_info_list">
                  <div class="stream_tab_el big">
                  @if(\Illuminate\Support\Facades\Auth::guest() )
                      <a rel="nofollow" onclick="sweetAlert('Oops...', 'Only registered user have the ability to comment!', 'error')" data-remote="false" class="btn btn-default">
                        <img src="{{ cdn('images/comments.png') }}" style="width: 15px;">
                      </a>
                  @elseif( \Illuminate\Support\Facades\Auth::user()->ban == 1)
                      <a rel="nofollow" onclick="sweetAlert('Oops...', 'Banned user haven\'t ability to comment!', 'error')" data-remote="false" class="btn btn-default">
                        <img src="{{ cdn('images/comments.png') }}" style="width: 15px;">
                      </a>
                  @else
                      <a rel="nofollow" data-toggle="tooltip" data-placement="bottom" title="Quote this stream in your comment" onclick="stream_comment_init({{ $stream->stream_id }})" data-remote="false" class="btn btn-default">
                        <img src="{{ cdn('images/comments.png') }}" style="width: 15px;">
                      </a>
                  @endif
                  </div>
                  <div class="stream_tab_el big">
                  @if(strtolower($stream->stream_type)=='acestream' || strtolower($stream->stream_type)=='vlc' || strtolower($stream->stream_type)=='sopcast')
                    <button data-clipboard-text="{{ $stream->url }}" class="btn btn-rss btn-copy">
                      <i class="fa fa-clipboard" aria-hidden="true"></i>
                    </button>
                  @else
                    <a href="{{ $stream->url }}" target="_blank" class="btn btn-rss btnWatch_{{ $stream->stream_id }}">
                        <span class="tag whatchbtn_tooltip tooltip_{{ $stream->stream_id }}" style="display: none;">Click here to watch the stream</span>
                      <i class="fa fa-play-circle-o" aria-hidden="true"></i>
                    </a>
                  @endif
                  </div>
                  <div class="stream_tab_el small">
                    <a class="btn-copy" data-toggle="tooltip" data-placement="top" data-original-title="{{ $stream->other_info }}">
                      @if( $stream->other_info )<i class="fa fa-info-circle"></i>
                      @endif
                    </a>
                  </div>
                  <div class="stream_tab_el small">
                    <img src="{{ cdn('images/languages/'.$flag.'.png') }}" alt="{{ $stream->language_flag }}">
                    <p class="hidden languageValue">{{ $stream->language_name }}</p>
                  </div>
                  <div class="stream_tab_el username">
                    @if( $stream->approved == 1 )
                      <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer streamer_approved" data-toggle="tooltip" data-placement="top" data-original-title="Approved Streamer">{{ $stream->username }}</span></a>
                    @elseif($stream->verified_user==1)
                      <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer streamer_verified" data-toggle="tooltip" data-placement="top" data-original-title="Verified Streamer">{{ $stream->username }}</span></a>
                    @else
                      <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer">{{ $stream->username }}</span></a>
                    @endif
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
                  <div class="stream_tab_el small">
                    @if(strtolower($stream->compatibility)!='no')
                      <img class="small_icon" src="{{ cdn('icons/streaminfo/mobilecompat.png') }}" alt="compatible" title="Mobile Compatible">
                    @endif
                  </div>
                  <div class="stream_tab_el">
                  @if($stream->nsfw==1)
                    <span class="tag advertag-nsfw" data-toggle="tooltip" data-placement="top" data-original-title="This Stream has {{$stream->ad_number}} ad overs and nsfw">{{ $stream->ad_number>0?$stream->ad_number.' clicks':'no over' }}</span>
                  @else
                    <span class="tag advertag" data-toggle="tooltip" data-placement="top" data-original-title="This Stream has {{$stream->ad_number}} ad over">{{ $stream->ad_number>0?$stream->ad_number.' clicks':'no over' }}</span>
                  @endif
                  </div>
                  <div class="stream_tab_el icons">
                    <a data-slug="{{ $stream->username }}_{{ $stream->stream_id }}" data-clipboard-text="{{ Request::url() }}#{{ $stream->username }}_{{ $stream->stream_id }}" class="btn-copy permalink" data-toggle="tooltip" data-placement="top" data-original-title="Copy stream permalink">
                      <i class="fa fa-share-square-o" aria-hidden="true"></i>
                    </a>
                  </div>
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
                  <div class="dropdown stream_tab_el icons">
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
                      <a href="#" data-toggle="modal" title="Edit" data-target="#edit_form" onclick="edit_form( {{$stream->stream_id}} )">
                        <i class="fa fa-edit red" aria-hidden="true"></i>
                      </a>
                      <a href="javascript:void(0);" onclick="streamAction(this,'{{ $stream->stream_id }}')" title="Delete"
                         data-href="{{ secure_url('moderator/stream/delete/'.$stream->stream_id) }}">
                        <i class="fa fa-trash red" aria-hidden="true"></i>
                      </a>
                    </div>
                  </div>
                @elseif(\Illuminate\Support\Facades\Auth::check() && $stream->user_id == \Illuminate\Support\Facades\Auth::user()->id )
                  <div class="dropdown">
                    <i class="fa fa-cog dropbtn"></i>
                    <div class="dropdown-content">
                    <a href="#" data-toggle="modal" title="Edit" data-target="#edit_form" onclick="edit_form( {{$stream->stream_id}} )">
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
              <div class="stream_heading">
                <ul class="multiple_stream_info">
                  <li>
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
                    <p class="hidden">{{ $stream->stream_type }}</p>
                  </li>
                  <li class="heading_flag">
                    <img src="{{ cdn('images/languages/'.$flag.'.png') }}" alt="{{ $stream->language_flag }}">
                    <p class="hidden languageValue">{{ $stream->language_name }}</p>
                  </li>
                  <!--li>
                    @if(strtolower($stream->quality)=='hd' || strtolower($stream->quality)=='sd')
                      <span class="tag stream-type-tag qualityValue">{{ $stream->quality }}</span>
                    @elseif(strtolower($stream->quality)=='520p')
                      <span class="tag quality-tag qualityValue">520</span>
                    @else
                      <span class="tag unknown quality-tag"></span>
                    @endif
                  </li-->
                </ul>
              </div>

            </div>
          @endforeach
          </td>
        </tr>
        @php $displayed = 1; $index =0; @endphp
      @endif
    @else
      @php $displayed = 0; @endphp
      <tr class="stream_block clickable-row {{ $geolock }} {{ $highlight }} @if(strtolower($stream->compatibility)=='no')hidden-xs @endif @if($highlight=="sponsor" && $geolock == "")isExpandable @endif" @if($highlight=="sponsor") style="background-color: #faf3b3 !important;" @endif data-href="{{ $stream->url }}" data-stream-id="{{ $stream->stream_id }}" data-type="{{ strtoupper($stream->stream_type) }}" data-quality="{{ strtoupper($stream->quality) }}" data-language="{{ strtoupper($stream->language_name) }}" data-mobile="{{ $stream->compatibility }}" {{ $tooltip }}>
        <td class="rating" width='7%'>
          @if(\Illuminate\Support\Facades\Auth::guest())
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
              </a>
            </div>
            <div class="rate color-gold">
              @if( $stream->vote ) {{ $stream->vote }}
              @else 0
              @endif
            </div>
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
              </a>
            </div>
          @elseif( \Illuminate\Support\Facades\Auth::user()->ban == 1 )
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Banned user haven\'t the ability to vote streams!', 'error');">
                <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
              </a>
            </div>
            <div class="rate color-gold">
              @if( $stream->vote ) {{ $stream->vote }}
              @else 0
              @endif
            </div>
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Banned user haven\'t the ability to vote streams!', 'error');">
                <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
              </a>
            </div>
          @elseif( $stream->user_id == \Illuminate\Support\Facades\Auth::user()->id )
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'You can\'t vote your stream.', 'error');">
                <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
              </a>
            </div>
            <div class="rate color-gold">
              @if( $stream->vote ) {{ $stream->vote }}
              @else 0
              @endif
            </div>
            <div class="vot disabled">
              <a href="javascript:void(0);" nclick="sweetAlert('Oops...', 'You can\'t vote your stream.', 'error');">
                <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
              </a>
            </div>
          @else
            <div class="vot @if( !is_null($stream->is_voted) && is_null($stream->is_downvoted) ) disabled @endif">
              <a href="javascript:void(0);" onclick="voteUp(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')">
                <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
              </a>
            </div>
            <div class="rate color-gold">
              @if( $stream->vote ) {{ $stream->vote }}
              @else 0
              @endif
            </div>
            <div class="vot @if( is_null($stream->is_voted) && !is_null($stream->is_downvoted) ) disabled @endif">
              <a href="javascript:void(0);" onclick="voteDown(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')">
                <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
              </a>
            </div>
          @endif
        </td>
        @if(\Illuminate\Support\Facades\Auth::guest() )
          <td width="5%">
            <a rel="nofollow" onclick="sweetAlert('Oops...', 'Only registered user have the ability to comment!', 'error')" data-remote="false" class="btn btn-default">
              <img src="{{ cdn('images/comments.png') }}" style="width: 15px;">
            </a>
          </td>
        @elseif( \Illuminate\Support\Facades\Auth::user()->ban == 1)
          <td width="5%">
            <a rel="nofollow" onclick="sweetAlert('Oops...', 'Banned user haven\'t ability to comment!', 'error')" data-remote="false" class="btn btn-default">
              <img src="{{ cdn('images/comments.png') }}" style="width: 15px;">
            </a>
          </td>
        @else
          <td width="5%">
            <a rel="nofollow" data-toggle="tooltip" data-placement="bottom" title="Quote this stream in your comment" onclick="stream_comment_init({{ $stream->stream_id }})" data-remote="false" class="btn btn-default">
              <img src="{{ cdn('images/comments.png') }}" style="width: 15px;">
            </a>
          </td>
        @endif
        <td>
          @if(strtolower($stream->stream_type)=='acestream' || strtolower($stream->stream_type)=='vlc' || strtolower($stream->stream_type)=='sopcast')
            <button data-clipboard-text="{{ $stream->url }}" class="btn btn-rss btn-copy">
              <i class="fa fa-clipboard" aria-hidden="true"></i>
            </button>
          @else
            <a href="{{ $stream->url }}" target="_blank" class="btn btn-rss btnWatch_{{ $stream->stream_id }}">
                <span class="tag whatchbtn_tooltip tooltip_{{ $stream->stream_id }}" style="display: none;">Click here to watch the stream</span>
              <i class="fa fa-play-circle-o" aria-hidden="true"></i>
            </a>
          @endif

        </td>
        <td width="1%">
          <a class="btn-copy" data-toggle="tooltip" data-placement="top" data-original-title="{{ $stream->other_info }}">
            @if( $stream->other_info )<i class="fa fa-info-circle"></i>
            @endif
          </a>
        </td>

        <td class="clickable">
          <img src="{{ cdn('images/languages/'.$flag.'.png') }}" alt="{{ $stream->language_flag }}">
          <p class="hidden languageValue">{{ $stream->language_name }}</p>
        </td>
        <td>
          @if( $stream->approved == 1 )
            <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer streamer_approved" data-toggle="tooltip" data-placement="top" data-original-title="Approved Streamer">{{ $stream->username }}</span></a>
          @elseif($stream->verified_user==1)
            <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer streamer_verified" data-toggle="tooltip" data-placement="top" data-original-title="Verified Streamer">{{ $stream->username }}</span></a>
          @else
            <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer">{{ $stream->username }}</span></a>
          @endif
        </td>
        <td class="clickable">
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
          <p class="hidden">{{ $stream->stream_type }}</p>
        </td>
        <td class="clickable">
          @if(strtolower($stream->quality)=='hd' || strtolower($stream->quality)=='sd')
            <span class="tag stream-type-tag qualityValue">{{ $stream->quality }}</span>
          @elseif(strtolower($stream->quality)=='520p')
            <span class="tag quality-tag qualityValue">520</span>
          @else
            <span class="tag unknown quality-tag"></span>
          @endif
        </td>
        <td class="clickable hidden-xs">
          @if(strtolower($stream->compatibility)!='no')
            <img class="small_icon" src="{{ cdn('icons/streaminfo/mobilecompat.png') }}" alt="compatible" title="Mobile Compatible">
          @endif
        </td>
        <td class="clickable">
          @if($stream->nsfw==1)
            <span class="tag advertag-nsfw" data-toggle="tooltip" data-placement="top" data-original-title="This Stream has {{$stream->ad_number}} ad overs and nsfw">{{ $stream->ad_number>0?$stream->ad_number.' clicks':'no over' }}</span>
          @else
            <span class="tag advertag" data-toggle="tooltip" data-placement="top" data-original-title="This Stream has {{$stream->ad_number}} ad over">{{ $stream->ad_number>0?$stream->ad_number.' clicks':'no over' }}</span>
          @endif
        </td>
        <td>
            <a data-slug="{{ $stream->username }}_{{ $stream->stream_id }}" data-clipboard-text="{{ Request::url() }}#{{ $stream->username }}_{{ $stream->stream_id }}" class="btn-copy permalink" data-toggle="tooltip" data-placement="top" data-original-title="Copy stream permalink">
              <i class="fa fa-share-square-o" aria-hidden="true"></i>
          </a>
        </td>
        <td>
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
        </td>
      @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
      <td>
        <div>
        <div class="dropdown">
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
            <a href="javascript:void(0);" onclick="recommend(this,'{{ $stream->stream_id }}', '{{ $stream->event_id }}', 1)" title="Recommend">
              <i class="fa fa-hand-o-up red" aria-hidden="true"></i>
            </a>
          @endif
            <a href="#" data-toggle="modal" title="Edit" data-target="#edit_form" onclick="edit_form( {{$stream->stream_id}} )">
              <i class="fa fa-edit red" aria-hidden="true"></i>
            </a>
            <a href="javascript:void(0);" onclick="streamAction(this,'{{ $stream->stream_id }}')" title="Delete"
               data-href="{{ secure_url('moderator/stream/delete/'.$stream->stream_id) }}">
              <i class="fa fa-trash red" aria-hidden="true"></i>
            </a>
          </div>
        </div>
        </div>
      </td>
      @elseif(\Illuminate\Support\Facades\Auth::check() && $stream->user_id == \Illuminate\Support\Facades\Auth::user()->id )
      <td>
        <div class="dropdown">
          <i class="fa fa-cog dropbtn"></i>
          <div class="dropdown-content">
          <a href="#" data-toggle="modal" title="Edit" data-target="#edit_form" onclick="edit_form( {{$stream->stream_id}} )">
            <i class="fa fa-edit red" aria-hidden="true"></i>
          </a>
          <a href="javascript:void(0);" onclick="streamAction(this,'{{ $stream->stream_id }}')" title="Delete"
             data-href="{{ secure_url('deleteStream/'.$stream->stream_id) }}">
            <i class="fa fa-trash red" aria-hidden="true"></i>
          </a>
          </div>
        </div>
      </td>
      @else
        <td></td>
      @endif
      </tr>
    @endif
  @endforeach
@else
  @foreach($allStreams as $stream)
    @if(empty($stream->language_flag))
      @php $flag = 'unknown'; @endphp
    @else
      @php $flag = $stream->language_flag; @endphp
    @endif
    @php
      $comments = StreamController::getComments($stream->stream_id);
      $commentsCount = StreamController::getCommentsCount($stream->stream_id);
    @endphp
    <div class="stream_comments parent_comment {{ ($stream->comments == 0) ? 'hidden':'' }}" @if(count($comments)) @if($comments[0]->votes) data-votes="{{ $comments[0]->votes }}" @endif @endif>
        <div class="stream_comment_row stream_{{ $stream->stream_id }} {{ ($stream->comments != 0) ? 'hidden':'' }} @if(strtolower($stream->compatibility)=='no') hidden-xs @endif" data-href="{{ $stream->url }}"
          data-stream-id="{{ $stream->stream_id }}" data-type="{{ strtoupper($stream->stream_type) }}" data-quality="{{ strtoupper($stream->quality) }}"
          data-language="{{ strtoupper($stream->language_name) }}" data-mobile="{{ $stream->compatibility }}">
        <td class="rating">
          @if(\Illuminate\Support\Facades\Auth::guest())
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
              </a>
            </div>
            <div class="rate color-gold">
              @if( $stream->vote ) {{ $stream->vote }}
              @else 0
              @endif
            </div>
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
              </a>
            </div>
          @elseif( \Illuminate\Support\Facades\Auth::user()->ban == 1 )
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Banned user haven\'t the ability to vote streams!', 'error');">
                <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
              </a>
            </div>
            <div class="rate color-gold">
              @if( $stream->vote ) {{ $stream->vote }}
              @else 0
              @endif
            </div>
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Banned user haven\'t the ability to vote streams!', 'error');">
                <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
              </a>
            </div>
          @elseif( $stream->user_id == \Illuminate\Support\Facades\Auth::user()->id )
            <div class="vot disabled">
              <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'You can\'t vote your stream.', 'error');">
                <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
              </a>
            </div>
            <div class="rate color-gold">
              @if( $stream->vote ) {{ $stream->vote }}
              @else 0
              @endif
            </div>
            <div class="vot disabled">
              <a href="javascript:void(0);" nclick="sweetAlert('Oops...', 'You can\'t vote your stream.', 'error');">
                <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
              </a>
            </div>
          @else
            <div class="vot @if( !is_null($stream->is_voted) && is_null($stream->is_downvoted) ) disabled @endif">
              <a href="javascript:void(0);" onclick="voteUp(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')">
                <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>
              </a>
            </div>
            <div class="rate color-gold">
              @if( $stream->vote ) {{ $stream->vote }}
              @else 0
              @endif
            </div>
            <div class="vot @if( is_null($stream->is_voted) && !is_null($stream->is_downvoted) ) disabled @endif">
              <a href="javascript:void(0);" onclick="voteDown(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')">
                <i class="fa fa-chevron-down fa-2x"aria-hidden="true"></i>
              </a>
            </div>
          @endif
        </td>
        <td>
          @if(strtolower($stream->stream_type)=='acestream' || strtolower($stream->stream_type)=='vlc' || strtolower($stream->stream_type)=='sopcast')
            <button data-clipboard-text="{{ $stream->url }}" class="btn btn-rss btn-copy">
              <i class="fa fa-clipboard" aria-hidden="true"></i>
            </button>
          @else
            <a href="{{ $stream->url }}" target="_blank" class="btn btn-rss">
              <i class="fa fa-play-circle-o" aria-hidden="true"></i>
            </a>
          @endif
          <a class="btn-copy" data-toggle="tooltip" data-placement="top" data-original-title="{{ $stream->other_info }}">
            @if( $stream->other_info )<i class="fa fa-info-circle"></i>
            @endif
          </a>
        </td>
        <td class="clickable">
          <img src="{{ cdn('images/languages/'.$flag.'.png') }}" alt="{{ $stream->language_flag }}">
          <p class="hidden languageValue">{{ $stream->language_name }}</p>
        </td>

        <td width="30%">
        @if( $stream->approved == 1 )
          <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer streamer_approved" data-toggle="tooltip" data-placement="top" data-original-title="Approved Streamer">{{ $stream->username }}</span></a>
        @elseif($stream->verified_user==1)
          <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer streamer_verified" data-toggle="tooltip" data-placement="top" data-original-title="Verified Streamer">{{ $stream->username }}</span></a>
        @else
          <a href="/publicProfile/<?php echo $stream->user_id; ?>"><span class="streamer">{{ $stream->username }}</span></a>
        @endif
        </td>
        <td class="clickable">
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
          <p class="hidden">{{ $stream->stream_type }}</p>
        </td>
        <td class="clickable">
          @if(strtolower($stream->quality)=='hd' || strtolower($stream->quality)=='sd')
            <span class="tag stream-type-tag qualityValue">{{ $stream->quality }}</span>
          @elseif(strtolower($stream->quality)=='520p')
            <span class="tag quality-tag qualityValue">520</span>
          @else
            <span class="tag unknown quality-tag"></span>
          @endif
        </td>
        <td class="clickable hidden-xs">
          @if(strtolower($stream->compatibility)!='no')
            <img class="small_icon" src="{{ cdn('icons/streaminfo/mobilecompat.png') }}" alt="compatible" title="Mobile Compatible">
          @endif
        </td>
        <td>
          @if($stream->nsfw==1)
            <span class="tag advertag-nsfw" data-toggle="tooltip" data-placement="top" data-original-title="This Stream has {{$stream->ad_number}} ad overs and nsfw">{{ $stream->ad_number>0?$stream->ad_number.' clicks':'no over' }}</span>
          @else
            <span class="tag advertag" data-toggle="tooltip" data-placement="top" data-original-title="This Stream has {{$stream->ad_number}} ad over">{{ $stream->ad_number>0?$stream->ad_number.' clicks':'no over' }}</span>
          @endif
        </td>
        <td>
          <a data-slug="{{ $stream->username }}_{{ $stream->stream_id }}" data-clipboard-text="{{ Request::url() }}#{{ $stream->username }}_{{ $stream->stream_id }}" class="btn-copy permalink" data-toggle="tooltip" data-placement="top" data-original-title="Copy stream permalink">
              <i class="fa fa-share-square-o" aria-hidden="true"></i>
          </a>
        </td>
        <td>
          @if(\Illuminate\Support\Facades\Auth::guest())
            <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
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

        </td>
      </div>

      <div class="comments">
        @if(count($comments))
            @foreach($comments as $comment)
              @include('partials.comment', ['comment'=> $comment, 'user_comment_count' => $commentsCount, 'event_id' => $comment->event_id, 'stream' => $stream])
            @endforeach
        @else
          @if(\Illuminate\Support\Facades\Auth::check())
            <form onsubmit="event.preventDefault();addComment($(this), '{{ $stream->stream_id }}');$(this).hide();" class="streamCommentAdd" method="post">
              <input type="hidden" id="streamId" value="{{ $stream->stream_id }}">
              <div class="form-group">
                <textarea name="comment" class="form-control" placeholder="Your Comment on stream" rows="3" required></textarea>
              </div>
              <button type="submit" class="btn btn-default">Send</button>
            </form>
            <br>
          @endif
        @endif
      </div>
    </div>

  @endforeach
@endif
