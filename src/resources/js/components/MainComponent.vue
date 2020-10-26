<template>
    <div class="container py-5 px-4">
        <div class="row rounded-lg overflow-hidden shadow">
            <!-- Users box-->
            <user-component v-on:select="select"></user-component>
            <!-- Chat Box-->
            <div class="col-8 px-0">
                <div class="px-4 py-5 chat-box bg-white">
                    <infinite-loading :identifier="activeChat.chatBoxId" direction="top" @infinite="infiniteHandler"></infinite-loading>
                    <chat-box-component v-bind:user="user" v-bind:messages="currentMessages">
                    </chat-box-component>
                </div>

                <!-- Typing area -->
                <input-box-component v-bind:active-chat="activeChat" v-on:send="appendMessage"></input-box-component>
            </div>
        </div>
    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';

    export default {
        components: {
            InfiniteLoading,
        },
        data: function () {
            return {
                activeChat: {},
                chatBoxMessages: {},
                user: {},
                currentMessages: [],
                state: null,
                defaultChannelId: 1
            }
        },
        created() {
            this.getUser();
            this.subscribeChannel(this.defaultChannelId);
        },
        methods: {
            getUser() {
                axios.get('/api/me')
                    .then(res => {
                        if (!_.isEmpty(res.data.data)) {
                            this.user = res.data.data;
                            this.subscribePrivateMessage();
                        }
                    });
            },
            getListMessage(chatId, chatType, options = {}) {
                const params = {
                    to: chatId,
                    type: chatType
                };

                if (!_.isEmpty(_.get(this.chatBoxMessages, `${chatType}-${chatId}`))) {
                    params.last_message_id = _.first(this.chatBoxMessages[`${chatType}-${chatId}`]).id;
                }

                return axios.get('/api/messages', { params })
                    .then(res => {
                        if (!_.isEmpty(res.data.data)) {
                            this.chatBoxMessages[`${chatType}-${chatId}`] = _.concat(
                                _.sortBy(_.get(res, 'data.data'), 'id'),
                                _.get(this.chatBoxMessages, `${chatType}-${chatId}`, [])
                            );

                            if (_.isEmpty(params.last_message_id)) {
                                this.scrollChatBoxToBottom();
                            }
                        } else {
                            this.state.complete();
                        }
                    })
                    .finally(() => this.state.loaded());
            },
            select(data) {
                this.activeChat = data;
                this.activeChat.chatBoxId = `${data.type}-${data.id}`;
                this.currentMessages = [];

                if (!_.isEmpty(this.chatBoxMessages[this.activeChat.chatBoxId])) {
                    this.currentMessages = this.chatBoxMessages[this.activeChat.chatBoxId];
                    this.scrollChatBoxToBottom();
                } else {
                    this.getListMessage(data.id, data.type)
                        .then(res => this.currentMessages = this.chatBoxMessages[this.activeChat.chatBoxId]);
                }
            },
            appendMessage(chatId, chatType, message) {
                const chatBoxId = `${chatType}-${chatId}`;

                this.chatBoxMessages[chatBoxId] = this.chatBoxMessages[chatBoxId] || [];
                this.chatBoxMessages[chatBoxId].push(message);
                this.chatBoxMessages[chatBoxId] = _.sortBy(this.chatBoxMessages[chatBoxId]);
                this.currentMessages = this.chatBoxMessages[this.activeChat.chatBoxId];
                this.scrollChatBoxToBottom();
            },
            scrollChatBoxToBottom() {
                const el = this.$el.getElementsByClassName('chat-box')[0];
                if (el) {
                    setTimeout(() => {
                        el.scrollTop = el.scrollHeight;
                    }, 0);
                }
            },
            infiniteHandler($state) {
                this.state = $state;
                if (
                    !_.isEmpty(this.activeChat) &&
                    !_.isEmpty(_.get(this.chatBoxMessages, `${this.activeChat.type}-${this.activeChat.id}`))
                ) {
                    this.getListMessage(this.activeChat.id, this.activeChat.type);
                }
            },
            subscribePrivateMessage() {
                return Echo.private(`user.${this.user.id}`)
                    .listen('SendPrivateMessageEvent', (e) => {
                        const data = e.data;
                        const chatBoxId = `private-${data.from_user.id}`;

                        if (!_.isEmpty(data) && !_.isEmpty(this.chatBoxMessages[chatBoxId])) {
                            this.appendMessage(data.from_user.id, 'private', data);
                        }
                    });
            },
            subscribeChannel(channelId) {
                return Echo.join(`channel.${channelId}`)
                    .listen('SendChannelMessageEvent', (e) => {
                        const data = e.data;
                        const chatBoxId = `group-${channelId}`;

                        if (!_.isEmpty(data) && !_.isEmpty(this.chatBoxMessages[chatBoxId])) {
                            this.appendMessage(channelId, 'group', data);
                        }
                    });
            }
        }
    }

</script>
