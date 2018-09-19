<template>
    <div dusk="my-activity-controller" class="my-activity-controller">
        <b-card v-bind:class="{ 'mt-5': index == 0 }" v-for="(year, index) in table_years" no-body class="mb-5">
            <b-card-header header-tag="header" class="p-1" role="tab">
                <b-btn block href="#" v-b-toggle="'accordion'+index" variant="info">{{ year }}</b-btn>
            </b-card-header>
            <b-collapse :id="'accordion'+index" :visible="active_collapse.index == index" accordion="my-accordion" role="tabpanel">
                <b-card-body>
                    <year-table></year-table>
                </b-card-body>
            </b-collapse>
        </b-card>


    </div>
</template>

<script>
    export default {
    	props: {
            'years': {
                type: Array
            },
        },
        data() {
            return {
            	table_years: [],
                active_collapse: {
                    index: 0,
                    state: true,
                },
                table_data:{},
            };
        },
        methods: {
            toggleState(toggle_id) {
                let index = toggle_id.substr(toggle_id.indexOf('accordion') + 9);
                if(toggle_id == ('accordion'+this.active_collapse.index)) {

                } else {
                    this.active_collapse.index = index;
                    this.active_collapse.state = true;
                    this.loadTableData(index);
                }
                console.log("TEST", collapse, toggle);
            },
            loadTableData(index) {
                console.log("Loading table data",this.table_data);
                if(!this.table_data[index].data){
                    axios.get(axios.defaults.baseUrl + 'my-activity/year', {
                        params:{
                            'year': this.table_years[index]
                        }
                    })
                    .then(activityResponse => {
                        if(activityResponse.status == 200) {
                            console.log(activityResponse);    
                        } else {
                            console.error(err);    
                        }
                    }, err => {
                        this.$toasted.error("Failed to load "+this.table_years[index]+" year data.")
                        console.error(err);
                    });
                }
            },
            initTableData() {
                this.table_years.forEach( (element, key) => {
                    this.table_data[key] = {
                        data: null,
                        date: element,
                    };
                });
                this.loadTableData(0)
            }
        },
        mounted() {
            let unordered_years = [];
            this.years.forEach(element => {
                unordered_years.push(element.year);
            });
            this.$root.dateStringArraySort(unordered_years, 'YYYY');
            this.table_years = unordered_years.reverse();
            this.$root.$on('bv::toggle::collapse',this.toggleState);
            this.initTableData();
        }
    }
</script>