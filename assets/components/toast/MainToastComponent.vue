<template>
    <div class="basicToastContainer d-flex flex-column-reverse" ref="basicToastContainer"></div>
</template>

<script>
import BasicToastComponent from "./BasicToastComponent";
import {emitter} from "../../services/Emitter";
import {createApp, defineComponent} from "vue";

export default {
    name: "MainToastComponent",
    components: {BasicToastComponent},
    mounted() {
        emitter.on('newBasicToast', toast => {
            const basicToastView = this.createComponent(BasicToastComponent, toast);

            const div = document.createElement('div');
            this.$refs.basicToastContainer.appendChild(div);
            createApp(basicToastView).mount(div);
        });
    },
    methods: {
        createComponent(component, toast) {
            const newToast = {
                toast: toast,
            };

            if ('squareColorClass' in toast) {
                newToast.squareColorClass = toast.squareColorClass;
            }

            return defineComponent({
                extends: component,
                data() {
                    return newToast;
                },
            });
        }
    },
}
</script>

<style scoped>

</style>