<template>
    <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" ref="toast">
        <div class="toast-header">
<!--            <img src="" class="rounded me-2" alt="...">-->
            <strong class="me-auto">{{ header ?? toast.header }}</strong>
            <small class="text-muted">{{ time ?? toast.time}}</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" @click="closeToast"></button>
        </div>
        <div class="toast-body">
            {{ message ?? toast.message }}
        </div>
    </div>
</template>

<script>
const $ = require('jquery');

export default {
    name: 'BasicToastComponent',
    data() {
        return {
            toast: {},
            liveSeconds: 7,
        };
    },
    mounted() {
        setTimeout(() => this.$refs.toast.remove(), this.liveSeconds * 1000);
    },
    props: ['header', 'message', 'time'],
    methods: {
        closeToast(event) {
            $(event.target).closest('.toast').fadeOut('fast');
        },
    },
};
</script>