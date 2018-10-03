<template>
  <div class="day-month-picker">
     <b-row>
          <b-col sm="6">
                <b-form-select class="day" name="day-month-picker-day" :options="days" v-model="selectedDay" @change="dateChange"></b-form-select>
          </b-col>

          <b-col sm="6">
                  <b-form-select class="month" name="day-month-picker-month" :options="months" v-model="selectedMonth" @change="dateChange"></b-form-select>
          </b-col>
    </b-row>
    <input type="hidden" :name="name" :value="selectedDate">
  </div>
</template>

<script>
    export default {
        props:{
          'value': {
            type: String
          },
          'name': {
            type: String
          }
        },
        data() { 
            return {          
                month : '',
                day  : '',
                months:[],
                days:[],
                monthNames:["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                selectedMonth: null,
                selectedDay: null,
                selectedDate: null
            }
        },
        methods: {
          dateChange() {

             Vue.nextTick(() => {
                  //set the date to null
                  this.selectedDate = null;
                  

                  if(this.selectedMonth != null && this.selectedDay != null)
                  {
                      let month = this.selectedMonth + 1;
                      this.selectedDate =  moment(month + '-' + this.selectedDay, 'M-DD', false).format('YYYY-MM-DD');
                  } 
              });
            
          }
        },
        mounted() {
        
          
          this.selectedDate = moment(this.value, 'YYYY-MM-DD', false).format('YYYY-MM-DD'); 
          this.selectedMonth = moment(this.value, 'YYYY-MM-DD', false).format('M') - 1; 
          this.selectedDay = moment(this.value, 'YYYY-MM-DD', false).format('DD'); 
          console.log(this.selectedMonth);
          this.days.push({text:"Please Select a day.",value:null});
          this.months.push({text:"Please Select a day.",value:null});

          for (var i = 1; i <= 31; i++) 
          {
            if(i < 10)
            {
                this.days.push({text:'0'+i,value:'0' + i});
            }else
            {
                this.days.push({text:i,value:i});
            }
          }
          
          for (var i = 0; i < this.monthNames.length  ; i++) 
          {
            this.months.push({text:this.monthNames[i],value:i});
          }

        }
    }
</script>