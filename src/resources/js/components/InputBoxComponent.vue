<template>
    <!-- Typing area -->
    <div class="bg-light">
        <div class="input-group">
            <input v-model="message" type="text" placeholder="Type a message" aria-describedby="button-addon2"
                class="form-control rounded-0 border-0 py-4 bg-light" v-on:keyup.enter="send()">
            <div class="input-group-append">
                <button v-on:click="send()" id="button-addon2" type="button" class="btn btn-link">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            activeChat: {
                type: Object
            },
        },
        data: function () {
            return {
                message: '',
                isSending: false
            }
        },
        created() {},
        methods: {
            send() {
                if (this.isSending || _.isEmpty(this.activeChat)) {
                    return;
                }

                const data = {
                    to: this.activeChat.id,
                    type: this.activeChat.type,
                    message: this.message,
                };

                this.message = '';
                this.isSending = true;

                axios.post('/api/messages', data)
                    .then(res => {
                        this.isSending = false;
                        if (!_.isEmpty(res.data.data)) {
                            this.$emit('send', data.to, data.type, res.data.data);
                        }
                    })
                    .catch((error) => {
                        this.message = data.message;
                        this.isSending = false;
                        alert(error.message);
                    });
            }
        }
    }

</script>
