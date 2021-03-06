@extends('layouts.site')
@inject('general_helper', 'App\Services\GeneralViewHelper')
<?php
$extraSmiles = $general_helper->getextraSmiles();
?>

@section('css')
    <!--SCEditor -  WYSIWYG BBCode editor -->
    <link rel="stylesheet" href="{{route('home')}}/js/sceditor/minified/themes/default.min.css"/>

    <!--JS plugin Select2 - autocomplete -->
    <link rel="stylesheet" href="{{route('home')}}/css/select2.min.css"/>
    <link rel="stylesheet" href="{{route('home')}}/css/dropzone.css"/>
@endsection

<?php
$countries = $general_helper->getCountries();
$races = \App\Replay::$races;
$maps = $general_helper->getReplayMaps();
$types = $general_helper->getReplayTypes();
$game_versions = $general_helper->getGameVersion();
?>

@section('sidebar-left')
    <!-- Gosu Replay -->
    @include('sidebar-widgets.gosu-replays')
    <!-- END Gosu Replay -->
    <!-- All Forum Topics -->
    @include('sidebar-widgets.search-replay-form')
    <!-- END All Forum Topics -->
@endsection

@section('content')

    <!-- Breadcrumbs -->
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li>
                    <a href="/">Главная</a>
                </li>
                <li>
                    <a href="{{route('user_profile',['id' =>Auth::id()])}}">/ Мой Аккаунт</a>
                </li>
                <li>
                    <a href="#" class="active">/ Редактирование реплея: {{$replay->title}}</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- END Breadcrumbs -->

    <div class="content-box">
        <div class="col-md-12 section-title">
            <div>Создать новый Replay</div>
        </div>
        <div class="row">
            <div class="col"></div>
            <div class="col-md-10">
                {{ Form::model($replay, [
                    'route' => ['replay.update', $replay->id],
                    'enctype' => 'multipart/form-data',
                    'class' => 'user-create-replay-form',
                    'method' => 'post'
                ]) }}

                    @csrf
                    <div class="form-fields-box">
                        <div class="form-group">
                            <label for="name">* Название:</label>
                            <input type="text" id="name" value="{{ old('title', $replay->title) }}" name="title"
                                   class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}">
                            @if ($errors->has('title'))
                                <span class="invalid-feedback">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_replay">* Пользовательский/Gosu:</label>
                                    @if($general_helper->isModerator() || $general_helper->isAdmin())
                                        <select class="custom-select {{ $errors->has('user_replay') ? ' is-invalid' : '' }}"
                                                id="user_replay" name="user_replay">
                                            <option value="0" {{ 0 == old('user_replay', $replay->user_replay) ? 'selected' : '' }}>Госу
                                            </option>
                                            <option value="1" {{ 1 == old('user_replay', $replay->user_replay) ? 'selected' : '' }}>
                                                Пользовательский
                                            </option>
                                        </select>
                                        @if ($errors->has('user_replay'))
                                            <span class="invalid-feedback">
                                            <strong>{{ $errors->first('user_replay') }}</strong>
                                        </span>
                                        @endif
                                    @else
                                        <div class="replay-type">
                                            {{$replay->user_replay == 1 ? 'Пользовательский' : 'Gosu'}}
                                        </div>
                                        <input type="hidden" name="user_replay" value="{{$replay->user_replay}}">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type_id">* Тип:</label>
                                    <select class="custom-select {{ $errors->has('type_idy') ? ' is-invalid' : '' }}"
                                            id="type_id" name="type_id">
                                        @foreach($types as $type)
                                            <option value="{{$type->id}}" {{ $type->id == old('type_id', $replay->type_id) ? 'selected' : '' }}>
                                                {{$type->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('type_id'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('type_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="map_id">* Карта:</label>
                            <select class="form-select-2 custom-select {{ $errors->has('map_id') ? ' is-invalid' : '' }}"
                                    id="map_id" name="map_id">
                                @foreach($maps as $map)
                                    <option value="{{$map->id}}" {{ $map->id == old('map_id', $replay->map_id) ? 'selected' : '' }}>
                                        {{$map->name}}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('map_id'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('map_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div><!--close div /.form-fields-box-->

                    <div class="form-fields-box">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_race">* Первая раса:</label>
                                    <select class="custom-select {{ $errors->has('first_race') ? ' is-invalid' : '' }}"
                                            id="first_race" name="first_race">
                                        @foreach(\App\Replay::$races as $race)
                                            <option value="{{$race}}" {{ $race == old('first_race', $replay->first_race) ? 'selected' : '' }}>
                                                {{$race}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('first_race'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('first_race') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_country_id">* Первая страна:</label>
                                    <select class="form-select-2 custom-select {{ $errors->has('first_country_id') ? ' is-invalid' : '' }}"
                                            id="first_country_id"
                                            name="first_country_id">
                                        @foreach($countries as $country)
                                            <option
                                                    value="{{$country->id}}" {{ $country->id == old('first_country_id', $replay->first_country_id) ? 'selected' : '' }}>
                                                {{$country->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('first_country_id'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('first_country_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="first_location">* Первая локация:</label>
                            <input type="text" id="first_location" value="{{ old('first_location', $replay->first_location) }}"
                                   name="first_location"
                                   class="form-control {{ $errors->has('first_location') ? ' is-invalid' : '' }}">
                            @if ($errors->has('first_location'))
                                <span class="invalid-feedback">
                                            <strong>{{ $errors->first('first_location') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div><!--close div /.form-fields-box-->

                    <div class="form-fields-box">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="second_race">* Вторая раса:</label>
                                    <select class="custom-select {{ $errors->has('second_race') ? ' is-invalid' : '' }}"
                                            id="second_race" name="second_race">
                                        @foreach(\App\Replay::$races as $race)
                                            <option value="{{$race}}" {{ $race == old('second_race', $replay->second_race) ? 'selected':'' }}>
                                                {{$race}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('second_race'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('second_race') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="second_country_id">* Вторая страна:</label>
                                    <select class="form-select-2 custom-select {{ $errors->has('second_country_id') ? ' is-invalid' : '' }}"
                                            id="second_country_id"
                                            name="second_country_id">
                                        @foreach($countries as $country)
                                            <option
                                                    value="{{$country->id}}" {{ $country->id ==old('second_country_id', $replay->second_country_id) ? 'selected' : '' }}>
                                                {{$country->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('second_country_id'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('second_country_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="second_location">* Вторая локация:</label>
                            <input type="text" id="second_location" value="{{ old('second_location', $replay->second_location) }}"
                                   name="second_location"
                                   class="form-control {{ $errors->has('second_location') ? ' is-invalid' : '' }}">
                            @if ($errors->has('second_location'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('second_location') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div><!--close div /.form-fields-box-->


                    <div class="form-group margin-top-30">
                        <label for="video_iframe">Вставить HTML код с Youtube с видео реплеем</label>
                        <textarea name="video_iframe"
                                  class="form-control {{ $errors->has('video_iframe') ? ' is-invalid' : '' }}"
                                  id="video_iframe" rows="16">{!! old('video_iframe')??$replay->video_iframe !!}</textarea>
                        @if ($errors->has('video_iframe'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('video_iframe') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group" id="replay-uploader-wrapper" data-upload-url="{{route('replay.upload')}}">
                        <label>*Загрузить новый Replay:</label>
                        <div id="file-uploader-dropzone">
                            @if ($file)
                                <div class="dz-preview dz-file-preview dz-processing dz-success dz-complete js-file-preview">
                                    <div class="dz-image"></div>
                                    <div class="dz-details">
                                        <div class="dz-size">
                                            <span data-dz-size=""><strong>{{ $file->getSizeFormatted() }}</strong> KB</span>
                                        </div>
                                        <div class="dz-filename">
                                            <span data-dz-name="">{{ $file->getFileName() }}</span>
                                        </div>
                                    </div>

                                    <a class="dz-remove js-remove-preloaded-file" href="#" data-dz-remove="">Remove file</a>
                                </div>
                            @endif
                        </div>

                        <input type="hidden" name="file_id" id="file_id"  data-is-uploaded="true" value="{{old('file_id', $replay->file_id)}}"
                               class="@if(old('file_id', $replay->file_id)) js-file-preloaded @endif"/>

                        <span id="replay-file-error-container" class="invalid-feedback" @if ($errors->has('file_id')) style="display: block; " @endif>
                            <strong>{{ $errors->first('file_id') }}</strong>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="content">Короткое описание:</label>
                        <textarea name="content" id="content"
                                  class="form-control {{ $errors->has('content') ? ' is-invalid' : '' }}"
                                  rows="10">{{ old('content') ?? $replay->content }}</textarea>
                        @if ($errors->has('content'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('content') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-blue btn-form">Сохранить</button>
                    </div>
                </form><!-- close div /.user-create-replay-form -->
            </div>
            <div class="col"></div>
        </div><!-- close div /.row -->
    </div><!-- close div /.content-box -->
    <div id="preview" style="display:none"></div>
@endsection

@section('sidebar-right')
    <!--Banners-->
    @include('sidebar-widgets.banner')
    <!-- END Banners -->

    <!-- New Users-->
    @include('sidebar-widgets.new-users')
    <!-- END New Users-->

    <!-- Top Points Users-->
    @include('sidebar-widgets.top-pts-users')
    <!-- END New Users-->

    <!-- Top Rating Users-->
    @include('sidebar-widgets.top-rating-users')
    <!-- END New Users-->


    <!-- User's Replays-->
    @include('sidebar-widgets.users-replays')
    <!-- END User's Replays-->

    <!-- Gallery -->
    @include('sidebar-widgets.random-gallery')
    <!-- END Gallery -->
@endsection

@section('js')
    <!--SCEditor -  WYSIWYG BBCode editor -->
    <script src="{{route('home')}}/js/sceditor/minified/jquery.sceditor.min.js"></script>
    <script src="{{route('home')}}/js/sceditor/minified/jquery.sceditor.xhtml.min.js"></script>
    <script src="{{route('home')}}/js/sceditor/minified/jquery.sceditor.bbcode.min.js"></script>
    <script src="{{route('home')}}/js/sceditor/languages/ru.js"></script>

    <!--JS plugin Select2 - autocomplete -->
    <script src="{{route('home')}}/js/select2.full.min.js"></script>
    <script src="{{route('home')}}/js/dropzone.js"></script>
    <script src="{{route('home')}}/js/replay_form.js"></script>

    <script>

        /**
         * Comments box is the same for all pages
         *SCEditor -  WYSIWYG BBCode editor
         * https://www.sceditor.com/
         * */
        $(function () {

            if ($('#content').length > 0) {
                var content = document.getElementById('content');
                var extraSmiles = <?php echo json_encode($extraSmiles) ?>;
                sceditor.create(content, {
                    format: 'bbcode',
                    style: '{{route("home")}}' + '/js/sceditor/minified/themes/content/default.min.css',
                    emoticonsRoot: '{{route("home")}}' + '/images/',
                    locale: 'ru',
                    toolbar: 'bold,italic,underline|' +
                    'left,center,right,justify|' +
                    'font,size,color,removeformat|' +
                    'emoticon,source|' +
                    'date,time',
                    emoticons: {
                        // Emoticons to be included in the dropdown
                        dropdown: getAllSmiles(extraSmiles),
                        // Emoticons to be included in the more section
                        more: getMoreSmiles()
                    }
                });
            }
            if ($('#video_iframe').length > 0) {
                var video_iframe = document.getElementById('video_iframe');

                sceditor.create(video_iframe, {
                    format: 'xhtml',
                    style: '{{route("home")}}' + '/js/sceditor/minified/themes/content/default.min.css',
                    emoticonsRoot: '{{route("home")}}' + '/images/',
                    locale: 'ru',
                    toolbar: 'youtube|'
                });
            }
        });
        $(function () {
            if($('.form-select-2').length > 0){
                $('.form-select-2').select2({

                });
            }
        });
    </script>
@endsection
