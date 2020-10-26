<template>
    <!-- Users box-->
    <div class="col-4 px-0">
        <div class="h-100 bg-white">

            <div class="bg-gray px-4 py-2 bg-light">
                <p class="h5 mb-0 py-1">Contacts</p>
            </div>

            <div class="messages-box">
                <div class="list-group rounded-0">
                    <template v-for="channel in channels">
                        <a v-bind:key="channel.key" v-on:click="select(channel)"
                            class="list-group-item list-group-item-action list-group-item-light rounded-0"
                            v-bind:class="{ active: activeKey === channel.key }">
                            <div class="media">
                                <div class="icon">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                </div>
                                <div class="media-body ml-4">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="mb-0">{{ channel.name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>

                    <template v-for="(user) in users">
                        <a v-bind:key="user.key" v-on:click="select(user)"
                            class="list-group-item list-group-item-action list-group-item-light rounded-0"
                            v-bind:class="{ active: activeKey === user.key }">
                            <div class="media">
                                <div class="icon">
                                    <i class="fa"
                                        v-bind:class="[user.is_online ? 'fa-circle text-success' : 'fa-circle-o']"
                                        aria-hidden="true"></i>
                                </div>
                                <div class="media-body ml-4">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="mb-0">{{ user.name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                activeKey: null,
                channels: [],
                users: {}
            }
        },
        created() {
            this.getListChannel();
            this.getListUser();
        },
        methods: {
            getListChannel() {
                axios.get('/api/channels')
                    .then(res => {
                        this.channels = (res.data.data || [])
                            .map(item => ({
                                ...item,
                                key: `channel-${item.id}`,
                                type: 'group'
                            }));

                        if (this.channels[0]) {
                            this.select(this.channels[0]);
                        }
                    });
            },
            getListUser() {
                axios.get('/api/users/online')
                    .then(res => {
                        _.map((res.data.data || []), (item) => {
                            this.users[item.id] = {
                                ...item,
                                key: `user-${item.id}`,
                                type: 'private',
                                is_online: false
                            };
                        });
                        this.subscribePublicChannel();
                        this.$forceUpdate();
                    });
            },
            select(data) {
                this.activeKey = data.key;
                this.$emit('select', data);
            },
            subscribePublicChannel() {
                return Echo.join(`channel.1`)
                    .here((users) => {
                        _.map(users, (user) => {
                            this.users[user.id] = {
                                ...user,
                                key: `user-${user.id}`,
                                type: 'private',
                                is_online: true
                            };
                        });
                        this.$forceUpdate();
                    })
                    .joining((user) => {
                        this.users[user.id] = {
                            ...user,
                            key: `user-${user.id}`,
                            type: 'private',
                            is_online: true
                        };
                        this.$forceUpdate();
                    })
                    .leaving((user) => {
                        _.set(this.users, `${user.id}.is_online`, false);
                        this.$forceUpdate();
                    });
            }
        }
    }

</script>
