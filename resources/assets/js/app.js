import Vue from 'vue';
import './bootstrap';
import BootstrapVue from 'bootstrap-vue';
import ToggleButton from 'vue-js-toggle-button';

Vue.use(ToggleButton);
Vue.use(BootstrapVue);

import FeatureFlagsDashboard from './components/FeatureFlagsDashboard';

const app = new Vue({
    el: '#feature-flags-app',
    render(h) {
        return h(FeatureFlagsDashboard);
    }
});
