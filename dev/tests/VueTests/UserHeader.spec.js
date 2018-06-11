import { mount } from '@vue/test-utils';
import UserHeader from '../../resources/assets/js/components/UserHeaderComponent.vue';

describe('UserHeaderComponent.vue', () => {
	it('This is just a test run', () => {
		const wrapper = mount(UserHeader, {
			propsData: {
				user_name: "Test Name",
				organisation: "Test Organisation",
				total_rebate: 748698,
			}
		})

		console.log(wrapper);
	})
});

