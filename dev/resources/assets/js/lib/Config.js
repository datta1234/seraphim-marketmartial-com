const Config = {
    configs: {},
    get(path) {
        return path.split('.').reduce((acc, cur) => {
            if(acc && typeof acc[cur] !== 'undefined') {
                return acc[cur];
            }
            return undefined;
        }, Config.configs);
    }
};
export default Config;