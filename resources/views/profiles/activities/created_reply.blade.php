@component('profiles.activities.activity', [
	'icon' => 'fa-reply'
])
	@slot('heading')
		{{ $profileUser->name }} replied to <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a>
	@endslot

	@slot('body')
		{{ $activity->subject->excerpt }}
	@endslot
@endcomponent
