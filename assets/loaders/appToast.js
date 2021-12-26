import {createApp} from "vue";
import MainToastComponent from "../components/toast/MainToastComponent";

const appToast = createApp(MainToastComponent);
appToast.mount('#toast-container');