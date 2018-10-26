import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins
export default {
	extends: Bar,
	props: ['chartData', 'options'],
    mixins: [reactiveProp],
	watch: {
        'chartData.labels': function(nV, oV) {
            this.fireRender();
        },
        'chartData.datasets': function(nV, oV) {
            this.fireRender();
        }
    },
    methods: {
        fireRender(){
            Vue.nextTick(() => {
                this.renderChart(this.chartData, this.options);
            })
        }
    },
	mounted () {
		this.fireRender(this.chartData, this.options)
	}
}