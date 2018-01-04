@component('profiles.activities.activity', [
	'icon' => 'fa-comments'
])
	@slot('heading')
		{{ $profileUser->name }} published <a href="{{ $activity->subject->path() }}">{{ $activity->subject->title }}</a>.
	@endslot

	@slot('body')
		{{ $activity->subject->excerpt }}
	@endslot
@endcomponent
