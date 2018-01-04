<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Browse <span class="caret"></span></a>
	<ul class="dropdown-menu">
		<li><a href="/threads">All Threads</a></li>

		@if (Auth::user())
			<li><a href="/threads?by={{ auth()->user()->name }}">My Threads</a></li>
		@endif

		<li><a href="/threads?popular=1">Popular Threads</a></li>
		<li><a href="/threads?unanswered=1">Unanswered Threads</a></li>
	</ul>
</li>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Channels <span class="caret"></span></a>
	<ul class="dropdown-menu">
		@foreach ($channels as $channel)
			<li><a href="{{ $channel->path() }}">{{ $channel->name }}</a></li>
		@endforeach
	</ul>
</li>
@if (Auth::user())
	<li><a href="/threads/create">New Discussion</a></li>
@endif
