@extends('layouts.site')
@inject('general_helper', 'App\Services\GeneralViewHelper')

@section('sidebar-left')
    <!--Votes-->
    @include('sidebar-widgets.votes')
    <!-- END Vote -->

    <!-- Gosu Replay -->
    @include('sidebar-widgets.gosu-replays')
    <!-- END Gosu Replay -->

    <!-- Main Forum Topics -->
    @include('sidebar-widgets.general-forum-sections')
    <!-- END Main Forum Topics -->
@endsection

@section('content')
    @php $types = $general_helper->getReplayTypes(); @endphp
    <div class="row">
        <div class="col-md-3">
            @include('left_inner_sidebar')
        </div>
        <div class="col-md-9">
            @if($last_news)
                <div class="page-title">Последние новости</div>
                <div class="row">
                    <div class="col w-100">
                        @php $i=0; @endphp
                        @foreach($last_news as $last_forum)
                            <div class="forum-wrapper">
                                <div class="forum-title">
                                    <a href="{{route('forum.topic.index',['id'=>$last_forum->id])}}">
                                        {!! $last_forum->title??'название форму' !!}</a>
                                </div>
                                <div class="forum-image">
                                    <a href="{{route('forum.topic.index',['id'=>$last_forum->id])}}">
                                        @if($last_forum->preview_image)
                                            <img src="{{$last_forum->preview_image->link??'/images/big-logo-header.jpg'}}"
                                                 alt="">
                                        @endif
                                    </a>
                                </div>
                                <div class="forum-extract">
                                    {!! $general_helper->oldContentFilter($last_forum->preview_content??substr($last_forum->content,0,45)) !!}
                                </div>
                                <a class="pull-right" href="{{route('forum.topic.index',['id'=>$last_forum->id])}}">[читать
                                    полностью]</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="page-title">Популярные форумы</div>
            <div class="row">
                <div class="col w-100">
                    @if($popular_forum_topics)
                        @php $i=0; @endphp
                        @foreach($popular_forum_topics as $last_forum)
                            @if($i == 0)
                                <div class="forum-wrapper">
                                    <div class="forum-title">
                                        <a href="{{route('forum.topic.index',['id'=>$last_forum->id])}}">
                                            {{$last_forum->title??'название форму'}}</a>
                                    </div>
                                    <div class="forum-image">
                                        <a href="{{route('forum.topic.index',['id'=>$last_forum->id])}}">
                                            <img src="{{$last_forum->preview_image->link??'/images/header.gif'}}"
                                                 alt="">
                                        </a>
                                    </div>
                                    <div class="forum-extract">
                                        {!! $last_forum->preview_content??substr($last_forum->content,0,45) !!}
                                    </div>
                                    <a class="pull-right" href="{{route('forum.topic.index',['id'=>$last_forum->id])}}">[читать
                                        полностью]</a>
                                </div>
                            @else
                                @if($i == 1)
                                    <div class="row top-forum-small-wrapper">
                                        @endif
                                        <div class="col-3">
                                            <a href="{{route('forum.topic.index',['id'=>$last_forum->id])}}"
                                               class="top-forum-small" data-id="{{$last_forum->id}}">
                                                <img src="{{$last_forum->preview_image->link??'/images/big-logo-header.jpg'}}"
                                                     alt="">
                                                <div class="top-forum-title-small">{{$last_forum->title}}</div>
                                            </a>
                                        </div>
                                        @if($i == 4)
                                    </div>
                                @endif
                            @endif
                            @php $i++; @endphp
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('sidebar-right')
    <!--Banners-->
    @include('sidebar-widgets.banner')
    <!-- END Banners -->

    <!-- New Users-->
    @include('sidebar-widgets.new-users')
    <!-- END New Users-->

    <!-- User's Replays-->
    @include('sidebar-widgets.users-replays')
    <!-- END User's Replays-->

    <!-- Gallery -->
    @include('sidebar-widgets.random-gallery')
    <!-- END Gallery -->
@endsection