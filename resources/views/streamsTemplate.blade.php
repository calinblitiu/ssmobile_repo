<style>
  .advertag {
    width: 90px !important;
  }
  
  .tag.verified {
    width: 130px !important;
  }
  
  .tag.nsfw-tag {
    width: 51px !important;
  }
  .tag.unknown{
    width: 56px !important;
  }
</style>
@foreach($streams as $stream)
  @if(empty($stream->language_flag))
    <?php $flag = 'unknown'; ?>
  @else
    <?php $flag = $stream->language_flag; ?>
  @endif
  <tr class="clickable-row @if(strtolower($stream->compatibility)=='no') hidden-xs @endif" data-href="{{ $stream->url }}"
      data-stream-id="{{ $stream->stream_id }}" data-type="{{ strtoupper($stream->stream_type) }}" data-quality="{{ strtoupper($stream->quality) }}"
      data-language="{{ strtoupper($stream->language_name) }}" data-mobile="{{ $stream->compatibility }}">
    <td width="5%" class="rating">
      <span class="rate">
        {{ \App\Evaluation::where(['stream_id'=>$stream->stream_id,'eval_type'=>1])->get()->count() }}
      </span>
      @if(Auth::guest())
        <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
          <i class="fa fa-thumbs-up fa-2x" style="color: green" aria-hidden="true"></i>
        </a>
      @else
        @php $vote = \App\Evaluation::where(['stream_id' => $stream->stream_id, 'user_id' => \Illuminate\Support\Facades\Auth::id(), 'eval_type' => 1])->count(); @endphp
        @if($vote >0 || \Illuminate\Support\Facades\Auth::id()==$stream->user_id)
          <span class="done"><i class="fa fa-check-circle-o fa-2x" aria-hidden="true"></i></span>
        @else
          <span class="vot">
            <a href="javascript:void(0);" onclick="voteUp(this,'{{ $stream->stream_id }}')">
              <i class="fa fa-thumbs-up fa-2x" style="color: green" aria-hidden="true"></i>
            </a>
          </span>
        @endif
        <span class="done" style="display: none">
          <i class="fa fa-check-circle-o fa-2x" aria-hidden="true"></i>
        </span>
      @endif
    </td>
    <td width="5%" class="">
      @if(strtolower($stream->stream_type)=='acestream' || strtolower($stream->stream_type)=='vlc' || strtolower($stream->stream_type)=='sopcast')
        <button data-clipboard-text="{{ $stream->url }}" class="btn btn-rss btn-copy">
          <i class="fa fa-clipboard" aria-hidden="true"></i>
        </button>
      @else
        <a href="{{ $stream->url }}" target="_blank" class="btn btn-rss">
          <i class="fa fa-play-circle-o" aria-hidden="true" margin-bottom="10px" ></i>
        </a>
      @endif
    </td>
    <td class="clickable">
      <img src="{{ secure_asset('images/languages/'.$flag.'.png') }}" alt="{{ $stream->language_flag }}">
      <p class="hidden languageValue">{{ $stream->language_name }}</p>
    </td>
    <td class="clickable" width="30%">
      {{ $stream->username }}
      @if($stream->verified_user==1)
        <span verified-hover-text="Verified Streamers are handpicked and represent the highest quality and/or most stable streams on Soccer Streams"
              verified-hover-position="top"
              class="tag verified"><b>VERIFIED STREAMER</b></span>
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
      @if(strtolower($stream->compatibility)=='no')
        <img class="small_icon" src="{{ secure_asset('icons/streaminfo/mobilecompatno.png') }}" alt="In compatible" title="Not a mobile compatible">
      @else
        <img class="small_icon" src="{{ secure_asset('icons/streaminfo/mobilecompat.png') }}" alt="compatible" title="Mobile Compatible">
      @endif
    </td>
    <td>
      <span class="tag ad_number">{{ $stream->ad_number>0?$stream->ad_number.' Ad-overlays':'no Ad-overlays' }}</span>
    </td>
    <td>
      @if($stream->nsfw==1)
        <span class="tag nsfw-tag">NSFW</span>
      @endif
    </td>
    <td>{{ $stream->other_info }}</td>
    <td>
      @if(Auth::guest())
        <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
          <i class="fa fa-exclamation-triangle" style="color: red" aria-hidden="true"></i>
        </a>
      @else
        <?php $report = \App\Evaluation::where(['stream_id' => $stream->stream_id, 'user_id' => \Illuminate\Support\Facades\Auth::id(), 'eval_type' => 0])->count(); ?>
        @if($report>0)
          <span><i class="fa fa-check" aria-hidden="true"></i></span>
        @else
          <a href="javascript:void(0);" onclick="report(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')" title="Report stream">
            <i class="fa fa-exclamation-triangle" style="color: red" aria-hidden="true"></i>
          </a>
        @endif
        <span style="display: none"><i class="fa fa-check" aria-hidden="true"></i></span>
      @endif
    </td>
  </tr>
@endforeach