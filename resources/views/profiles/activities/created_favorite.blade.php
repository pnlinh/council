@component('profiles.activities.activity', [
	'icon' => 'fa-star'
])
	@slot('heading')
		{{ $profileUser->name }} favorited a <a href="{{ $activity->subject->favorited->path() }}">reply.</a>
	@endslot

	@slot('body')
		{{ $activity->subject->favorited->body }}
	@endslot
@endcomponent
