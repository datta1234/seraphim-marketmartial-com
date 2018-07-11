<template>
    
<span id="header-wrapper">
    <flat-surface-shader v-if="config_options.show_shader" class="shader"
      type="canvas" 
      :light="light" 
      :mesh="mesh">
    </flat-surface-shader>
</span>

</template>

<script>
    export default {

        data: function() {

          return {

            mesh: {
                width: 1.2,
                height: 1.2,
                depth: 10,
                segments: 16,
                slices: 8,
                xRange: 0.3,
                yRange: 0.1,
                zRange: 0.5,
                ambient: '#000021',
                diffuse: '#FFFFFF',
                speed: 0.0006,
            },

            light: {
                count: 1,
                xyScalar: 1,
                zOffset: 100,
                ambient: '#000705', 
                diffuse: '#239192', 
                draw: false,
                speed: 0.0002,
                gravity: 500,
                dampening: 0.1,
                minLimit: 10,
                maxLimit: null,
                minDistance: 20,
                maxDistance: 400,
                autopilot: false,
                draw: false
            },

            config_options: {
                window_width: 768,
                show_shader: true
            }


          }

        },

        methods: {

        },
        computed: {

        },
        mounted: function() { 
            if(window.innerWidth < this.config_options.window_width) {
                this.config_options.show_shader = false;
            }
        }
    }
</script>
