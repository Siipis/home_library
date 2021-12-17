<template>
    <div id="scanner_area"></div>
</template>

<script>
import {Html5QrcodeScanner} from "html5-qrcode"

export default {
    name: "Scanner",

    props: {
        qrbox: {
            type: Number,
            default: 250
        },
        fps: {
            type: Number,
            default: 10
        },
    },

    data() {
        return {
            scanner: undefined
        }
    },

    mounted () {
        const config = {
            fps: this.fps,
            qrbox: this.qrbox,
            facingMode: 'environment',
        };
        this.scanner = new Html5QrcodeScanner('scanner_area', config);
        this.scanner.render(this.onScanSuccess);
    },

    methods: {
        onScanSuccess (decodedText, decodedResult) {
            this.$emit('result', decodedText, decodedResult);
        }
    }
}
</script>

<style scoped>

</style>
