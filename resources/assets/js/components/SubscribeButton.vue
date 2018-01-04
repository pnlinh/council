<template lang="html">
	<button type="button" class="btn" :class="active ? 'btn-primary' : 'btn-default'" @click="toggleSubscription" v-text="active ? 'Subscribed' : 'Subscribe'">
	</button>
</template>

<script>
export default {
	props: ['subscribed'],

	data() {
		return {
			active: this.subscribed,
		}
	},

	methods: {
		toggleSubscription() {
			let requestType = this.active ? 'delete' : 'post';

			axios[requestType](`${location.pathname}/subscriptions`)
				.then(() => {
					this.active = ! this.active;
				})
		}
	}
}
</script>
