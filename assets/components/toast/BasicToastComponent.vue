<template>
    <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
<!--            <img src="" class="rounded me-2" alt="...">-->
            <strong class="me-auto">{{ toast.header }}</strong>
            <small class="text-muted">{{ toast.time}}</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" @click="closeToast"></button>
        </div>
        <div class="toast-body">
            {{ toast.message }}
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
        };
    },
    methods: {
        closeToast(event) {
            $(event.target).closest('.toast').fadeOut('fast');
        },
    },
};
</script>