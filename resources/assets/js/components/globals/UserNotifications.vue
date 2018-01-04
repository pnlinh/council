<template lang="html">
	<li class="dropdown" v-if="notifications.length">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<span class="fa fa-bell"></span>
		</a>

		<ul class="dropdown-menu" id="notifications-menu">
			<li v-for="notification in notifications">
				<a :href="notification.data.link" @click="markAsRead(notification)">
					<i class="text-primary fa fa-fw fa-btn fa-comment"></i> {{ notification.data.notification_message }}
				</a>
			</li>
		</ul>
	</li>
</template>

<script>
export default {
	data() {
		return {
			notifications: [],
		}
	},

	created() {
		this.fetchNotifications();
	},

	methods: {
		fetchNotifications() {
			axios.get(`/profiles/${window.App.user.name}/notifications`)
				.then(({data}) => this.notifications = data)
		},

		markAsRead(notification) {
			axios.delete(`/profiles/${window.App.user.name}/notifications/${notification.id}`);
		}
	}
}
</script>

<style lang="scss" scoped>
	ul#notifications-menu {
		li {
			padding: 3px 0;

			&:not(:last-child) {
				border-bottom: 1px dashed #D3E0EA;
			}
		}
	}
</style>
