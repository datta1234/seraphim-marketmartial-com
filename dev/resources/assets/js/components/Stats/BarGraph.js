import { Bar } from 'vue-chartjs'
 
export default {
	extends: Bar,
	props: ['data', 'options'],
	watch: {
        'data': {
            handler: function(nV, oV) {
                this.renderChart(this.data, this.options);
            },
            deep: true
        }
    },
	mounted () {
		this.renderChart(this.data, this.options)
	}
}