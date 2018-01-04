<div class="panel panel-default" v-if="editing">
        <div class="panel-heading">
            <div class="level">
                <input type="text" class="form-control" v-model="form.title">
            </div>
        </div>

        <div class="panel-body">
            <wysiwyg v-model="form.body"></wysiwyg>
        </div>

        <div class="panel-footer level">
            <button class="btn btn-xs btn-primary mr-1" @click="update">Update</button>
            <button class="btn btn-xs btn-default" @click="resetForm">Cancel</button>
            <form class="ml-auto" id="delete-form" action="{{ $thread->path() }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}

                <button type="submit" class="btn btn-link">Delete Thread</button>
            </form>
        </div>
    </div>

<div class="panel panel-default" v-else>
    <div class="panel-heading">
        <div class="level">
            <span class="flex">
                <img src="{{ $thread->creator->avatar_path }}" width="32" height="32" class="avatar">
                <a href="{{ route('profile', $thread->creator) }}">
                    {{ $thread->creator->name }}
                </a> posted @{{ title }}
            </span>
        </div>
    </div>

    <div class="panel-body" v-html="body"></div>

    @can('update', $thread)
        <div class="panel-footer">
            <div class="btn btn-xs btn-default" @click="editing = true">Edit</div>
        </div>
    @endcan
</div>