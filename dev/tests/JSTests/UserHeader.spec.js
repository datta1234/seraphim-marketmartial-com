import { mount } from '@vue/test-utils';
import { createLocalVue } from '@vue/test-utils'
import UserHeader from '../../resources/assets/js/components/UserHeaderComponent.vue';

const localVue = createLocalVue();
localVue.mixin({
    methods: {
        formatRandQty(val) {
            return ('' + val);
        },
    }
});

describe('UserHeaderComponent.vue', () => {

	describe('Component test with user details', () => {

		const passedData = {
			user: "Test Name",
			organisation: "Test Organisation",
			rebate: 748698
		};
		const wrapper = mount(UserHeader, {
			propsData: {
				user_name: passedData.user,
				organisation: passedData.organisation,
				total_rebate: passedData.rebate,
			},
			localVue
		});

		it('User details are present in the header', () => {
			const user_details = wrapper.find('.user-details');
			chai.assert(user_details.text() == 'Welcome ' + passedData.user + ' (' + passedData.organisation + ')', 'Both User name and Organisation is present in the user-details');
		});

		it('Rebate amount is present in the header', () => {
			const rebate = wrapper.find('.total-rebate');
			chai.assert(rebate.text() == 'Rebates: ' + passedData.rebate);
		});
	});

	describe('Component test with admin details', () => {
		const passedData = {
			user: "Test Admin Name",
			rebate: 748698
		};
		const wrapper = mount(UserHeader, {
			propsData: {
				user_name: passedData.user,
				total_rebate: passedData.rebate,
			},
			localVue
		});

		it('User details are present in the header', () => {
			const user_details = wrapper.find('.user-details');
			chai.assert(user_details.text() == 'Welcome ' + passedData.user, 'Admin name is present in the user-details');
		});
	});
});

