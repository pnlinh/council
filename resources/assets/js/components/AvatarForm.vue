<template>
    <div>

        <div class="level">
            <img :src="avatar" width="50" height="50" class="avatar mr-1">
            <h1>
                {{ user.name }}
                <small v-text="reputation"></small>
            </h1>
        </div>

        <form v-if="canUpdate" method="post" enctype="multipart/form-data">
            <div class="level">
                <image-upload name="avatar" @loaded="onLoad"></image-upload>
            </div>
        </form>
    </div>
</template>

<script>

import ImageUpload from './ImageUpload';

export default {
    props: ['user'],

    components: { ImageUpload },

    data() {
        return {
            avatar: this.user.avatar_path
        }
    },

    computed: {
        canUpdate() {
            return this.authorize(user => user.id === this.user.id)
        },

        reputation() {
            return `${this.user.reputation} XP`;
        }
    },

    methods: {
        onLoad(avatar) {
            this.avatar = avatar.src;
            this.persist(avatar.file);
        },

        persist(avatar) {
            let data = new FormData();

            data.append('avatar', avatar);

            axios.post(`/api/users/${this.user.name}/avatar`, data)
                .then(() => flash('Avatar uploaded.'));
        }
    }
}
</script>

<style lang="css">
</style>
