<template lang="html">
	<div :id="`reply-${id}`" class="panel" :class="isBest ? 'panel-success' : 'panel-default'">
		<div class="panel-heading">
			<div class="level">
				<span class="flex"><a :href="`/profiles/${reply.owner.name}`" v-text="reply.owner.name"></a> replied <span v-text="ago"></span></span>

				<div>
					<favorite :reply="reply" v-if="signedIn"></favorite>
				</div>
			</div>
		</div>

		<div class="panel-body">
			<div v-if="editing">
				<form @submit.prevent="update">
					<div class="form-group">
						<wysiwyg v-model="body"></wysiwyg>
					</div>
					<button type="submit" class="btn btn-xs btn-primary">Update</button>
					<button type="submit" class="btn btn-danger btn-xs" @click="destroy">Delete</button>
					<button type="button" class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
				</form>
			</div>

			<div v-else v-html="body"></div>
		</div>

		<div class="panel-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
			<div v-if="authorize('owns', reply)">
				<button class="btn btn-xs mr-1" @click="editing = true" v-if="! editing">Edit</button>
			</div>
			<button type="submit" class="btn btn-default btn-xs ml-auto" @click="markBestReply" v-if="authorize('owns', reply.thread) && ! isBest">Best Reply?</button>
		</div>
	</div>
</template>
<script>
	import Favorite from './Favorite.vue';
	export default {
		props: ['reply'],

		components: { Favorite },

		data() {
			return {
				editing: false,
				id: this.reply.id,
				body: this.reply.body,
				created_at: this.reply.created_at,
				isBest: this.reply.isBest,
			}
		},

		computed: {
			ago() {
				return `${moment.utc(this.created_at).fromNow()}...`;
			}
		},

		created() {
			window.events.$on('best-reply-selected', id => {
				this.isBest = (id === this.id);
			});
		},

		methods: {
			update() {
				axios.patch(`/replies/${this.id}`, {
					body: this.body
				})
				.then(() => {
					this.editing = false;
					flash('Your reply has been updated.');
				})
				.catch(error => {
					flash(error.response.data, 'danger');
				});
			},
			destroy() {
				axios.delete(`/replies/${this.id}`)
					.then(() => {
						this.$emit('deleted', this.id);
						flash('Your reply was deleted.');
					});
			},
			markBestReply() {
				axios.post(`/replies/${this.id}/best`);

				window.events.$emit('best-reply-selected', this.id);
			}
		}
	}
</script>
