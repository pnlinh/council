@extends('layouts.app')

@section('head-css')
<link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
@endsection

@section('content')
<thread-view :thread="{{ $thread }}" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                @include('threads._body')

                <replies @removed="repliesCount--" @added="repliesCount++"></replies>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>
                            This thread was published {{ $thread->created_at->diffForHumans() }} by <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a>, and currently has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}.
                        </p>

                        <p>
                            <subscribe-button :subscribed="{{ json_encode($thread->isSubscribedTo) }}" v-if="signedIn"></subscribe-button>

                            @can('admin', auth()->user())
                            <button class="btn btn-default"
                                    v-if="authorize('isAdmin')"
                                    @click="toggleLock"
                                    v-text="locked ? 'Unlock' : 'Lock'"
                                    >
                            </button>
                            @endcan
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</thread-view>
@endsection
