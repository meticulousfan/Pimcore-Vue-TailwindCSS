<template>
    <div @click="doCopy(value)" class="select-all text-xs italic">
        {{ value }}
    </div>
</template>

<script>
import { copyText } from 'vue3-clipboard'
import { useToast } from "vue-toastification";

export default {
    name: "Copy",
    props: {
        value: String,
        name: String,
    },
    setup () {
        const toast = useToast();

        return {copyText, toast}
    },
    methods: {
        doCopy(value) {
            this.copyText(value, undefined, (error, event) => {
                if (!error) {
                    this.toast.success(this.name + ' copied to the clipboard', {
                        timeout: 2000
                    });
                }
            })
        }
    }
}
</script>
