<template>
    <div>
        <message-component v-for="item in messages" v-bind:key="item.key" v-bind:message="item"
            v-bind:isOwnerMessage="user.id === item.from_user.id">
        </message-component>
    </div>
</template>

<script>
    export default {
        props: {
            messages: {
                type: Array,
                default: function() {
                    return [];
                }
            },
            user: {
                type: Object
            }
        },
        methods: {
        }
    }
</script>
