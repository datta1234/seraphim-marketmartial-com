<template>

    <div class="typehead-wrapper">

        <input class="form-control" type="text" name="search" placeholder="Search" v-model="params.code" @input="getResource" @focus="showHead()"/>
        <div class="head" v-if="head.visable">
            <ul class="list-group">
                <li v-for="item in dataList" class="list-group-item list-group-item-action" @click="setValue(item.code, item)">{{ item.code }}</li>
            </ul>
        </div>

    </div>

</template>
<style type="text/css">

.head > ul > li {
    cursor: pointer;
}

.head{

    position: absolute;
    background: #fff;
    width: 100%;
    z-index: 1;

}

.typehead-wrapper{
    position: relative;
}

</style>
<script>
export default {

    props: {
        'route': {
            type: String
        },
        'callback': {
            type: Function
        },
    },
    data()
    {
        return{

            head: {
                visable: false
            },
            dataList: [],
            params: {
                code: '',
                stock: null,
            },
            config: {
                min: 2,
                debounce: 250
            }

        }
    },
    methods: {

        /**
         * Set input value on select
         * @param String Value
         * @return Void()
         */
         setValue(value, stock){
            this.params.code  = value;
            this.params.stock  = stock;
            this.head.visable   = false;
            this.callback(this.params, true);
        },

        /**
         * HTTP Get request for lsit of filtered users
         * @return Void()
         */
         getResource(){

            if( window.typeHeadDebounce ) {
                clearTimeout(window.typeHeadDebounce);
            }

            if(this.params.code.length >= this.config.min) {

                window.typeHeadDebounce = setTimeout(()=>{

                    axios.get(this.route, {params: this.params})
                    .then((response)=>{
                        this.dataList = response.data;
                        if(this.dataList.length < 1) {
                            this.callback(this.params, false);
                        }
                    })

                }, this.config.debounce )  

            }

            if(this.params.code.length < this.config.min) {
                this.dataList = [];
                this.callback(null, true);
            }


        },

        /**
         * Show type head drop down
         * @return Void()
         */
         showHead(){
            this.head.visable = true;
        },

        /**
         * Hide type a head drop down
         * @return Void()
         */
         hideHead(){
            this.head.visable = false;
        },
    },

    mounted() {
    }
}

</script>
