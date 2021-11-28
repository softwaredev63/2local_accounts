<template>
    <div>
        <div class="buy-send-container">
            <button class="btn btn-show-send-modal"  v-on:click="toggleModal(true)">
                Send
            </button>
        </div>
        <send-token v-if="isShowModal" :is-show-modal="isShowModal" :tokens="getTokens()" :on-close="closeSendTokenModal"></send-token>
    </div>
</template>

<script>
    import SendToken from "./SendToken";

    export default {
        data() {
            return {
                isShowModal: false
            }
        },
        props: {
            tokens: Array,
            twoFAEnabled: Boolean
        },
        methods: {
            toggleModal: function(value) {
                if (!this.twoFAEnabled) {
                    alert('2FA should be enabled to send tokens'); return;
                }
                this.isShowModal = value;
            },
            closeSendTokenModal: function() {
                this.isShowModal = false;
            },
            getTokens: function () {
                return this.tokens || [];
            },
        },
        comments: {
            SendToken
        },
    }
</script>
