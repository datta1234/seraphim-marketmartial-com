<template>
    <div dusk="notification-toggle" class="notification-toggle">
         <!-- Rounded toggle switch -->
        <div class="float-right">
            <span class="toggle">Enable chat notification sound</span>
            <label class="switch mb-0 ml-1" id="notification-toggle">
                <input type="checkbox" v-model="notification_toggler">
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
                notification_sound: null
            };
        },
        watch: {
            'notification_toggler': function(val) {
                if(val && this.notification_sound) {
                    // Play notification sound
                    this.notification_sound.play(`${axios.defaults.baseUrl}/sounds/plink_sound`);
                    this.test();
                }
            }
        },
        methods: {
            /**
             * Listens for a chatMessageReceived event firing
             *
             * @event /$root#chatMessageReceived
             */
            newChatMessageListener() {
                this.$root.$on('chatMessageReceived', this.notifyEventReceived);
            },
            notifyEventReceived() {
                if(this.notification_toggler && this.notification_sound) {
                    // Play notification sound
                    this.notification_sound.play(`${axios.defaults.baseUrl}/sounds/plink_sound`);
                }
            },
            test() {
                let src = 'https://file-examples.com/wp-content/uploads/2017/11/file_example_MP3_700KB.mp3';
                let audio = new Audio(src);
                audio.play();
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
            this.notification_sound = new Audio();
            console.log(`CHECK: ${axios.defaults.baseUrl}/sounds/plink_sound`);
            /*axios.get(`${axios.defaults.baseUrl}/sounds/plink_sound`)
            .then(response => {
                console.log("Response: ", response);
            })
            .catch(err => {
                console.log(err)
            });*/

        }
    }

</script>