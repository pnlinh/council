<script>
import Replies from './../components/Replies.vue'
import SubscribeButton from './../components/SubscribeButton.vue'
export default {
	components: { Replies, SubscribeButton },

	props: ['thread'],

	data() {
		return {
			repliesCount: this.thread.replies_count,
			locked: this.thread.locked,
			editing: false,
			title: this.thread.title,
			body: this.thread.body,
			form: {}
		}
	},

	created() {
		this.resetForm()
	},

	methods: {
		toggleLock() {
			axios[this.locked ? 'post' : 'delete'](`/locked-threads/${this.thread.slug}`);

			this.locked = ! this.locked;
		},

		update() {
			let uri = `/threads/${this.thread.channel.slug}/${this.thread.slug}`;

			axios.patch(uri, this.form).then(() => {
				this.title = this.form.title;
				this.body = this.form.body;
				this.editing = false;
				flash('Your thread has been updated.');
			});
		},

		resetForm() {
			this.form.title = this.thread.title;
			this.form.body = this.thread.body;

			this.editing = false;
		}
	}
}
</script>
