@extends('layouts.app')

@section('head')
<script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">New Discussion</div>
                <div class="panel-body">
                    <form class="form" action="/threads" method="post">
                        {{ csrf_field() }}
                        <div class="form-group {{ $errors->has('channel_id') ? 'has-error' : ''}}">
                            <label for="channel_id">Channel</label>
                            <select class="form-control" name="channel_id" required>
                                <option value="">Select a Channel</option>
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('channel_id'))
                                <span class="help-block">{{ $errors->get('channel_id')[0] }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required>
                            @if ($errors->has('title'))
                                <span class="help-block">{{ $errors->get('title')[0] }}</span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('body') ? 'has-error' : ''}}">
                            <label for="title">Body</label>
                            <wysiwyg name="body"></wysiwyg>
                            @if ($errors->has('body'))
                                <span class="help-block">{{ $errors->get('body')[0] }}</span>
                            @endif
                        </div>
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key')}}"></div>
                        @if ($errors->has('g-recaptcha-response'))
                            <span class="help-block" style="color:#a94442">{{ $errors->get('g-recaptcha-response')[0] }}</span>
                        @endif
                        <div class="form-group action-buttons" style="display:flex; justify-content:flex-end;">
                            <a class="btn btn-default" href="/threads" style="margin-right: 10px">Cancel</a>
                            <button type="submit" class="btn btn-primary">Publish Discussion</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
