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
@php $displayed = 0; $index = 0; @endphp
  @foreach($streams as $stream)
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
    @if( !isset($stream->displayed) )
      @include('stream_row')
    @endif
  @endforeach
