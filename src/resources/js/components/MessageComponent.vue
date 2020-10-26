<template>
    <div v-if="isOwnerMessage" class="media w-50 ml-auto mb-3">
        <div class="media-body">
            <div class="bg-primary rounded py-2 px-3 mb-2">
                <p class="text-small mb-0 text-white">{{ message.message }}</p>
            </div>
        </div>
    </div>
    <div v-else class="media w-50 mb-3">
        <div class="media-body ml-3">
            <h6 class="mt-0">{{ message.from_user.name }}</h6>
            <div class="bg-light rounded py-2 px-3 mb-2">
                <p class="text-small mb-0 text-muted">{{ message.message }}</p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['message', 'isOwnerMessage']
    }
</script>
