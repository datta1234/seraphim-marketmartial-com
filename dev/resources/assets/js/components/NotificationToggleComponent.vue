<template>
    <div dusk="notification-toggle" class="notification-toggle">
         <!-- Rounded toggle switch -->
        <div class="float-right">
            <span class="toggle">Enable notification sound</span>
            <label class="switch mb-0 ml-1" id="notification-toggle">
                <input type="checkbox" v-model="notification_toggler" @change="toggleUpdate">
                <span class="slider round"></span>
            </label>
        </div>
    </div>
</template>

<script>
    //lib imports
    import { EventBus } from '~/lib/EventBus.js';
    export default {
        data() {
            return {
                notification_toggler: false,
                notification_sound: null,
                audio_timeout_buffer: null,
            };
        },
        methods: {
            /**
             * Listens for a playNotificationAudio event firing
             *
             * @event /$root#playNotificationAudio
             */
            notifyListener() {
                this.$root.$on('audioNotify', this.playNotificationAudio);
            },
            toggleUpdate() {
                try {
                    localStorage.setItem('notificationState', this.notification_toggler);
                } catch(e) {
                    localStorage.removeItem('notificationState');
                }
                this.playNotificationAudio();
            },
            playNotificationAudio() {
                // Play notification sound
                if(this.notification_toggler && this.notification_audio) {
                    try {
                        // Timeout buffer to not spam the user with notification sounds
                        if(!this.audio_timeout_buffer) {
                            this.notification_audio.play();
                            this.audio_timeout_buffer = setTimeout(() => {
                                this.audio_timeout_buffer = null;
                            }, 2000);
                        }
                    } catch(error) {
                        console.log("Failed to play notification audio.");
                    }
                }
            }
        },
        mounted() {
            if (localStorage.getItem('notificationState') != null) {
                try {
                    this.notification_toggler = localStorage.getItem('notificationState') === 'true';
                } catch(e) {
                    this.notification_toggler = false;
                    localStorage.removeItem('notificationState');
                }
            } else {
                this.notification_toggler = false;
                try {
                    localStorage.setItem('notificationState', this.notification_toggler);
                } catch(e) {
                    localStorage.removeItem('notificationState');
                }
            }

            this.notification_audio = document.getElementById("notifyAudio");
            this.notifyListener();
        }
    }

</script>