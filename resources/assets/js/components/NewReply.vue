<template>
	<div>
		<template v-if="signedIn">
			<div class="panel panel-default">
				<div class="panel-heading">Have something to say...</div>
				<div class="panel-body">
					<div class="form-group">
						<wysiwyg name="body" v-model="body" :shouldClear="completed"></wysiwyg>
					</div>
					<div class="form-group" style="display:flex; justify-content:flex-end;">
						<button type="submit" class="btn btn-primary" @click="addReply">Reply</button>
					</div>
				</div>
			</div>
		</template>
		<p class="text-center" v-else>
			Please <a href="/login">log in</a> or <a href="/register">sign up</a> to participate in this discussion.
		</p>
	</div>
</template>

<script>
import 'jquery.caret';
import 'at.js';

export default {
	data() {
		return {
			body: '',
			endpoint: location.pathname + '/replies',
			completed: false
		}
	},

	mounted() {
		$('#body').atwho({
			at: '@',
			delay: 750,
			callbacks: {
				remoteFilter: function(query, callback) {
					$.getJSON('/api/users', {name: query}, function(usernames){
						callback(usernames);
					});
				}
			}
		})
	},

	methods: {
		addReply() {
			axios.post(this.endpoint, { body: this.body })
				.then(({data}) => {
					this.body = '';
					this.completed = true;

					this.$emit('created', data);

					flash('Your reply was posted.');
				}).catch(error => {
					flash(error.response.data, 'danger');
				});
		}
	}
}
</script>
