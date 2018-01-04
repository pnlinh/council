@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <ais-index
            app-id="{{ config('scout.algolia.id') }}"
            api-key="{{ config('scout.algolia.key') }}"
            index-name="threads"
            query="{{ request('q') }}"
        >
            <div class="col-md-8">
                <ul class="list-group">
                <ais-results>
                    <template slot-scope="{ result }">
                        <li class="list-group-item">
                            <div class="level">
                                <a :href="result.path">
                                    <ais-highlight :result="result" attribute-name="title"></ais-highlight>
                                </a>
                                <span class="glyphicon glyphicon-ok-circle ml-auto" :class="result.best_reply_id ? 'success' : ''"></span>
                            </div>
                            <p>
                                <ais-highlight :result="result" attribute-name="body"></ais-highlight>
                            </p>
                        </li>
                    </template>
                </ais-results>
                </ul>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Search</div>
                    <div class="panel-body">
                        <ais-search-box>
                            <ais-input :autofocus="true" placeholder="Find a thread..." class="form-control"></ais-input>
                        </ais-search-box>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Filter by Channel</div>
                    <div class="panel-body">
                        <ais-refinement-list attribute-name="channel.name"></ais-refinement-list>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Filter by Status</div>
                    <div class="panel-body">
                        <ais-refinement-list attribute-name="resolved_status"></ais-refinement-list>
                    </div>
                </div>
            </div>
        </ais-index>
    </div>
</div>
@endsection
